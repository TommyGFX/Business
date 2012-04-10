<?php
// wcf imports
require_once(WCF_DIR.'lib/data/tag/AbstractTaggableObject.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
require_once(WCF_DIR.'lib/data/business/TaggedBusinessLink.class.php');

/**
 * An implementation of Taggable to support the tagging of links.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework
 */
class TaggableBusinessLink extends AbstractTaggableObject {
	/**
	 * @see Taggable::getObjectsByIDs()
	 */
	public function getObjectsByIDs($objectIDs, $taggedObjects) {
		$sql = "SELECT		*
				FROM		wcf".WCF_N."_business_link
				WHERE		linkID IN (".implode(",", $objectIDs).")
					AND		isDisabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$taggedObjects[] = new TaggedBusinessLink(null, $row);
		}
		return $taggedObjects;
	}
	
	/**
	 * @see Taggable::countObjectsByTagID()
	 */
	public function countObjectsByTagID($tagID) {
		$categories = BusinessCategory::getAccessibleCategoryIDArray();
		if (count($categories) == 0) return 0;
		
		$sql = "SELECT		COUNT(*) AS count
				FROM		wcf".WCF_N."_tag_to_object tag_to_object
				LEFT JOIN	wcf".WCF_N."_business_link business_link
					ON		(business_link.linkID = tag_to_object.objectID)
				WHERE 		tag_to_object.tagID = ".$tagID."
					AND		tag_to_object.taggableID = ".$this->getTaggableID()."
					AND		business_link.categoryID IN (".implode(',', $categories).")
					AND		business_link.isDisabled = 0";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see Taggable::getObjectsByTagID()
	 */
	public function getObjectsByTagID($tagID, $limit = 0, $offset = 0) {
		$categories = BusinessCategory::getAccessibleCategoryIDArray();
		if (count($categories) == 0) return array();
		
		$links = array();
		$sql = "SELECT		business_link.*,
							business_category.categoryID, business_category.title
				FROM		wcf".WCF_N."_tag_to_object tag_to_object
				LEFT JOIN	wcf".WCF_N."_business_link business_link
					ON		(business_link.linkID = tag_to_object.objectID)
				LEFT JOIN 	wcf".WCF_N."_business_category business_category
					ON 		(business_category.categoryID = business_link.categoryID)
				WHERE		tag_to_object.tagID = ".$tagID."
					AND		tag_to_object.taggableID = ".$this->getTaggableID()."
					AND		business_link.categoryID IN (".implode(',', $categories).")
					AND 	business_link.isDisabled = 0
				ORDER BY	business_link.lastChangeTime DESC";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$links[] = new TaggedBusinessLink(null, $row);
		}
		return $links;
	}

	/**
	 * @see Taggable::getIDFieldName()
	 */
	public function getIDFieldName() {
		return 'linkID';
	}
	
	/**
	 * @see Taggable::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'taggedBusinessLinks';
	}
	
	/**
	 * @see Taggable::getSmallSymbol()
	 */
	public function getSmallSymbol() {
		return StyleManager::getStyle()->getIconPath('businessLinkS.png');
	}

	/**
	 * @see Taggable::getMediumSymbol()
	 */
	public function getMediumSymbol() {
		return StyleManager::getStyle()->getIconPath('businessLinkM.png');
	}
	
	/**
	 * @see Taggable::getLargeSymbol()
	 */
	public function getLargeSymbol() {
		return StyleManager::getStyle()->getIconPath('businessLinkL.png');
	}
}
?>
