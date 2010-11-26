<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
require_once(WCF_DIR.'lib/data/business/ViewableBusinessLink.class.php');
require_once(WCF_DIR.'lib/page/util/menu/BusinessMenu.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows the link page.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page
 * @category 	WoltLab Community Framework
 */
class BusinessLinkPage extends AbstractPage {
	// system
	public $templateName = 'businessLink';

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
	 * contains link tags
	 * 
	 * @var array
	 */
	public $tags = array();
	
	/**
	 * list of general selection
	 * 
	 * @var	array
	 */
	public $generalSelection = array();
	
	/**
	 * attachment list object
	 * 
	 * @var	MessageAttachmentList
	 */
	public $attachmentList = null;
	
	/**
	 * list of attachments
	 * 
	 * @var	array<Attachment>
	 */
	public $attachments = array();

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
		
		// handle rating
		$this->handleRating();
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
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
			
			// add comment
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
		
		// read attachments
		if (MODULE_ATTACHMENT == 1 && $this->link->attachments > 0) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentList.class.php');
			$this->attachmentList = new MessageAttachmentList($this->linkID, 'businessLink', '', WCF::getPackageID('de.wcf.tommygfx.business'));
			$this->attachmentList->readObjects();
			$this->attachments = $this->attachmentList->getSortedAttachments($this->category->getPermission('canViewAttachmentPreview'));
			
			// set embedded attachments
			if ($this->category->getPermission('canViewAttachmentPreview')) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachments);
			}
			
			// remove embedded attachments from list
			if (count($this->attachments) > 0) {
				MessageAttachmentList::removeEmbeddedAttachments($this->attachments);
			}
		}

	}
	
	/**
	* @see Page::assignVariables();
	*/
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'link' => $this->link,
			'linkID' => $this->linkID,
			'category' => $this->category,
			'categoryID' => $this->category->categoryID,
			'tags' => (MODULE_TAGGING ? $this->link->getTags(WCF::getSession()->getVisibleLanguageIDArray()) : array()),
			'generalSelection' => $this->generalSelection,
			'allowSpidersToIndexThisPage' => true,
			'attachments' => $this->attachments
		));
	}
	
	/**
	 * Handles a rating request.
	 */
	public function handleRating() {
		if (isset($_POST['rating'])) {
			$rating = intval($_POST['rating']);
			
			// rating is disabled
			if ($this->link->ratingDisabled) {
				throw new IllegalLinkException();
			}
			
			// user has already rated this link and the rating is NOT changeable
			if ($this->link->userRating !== null && !$this->link->userRating) {
				throw new IllegalLinkException();
			}
			
			// check permission
			$this->category->checkPermission('canRateLink');
			
			// illegal rating
			if ($rating < 1 || $rating > 5) {
				throw new IllegalLinkException();
			}
			
			// change rating
			if ($this->link->userRating) {
				$sql = "UPDATE 	wcf".WCF_N."_business_link_rating
					SET 	rating = ".$rating."
					WHERE 	linkID = ".$this->linkID."
						AND ".(WCF::getUser()->userID ? "userID = ".WCF::getUser()->userID : "ipAddress = '".escapeString(WCF::getSession()->ipAddress)."'");
				WCF::getDB()->sendQuery($sql);
				
				$sql = "UPDATE 	wcf".WCF_N."_business_link
					SET 	rating = rating + ".$rating." - ".$this->link->userRating."
					WHERE 	linkID = ".$this->linkID;
				WCF::getDB()->sendQuery($sql);
			}	
			// insert new rating
			else {
				$sql = "INSERT INTO	wcf".WCF_N."_business_link_rating
								(linkID, rating, userID, ipAddress)
					VALUES		(".$this->linkID.",
								".$rating.",
								".WCF::getUser()->userID.",
								'".escapeString(WCF::getSession()->ipAddress)."')";
				WCF::getDB()->sendQuery($sql);	
				
				$sql = "UPDATE 	wcf".WCF_N."_business_link
					SET 	ratings = ratings + 1,
							rating = rating + ".$rating."
					WHERE 	linkID = ".$this->linkID;
				WCF::getDB()->sendQuery($sql);	
			}
		
			HeaderUtil::redirect('index.php?page=BusinessLink&linkID='.$this->linkID.SID_ARG_2ND_NOT_ENCODED);
			exit;
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
	
		// set active link menu item
		BusinessMenu::getInstance()->linkID = $this->linkID;
		BusinessMenu::getInstance()->setActiveMenuItem('wcf.business.menu.link.link');
		
		// set active menu items
		PageMenu::setActiveMenuItem('wcf.header.menu.business.links');
		
		// check permission
		$this->category->checkPermission('canEnterLink');
		
		// check module options
		if (MODULE_BUSINESS != 1) {
			throw new IllegalLinkException();
		}
		
		// show page
		parent::show();
	}
	
}
?>
