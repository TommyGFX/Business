<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessEditor.class.php');
 
/**
 * Deletes a link report.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	action
 * @category 	WoltLab Community Framework 
 */
class BusinessReportDeleteAction extends AbstractSecureAction {

	/**
	 * report id
	 *
	 * @var integer
	 */
	public $reportID = 0;
	
	/**
	 * link editor object
	 *
	 * @var LinkEditor
	 */
	public $link = null;

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get report id
		if (isset($_REQUEST['reportID'])) $this->reportID = intval($_REQUEST['reportID']);
		
		// get link id
		$sql = "SELECT linkID
		FROM 	wcf".WCF_N."_business_link_report
		WHERE 	reportID = ".$this->reportID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->linkID = $row['linkID'];
		}
		
		// create a new link instance
		$this->link = new BusinessEditor($this->linkID);
		if (!$this->link->linkID) {
			throw new IllegalLinkException();
		}
	
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// delete report
		$this->link->deleteReport($this->reportID);		
		$this->executed();
		
		// forward
		HeaderUtil::redirect('index.php?page=BusinessModeration'.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>
