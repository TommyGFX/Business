<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessEditor.class.php');

/**
 * Close a link.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	action
 * @category 	WoltLab Community Framework
 */
class BusinessLinkClosedAction extends AbstractAction {
	public $linkID = 0;
	public $link = null;
	
	/**
	 * isClosed
	 *
	 * @var intval
	 */
	public $isClosed = 0;	
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get link
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
		$this->link = new BusinessEditor($this->linkID);
		
		// get isClosed
		if (isset($_REQUEST['isClosed'])) $this->isClosed = intval($_REQUEST['isClosed']);
		
	}
	
	/**
	 * @see Action::execute();
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('mod.business.canEditLinks');

		// change isClosed
		$this->link->update(array(
			'isClosed' => $this->isClosed
		));
		
		// call event
		$this->executed();
		
		// forward
		HeaderUtil::redirect('index.php?page=BusinessLink&linkID='.$this->linkID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>
