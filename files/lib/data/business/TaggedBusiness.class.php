<?php
// wcf imports
require_once(WCF_DIR.'lib/data/business/ViewableBusiness.class.php');

/**
 * TaggedBusiness provides extended functions for displaying a list of links of a specific tag.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework
 */
class TaggedBusiness extends ViewableBusiness {
	/**
	 * tag id
	 * 
	 * @var	integer
	 */
	public $tagID = 0;
	
	/**
	 * taggable object
	 * 
	 * @var	Taggable
	 */
	public $taggable = null;

	/**
	 * Creates a new TaggedRefeList object.
	 */
	public function __construct($tagID) {
		$this->tagID = $tagID;
		$this->taggable = TagEngine::getInstance()->getTaggable('de.wcf.tommygfx.business');
	}
	
	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		if (!empty($this->sqlConditions)) {
			$sql = "SELECT	COUNT(*) AS count
					FROM	wcf".WCF_N."_tag_to_object tag_to_object,
							wcf".WCF_N."_business_link business_link
					WHERE	tag_to_object.tagID = ".$this->tagID."
						AND tag_to_object.taggableID = ".$this->taggable->getTaggableID()."
						AND business_link.linkID = tag_to_object.objectID
						AND ".$this->sqlConditions;
		}
		else {
			$sql = "SELECT	COUNT(*) AS count
					FROM	wcf".WCF_N."_tag_to_object
					WHERE	tagID = ".$this->tagID."
						AND taggableID = ".$this->taggable->getTaggableID();
		}
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		$sql = "SELECT	".$this->sqlSelects."
						business_link.*
				FROM	wcf".WCF_N."_tag_to_object tag_to_object,
						wcf".WCF_N."_business_link business_link
				".$this->sqlJoins."
				WHERE	tag_to_object.tagID = ".$this->tagID."
					AND tag_to_object.taggableID = ".$this->taggable->getTaggableID()."
					AND business_link.linkID = tag_to_object.objectID
					".(!empty($this->sqlConditions) ? "AND ".$this->sqlConditions : '')."
					".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->links[] = new ViewableBusinessLink(null, $row);
		}
	}
}
?>
