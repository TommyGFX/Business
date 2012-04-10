<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategoryEditor.class.php');

/**
 * Sorts categories.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	acp.action
 * @category 	WoltLab Community Framework
 */
class BusinessCategorySortAction extends AbstractAction {
	/**
	 * New positions
	 *
	 * @var array
	 */
	public $positions = array();
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_POST['categoryListPositions']) && is_array($_POST['categoryListPositions'])) $this->positions = ArrayUtil::toIntegerArray($_POST['categoryListPositions']);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
	
		// check permission
		WCF::getUser()->checkPermission('admin.business.canEditCategory');

		// delete old positions
		$sql = "TRUNCATE wcf".WCF_N."_business_category_structure";
		WCF::getDB()->sendQuery($sql);
		
		// update postions
		foreach ($this->positions as $categoryID => $data) {
			foreach ($data as $parentID => $position) {
				BusinessCategoryEditor::updatePosition(intval($categoryID), intval($parentID), intval($position));
			}
		}
		
		// insert default values
		$sql = "INSERT IGNORE INTO	wcf".WCF_N."_business_category_structure
						(parentID, categoryID)
			SELECT			parentID, categoryID
			FROM			wcf".WCF_N."_business_category";
		WCF::getDB()->sendQuery($sql);
		
		// reset cache
		BusinessCategory::resetCache();
		$this->executed();
		
		// forward to list page
			HeaderUtil::redirect('index.php?page=BusinessCategoryList&successfulSorting=1&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>
