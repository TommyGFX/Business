<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
 
/**
 * Caches business categories.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.system.cache
 * @category 	WoltLab Community Framework
 */
class CacheBuilderBusinessCategory implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array('categories' => array(), 'categoryStructure' => array());
				
		// cache categories
		$sql = "SELECT	* 
				FROM	wcf".WCF_N."_business_category";
		$result = WCF::getDB()->sendQuery($sql);
		
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['categories'][$row['categoryID']] = new BusinessCategory(null, $row);
		}
			
		// cache category structure
		$sql = "SELECT		* 
				FROM		wcf".WCF_N."_business_category_structure
				ORDER BY 	parentID, position";
		$result = WCF::getDB()->sendQuery($sql);
		
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['categoryStructure'][$row['parentID']][] = $row['categoryID'];		
		}
		
		return $data;
	}
}
?>
