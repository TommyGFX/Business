<?php
// wcf imports
require_once(WCF_DIR.'lib/data/tag/TagCloud.class.php');
 
/**
 * Gets the tags of links in a category.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.category
 * @category 	WoltLab Community Framework
 */
class BusinessCategoryTagCloud extends TagCloud {
	/**
	 * Contructs a new BusinessCategoryTagCloud.
	 *
	 * @param	integer		$categoryID
	 * @param	array<integer>	$languageIDArray
	 */
	public function __construct($categoryID, $languageIDArray = array()) {
		$this->categoryID = $categoryID;
		$this->languageIDArray = $languageIDArray;
		if (!count($this->languageIDArray)) $this->languageIDArray = array(0);
		
		// init cache
		$this->cacheName = 'businessCategoryTagCloud-'.$this->categoryID.'-'.implode(',', $this->languageIDArray);
		$this->loadCache();
	}
	
	/**
	 * Loads the tag cloud cache.
	 */
	public function loadCache() {
		if ($this->tags !== null) return;

		// get cache
		WCF::getCache()->addResource($this->cacheName, WCF_DIR.'cache/cache.businessCategoryTagCloud-'.$this->categoryID.'-'.StringUtil::getHash(implode(',', $this->languageIDArray)).'.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessCategoryTagCloud.class.php', 0, 86400);
		$this->tags = WCF::getCache()->get($this->cacheName);
	}
}
?>
