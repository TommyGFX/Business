<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/data/business/comment/BusinessCommentList.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
require_once(WCF_DIR.'lib/data/business/ViewableBusinessLink.class.php');
require_once(WCF_DIR.'lib/page/util/menu/BusinessMenu.class.php');
require_once(WCF_DIR.'lib/data/message/sidebar/MessageSidebarFactory.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
 
/**
 * List all comments of a link.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page
 * @category 	WoltLab Community Framework 
 */
class BusinessCommentsListPage extends MultipleLinkPage {
	// system
	public $templateName = 'businessComments';
	
	/**
	 * link id
	 * 
	 * @var integer
	 */
	public $linkID = 0;
	
	/**
	 * link instance
	 * 
	 * @var ViewableBusinessLink
	 */
	public $link = null;
	
	/**
	 * comment list object
	 * 
	 * @var	BusinessCommentList
	 */
	public $commentList = null;
	
	/**
	 * list of general selections
	 * 
	 * @var	array
	 */
	public $generalSelection = array();
	
	/**
	 * comment id
	 *
	 * @var integer
	 */
	public $commentID = 0;
	
	/**
	 * comment object
	 *
	 * @var BusinessComment
	 */
	public $comment = null;
	
	/**
	 * sidebar factory object
	 * 
	 * @var	MessageSidebarFactory
	 */
	public $sidebarFactory = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// link id
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
		// create new link instance
		$this->link = new ViewableBusinessLink($this->linkID);
		// enter link
		$this->link->enter();
		
		// create a new category instance
		$this->category = BusinessCategory::getCategory($this->link->categoryID);
		// enter category
		$this->category->enter();
		
		// init comment list
		$this->commentList = new BusinessCommentList();
		$this->commentList->sqlConditions .= 'business_comment.linkID = '.$this->linkID;
		
		if (isset($_REQUEST['commentID'])) {
			$this->commentID = intval($_REQUEST['commentID']);
			$this->comment = new BusinessComment($this->commentID);
			if (!$this->comment->commentID || $this->comment->linkID != $this->linkID) {
				throw new IllegalLinkException();
			}
			
			$sql = "SELECT	COUNT(*) AS comments
				FROM 	wcf".WCF_N."_business_comment
				WHERE 	linkID = ".$this->linkID."
					AND time >= ".$this->comment->time;
			$result = WCF::getDB()->getFirstRow($sql);
			$this->pageNo = intval(ceil($result['comments'] / $this->itemsPerPage));
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read objects
		$this->commentList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->commentList->sqlLimit = $this->itemsPerPage;
		$this->commentList->readObjects();
		
		// init sidebars
		$this->sidebarFactory = new MessageSidebarFactory($this);
		foreach ($this->commentList->getObjects() as $comment) {
			$this->sidebarFactory->create($comment);
		}
		$this->sidebarFactory->init();
		
		// add general selecttion
			// visit link
			if ($this->category->getPermission('canVisitLink')) {
				$this->generalSelection[] = array(
					'icon' => StyleManager::getStyle()->getIconPath('visitsM.png'),
					'title' => WCF::getLanguage()->get('wcf.business.link.visit'),
					'value' => WCF::getLanguage()->get('wcf.business.link.visit.description'),
					'url' => 'index.php?page=BusinessLinkVisit&amp;linkID='.$this->linkID.SID_ARG_2ND,
						'active' => false
				);
			}
			
			// comment add
			if ($this->link->isCommentable() && $this->category->getPermission('canAddComment')) {
				$this->generalSelection[] = array(
					'icon' => StyleManager::getStyle()->getIconPath('messageAddM.png'),
					'title' => WCF::getLanguage()->get('wcf.business.comment.comments'),
					'url' => 'index.php?form=BusinessCommentAdd&amp;linkID='.$this->linkID.SID_ARG_2ND,
					'value' =>  WCF::getLanguage()->get('wcf.business.comment.commentAdd'),
					'active' => false
				);
			}
			// report link
			if ($this->category->getPermission('canReportLink')) {
				$this->generalSelection[] = array(
					'icon' => StyleManager::getStyle()->getIconPath('businessLinkReportM.png'),
					'title' => WCF::getLanguage()->get('wcf.business.link.report'),
					'url' => 'index.php?form=BusinessLinkReport&amp;linkID='.$this->linkID.SID_ARG_2ND,
					'value' => WCF::getLanguage()->get('wcf.business.link.report.description'),
					'active' => false
				);
			}
			// status comment edit
			if (WCF::getUser()->getPermission('mod.business.canEnableLinks')) {
				$this->generalSelection[] = array(
					'icon' => StyleManager::getStyle()->getIconPath('editM.png'),
					'title' => WCF::getLanguage()->get('wcf.business.link.statusComment'),
					'url' => 'index.php?page=BusinessLink&amp;linkID='.$this->linkID.SID_ARG_2ND.'#statusEdit',
					'value' => WCF::getLanguage()->get('wcf.business.link.changeStatus'),
					'active' => false
				);
			}

	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return $this->commentList->countObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'comments' => $this->commentList->getObjects(),
			'sidebarFactory' => $this->sidebarFactory,
			'link' => $this->link,
			'linkID' => $this->linkID,
			'category' => $this->category,
			'categoryID' => $this->category->categoryID,
			'allowSpidersToIndexThisPage' => true,
			'generalSelection' => $this->generalSelection,
			'tags' => (MODULE_TAGGING ? $this->link->getTags(WCF::getSession()->getVisibleLanguageIDArray()) : array())
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
	
		// set active link menu item
		BusinessMenu::getInstance()->linkID = $this->linkID;
		BusinessMenu::getInstance()->setActiveMenuItem('wcf.business.menu.link.commentsList');
	
		// set active menu items
		PageMenu::setActiveMenuItem('wcf.header.menu.business.links');
		
		// check permissions
		$this->category->checkPermission('canSeeComments');
		
		// check module options
		if (MODULE_BUSINESS != 1) {
			throw new IllegalLinkException();
		}
		
		// show page
		parent::show();
	
	}
}
?>
