<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessEditor.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategoryEditor.class.php');
 
/**
 * Edits the link status.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	action
 * @category 	WoltLab Community Framework
 */
class BusinessLinkStatusEditAction extends AbstractAction {
	public $linkID = 0;
	public $link = null;
	
	/**
	 * statusComment
	 *
	 * @var string
	 */
	public $statusComment = '';
	
	/**
	 * status
	 *
	 * @var intval
	 */
	public $status = 0;
	
	/**
	 * isDisabled
	 *
	 * @var intval
	 */
	public $isDisabled = 0;
	
	/**
	 * notificationViaPN
	 *
	 * @var intval
	 */
	public $notificationViaPN = 0;
	
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get link
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
		$this->link = new BusinessEditor($this->linkID);
		
		// create new category editor instance
		$this->category = new BusinessCategoryEditor($this->link->categoryID);
		
		// get status
		if (isset($_POST['status'])) $this->status = intval($_POST['status']);
		
		// get isDisabled
		if (isset($_POST['isDisabled'])) $this->isDisabled = intval($_POST['isDisabled']);
		
		// get notificationViaPN
		if (isset($_POST['notificationViaPN'])) $this->notificationViaPN = intval($_POST['notificationViaPN']);
		
		// get statusComment
		if (isset($_POST['statusComment'])) {
			$this->statusComment = $_POST['statusComment'];
			if (CHARSET != 'UTF-8') $this->statusComment = StringUtil::convertEncoding('UTF-8', CHARSET, $this->statusComment);
		}
	}
	
	/**
	 * @see BusinessLinkStatusEditAction::checkDisabledStatus();
	 */
	public function checkDisabledStatus() {
		if ($this->status == 3) {
			return 0;
		} else {
			return 1;
		}
	}
	
	/**
	 * @see Action::execute();
	 */
	public function execute() {
		parent::execute();
		
		WCF::getUser()->checkPermission('mod.business.canEnableLinks');
		
		if ($this->link->isDisabled) {
			if ($this->status == 3) {
				// set link count
				$this->category->updateLinks();
			
				// reset cache
				WCF::getCache()->clearResource('businessCategory');
				// reset tag cloud cache
				WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.businessCategoryTagCloud-*', true);
			}
		}
		else {
			// set link count
			$this->category->updateLinks(-1);
			
			// reset cache
			WCF::getCache()->clearResource('businessCategory');
			// reset tag cloud cache
			WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.businessCategoryTagCloud-*', true);
		}

		// change status
		$this->link->update(array(
			'status' => $this->status,
			'statusComment' => $this->statusComment,
			'isDisabled' => $this->checkDisabledStatus()
		));
		
		// Sends a personal message.
		if ($this->notificationViaPN == 1 && $this->link->userID != 0 && $this->status != 1) {
		require_once(WCF_DIR.'lib/data/message/pm/PMEditor.class.php');				
                        
		PMEditor::create(false, array(
			array(
					'username' => $this->link->username, 
					'userID' => $this->link->userID
				)
				), array(
				), WCF::getLanguage()->get('wcf.business.statusEdit'.$this->status.'.pm.subject'),
				WCF::getLanguage()->get('wcf.business.statusEdit'.$this->status.'.pm.message',
				array(
					'$linkUsername' => $this->link->username,
					'$link' => FileUtil::addTrailingSlash(PAGE_URL)."index.php?page=BusinessLink&linkID=".$this->linkID.SID_ARG_2ND_NOT_ENCODED,
					'$linkSubject' => $this->link->subject,
					'$workerUsername' => "[url=".FileUtil::addTrailingSlash(PAGE_URL)."index.php?page=User&userID=".WCF::getUser()->userID.SID_ARG_2ND_NOT_ENCODED."]".WCF::getUser()->username."[/url]",
					'$statusComment' => ($this->statusComment ? $this->statusComment : "-"),
					'$pageTitle' => "[b]".PAGE_TITLE."[/b]"
					)),
					WCF::getUser()->userID, WCF::getUser()->username,
				array(
					'enableSmilies' => true,
				 	'enableHtml' => false,
				 	'enableBBCodes' => true,
				 	'showSignature' => true
					));
		}
		
		// call event
		$this->executed();
		
		// forward
		HeaderUtil::redirect('index.php?page=BusinessLink&linkID='.$this->linkID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>
