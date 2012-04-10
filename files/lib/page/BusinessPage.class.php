<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
// get cache
WCF::getCache()->addResource('businessStatistics', WCF_DIR.'cache/cache.businessStatistics.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessStatistics.class.php');

/**
 * Shows a list of all categories.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page
 * @category 	WoltLab Community Framework
 */
class BusinessPage extends MultipleLinkPage {
	// system
	public $templateName = 'business';
	public $categories = array();
	public $categoryStucture = array();
	public $categoryList = array();
	
	public $reportedLinks = 0;
	public $disabledLinks = 0;
	
	public $statistics = array();
    
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		WCF::getCache()->addResource('businessCategory', WCF_DIR.'cache/cache.businessCategory.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessCategory.class.php');

		// read category cache
		$this->categories = WCF::getCache()->get('businessCategory', 'categories');
		$this->categoryStructure = WCF::getCache()->get('businessCategory', 'categoryStructure');
		$this->makeCategoryList();

	}
	
		/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (WCF::getUser()->getPermission('mod.business.canSeeModerationOverview')) {		
			$sql = "SELECT	COUNT(*) AS disabledLinks
			FROM	wcf".WCF_N."_business_link
			WHERE isDisabled = 1";
			$row = WCF::getDB()->getFirstRow($sql);
			$this->disabledLinks = $row['disabledLinks'];
			
			$sql = "SELECT	COUNT(*) AS reportedLinks
			FROM	wcf".WCF_N."_business_link_report";
			$row = WCF::getDB()->getFirstRow($sql);
			$this->reportedLinks = $row['reportedLinks'];
		}
	
		$this->statistics = WCF::getCache()->get('businessStatistics');
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		
		// set active header menu item
		PageMenu::setActiveMenuItem('wcf.header.menu.business.links');
		
		// check permission
		WCF::getUser()->checkPermission('user.business.canViewBusiness');
		
		// check options
		if (!MODULE_BUSINESS) {
			throw new IllegalLinkException();
		}
		
		// show page
		parent::show();
	}
	
	
	/**
	* @see Page::assignVariables();
	 */
	public function assignVariables() {	
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'categories' => $this->categoryList,
			'reportedLinks' => $this->reportedLinks,
			'disabledLinks' => $this->disabledLinks,
			'statistics' => $this->statistics
		));
	}
	
	/**
	 * Renders one level of the category structure
	 *
	 * @param	integer		$parentID
	 */
	protected function makeCategoryList($parentID = 0) {		
		// render categories
		if (isset($this->categoryStructure[$parentID])) {
			foreach ($this->categoryStructure[$parentID] as $categoryID) {
				if (!$this->categories[$categoryID]->getPermission('canViewCategory')) continue;
				// read sub categories
				$subCategories = array();
				if (isset($this->categoryStructure[$categoryID])) {
					foreach ($this->categoryStructure[$categoryID] as $subCategoryID) {
						$subCategories[] = $this->categories[$subCategoryID];
					}
				}
				
				// save categories in array
				$this->categoryList[] = array(
					'category' => $this->categories[$categoryID],
					'subCategories' => $subCategories
				);
			}
		}
	}
	
}
?>
