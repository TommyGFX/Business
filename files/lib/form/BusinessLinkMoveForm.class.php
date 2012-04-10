<?php
// wcf imports
require_once(WCF_DIR.'lib/form/AbstractForm.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategoryEditor.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessEditor.class.php');
require_once(WCF_DIR.'lib/data/business/ViewableBusinessLink.class.php');
require_once(WCF_DIR.'lib/page/util/menu/BusinessMenu.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Moves a link.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.form
 * @category 	WoltLab Community Framework 
 */
class BusinessLinkMoveForm extends AbstractForm {
	// system
	public $templateName = 'businessLinkMove';

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
	public $link;
	
	/**
	 * category instance
	 * 
	 * @var BusinessCategory
	 */
	public $category;

	/**
	 * @see Page::readParameters()
	 */		
	public function readParameters() {		
		parent::readParameters();
		
		// get link id
		if (isset($_GET['linkID'])) $this->linkID = intval($_GET['linkID']);
		
		// create new link editor instance
		$this->link = new BusinessEditor($this->linkID);
		// enter link
		$this->link->enter();
		
		// get category instance
		$this->category = BusinessCategory::getCategory($this->link->categoryID);		
		// enter category
		$this->category->enter();
		
	}

	/**
	 * @see Form::readFormParameters()
	 */		
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['categoryID'])) $this->categoryID = $_POST['categoryID'];
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
					'active' => true
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
	 * @see Form::validate()
	 */		
	public function validate() {
		parent::validate();	
		
		// check if category id is empty
		if (empty($this->categoryID)) {
			throw new UserInputException('categoryID', 'empty');
		}
		
		// check if category is the same as before
		if ($this->categoryID == $this->category->categoryID) {
			throw new UserInputException('categoryID', 'illegalCategory');
		}		
		
		// check if category id is valid
		try {
			$this->newCategory = BusinessCategory::getCategory($this->categoryID);
		}
		catch (IllegalLinkException $e) {
			throw new UserInputException('categoryID', 'illegalCategory');
		}
		
	}

	/**
	 * @see Form::save()
	 */	
	public function save() {
		parent::save();

		// update link
		$this->link->update(array('categoryID' => $this->categoryID));
		// update links of the old category
		$this->categoryEditor = new BusinessCategoryEditor($this->category->categoryID);
		if ($this->link->isDisabled == 0) {
			$this->categoryEditor->updateLinks(-1);
			// clear cache
			WCF::getCache()->clearResource('businessCategory');
		}
		// update links of the new category		
		$this->newCategoryEditor = new BusinessCategoryEditor($this->newCategory->categoryID);
		if ($this->link->isDisabled == 0) {
			$this->newCategoryEditor->updateLinks(1);
			// clear cache
			WCF::getCache()->clearResource('businessCategory');
		}
		
		// reset tag cloud cache
		WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.businessCategoryTagCloud-*', true);
		
		// forward
		HeaderUtil::redirect('index.php?page=BusinessLink&linkID='.$this->link->linkID.SID_ARG_2ND_NOT_ENCODED);
		exit;
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
		if ((!$this->category->getPermission('canEditOwnLink') && !$this->link->isOwnLink() && !WCF::getUser()->userID) || !WCF::getUser()->getPermission('mod.business.canEditLinks')) {
			throw new PermissionDeniedException();
		}
		
		// check module options
		if (MODULE_BUSINESS != 1) {
			throw new IllegalLinkException();
		}
		
		// show page
		parent::show();		
	}

	/**
	 * @see Page::assignVariables()
	 */	
	public function assignVariables() {
		parent::assignVariables();
	
		// assign variables
		WCF::getTPL()->assign(array(
			'categorySelect' => BusinessCategory::getSelect(),
			'link' => new ViewableBusinessLink($this->linkID),
			'linkID' => $this->linkID,
			'category' => $this->category,
			'categoryID' => $this->category->categoryID,
			'tags' => (MODULE_TAGGING ? $this->link->getTags(WCF::getSession()->getVisibleLanguageIDArray()) : array()),
			'generalSelection' => $this->generalSelection
		));
	}
}
?>
