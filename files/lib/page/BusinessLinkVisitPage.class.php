<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
require_once(WCF_DIR.'lib/data/business/ViewableBusinessLink.class.php');
require_once(WCF_DIR.'lib/page/util/menu/BusinessMenu.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
// get cache
WCF::getCache()->addResource('businessStatistics', WCF_DIR.'cache/cache.businessStatistics.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessStatistics.class.php');

/**
 * Shows the link-visit page.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page
 * @category 	WoltLab Community Framework
 */
class BusinessLinkVisitPage extends AbstractPage {	
	// system
	public $templateName = 'businessLinkVisit';
	public $visit = 0;

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
	 * list of general selections
	 * 
	 * @var	array
	 */
	public $generalSelections = array();
	
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
		
		// visit
		if (isset($_REQUEST['visit'])) $this->visit = intval($_REQUEST['visit']);
		
		// redirect to the link
		if (!$this->link->isReported || $this->visit) {
			if (!WCF::getSession()->spiderID) {
				// count redirects
				$sql = "UPDATE	wcf".WCF_N."_business_link
					SET	hits = hits + 1
					WHERE	linkID = ".$this->linkID;
				WCF::getDB()->registerShutdownUpdate($sql);
				
				// clear statistics cache
				WCF::getCache()->clearResource('businessStatistics');
			}
			
			// do redirect
			HeaderUtil::redirect($this->link->url, false);
			exit;
		}
		
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
			// edit status comment
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
			'allowSpidersToIndexThisPage' => true
		));
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
		$this->category->checkPermission('canVisitLink');
		
		// check module options
		if (MODULE_BUSINESS != 1) {
			throw new IllegalLinkException();
		}
		
		// show page
		parent::show();
	}
}
?>
