<?php
// wcf imports
require_once(WCF_DIR.'lib/data/business/TaggedBusiness.class.php');

/**
 * Represents a list of tagged links.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework
 */
class TaggedBusinessOverviewList extends TaggedBusiness {
	/**
	 * Creates a new TaggedBusinessOverviewList object.
	 */
	public function __construct($tagID) {
		$this->sqlSelects = 'business_link.*';
		$this->sqlJoins = "LEFT JOIN wcf".WCF_N."_user user_table ON (user_table.userID = business_link.userID) ";
		parent::__construct($tagID);
	}
}
?>
