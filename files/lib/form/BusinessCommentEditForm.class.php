<?php
// wcf imports
require_once(WCF_DIR.'lib/form/BusinessCommentAddForm.class.php');
 
/**
 * Shows the form for editing comments.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.form
 * @category 	WoltLab Community Framework 
 */
class BusinessCommentEditForm extends BusinessCommentAddForm {
	/**
	 * comment id
	 *
	 * @var integer
	 */
	public $commentID = 0;
	
	/**
	 * comment editor object
	 *
	 * @var BusinessCommentEditor
	 */
	public $comment = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		MessageForm::readParameters();
		
		if (isset($_REQUEST['commentID'])) $this->commentID = intval($_REQUEST['commentID']);
		$this->comment = new BusinessCommentEditor($this->commentID);
		if (!$this->comment->commentID || !$this->comment->isEditable()) {
			throw new IllegalLinkException();
		}
		
		// create new link instance
		$this->link = new ViewableBusinessLink($this->comment->linkID);
		// enter link
		$this->link->enter();
		
		// create a new category instance
		$this->category = BusinessCategory::getCategory($this->link->categoryID);
		// enter category
		$this->category->enter();
		// check permission
		$this->category->checkPermission('canEditOwnComment');
		
		if ($this->link->isClosed == 1 && !WCF::getUser()->getPermission('mod.business.canEditComments')) {
			throw new PermissionDeniedException();
		}
		
		// set active link menu item
		BusinessMenu::getInstance()->linkID = $this->link->linkID;
		BusinessMenu::getInstance()->setActiveMenuItem('wcf.business.menu.link.commentAdd');

	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		MessageForm::save();
		
		// save comment
		$this->comment->update($this->text, $this->getOptions());
		$this->saved();
		
		// forward
		HeaderUtil::redirect('index.php?page=BusinessCommentsList&linkID='.$this->link->linkID.'&commentID='.$this->comment->commentID.SID_ARG_2ND_NOT_ENCODED.'#comment'.$this->comment->commentID);
		exit;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// default values
		if (!count($_POST)) {
			$this->text = $this->comment->message;
			$this->enableSmilies =  $this->comment->enableSmilies;
			$this->enableHtml = $this->comment->enableHtml;
			$this->enableBBCodes = $this->comment->enableBBCodes;
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'commentID' => $this->commentID,
			'link' => $this->link,
			'linkID' => $this->link->linkID,
			'category' => $this->category,
			'categoryID' => $this->category->categoryID
		));
	}
}
?>
