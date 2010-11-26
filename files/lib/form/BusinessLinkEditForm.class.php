<?php
// wcf imports
require_once(WCF_DIR.'lib/form/BusinessLinkAddForm.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategoryEditor.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
 
/**
 * Shows the form for edit links.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.form
 * @category 	WoltLab Community Framework 
 */
class BusinessLinkEditForm extends BusinessLinkAddForm {

	public $categoryID = 0;
	public $category = null;
	public $kinds = '';
	
	/**
	 * link id
	 *
	 * @var integer
	 */
	public $linkID = 0;
	
	/**
	 * link editor object
	 *
	 * @var BusinessEditor
	 */
	public $link = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		MessageForm::readParameters();
		
		// get link id
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
		// new link editor object
		$this->link = new BusinessEditor($this->linkID);
		if (!$this->link->linkID || !$this->link->isEditable()) {
			throw new IllegalLinkException();
		}
		
		// create a new category instance
		$this->category = BusinessCategory::getCategory($this->link->categoryID);
		// enter category
		$this->category->enter();
		// check permission
		$this->category->checkPermission('canEditOwnLink');
		
		// get kinds
		if (BUSINESS_LINK_KINDS) {
			$this->kinds = explode("\n", StringUtil::unifyNewlines(BUSINESS_LINK_KINDS));
		}
		
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		MessageForm::save();
		
		// get number of attachments
		$attachmentsAmount = ($this->attachmentListEditor !== null ? count($this->attachmentListEditor->getAttachments($this->linkID)) : 0);
		
		// update link
		$this->link->update(array(
			'subject' => $this->subject,
			'message' => $this->text,
			'shortDescription' => $this->shortDescription,
			'url' => $this->url,
			'kind' => $this->kind,
			'age' => $this->age,
			'languageID' => $this->languageID,
			'isSticky' => $this->isSticky,
			'isDisabled' => intval(!$this->category->getPermission('canAddLinkWithoutModeration')),
			'status' => ($this->category->getPermission('canAddLinkWithoutModeration') ? 3 : 1),
			'statusComment' => null,
			'enableSmilies' => $this->enableSmilies,
			'enableHtml' => $this->enableHtml,
			'enableBBCodes' => $this->enableBBCodes,
			'lastChangeTime' => TIME_NOW,
			'attachments' => $attachmentsAmount
		));
		
		if (!$this->category->getPermission('canAddLinkWithoutModeration') && !$this->link->isDisabled) {
			// get business Category Editor object
			$this->categoryEditor = new BusinessCategoryEditor($this->link->categoryID);
			
			// set links count
			$this->categoryEditor->updateLinks(-1);
			
			// clear cache
			WCF::getCache()->clearResource('businessCategory');
			
			// reset tag cloud cache
			WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.businessCategoryTagCloud-*', true);
		}
		
		// save tags
		if (MODULE_TAGGING) {
			$this->link->updateTags(TaggingUtil::splitString($this->tags));
		}
		$this->saved();
		
		if (!$this->category->getPermission('canAddLinkWithoutModeration')) {
			// redirect to url
			WCF::getTPL()->assign(array(
				'url' => ('index.php?page=BusinessLink&linkID='.$this->linkID.SID_ARG_2ND_NOT_ENCODED),
				'message' => WCF::getLanguage()->get('wcf.business.link.edit.success'),
				'wait' => 5
			));
			WCF::getTPL()->display('redirect');
		}
		else {
			// forward
			HeaderUtil::redirect('index.php?page=BusinessLink&linkID='.$this->linkID.SID_ARG_2ND_NOT_ENCODED);
		}
		exit;

	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// default values
		if (!count($_POST)) {
			$this->subject = $this->link->subject;
			$this->text = $this->link->message;
			$this->url = $this->link->url;
			$this->kind = $this->link->kind;
			$this->shortDescription = $this->link->shortDescription;
			$this->age = $this->link->age;
			$this->languageID = $this->link->languageID;
			$this->isSticky = $this->link->isSticky;
			$this->attachments = $this->link->attachments;
			$this->enableSmilies =  $this->link->enableSmilies;
			$this->enableHtml = $this->link->enableHtml;
			$this->enableBBCodes = $this->link->enableBBCodes;
			
			// tags
			if (MODULE_TAGGING) {
				$this->tags = TaggingUtil::buildString($this->link->getTags(array((count(Language::getAvailableContentLanguages()) > 0 ? WCF::getLanguage()->getLanguageID() : 0))));
			}
			
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
			'kinds' => $this->kinds,
			'link' => $this->link,
			'linkID' => $this->linkID,
			'category' => $this->category,
			'categoryID' => $this->category->categoryID
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
		$this->attachmentListEditor = new MessageAttachmentListEditor(array($this->linkID), 'businessLink', WCF::getPackageID('de.wcf.tommygfx.business'), WCF::getUser()->getPermission('user.business.maxAttachmentSize'), WCF::getUser()->getPermission('user.business.allowedAttachmentExtensions'), WCF::getUser()->getPermission('user.business.maxAttachmentCount'));
		
		parent::show();
	}
}
?>
