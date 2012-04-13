<?php
// wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');
require_once(WCF_DIR.'lib/system/session/Session.class.php');

/**
 * Updates links in a category.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	acp.action
 * @category 	WoltLab Community Framework
 */
class BusinessCategoriesLinksUpdater implements Cronjob {
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
	}
	
}
?>
