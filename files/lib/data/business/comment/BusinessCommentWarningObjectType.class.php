<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/infraction/warning/object/WarningObjectType.class.php');
require_once(WCF_DIR.'lib/data/business/comment/BusinessCommentWarningObject.class.php');
 
/**
 * An implementation of WarningObjectType to support the usage of a Business comment as a warning object.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.comment
 * @category 	WoltLab Community Framework 
 */
class BusinessCommentWarningObjectType implements WarningObjectType {
	/**
	 * @see WarningObjectType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		if (is_array($objectID)) {
			$comments = array();
			$sql = "SELECT		*
				FROM 		wcf".WCF_N."_business_comment
				WHERE 		commentID IN (".implode(',', $objectID).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$comments[$row['commentID']] = new BusinessCommentWarningObject(null, $row);
			}
			
			return (count($comments) > 0 ? $comments : null); 
		}
		else {
			// get object
			$comment = new BusinessCommentWarningObject($objectID);
			if (!$comment->commentID) return null;
			
			// return object
			return $comment;
		}
	}
}
?>
