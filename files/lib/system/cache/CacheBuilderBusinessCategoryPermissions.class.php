<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the board permissions for a combination of user groups.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.system.cache
 * @category 	Burning Board
 */
class CacheBuilderBusinessCategoryPermissions implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $groupIDs) = explode('-', $cacheResource['cache']);
		$data = array();
		
		$sql = "SELECT		*
			FROM		wcf".WCF_N."_business_category_to_group
			WHERE		groupID IN (".$groupIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$categoryID = $row['categoryID'];
			unset($row['categoryID'], $row['groupID']);
			
			foreach ($row as $permission => $value) {
				if ($value == -1) continue;
				
				if (!isset($data[$categoryID][$permission])) $data[$categoryID][$permission] = $value;
				else $data[$categoryID][$permission] = $value || $data[$categoryID][$permission];
			}
		}
		
		if (count($data)) {
			require_once(WCF_DIR.'lib/data/buiness/category/BusinessCategory.class.php');
			RefeListCategory::inheritPermissions(0, $data);
		}
		
		$data['groupIDs'] = $groupIDs;
		return $data;
	}
}
?>
