<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/search/AbstractSearchableMessageType.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessSearchResult.class.php');
 
/**
 * An implementation of SearchableMessageType for searching in the Business Database.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework
 */
class BusinessSearch extends AbstractSearchableMessageType {
	protected $messageCache = array();
	
	/**
	 * Caches the data of the messages with the given ids.
	 */
	public function cacheMessageData($messageIDs, $additionalData = null) {
		// get links
		$sql = "SELECT		business_category.*, business_link.*
			FROM		wcf".WCF_N."_business_link business_link
			LEFT JOIN	wcf".WCF_N."_business_category business_category
			ON		(business_category.categoryID = business_link.categoryID)
			WHERE		business_link.linkID IN (".$messageIDs.")
					AND isDisabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$link = new BusinessSearchResult(null, $row);
			$this->messageCache[$row['linkID']] = array('type' => 'businessLink', 'message' => $link);
		}
	}
	
	/**
	 * @see SearchableMessageType::getMessageData()
	 */
	public function getMessageData($messageID, $additionalData = null) {
		if (isset($this->messageCache[$messageID])) return $this->messageCache[$messageID];
		return null;
	}
	
	/**
	 * Returns the database table name for this search type.
	 */
	public function getTableName() {
		return 'wcf'.WCF_N.'_business_link';
	}
	
	/**
	 * Returns the message id field name for this search type.
	 */
	public function getIDFieldName() {
		return 'linkID';
	}
	
	/**
	 * @see SearchableMessageType::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'searchResultBusiness';
	}
}
?>
