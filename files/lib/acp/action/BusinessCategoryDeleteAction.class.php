<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategoryEditor.class.php');
// get cache
WCF::getCache()->addResource('businessStatistics', WCF_DIR.'cache/cache.businessStatistics.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessStatistics.class.php');
 
/**
 * Deletes a category
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	acp.action
 * @category 	WoltLab Community Framework
 */
class BusinessCategoryDeleteAction extends AbstractAction {
	/**
	 * category id
	 * 
	 * @var	integer
	 */
	public $categoryID = 0;
	
	/**
	 * Business category editor object
	 * 
	 * @var	BusinessCategoryEditor
	 */
	public $category = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category id
		if (isset($_GET['categoryID'])) $this->categoryID = intval($_GET['categoryID']);
		$this->category = new BusinessCategoryEditor($this->categoryID);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
				
		// delete category
		$this->category->delete();
		
		// reset category cache
		BusinessCategory::resetCache();
		
		// clear statistics cache
		WCF::getCache()->clearResource('businessStatistics');
		
		// forward to category list
		HeaderUtil::redirect('index.php?page=BusinessCategoryList'.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>
