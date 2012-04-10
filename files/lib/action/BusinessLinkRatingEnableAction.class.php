<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessEditor.class.php');

/**
 * Disable/enable rating of a link.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	action
 * @category 	WoltLab Community Framework
 */
class BusinessLinkRatingEnableAction extends AbstractAction {
	public $linkID = 0;
	public $link = null;
	
	/**
	 * isDisabled
	 *
	 * @var intval
	 */
	public $isDisabled = 0;	
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get link
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
		$this->link = new BusinessEditor($this->linkID);
		
		// get isDisabled
		if (isset($_REQUEST['isDisabled'])) $this->isDisabled = intval($_REQUEST['isDisabled']);
		
	}
	
	/**
	 * @see Action::execute();
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('mod.business.canEditLinks');

		// change ratingDisabled
		$this->link->update(array(
			'ratingDisabled' => $this->isDisabled
		));
		
		// call event
		$this->executed();
		
		// forward
		HeaderUtil::redirect('index.php?page=BusinessLink&linkID='.$this->linkID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>
