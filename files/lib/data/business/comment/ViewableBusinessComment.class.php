<?php
// wcf imports
require_once(WCF_DIR.'lib/data/business/comment/BusinessComment.class.php');
require_once(WCF_DIR.'lib/data/message/sidebar/MessageSidebarObject.class.php');

/**
 * Represents a viewable comment.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.comment
 * @category 	WoltLab Community Framework 
 */
class ViewableBusinessComment extends BusinessComment implements MessageSidebarObject {
	/**
	 * user object
	 *
	 * @var UserProfile
	 */
	protected $user = null;

	/**
	 * Creates a new ViewableBusinessComment object.
	 *
	 * @param	integer		$commentID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($commentID, $row = null) {
		if ($commentID !== null) {
			$sql = "SELECT		user_table.*, business_comment.*
				FROM		wcf".WCF_N."_business_comment business_comment
				LEFT JOIN	wcf".WCF_N."_user user_table
				ON		(user_table.userID = business_comment.userID)
				WHERE 		business_comment.commentID = ".$commentID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		DatabaseObject::__construct($row);
	}
	
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		$this->user = new UserProfile($this->userID);
	}
	
	/**
	 * Returns an excerpt of the comment.
	 * 
	 * @return	string
	 */
	public function getExcerpt() {
		$message = self::getFormattedMessage();
		
		// remove html codes
		$message = StringUtil::stripHTML($message);
		
		// decode html
		$message = StringUtil::decodeHTML($message);
		
		// get abstract
		if (StringUtil::length($message) > 100) {
			$message = StringUtil::substring($message, 0, 97) . '...';
		}
		
		// trim message
		$message = StringUtil::trim($message);
		
		// encode html
		if (!empty($message)) {
			$message = StringUtil::encodeHTML($message);
		}
		else {
			$message = '#'.$this->commentID;
		}
		
		return $message;
	}
	
	/**
	 * Returns the formatted message.
	 * 
	 * @return	string
	 */
	public function getFormattedMessage() {
		require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, false);
	}
	
	/**
	 * @see MessageSidebarObject::getUser()
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * @see MessageSidebarObject::getMessageID()
	 */
	public function getMessageID() {
		return $this->commentID;
	}
	
	/**
	 * @see MessageSidebarObject::getMessageType()
	 */
	public function getMessageType() {
		return 'businessComment';
	}
}
?>
