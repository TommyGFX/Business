<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR.'lib/data/user/group/Group.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategoryEditor.class.php');
// get cache
WCF::getCache()->addResource('businessStatistics', WCF_DIR.'cache/cache.businessStatistics.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessStatistics.class.php');

/**
 * Shows the add form for a new business category.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	acp.form
 * @category 	WoltLab Community Framework 
 */
class BusinessCategoryAddForm extends ACPForm {
	// system
	public $templateName = 'businessCategoryAdd';
	public $activeMenuItem = 'wcf.acp.menu.link.content.business.categoryAdd';
	public $neededPermissions = 'admin.business.canAddCategory';
	public $activeTabMenuItem = 'data';
	
	/**
	 * category editor object
	 * 
	 * @var	businessCategoryEditor
	 */
	public $category;
	
	/**
	 * list of all categories
	 * 
	 * @var array
	 */
	public $categorySelect = array();
	
	/**
	 * list of additional fields
	 * 
	 * @var	array
	 */
	public $additionalFields = array();
	
	// parameters
	public $parentID = 0;
	public $position = '';
	public $title = '';
	public $description = '';
	public $allowDescriptionHtml = 0;
	public $image = '';
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get parent id
		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (!empty($_POST['position'])) $this->position = intval($_POST['position']);
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['allowDescriptionHtml'])) $this->allowDescriptionHtml = intval($_POST['allowDescriptionHtml']);
		if (isset($_POST['image'])) $this->image = StringUtil::trim($_POST['image']);
		if (isset($_POST['activeTabMenuItem'])) $this->activeTabMenuItem = $_POST['activeTabMenuItem'];
		
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// reload category select
		$this->readCategorySelect();
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// validate title
		if (empty($this->title)) {
			throw new UserInputException('title, empty');
		}
		
		// validate parent category id
		$this->validateParentID();
	}
	
	/**
	 * Validates the parent id.
	 */
	protected function validateParentID() {
		if ($this->parentID) {
			try {
				BusinessCategory::getCategory($this->parentID);
			}
			catch (IllegalLinkException $e) {
				throw new UserInputException('parentID', 'invalid');
			}
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save board
		$this->category = BusinessCategoryEditor::create($this->parentID, ($this->position ? $this->position : 0), $this->title, $this->description, $this->allowDescriptionHtml, $this->image);
		
		// call event
		$this->saved();
		
		// clear statistics cache
		WCF::getCache()->clearResource('businessStatistics');
		
		// reset category cache
		BusinessCategoryEditor::resetCache();
		
		// clear form input data
		$this->title = $this->description = $this->image = '';
		$this->parentID = $this->position = $this->allowDescriptionHtml = 0;
		$this->permissions = array();
		
		// show success message
		WCF::getTPL()->assign(array(
			'success' => true
		));
	}
	
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'parentID' => $this->parentID,
			'position' => $this->position,
			'title' => $this->title,
			'description' => $this->description,
			'allowDescriptionHtml' => $this->allowDescriptionHtml,
			'image' => $this->image,
			'categorySelect' => $this->categorySelect,
		));
	}
	
	/**
	 * Gets a list of available categories
	 */
	protected function readCategorySelect() {
		$this->categorySelect = BusinessCategory::getSelect();
	}
	
	/**
	 * @see Page::show()
	 */			
	public function show() {		
		// check module options
		if (MODULE_BUSINESS != 1) {
			throw new IllegalLinkException();
		}
		
		// show page
		parent::show();
	}
	
}
?>
