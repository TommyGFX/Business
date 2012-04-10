<?php
// wcf imports
require_once(WCF_DIR.'lib/data/business/comment/BusinessComment.class.php');

/**
 * Provides functions to edit, add or delete this comment.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.comment
 * @category 	WoltLab Community Framework 
 */

class BusinessCommentEditor extends BusinessComment {
	
	/**
	 * Creates a new comment.
	 *
	 * @param 	integer		$linkID
	 * @param 	string		$username
	 * @param 	string		$message
	 * @param 	array<integer>	$options
	 * @return	BusinessCommentEditor
	 */
	public static function create($linkID, $username, $message, $options) {
		$sql = "INSERT INTO	wcf".WCF_N."_business_comment
					(linkID, userID, username, message, time, enableSmilies, enableHtml, enableBBCodes)
			VALUES		(".$linkID.", ".WCF::getUser()->userID.", '".escapeString($username)."', '".escapeString($message)."', ".TIME_NOW.", ".$options['enableSmilies'].", ".$options['enableHtml'].", ".$options['enableBBCodes'].")";
		WCF::getDB()->sendQuery($sql);
		$commentID = WCF::getDB()->getInsertID("wcf".WCF_N."_business_link_comment", 'commentID');
		
		return new BusinessCommentEditor($commentID);
	}
	
	/**
	 * Creates a preview of a comment.
	 *
	 * @param 	string		$message
	 * @param 	boolean		$enableSmilies
	 * @param 	boolean		$enableHtml
	 * @param 	boolean		$enableBBCodes
	 */
	public static function createPreview($message, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
		$row = array(
			'commentID' => 0,
			'message' => $message,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes
		);

		require_once(WCF_DIR.'lib/data/business/comment/ViewableBusinessComment.class.php');
		$comment = new ViewableBusinessComment(null, $row);
		return $comment->getFormattedMessage();
	}
	
	/**
	 * Updates a comment.
	 *
	 * @param 	string		$message
	 * @param 	array<integer>	$options
	 */
	public function update($message, $options) {
		$sql = "UPDATE	wcf".WCF_N."_business_comment
			SET	message = '".escapeString($message)."',
				enableSmilies = ".$options['enableSmilies'].",
				enableHtml = ".$options['enableHtml'].",
				enableBBCodes = ".$options['enableBBCodes']."
			WHERE	commentID = ".$this->commentID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes a comment.
	 */
	public function delete() {
		$sql = "DELETE FROM	wcf".WCF_N."_business_comment
			WHERE		commentID = ".$this->commentID;
		WCF::getDB()->sendQuery($sql);		
	}
}
?>
