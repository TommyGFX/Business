<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessEditor.class.php');

/**
 * Make a link important.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	action
 * @category 	WoltLab Community Framework
 */
class BusinessLinkStickyAction extends AbstractAction {
	public $linkID = 0;
	public $link = null;
	
	/**
	 * isSticky
	 *
	 * @var intval
	 */
	public $isSticky = 0;	
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get link
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
		$this->link = new BusinessEditor($this->linkID);
		
		// get isSticky
		if (isset($_REQUEST['isSticky'])) $this->isSticky = intval($_REQUEST['isSticky']);
		
	}
	
	/**
	 * @see Action::execute();
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('mod.business.canEditLinks');

		// change isSticky
		$this->link->update(array(
			'isSticky' => $this->isSticky
		));
		
		// call event
		$this->executed();
		
		// forward
		HeaderUtil::redirect('index.php?page=BusinessLink&linkID='.$this->linkID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>
