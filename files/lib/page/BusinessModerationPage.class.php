<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
require_once(WCF_DIR.'lib/data/business/ViewableBusinessLink.class.php');
require_once(WCF_DIR.'lib/data/business/ViewableBusiness.class.php');

/**
 * Shows the moderation page of the business.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page
 * @category 	WoltLab Community Framework
 */
class BusinessModerationPage extends MultipleLinkPage {
	// system
	public $templateName = 'businessModeration';
	
	/**
	 * reported links
	 * 
	 * @var	array
	 */
	public $reportedLinks = array();
	
	/**
	 * disabled links
	 * 
	 * @var	array
	 */
	public $business = null;

	

	/**
	 * @see Page::readParameters()
	 */	
	public function readParameters() {
		parent::readParameters();
		
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
			
		// read disabled links
		$this->business = new ViewableBusiness();
		$this->business->sqlOrderBy = 'time DESC';
		$this->business->sqlConditions = 'isDisabled = 1';
		$this->business->sqlLimit = 15;
		$this->business->readObjects();
			
		$sql = "SELECT 	*, business_report.userID AS reportUserID, business_report.username AS reportUsername
			FROM wcf".WCF_N."_business_link_report AS business_report
			LEFT JOIN wcf".WCF_N."_business_link AS business_link
			ON (business_link.linkID = business_report.linkID)
			ORDER BY business_report.reportTime DESC";
		$result = WCF::getDB()->sendQuery($sql, 15);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['link'] = new ViewableBusinessLink($row['linkID']);
			$this->reportedLinks[] = $row;
		}
	}
			
	
	/**
	* @see Page::assignVariables();
	*/
	public function assignVariables() {
		parent::assignVariables();
		
		// assign variables
		WCF::getTPL()->assign(array(
			'reportedLinks' => $this->reportedLinks,
			'disabledLinks' => $this->business->getObjects()
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		
		// set active menu items
		PageMenu::setActiveMenuItem('wcf.header.menu.business.links');
		
		// check permission
		WCF::getUser()->checkPermission('mod.business.canSeeModerationOverview');
		
		// check module options
		if (MODULE_BUSINESS != 1) {
			throw new IllegalLinkException();
		}
		
		// show page
		parent::show();
	}
	
}
?>
