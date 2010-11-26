<?php
// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategory.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategoryEditor.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessEditor.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
// get cache
WCF::getCache()->addResource('businessStatistics', WCF_DIR.'cache/cache.businessStatistics.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessStatistics.class.php');

 /**
 * Shows the form for adding new link.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.form
 * @category 	WoltLab Community Framework 
 */
class BusinessLinkAddForm extends MessageForm {
	// system
	public $templateName = 'businessLinkAdd';
	public $useCaptcha = 1;
	public $showPoll = false;
	public $showSignatureSetting = false;
	public $preview, $send;
	public $linkID = 0;
	
	// form parameters
	public $tags = '';
	public $shortDescription = '';
	public $url = '';
	public $username;
	public $kind = '';
	public $age = 0;
	public $languageID = 0;
	public $isSticky = 0;
	public $kinds = '';
	
	/**
	 * category id
	 *
	 * @var integer
	 */
	public $categoryID = 0;
	
	/**
	 * category object
	 * 
	 * @var	BusinessCategory
	 */
	public $category = null;
	
	/**
	 * attachment list editor
	 * 
	 * @var	AttachmentListEditor
	 */
	public $attachmentListEditor = null;
	
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_GET['categoryID'])) $this->categoryID = intval($_GET['categoryID']);
		
		// create a new category instance
		$this->category = BusinessCategory::getCategory($this->categoryID);
		// enter category
		$this->category->enter();
		// check permission
		$this->category->checkPermission('canAddLink');
		
		// get kinds
		if (BUSINESS_LINK_KINDS != "") {
			$this->kinds = explode("\n", StringUtil::unifyNewlines(BUSINESS_LINK_KINDS));
		}
		
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['tags'])) $this->tags = StringUtil::trim($_POST['tags']);
		if (isset($_POST['url'])) $this->url = StringUtil::trim($_POST['url']);
		if (isset($_POST['kind'])) $this->kind = StringUtil::trim($_POST['kind']);
		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
		if (isset($_POST['preview'])) $this->preview = (boolean) $_POST['preview'];
		if (isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
		if (isset($_POST['shortDescription'])) $this->shortDescription = StringUtil::trim($_POST['shortDescription']);
		if (isset($_POST['age'])) $this->age = intval($_POST['age']);
		if (isset($_POST['languageID'])) $this->languageID = intval($_POST['languageID']);
		if (isset($_POST['isSticky'])) $this->isSticky = intval($_POST['isSticky']);
	}
	
	/**
	 * @see Form::submit()
	 */
	public function submit() {
		// call submit event
		EventHandler::fireAction($this, 'submit');
		
		$this->readFormParameters();
		
		try {
			// attachment handling
			if ($this->showAttachments) {
				$this->attachmentListEditor->handleRequest();
			}
				
			// preview
			if ($this->preview) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachmentListEditor->getSortedAttachments());
				WCF::getTPL()->assign('preview', BusinessEditor::createPreview($this->subject, $this-> url, $this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes));
			}
			// save message
			if ($this->send) {
				$this->validate();
				// no errors
				$this->save();
			}
		}
		catch (UserInputException $e) {
			$this->errorField = $e->getField();
			$this->errorType = $e->getType();
		}
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// validate username
		$this->validateUsername();
		
		// validate shortDescription
		if (empty($this->shortDescription)) {
			throw new UserInputException('shortDescription', 'empty');
		}
		
		// validate url
		if (!FileUtil::isURL($this->url)) {
			throw new UserInputException('url', 'illegalURL');
		}

	}
	
	/**
	 * Validates the username
	 */
	protected function validateUsername() {
		if (WCF::getUser()->userID == 0) {
			if (empty($this->username)) {
				throw new UserInputException('username');
			}
			if (!UserUtil::isValidUsername($this->username)) {
				throw new UserInputException('username', 'notValid');
			}
			if (!UserUtil::isAvailableUsername($this->username)) {
				throw new UserInputException('username', 'notAvailable');
			}
			
			WCF::getSession()->setUsername($this->username);
		}
		else {
			$this->username = WCF::getUser()->username;
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save link
		$link = BusinessEditor::create($this->categoryID, $this->subject, $this->text, $this->shortDescription, $this->url, $this->kind, (BUSINESS_LINK_ENABLE_AGE ? $this->age : 0), $this->languageID, $this->isSticky, $this->getOptions(), $this->username, intval(!$this->category->getPermission('canAddLinkWithoutModeration')), ($this->category->getPermission('canAddLinkWithoutModeration') ? 3 : 1), $this->attachmentListEditor);
		$this->saved();
		
		// clear statistics cache
		WCF::getCache()->clearResource('businessStatistics');
		// reset tag cloud cache
		WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.businessCategoryTagCloud-*', true);
		
		if ($this->category->getPermission('canAddLinkWithoutModeration')) {
			// get new Business Category Editor object
			$this->categoryEditor = new BusinessCategoryEditor($link->categoryID);
			
			// set links count
			$this->categoryEditor->updateLinks();
			
			// clear cache
			WCF::getCache()->clearResource('businessCategory');
		}
		
		// save tags
		if (MODULE_TAGGING) {
			$tagArray = TaggingUtil::splitString($this->tags);
			if (count($tagArray)) $link->updateTags($tagArray);
		}
		
		if (!WCF::getUser()->userID && !$this->category->getPermission('canAddLinkWithoutModeration')) {
			// redirect to url
			WCF::getTPL()->assign(array(
				'url' => ('index.php?page=BusinessCategory&categoryID='.$this->categoryID.SID_ARG_2ND_NOT_ENCODED),
				'message' => WCF::getLanguage()->get('wcf.business.link.add.success'),
				'wait' => 5
			));
			WCF::getTPL()->display('redirect');
		}
		else {
			// forward
			HeaderUtil::redirect('index.php?page=BusinessLink&linkID='.$link->linkID.SID_ARG_2ND_NOT_ENCODED);
		}
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'tags' => $this->tags,
			'category' => $this->category,
			'category' => $this->category,
			'categoryID' => $this->categoryID,
			'linkID' => $this->linkID,
			'username' => $this->username,
			'shortDescription' => $this->shortDescription,
			'kinds' => $this->kinds,
			'kind' => $this->kind,
			'url' => $this->url,
			'age' => $this->age,
			'languageID' => $this->languageID,
			'isSticky' => $this->isSticky,
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		
		// set active menu items
		PageMenu::setActiveMenuItem('wcf.header.menu.business.links');
		
		// check module options
		if (MODULE_BUSINESS != 1) {
			throw new IllegalLinkException();
		}
		
		// check upload permission
		if (MODULE_ATTACHMENT != 1 && !$this->category->getPermission('canUploadAttachment')) {
			$this->showAttachments = false;
		}
		
		// get attachments editor
		if ($this->attachmentListEditor == null) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
			$this->attachmentListEditor = new MessageAttachmentListEditor(array(), 'businessLink', WCF::getPackageID('de.wcf.tommygfx.business'), WCF::getUser()->getPermission('user.business.maxAttachmentSize'), WCF::getUser()->getPermission('user.business.allowedAttachmentExtensions'), WCF::getUser()->getPermission('user.business.maxAttachmentCount'));
		}
		
		parent::show();
	}
}
?>
