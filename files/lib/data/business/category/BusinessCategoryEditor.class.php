<?php
// wcf imports
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');

/**
 * Show the editor for delete, update or add a category.
 *
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage		data.business.category
 */
class BusinessCategoryEditor extends BusinessCategory {
	/**
	 * Creates a new BusinessCategoryEditor object.
	 */
	public function __construct($categoryID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($categoryID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
					FROM	wcf".WCF_N."_business_category
					WHERE	categoryID = ".$categoryID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);			
		}
	}
	
	/**
	 * Adds the position for this category.
	 * 
	 * @param	integer		$parentID
	 * @param	integer		$position
	 */
	public function addPosition($parentID, $position = null) {
		// shift categories
		if ($position !== null) {
			$sql = "UPDATE	wcf".WCF_N."_business_category_structure
					SET		position = position + 1
					WHERE 	parentID = ".$parentID."
						AND position >= ".$position;
			WCF::getDB()->sendQuery($sql);
		}
		
		// get final position
		$sql = "SELECT 	IFNULL(MAX(position), 0) + 1 AS position
				FROM	wcf".WCF_N."_business_category_structure
				WHERE	parentID = ".$parentID."
					".($position ? "AND position < ".$position : '');
		$row = WCF::getDB()->getFirstRow($sql);
		$position = $row['position'];
		
		// save position
		$sql = "INSERT INTO	wcf".WCF_N."_business_category_structure
							(parentID, categoryID, position)
				VALUES		(".$parentID.", ".$this->categoryID.", ".$position.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Removes the positions of this category.
	 */
	public function removePositions() {
		// unshift categories
		$sql = "SELECT 	parentID, position
				FROM	wcf".WCF_N."_business_category_structure
				WHERE	parentID = ".$this->parentID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sql = "UPDATE	wcf".WCF_N."_business_category_structure
					SET		position = position - 1
					WHERE 	parentID = ".$row['parentID']."
						AND position > ".$row['position'];
			WCF::getDB()->sendQuery($sql);
		}
		
		// delete category structure record
		$sql = "DELETE FROM	wcf".WCF_N."_business_category_structure
				WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Creates a new category.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$position
	 * @param	string		$title
	 * @param	string		$description
	 * @param	integer		$allowDescriptionHtml
	 * @param	string		$image
	 * @param	array		$canAddLinkGroupIDs
	 * @param	array		$canAddLinkGroupIDs
	 */
	public static function create($parentID, $position, $title, $description, $allowDescriptionHtml, $image) {
		// insert category
		$categoryID = self::insert(array_merge(array(
			'parentID' => $parentID, 
			'title' => $title,
			'position' => $position,
			'description' => $description, 
			'allowDescriptionHtml' => $allowDescriptionHtml,
			'image' => $image, 
			'time' => time()
		)));
		
		// get category
		$category = new BusinessCategoryEditor($categoryID, null, null, false);
		
		// add position
		$category->addPosition($parentID, $position);
		
		// return new category
		return $category;	
	}

	/**
	 * Creates the category row in database table.
	 *
	 * @param 	array		$fields
	 * @return	integer		new category id
	 */
	public static function insert($fields = array()) { 
		$keys = $values = '';
		foreach ($fields as $key => $value) {
			if ($keys != '') $keys .= ',';
			$keys .= $key;
			if ($values != '') $values .= ',';
			if (is_int($value)) $values .= $value;
			else $values .= "'".escapeString($value)."'";			
		}
		
		$sql = "INSERT INTO	wcf".WCF_N."_business_category
							(".$keys.")
				VALUES		(".$values.")";
		WCF::getDB()->sendQuery($sql);
		return WCF::getDB()->getInsertID();
	}
	
	/**
	 * Updates the data of this category.
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
			$sql = "UPDATE	wcf".WCF_N."_business_category
					SET		".$updates."
					WHERE	categoryID = ".$this->categoryID;
			WCF::getDB()->sendQuery($sql);
		}
	}
	
	/**
	 * Updates the amount of links for this category.
	 * 
	 * @param	integer		$links
	 */
	public function updateLinks($links = 1) {
		$sql = "UPDATE	wcf".WCF_N."_business_category
				SET		links = links + ".$links."
				WHERE 	categoryID = ".$this->categoryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Deletes this category.
	 */
	public function delete() {
		self::deleteAll($this->categoryID);
	}
	
	/**
	 * Updates the position of a specific category.
	 *
	 * @param	integer		$categoryID
	 * @param	integer		$parentID
	 * @param	integer		$position
	 */
	public static function updatePosition($categoryID, $parentID, $position) {		
	$sql = "UPDATE	wcf".WCF_N."_business_category
			SET	parentID = ".$parentID."
			WHERE 	categoryID = ".$categoryID;
		WCF::getDB()->sendQuery($sql);
		
		$sql = "REPLACE INTO	wcf".WCF_N."_business_category_structure
					(categoryID, parentID, position)
			VALUES		(".$categoryID.", ".$parentID.", ".$position.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes all categories with the given category ids.
	 *
	 * @param	string		$categoryIDs
	 * @param	boolean		$deleteSubCategories
	 * @param	boolean		$deleteLinks
	 */
	public static function deleteAll($categoryIDs, $deleteSubCategories = true, $deleteLinks = true) {
		// delete sub categories
		if ($deleteSubCategories) {
			$subCategoryIDs = '';
			$sql = "SELECT	categoryID
					FROM wcf".WCF_N."_business_category
					WHERE	parentID IN (".$categoryIDs.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!empty($subCategoryIDs)) $subCategoryIDs .= ',';
				$subCategoryIDs .= $row['categoryID'];
			}
			if (!empty($subCategoryIDs)) {
				self::deleteAll($subCategoryIDs, $deleteSubCategories, $deleteLinks);
			}
		}
		
		// delete links
		if ($deleteLinks) {
			$linkIDs = '';
			$sql = "SELECT	linkID, attachments
					FROM	wcf".WCF_N."_business_link
					WHERE	categoryID IN (".$categoryIDs.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!empty($linkIDs)) $linkIDs .= ',';
				$linkIDs .= $row['linkID'];
			}
			if (!empty($linkIDs)) {
				// delete link
				$sql = "DELETE FROM	wcf".WCF_N."_business_link
				WHERE		linkID IN (".$linkIDs.")";
				WCF::getDB()->sendQuery($sql);
		
				// delete comments
				$sql = "DELETE FROM	wcf".WCF_N."_business_link_comment
				WHERE		linkID IN (".$linkIDs.")";
				WCF::getDB()->sendQuery($sql);
		
				// delete ratings
				$sql = "DELETE FROM	wcf".WCF_N."_business_link_rating
				WHERE		linkID IN (".$linkIDs.")";
				WCF::getDB()->sendQuery($sql);
				
				// delete reports
				$sql = "DELETE FROM	wcf".WCF_N."_business_link_report
				WHERE		linkID IN (".$linkIDs.")";
				WCF::getDB()->sendQuery($sql);
		
				// delete tags
				if (MODULE_TAGGING) {
				require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
				$taggable = TagEngine::getInstance()->getTaggable('de.wcf.tommygfx.business');
			
				$sql = "DELETE FROM	wcf".WCF_N."_tag_to_object
				WHERE 		taggableID = ".$taggable->getTaggableID()."
						AND objectID IN (".$linkIDs.")";
				WCF::getDB()->registerShutdownUpdate($sql);
				}
				
				// delete attachments
				if ($this->attachments > 0) {
					require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
					$attachmentList = new MessageAttachmentListEditor($linkIDs, 'businessLink');
					$attachmentList->deleteAll();
				}
		
			}
		}
		
		// delete categories
		self::deleteSqlData($categoryIDs);
	}
	
	/**
	 * Deletes the sql data of the categories with the given category ids.
	 *
	 * @param	string		$categoryIDs
	 */
	public static function deleteSqlData($categoryIDs) {
		
		$sql = "DELETE FROM	wcf".WCF_N."_business_category_structure
				WHERE		categoryID IN (".$categoryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		$sql = "DELETE FROM	wcf".WCF_N."_business_category
				WHERE		categoryID IN (".$categoryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
}
?>
