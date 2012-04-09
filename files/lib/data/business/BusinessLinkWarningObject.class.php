<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/infraction/warning/object/WarningObject.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessLink.class.php');
 
/**
 * An implementation of WarningObject to support the usage of a link as a warning object.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework 
 */
class BusinessLinkWarningObject extends BusinessLink implements WarningObject {
	/**
	 * @see WarningObject::getTitle()
	 */
	public function getTitle() {
		return $this->data['subject'];
	}
	
	/**
	 * @see WarningObject::getURL()
	 */
	public function getURL() {
		return 'index.php?page=BusinessLink&linkID='.$this->linkID;
	}
}
?>
