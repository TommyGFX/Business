<?php
// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');
 
/**
 * BusinessCategoryLocation is an implementation of Location for the Business Database category page.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page.location
 * @category 	WoltLab Community Framework
 */
class BusinessCategoryLocation implements Location {
	public $cachedCategoryIDArray = array();
	public $categories = null;
	
	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {
		$this->cachedCategoryIDArray[] = $match[1];
	}
	
	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->categories == null) {
			$this->readCategories();
		}
		
		$categoryID = $match[1];
		if (!isset($this->categories[$categoryID])) {
			return '';
		}
		
		return WCF::getLanguage()->get($location['locationName'], array(
			'$category' => '<a href="index.php?page=BusinessCategory&amp;categoryID='.$categoryID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->categories[$categoryID]['title']).'</a>'
		));
	}
	
	/**
	 * Gets entries.
	 */
	protected function readCategories() {
		$this->categories = array();
		
		if (!count($this->cachedCategoryIDArray)) {
			return;
		}
		
		$sql = "SELECT		business_category.*
			FROM		wcf".WCF_N."_business_category business_category
			WHERE		business_category.categoryID IN (".implode(',', $this->cachedCategoryIDArray).")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->categories[$row['categoryID']] = $row;
		}
	}
}
?>
