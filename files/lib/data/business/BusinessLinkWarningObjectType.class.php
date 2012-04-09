<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/infraction/warning/object/WarningObjectType.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessLinkWarningObject.class.php');
 
/**
 * An implementation of WarningObjectType to support the usage of a link as a warning object.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework 
 */
class BusinessLinkWarningObjectType implements WarningObjectType {
	/**
	 * @see WarningObjectType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		if (is_array($objectID)) {
			$links = array();
			$sql = "SELECT		*
				FROM 		wcf".WCF_N."_business_link
				WHERE 		linkID IN (".implode(',', $objectID).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$links[$row['linkID']] = new BusinessLinkWarningObject(null, $row);
			}
			
			return (count($links) > 0 ? $links : null); 
		}
		else {
			// get object
			$link = new BusinessLinkWarningObject($objectID);
			if (!$link->linkID) return null;
			
			// return object
			return $link;
		}
	}
}
?>
