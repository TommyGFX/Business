<?php
// wcf imports
require_once(WCF_DIR.'lib/page/SortablePage.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Shows an overview of all links in a category.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page
 * @category 	WoltLab Community Framework 
 */
class BusinessCategoryPage extends SortablePage {
	// system
	public $templateName = 'businessCategory';
	public $defaultSortField = BUSINESS_CATEGORY_DEFAULT_SORT_FIELD;
	public $defaultSortOrder = BUSINESS_CATEGORY_DEFAULT_SORT_ORDER;
	public $itemsPerPage = BUSINESS_CATEGORY_LINKS_PER_PAGE;
	
	public $categories = array();
	public $categoryStucture = array();
	public $categoryList = array();
	
	/**
	 * list of links
	 *
	 * @var business
	 */
	public $business = null;
	
	/**
	 * category id
	 *
	 * @var integer
	 */
	public $categoryID = 0;
	
	/**
	 * show category
	 *
	 * @var Category
	 */
	public $category = null;
	
	/**
	 * tag list object
	 *
	 * @var TagList
	 */
	public $tagList = null;
	
	/**
	 * list of tags
	 * 
	 * @var	array
	 */
	public $tags = array();
	
	/**
	 * tag id
	 *
	 * @var integer
	 */
	public $tagID = 0;
	
	/**
	 * tag object
	 *
	 * @var Tag
	 */
	public $tag = null;
	
	/**
	 * taggable object
	 *
	 * @var Taggable
	 */
	public $taggable = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category
		if (isset($_GET['categoryID'])) $this->categoryID = intval($_GET['categoryID']);
		
		// create a new category instance
		$this->category = BusinessCategory::getCategory($this->categoryID);		
		// check permission
		$this->category->enter();

		
		// get tag
		if (isset($_REQUEST['tagID'])) $this->tagID = intval($_REQUEST['tagID']);
		
		// init business
		if (MODULE_TAGGING && $this->tagID) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$this->tag = TagEngine::getInstance()->getTagByID($this->tagID);
			if ($this->tag === null) {
				throw new IllegalLinkException();
			}
			require_once(WCF_DIR.'lib/data/business/TaggedBusiness.class.php');
			$this->business = new TaggedBusiness($this->tagID);
		}
		else {
			require_once(WCF_DIR.'lib/data/business/ViewableBusiness.class.php');
			$this->business = new ViewableBusiness();
		}
		
		// init tag list
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagList.class.php');
			$this->tagList = new TagList(array('de.wc.tommygfx.business'), WCF::getSession()->getVisibleLanguageIDArray());
		}
		
		// sql conditions
		$this->business->sqlConditions = 'categoryID = '.$this->categoryID;
		
		// read categories
		$this->categories = WCF::getCache()->get('businessCategory', 'categories');
		$this->categoryStructure = WCF::getCache()->get('businessCategory', 'categoryStructure');
		$this->makeCategoryList();

	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read all
		$this->business->sqlLimit = $this->itemsPerPage;
		$this->business->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->business->sqlLimit = $this->itemsPerPage;
		$this->business->sqlOrderBy = 'business_link.isSticky DESC, business_link.'.$this->sortField.' '.$this->sortOrder;
		$this->business->links = array();
		$this->business->readObjects();

		// get tags
		if (MODULE_TAGGING) {
			$this->readTags();
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return $this->business->countObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'links' => $this->business->getObjects(),
			'tags' => $this->business->getTags(),
			'category' => $this->category,
			'categoryID' => $this->categoryID,
			'availableTags' => $this->tags,
			'tagID' => $this->tagID,
			'tag' => $this->tag,
			'taggableID' => ($this->taggable !== null ? $this->taggable->getTaggableID() : 0),
			'allowSpidersToIndexThisPage' => true,
			'categories' => $this->categoryList,
		));
	}
	
	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField() {
		parent::validateSortField();
		
		switch ($this->sortField) {
			case 'subject':
			case 'lastChangeTime':
			case 'hits':
			case 'rating':
			case 'comments':
			case 'languageID':
			case 'time': 
			break;
			default: $this->sortField = $this->defaultSortField;
		}
	}
	
	/**
	 * Renders one level of the category structure
	 *
	 * @param	integer		$parentID
	 */
	protected function makeCategoryList($parentID = 0) {
	// render categories
		if (isset($this->categoryStructure[$this->categoryID])) {
			foreach ($this->categoryStructure[$this->categoryID] as $categoryID) {
				if (!$this->categories[$categoryID]->getPermission('canViewCategory')) continue;
				// save categories in array
				$this->categoryList[] = array(
					'category' => $this->categories[$categoryID]
				);
			}
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active header menu item
		PageMenu::setActiveMenuItem('wcf.header.menu.business.links');
		
		// check options
		if (!MODULE_BUSINESS) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
	
	/**
	 * Gets the tags of this category.
	 */
	protected function readTags() {
		// get tags
		require_once(WCF_DIR.'lib/data/business/category/BusinessCategoryTagCloud.class.php');
		$tagCloud = new BusinessCategoryTagCloud($this->categoryID, WCF::getSession()->getVisibleLanguageIDArray());
		$this->tags = $tagCloud->getTags();
	}
}
?>
