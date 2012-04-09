<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/Message.class.php');
 
/**
 * Represents a comment
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.comment
 * @category 	WoltLab Community Framework 
 */
class BusinessComment extends Message {
	/**
	 * Creates a new BusinessComment object.
	 *
	 * @param	integer		$commentID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($commentID, $row = null) {
		if ($commentID !== null) {
			$sql = "SELECT	*
				FROM 	wcf".WCF_N."_business_comment
				WHERE 	commentID = ".$commentID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
		$this->messageID = $row['commentID'];
	}
	
	/**
	 * Returns true, if this user can edit this comment.
	 *
	 * @return boolean
	 */
	public function isEditable() {
		return (($this->userID == WCF::getUser()->userID && WCF::getUser()->getPermission('user.business.canEditOwnComment'))
			|| (WCF::getUser()->getPermission('mod.business.canEditComments')));
	}
	
	/**
	 * Returns true, if this user can delete this comment.
	 *
	 * @return boolean
	 */
	public function isDeletable() {
		return (($this->userID == WCF::getUser()->userID && WCF::getUser()->getPermission('user.business.canEditOwnComment'))
			|| (WCF::getUser()->getPermission('mod.business.canDeleteComments')));
	}
	
}
?>
