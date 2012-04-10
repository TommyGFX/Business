<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
 
/**
 * Shows an overview of all categories.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	acp.page
 * @category 	WoltLab Community Framework
 */
class BusinessCategoryListPage extends AbstractPage {
	public $templateName = 'businessCategoryList';
	public $categories = array();
	public $categoryStucture = array();
	public $categoryList = array();
	public $successfulSorting = false;
	public $update = false;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['successfulSorting'])) $this->successfulSorting = true;
		
		if (isset($_REQUEST['update'])) $this->update = true;
		
		WCF::getCache()->addResource('businessCategory', WCF_DIR.'cache/cache.businessCategory.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessCategory.class.php');

	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active acpmenu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.content.business.categories');
		
		// read categories
		$this->categories = WCF::getCache()->get('businessCategory', 'categories');
		$this->categoryStructure = WCF::getCache()->get('businessCategory', 'categoryStructure');
		$this->makeCategoryList();
		
		// check module options
		if (MODULE_BUSINESS != 1) {
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
				
		WCF::getTPL()->assign(array(
			'categories' => $this->categoryList,
			'successfulSorting' => $this->successfulSorting,
			'update' => $this->update
		));
	}
	
	/**
	 * Make the category structure for an overview.
	 */
	protected function makeCategoryList($parentID = 0, $depth = 1, $openParents = 0) {
		if (!isset($this->categoryStructure[$parentID])) return;
		
		$i = 0;
		$count = count($this->categoryStructure[$parentID]);
		foreach ($this->categoryStructure[$parentID] as $categoryID) {
			// get category
			$category = $this->categories[$categoryID];
			
			// update options
			$childrenOpenParents = $openParents + 1;
			
			// check if category should be displayed
			$hasChildren = isset($this->categoryStructure[$categoryID]);
			$last = ($i == ($count - 1));
			if ($hasChildren && !$last) $childrenOpenParents = 1;
				
			// update category list
			$this->categoryList[] = array(
				'depth' => $depth,
				'hasChildren' => $hasChildren,
				'openParents' => ((!$hasChildren && $last) ? ($openParents) : (0)),
				'category' => $category,
				'maxPosition' => $count,
				'position' => $i+1
			);
			
			// make next level of the category list
			$this->makeCategoryList($categoryID, $depth + 1, $childrenOpenParents);
			
			$i++;
		}
	}
}
?>
