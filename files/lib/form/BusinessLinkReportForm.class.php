<?php
// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
require_once(WCF_DIR.'lib/data/business/ViewableBusinessLink.class.php');
require_once(WCF_DIR.'lib/page/util/menu/BusinessMenu.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessEditor.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
 
/**
 * Shows the report form for links.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.form
 * @category 	WoltLab Community Framework
 */
class BusinessLinkReportForm extends MessageForm {
	// system
	public $templateName = 'businessLinkReport';

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
	
	// form parameters
	public $username = '';
	public $useCaptcha = 1;
	
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
		
		// check whether this link was already reported
		if ($this->link->isReported == 1) {
			throw new NamedUserException(WCF::getLanguage()->get('wcf.business.report.wasAlreadyReported'));
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
					'active' => true
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
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// username
		$this->validateUsername();
	}
	
	/**
	 * Validates the username.
	 */
	protected function validateUsername() {
		// only for guests
		if (WCF::getUser()->userID == 0) {
			// username
			if (empty($this->username)) {
				throw new UserInputException('username');
			}
			if (!UserUtil::isValidUsername($this->username)) {
				throw new UserInputException('username', 'notValid');
			}
			if (!UserUtil::isAvailableUsername($this->username)) {
				throw new UserInputException('username', 'notAvailable');
			}
			
			WCF::getSession()->setUsername($this->username);
		}
		else {
			$this->username = WCF::getUser()->username;
		}
	}
	
	/**
	 * Does nothing.
	 */
	protected function validateSubject() {}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save report
		$report = BusinessEditor::createReport($this->linkID, $this->username, $this->text);
		$this->saved();
		
		HeaderUtil::redirect('index.php?page=BusinessLink&linkID='.$this->linkID.SID_ARG_2ND_NOT_ENCODED);
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
			'username' => $this->username
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
		$this->category->checkPermission('canReportLink');
		
		// check module options
		if (MODULE_BUSINESS != 1) {
			throw new IllegalLinkException();
		}
		
		// show page
		parent::show();
	}
}
?>
