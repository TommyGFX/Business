<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/BusinessCategoryAddForm.class.php');

/**
 * Shows the edit form of a category.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	acp.form
 * @category 	WoltLab Community Framework 
 */
class BusinessCategoryEditForm extends BusinessCategoryAddForm {

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_GET['categoryID']))	$this->categoryID = intval($_GET['categoryID']);
		$this->category = new BusinessCategoryEditor($this->categoryID);
		
	}

	/**
	 * @see Page::readData()
	 */		
	public function readData() {
		parent::readData();
		
		if (!count($_POST)) {
			$this->parentID = $this->category->parentID;
			$this->title = $this->category->title;
			$this->description = $this->category->description;
			$this->allowDescriptionHtml = $this->category->allowDescriptionHtml;
			$this->image = $this->category->image;
			
			// get position
			$sql = "SELECT	position
					FROM	wcf".WCF_N."_business_category_structure
					WHERE	categoryID = ".$this->categoryID;
			$row = WCF::getDB()->getFirstRow($sql);
			if (isset($row['position'])) $this->position = $row['position'];
		}
	}

	/**
	 * @see Form::save()
	 */		
	public function save() {
		AbstractForm::save();
		
		// update category
		$this->category->update(array(
			'parentID' => $this->parentID,
			'position' => $this->position,
			'title' => $this->title,
			'description' => $this->description,
			'allowDescriptionHtml' => $this->allowDescriptionHtml,
			'image' => $this->image
		));
		
		// update position
		$this->category->removePositions();
		$this->category->addPosition($this->parentID, ($this->position ? $this->position : null));
		
		// reset category cache
		BusinessCategoryEditor::resetCache();
		
		// call event
		$this->saved();
		
		// add success message
		WCF::getTPL()->assign(array(
			'success' => true
		));
	}

	/**
	 * @see Page::assignVariables()
	 */		
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'categoryID' => $this->categoryID,
			'categoryQuickJumpOptions' => BusinessCategory::getSelect(0, 0, array())
		));	
	}
	
	
	/**
	 * Gets a list of available categories
	 */
	protected function readCategorySelect() {
		$this->categorySelect = BusinessCategory::getSelect(false, false, array($this->categoryID));
	}
	
}
?>
