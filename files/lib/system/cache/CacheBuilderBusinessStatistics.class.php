<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches general data.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.system.cache
 * @category 	WoltLab Community Framework
 */
class CacheBuilderBusinessStatistics implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array();
		
		// number of categories
		$sql = "SELECT	COUNT(*) AS categories
				FROM	wcf".WCF_N."_business_category";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['categories'] = $row['categories'];
		// number of links
		$sql = "SELECT	COUNT(*) AS links
				FROM wcf".WCF_N."_business_link";			
		$row = WCF::getDB()->getFirstRow($sql);
		$data['links'] = $row['links'];
		// number of comments
		$sql = "SELECT 	COUNT(*) AS comments
			FROM 	wcf".WCF_N."_business_comment";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['comments'] = $result['comments'];
		// number of visits
		$sql = "SELECT	SUM(hits) AS visits
				FROM	wcf".WCF_N."_business_link";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['visits'] = $row['visits'];
		
		return $data;
	}
}
?>
