<?php
// wcf imports
require_once(WCF_DIR.'lib/data/business/Business.class.php');
require_once(WCF_DIR.'lib/data/business/ViewableBusinessLink.class.php');

/**
 * Represents a list of links.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework 
 */
class ViewableBusiness extends Business {
	/**
	 * list of object ids
	 * 
	 * @var	array<integer>
	 */
	public $objectIDArray = array();
	
	/**
	 * list of object ids
	 * 
	 * @var	array<integer>
	 */
	public $attachmentLinkIDArray = array();
	
	/**
	 * attachment list object
	 * 
	 * @var	MessageAttachmentList
	 */
	public $attachmentList = null;
	
	/**
	 * list of attachments
	 * 
	 * @var	array
	 */
	public $attachments = array();
	
	/**
	 * list of tags
	 * 
	 * @var	array
	 */
	public $tags = array();
	
	/**
	 * Gets the object ids.
	 */
	protected function readObjectIDArray() {
		$sql = "SELECT		business_link.linkID, business_link.attachments
			FROM		wcf".WCF_N."_business_link business_link
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->objectIDArray[] = $row['linkID'];
			if ($row['attachments']) $this->attachmentLinkIDArray[] = $row['linkID'];
		}
	}
	
	/**
	 * Gets a list of attachments.
	 */
	protected function readAttachments() {
		// read attachments
		if (MODULE_ATTACHMENT == 1 && count($this->attachmentLinkIDArray) > 0) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentList.class.php');
			$this->attachmentList = new MessageAttachmentList($this->attachmentLinkIDArray, 'businessLink', '', WCF::getPackageID('de.wcf.tommygfx.business'));
			$this->attachmentList->readObjects();
			$this->attachments = $this->attachmentList->getSortedAttachments(WCF::getUser()->getPermission('user.business.canViewAttachmentPreview'));
			
			// set embedded attachments
			if (WCF::getUser()->getPermission('user.business.canViewAttachmentPreview')) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachments);
			}
			
			// remove embedded attachments from list
			if (count($this->attachments) > 0) {
				MessageAttachmentList::removeEmbeddedAttachments($this->attachments);
			}
		}
	}
	
	/**
	 * Gets the list of tags.
	 */
	protected function readTags() {
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$taggable = TagEngine::getInstance()->getTaggable('de.wcf.tommygfx.business');
			$sql = "SELECT		tag_to_object.objectID AS linkID,
						tag.tagID, tag.name
				FROM		wcf".WCF_N."_tag_to_object tag_to_object
				LEFT JOIN	wcf".WCF_N."_tag tag
				ON		(tag.tagID = tag_to_object.tagID)
				WHERE		tag_to_object.taggableID = ".$taggable->getTaggableID()."
						AND tag_to_object.languageID IN (".implode(',', (count(WCF::getSession()->getVisibleLanguageIDArray()) ? WCF::getSession()->getVisibleLanguageIDArray() : array(0))).")
						AND tag_to_object.objectID IN (".implode(',', $this->objectIDArray).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!isset($this->tags[$row['linkID']])) $this->tags[$row['linkID']] = array();
				$this->tags[$row['linkID']][] = new Tag(null, $row);
			}
		}
	}
	
	/**
	 * @see DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		// get ids
		$this->readObjectIDArray();
		
		// get links
		if (count($this->objectIDArray)) {
			$this->readAttachments();
			$this->readTags();
			
			$sql = "SELECT		".(!empty($this->sqlSelects) ? $this->sqlSelects.',' : '')."
						business_link.*
				FROM		wcf".WCF_N."_business_link business_link
				".$this->sqlJoins."
				WHERE 		business_link.linkID IN (".implode(',', $this->objectIDArray).")
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!$row['isDisabled'] || WCF::getUser()->getPermission('mod.business.canEnableLinks') || (WCF::getUser()->userID == $row['userID'] && WCF::getUser()->userID)) {
					$this->links[] = new ViewableBusinessLink(null, $row);
				}
			}
		}
	}
	
	/**
	 * Returns the list of attachments.
	 * 
	 * @return	array
	 */
	public function getAttachments() {
		return $this->attachments;
	}
	
	/**
	 * Returns the list of tags.
	 * 
	 * @return	array
	 */
	public function getTags() {
		return $this->tags;
	}
}
?>
