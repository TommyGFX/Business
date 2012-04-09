<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');
require_once(WCF_DIR.'lib/data/business/comment/BusinessCommentEditor.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessEditor.class.php');
// get cache
WCF::getCache()->addResource('businessStatistics', WCF_DIR.'cache/cache.businessStatistics.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessStatistics.class.php');
 
/**
 * Deletes a Business comment.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	action
 * @category 	WoltLab Community Framework 
 */
class BusinessCommentDeleteAction extends AbstractSecureAction {

	/**
	 * comment id
	 *
	 * @var integer
	 */
	public $commentID = 0;
	
	/**
	 * business comment editor object
	 *
	 * @var BusinessCommentEditor
	 */
	public $comment = null;

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// comment id
		if (isset($_REQUEST['commentID'])) $this->commentID = intval($_REQUEST['commentID']);
		$this->comment = new BusinessCommentEditor($this->commentID);
		if (!$this->comment->commentID) {
			throw new IllegalLinkException();
		}
		
		if (!$this->comment->isDeletable()) {
			throw new IllegalLinkException();
		}
		
		// create new link instance
		$this->link = new BusinessEditor($this->comment->linkID);
		// enter link
		$this->link->enter();
		
		if ($this->link->isClosed == 1 && !WCF::getUser()->getPermission('mod.business.canEditComments')) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// update link
		$this->link->update(array(
			'comments' => '- 1'
		));
		
		// clear statistics cache
		WCF::getCache()->clearResource('businessStatistics');
		
		// delete comment
		$this->comment->delete();		
		$this->executed();
		
		// forward
		HeaderUtil::redirect('index.php?page=BusinessCommentsList&linkID='.$this->comment->linkID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>
