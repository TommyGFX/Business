<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

// load cache
WCF::getCache()->addResource('businessCategory', WCF_DIR.'cache/cache.businessCategory.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessCategory.class.php');

/**
 * Represents a category.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.category
 * @category 	WoltLab Community Framework
 */
class BusinessCategory extends DatabaseObject {
	
	// attributes
	protected static $categories = null;
	protected static $categoryStructure = null;
	protected static $categorySelect = array();
	
	public static $categoryPermissions = null;
	
	/**
	 * Creates a new BusinessCategory object.
	 */
	public function __construct($categoryID, $row = null, $cacheObject = null) {
		if ($categoryID !== null) $cacheObject = self::getCategory($categoryID);
		if ($row != null) parent::__construct($row);
		if ($cacheObject != null) parent::__construct($cacheObject->data);
	}
	
	/**
	 * Returns icon name for this category.
	 *
	 * @return	string
	 */
	public function getIconName() {
		if ($this->image) return $this->image;
		if (!$this->image) return 'businessCategory';
	}
	
	/**
	 * Returns a list of the parent categories.
	 * 
	 * @return	array
	 */
	public function getParentCategories() {
		$parentCategories = array();
		$categories = WCF::getCache()->get('businessCategory', 'categories');
			
		$parentCategory = $categories[$this->categoryID];
		while ($parentCategory->parentID != 0) {
			$parentCategory = $categories[$parentCategory->parentID];
			array_unshift($parentCategories, $parentCategory);
		}

		return $parentCategories;
	}
	
	/**
	 * Checks the given category permissions.
	 * Throws a PermissionDeniedException if the active user doesn't have one of the given permissions.
	 * @see		BusinessCategory::getPermission()
	 * 
	 * @param	mixed		$permissions
	 */
	public function checkPermission($permissions = 'canAddLink') {
		if (!is_array($permissions)) $permissions = array($permissions);
        
		foreach ($permissions as $permission) {
			if (!$this->getPermission($permission)) {
				throw new PermissionDeniedException();
			}
		}
	}
    
	/**
	 * Checks whether the active user has the permission with the given name on this category.
	 * 
	 * @param	string		$permission	name of the requested permission
	 * @return	boolean
	 */
	public function getPermission($permission = 'canAddLink') {
		return (boolean) BusinessCategory::getCategoryPermission($permission, $this->categoryID);
	}
	
	/**
	 * Checks whether this user has the permission with the given name on the category with the given category id.
	 * 
	 * @param	string		$permission	name of the requested permission
	 * @param	integer		$category
	 * @return	mixed				value of the permission
	 */
	public function getCategoryPermission($permission, $categoryID) {
		return WCF::getUser()->getPermission('user.business.'.$permission);
	}
	
	/**
	 * @see BusinessCategorySession::getGroupData()
	 */
	protected function getGroupData() {
		// get group permissions from cache (business_category_to_group)
		$groups = implode(",", WCF::getUser()->getGroupIDs());
		$groupsFileName = StringUtil::getHash(implode("-", WCF::getUser()->getGroupIDs()));
		
		// register cache resource
		WCF::getCache()->addResource('businessCategoryPermissions-'.$groups, WCF_DIR.'cache/cache.businessCategoryPermissions-'.$groupsFileName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessCategoryPermissions.class.php');
		
		// get group data from cache
		$this->categoryPermissions = WCF::getCache()->get('businessCategoryPermissions-'.$groups);
		if (isset($this->categoryPermissions['groupIDs']) && $this->categoryPermissions['groupIDs'] != $groups) {
			$this->categoryPermissions = array();
		}
	}
	
	/**
	 * Enters the active user to this category.
	 */
	public function enter() {
		// check permissions
		BusinessCategory::checkPermission(array('canViewCategory', 'canEnterCategory'));
	}
	
	/**
	
	/**
	 * Gets the category with the given category id from cache.
	 * 
	 * @param 	integer		$categoryID
	 * @return	BusinessCategory
	 */
	public static function getCategory($categoryID) {
		// get category cache
		if (self::$categories === null) {
			self::$categories = WCF::getCache()->get('businessCategory', 'categories');
		}

		// check if requested category exists
		if (!isset(self::$categories[$categoryID])) {
			throw new IllegalLinkException();
		}
		
		// return requested category
		return self::$categories[$categoryID];
	}
	
	/**
	 * Returns a category select with given parameters.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$depth
	 * @param	array		$ignore			list of category ids to ignore in result
	 */
	public static function getSelect($parentID = 0, $depth = 0, $ignore = array()) {
		// set empty array
		self::$categorySelect = array();
		
		// load cache
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('businessCategory', 'categoryStructure');
		if (self::$categories === null) self::$categories = WCF::getCache()->get('businessCategory', 'categories');
		
		// make select
		self::makeSelect($parentID, $depth, $ignore);
		
		// return select
		return self::$categorySelect;
	}

	 /** Builds a category select with given parameters.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$depth
	 */		
	protected static function makeSelect($parentID = 0, $depth = 0, $ignore = array()) {	
		if (!isset(self::$categoryStructure[$parentID])) return;
		
		foreach (self::$categoryStructure[$parentID] as $categoryID) {
			if (!empty($ignore) && in_array($categoryID, $ignore)) continue;
		
			// save category in category list array
			self::$categorySelect[$categoryID] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth).' '.self::$categories[$categoryID]->title;
			
			// get sub categories of current category
		self::makeSelect($categoryID, $depth + 1, $ignore);
		}
	}
	
	/**
	 * Returns a list of accessible categories.
	 * 
	 * @return	array<integer>
	 */
	public static function getAccessibleCategoryIDArray() {
		// get category cache
		if (self::$categories === null) self::$categories = WCF::getCache()->get('businessCategory', 'categories');
		
		$categories = array();
		foreach (self::$categories as $category) {
			
			// check if user has permission for this category
			if ($category->getPermission('canViewCategory')) {
				$categories[] = $category->categoryID;
			}
		}
		
		// return accessible category array
		return $categories;
	}
	
	/**
	 * Returns sub categories of given categories.
	 *
	 * @param	array		$categoryIDArray
	 */
	public static function getSubCategoryIDArray($categoryIDArray) {
		// get category structure cache
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('businessCategory', 'categoryStructure');
		
		// set empty sub category array
		$subCategoryIDArray = array();
		
		// read sub categories
		foreach ($categoryIDArray as $categoryID) {
			if (isset(self::$categoryStructure[$categoryID])) {
				$subCategoryIDArray = array_merge($subCategoryIDArray, self::$categoryStructure[$categoryID]);
			}
		}
		
		// return sub categories
		return array_unique($subCategoryIDArray);
	}
	
	/** 
	 * Inherits category permissions.
	 *
	 * @param 	integer 	$parentID
	 * @param 	array 		$permissions
	 */
	public static function inheritPermissions($parentID = 0, &$permissions) {
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('businessCategory', 'categoryStructure');
        if (self::$categories === null) self::$categories = WCF::getCache()->get('businessCategory', 'categories');
        
        if (isset(self::$categoryStructure[$parentID]) && is_array(self::$categoryStructure[$parentID])) {
            foreach (self::$categoryStructure[$parentID] as $categoryID) {
                $category = self::$categories[$categoryID];
                if ($category->parentID) {
                    if (isset($permissions[$category->parentID]) && !isset($permissions[$categoryID])) {
                        $permissions[$categoryID] = $permissions[$category->parentID];
                    }
                }    
                self::inheritPermissions($categoryID, $permissions);
            }
        }
    }
	
	/**
	 * Resets the category cache after changes.
	 */
	public static function resetCache() {
		// reset category cache
		WCF::getCache()->clearResource('businessCategory');
		
		self::$categories = self::$categoryStructure = self::$categorySelect = null;
	}
}
?>
