<?php
// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * BusinessLinkLocation is an implementation of Location for the Business Database link page.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page.location
 * @category 	WoltLab Community Framework
 */
class BusinessLinkLocation implements Location {
	public $cachedLinkIDArray = array();
	public $links = null;
	
	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {
		$this->cachedLinkIDArray[] = $match[1];
	}
	
	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->links == null) {
			$this->readLinks();
		}
		
		$linkID = $match[1];
		if (!isset($this->links[$linkID])) {
			return '';
		}
		
		return WCF::getLanguage()->get($location['locationName'], array(
			'$link' => '<a href="index.php?page=BusinessLink&amp;linkID='.$linkID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->links[$linkID]['subject']).'</a>'
		));
	}
	
	/**
	 * Gets entries.
	 */
	protected function readLinks() {
		$this->links = array();
		
		if (!count($this->cachedLinkIDArray)) {
			return;
		}
		
		$sql = "SELECT		business_link.*
			FROM		wcf".WCF_N."_business_link business_link
			WHERE		business_link.linkID IN (".implode(',', $this->cachedLinkIDArray).")
					AND isDisabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->links[$row['linkID']] = $row;
		}
	}
}
?>
