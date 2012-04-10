<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategoryEditor.class.php');

/**
 * Renames a category.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	acp.action
 * @category 	WoltLab Community Framework
 */
class BusinessCategoryRenameAction extends AbstractAction {
	/**
	 * category id
	 * 
	 * @var	integer
	 */
	public $categoryID = 0;
	
	/**
	 * Business category editor object
	 * 
	 * @var	BusinessCategoryEditor
	 */
	public $category = null;
	
	/**
	 * new category title
	 *
	 * @var string
	 */
	public $title = '';
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		$this->category = new BusinessCategoryEditor($this->categoryID);
		
		// get title
		if (isset($_POST['title'])) {
			$this->title = $_POST['title'];
			if (CHARSET != 'UTF-8') $this->title = StringUtil::convertEncoding('UTF-8', CHARSET, $this->title);
		}
	}
	
	/**
	 * @see Action::execute();
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.business.canEditCategory');

		// change title
		$this->category->update(array('title' => $this->title));
		
		// reset category cache
		BusinessCategoryEditor::resetCache();
		
		// call event
		$this->executed();
	}
}
?>
