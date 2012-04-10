<?php
// wcf imports
require_once(WCF_DIR.'lib/data/business/BusinessLink.class.php');
 
/**
 * Provides functions to manage this link.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page.location
 * @category 	WoltLab Community Framework
 */
class BusinessLinkEditor extends BusinessLink {

	/**
	 * Creates a new link.
	 * 
	 * @param	integer				$categoryID
	 * @param	string				$subject
	 * @param	string				$message
	 * @param	string				$shortDescription
	 * @param	string				$url
	 * @param	string				$kind
	 * @param	integer				$age
	 * @param	integer				$languageID
	 * @param	integer				$isSticky
	 * @param array				$options
	 * @param	string				$username
	 * @param	integer				$isDisabled
	 * @param	integer				$status
	 * @param	MessageAttachmentListEditor	$attachmentList
	 * @return	BusinessEditor
	 */
	public static function create($categoryID, $subject, $message, $shortDescription, $url, $kind, $age, $languageID, $isSticky, $options = array(), $username, $isDisabled, $status, $attachmentList = null) {
		// get number of attachments
		$attachmentsAmount = ($attachmentList !== null ? count($attachmentList->getAttachments()) : 0);
		
		// save entry
		$sql = "INSERT INTO	wcf".WCF_N."_business_link
					(categoryID, subject, message, shortDescription, url, kind, age, languageID, isSticky, userID, username, isDisabled, status, time, lastChangeTime, attachments, enableSmilies, enableHtml, enableBBCodes)
			VALUES		(".$categoryID.", '".escapeString($subject)."', '".escapeString($message)."', '".escapeString($shortDescription)."', '".escapeString($url)."', '".escapeString($kind)."', ".$age.", ".$languageID.", ".$isSticky.", ".WCF::getUser()->userID.", '".escapeString($username)."', ".$isDisabled.", ".$status.", ".TIME_NOW.", ".TIME_NOW.", ".$attachmentsAmount.",
					".(isset($options['enableSmilies']) ? $options['enableSmilies'] : 1).",
					".(isset($options['enableHtml']) ? $options['enableHtml'] : 0).",
					".(isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1).")";
		WCF::getDB()->sendQuery($sql);
		
		// get id
		$linkID = WCF::getDB()->getInsertID("wcf".WCF_N."_business_link", 'linkID');
		
		// get new object
		$link = new BusinessEditor($linkID);
		
		// update attachments
		if ($attachmentList !== null) {
			$attachmentList->updateContainerID($linkID);
			$attachmentList->findEmbeddedAttachments($message);
		}
		
		// return object
		return $link;
	}
	
	/**
	 * Creates a preview of a link.
	 *
	 * @param 	string		$title
	 * @param 	string		$url
	 * @param 	string		$message
	 * @param 	boolean		$enableSmilies
	 * @param 	boolean		$enableHtml
	 * @param 	boolean		$enableBBCodes
	 * @return	string
	 */
	public static function createPreview($title, $url, $message, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
		$row = array(
			'linkID' => 0,
			'url' => $url,
			'title' => $title,
			'message' => $message,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes,
			'messagePreview' => true
		);

		require_once(WCF_DIR.'lib/data/refelist/ViewableBusinessLink.class.php');
		$link = new ViewableBusinessLink(null, $row);
		return $link->getFormattedMessage();
	}
	
	/**
	 * Deletes this link.
	 */
	public function delete() {
		// delete link
		$sql = "DELETE FROM	wcf".WCF_N."_business_link
			WHERE		linkID = ".$this->linkID;
		WCF::getDB()->sendQuery($sql);
		
		// delete comments
		$sql = "DELETE FROM	wcf".WCF_N."_business_comment
			WHERE		linkID = ".$this->linkID;
		WCF::getDB()->sendQuery($sql);
		
		// delete ratings
		$sql = "DELETE FROM	wcf".WCF_N."_business_link_rating
			WHERE		linkID = ".$this->linkID;
		WCF::getDB()->sendQuery($sql);
		
		// delete reports
		$sql = "DELETE FROM	wcf".WCF_N."_business_link_report
			WHERE		linkID = ".$this->linkID;
		WCF::getDB()->sendQuery($sql);
		
		// delete tags
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$taggable = TagEngine::getInstance()->getTaggable('de.wcf.tommygfx.business');
			
			$sql = "DELETE FROM	wcf".WCF_N."_tag_to_object
				WHERE 		taggableID = ".$taggable->getTaggableID()."
						AND objectID = ".$this->linkID;
			WCF::getDB()->registerShutdownUpdate($sql);
		}
		
		// delete attachments
		if ($this->attachments > 0) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
			$attachmentList = new MessageAttachmentListEditor($this->linkID, 'businessLink');
			$attachmentList->deleteAll();
		}
	}
	
	/**
	 * Updates this status.
	 * 
	 * @param	integer				$status
	 * @param	string				$statusComment
	 * @param	integer				$isDisabled
	 */
	public function updateStatus($status, $statusComment, $isDisabled) {
		
		// update data
		$sql = "UPDATE	wcf".WCF_N."_business_link
			SET	status = ".$status.",
				statusComment = '".escapeString($statusComment)."',
				isDisabled = ".$isDisabled."
			WHERE	linkID = ".$this->linkID;
		WCF::getDB()->sendQuery($sql);
		
	}
	
	/**
	 * Updates the data of this link.
	 *
	 * @param 	array		$fields
	 */
	public function update($fields = array()) { 
		$updates = '';
		foreach ($fields as $key => $value) {
			if ($updates != '') $updates .= ',';
			$updates .= $key.'=';
			if (is_int($value)) $updates .= $value;
			else $updates .= "'".escapeString($value)."'";
		}
		
		if (!empty($updates)) {
			$sql = "UPDATE	wcf".WCF_N."_business_link
					SET		".$updates."
					WHERE	linkID = ".$this->linkID;
			WCF::getDB()->sendQuery($sql);
		}
	}
	
	/**
	 * Updates the tags of this link.
	 * 
	 * @param	array<string>		$tagArray
	 */
	public function updateTags($tagArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(WCF_DIR.'lib/data/business/TaggedBusinessLink.class.php');
		
		// save tags
		$tagged = new TaggedBusinessLink(null, array(
			'linkID' => $this->linkID,
			'taggable' => TagEngine::getInstance()->getTaggable('de.wcf.tommygfx.business')
		));

		$languageID = 0;
		if (count(Language::getAvailableContentLanguages()) > 0) {
			$languageID = WCF::getLanguage()->getLanguageID();
		}
		
		// delete old tags
		TagEngine::getInstance()->deleteObjectTags($tagged, array($languageID));
		
		// save new tags
		if (count($tagArray) > 0) {
			TagEngine::getInstance()->addTags($tagArray, $tagged, $languageID);
		}
	}
	
	/**
	 * Creates a new report.
	 * 
	 * @param	integer				$linkID
	 * @param	string				$username
	 * @param	string				$report
	 */
	public function createReport($linkID, $username, $report) {
		
		// save report in database
		$sql = "INSERT IGNORE INTO	wcf".WCF_N."_business_link_report
						(linkID, userID, username, report, reportTime)
			VALUES 			(".$linkID.", ".WCF::getUser()->userID.", '".escapeString($username)."',
						'".escapeString($report)."', ".TIME_NOW.")";
		WCF::getDB()->sendQuery($sql);
		
		// update data
		$sql = "UPDATE	wcf".WCF_N."_business_link
			SET	isReported = 1
			WHERE	linkID = ".$linkID;
		WCF::getDB()->sendQuery($sql);
		
	}
	
	/**
	 * Deletes report.
	 */
	public function deleteReport($reportID) {
		// delete report
		$sql = "DELETE FROM	wcf".WCF_N."_business_link_report
			WHERE		reportID = ".$reportID;
		WCF::getDB()->sendQuery($sql);
		
		// update data
		$sql = "UPDATE	wcf".WCF_N."_business_link
			SET	isReported = 0
			WHERE	linkID = ".$this->linkID;
		WCF::getDB()->sendQuery($sql);
	}
}
?>
