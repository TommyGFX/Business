<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Updates links in a category.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	acp.action
 * @category 	WoltLab Community Framework
 */
class BusinessCategoriesLinksUpdaterAction extends AbstractAction {
	public $categories = array();

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// get category ids
		$categoryIDs = '';
		$sql = "SELECT		categoryID
				FROM		wcf".WCF_N."_business_category
				ORDER BY	categoryID";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($categoryIDs)) $categoryIDs .= ',';
			$categoryIDs .= $row['categoryID'];
		}
		
		if ($categoryIDs) {
		
		// update categories
		$sql = "UPDATE wcf".WCF_N."_business_category business_category
				SET		links = (
						SELECT	COUNT(*)
						FROM	wcf".WCF_N."_business_link business_link
						WHERE	business_link.categoryID = business_category.categoryID
							AND isDisabled = 0
						)
			WHERE		business_category.categoryID IN (".$categoryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		// clear cache
		WCF::getCache()->clear(WCF_DIR.'cache', 'cache.businessCategory.php');
		
		$this->executed();
		}		
		
		// forward to category list page
		HeaderUtil::redirect('index.php?page=BusinessCategoryList&update=1&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
}
?>
