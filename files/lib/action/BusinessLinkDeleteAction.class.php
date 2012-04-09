<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessEditor.class.php');
require_once(WCF_DIR.'lib/data/business/category/BusinessCategoryEditor.class.php');
// get cache
WCF::getCache()->addResource('businessStatistics', WCF_DIR.'cache/cache.businessStatistics.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessStatistics.class.php');
 
/**
 * Deletes a link.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	action
 * @category 	WoltLab Community Framework
 */
class BusinessLinkDeleteAction extends AbstractSecureAction {
	/**
	 * link id
	 *
	 * @var integer
	 */
	public $linkID = 0;
	
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
		
		// get link id
		if (isset($_REQUEST['linkID'])) $this->linkID = intval($_REQUEST['linkID']);
		$this->link = new BusinessEditor($this->linkID);
		if (!$this->link->linkID) {
			throw new IllegalLinkException();
		}
		// get category editor object
		$this->categoryEditor = new BusinessCategoryEditor($this->link->categoryID);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		if ((!$this->link->isOwnLink() && !$this->categoryEditor->getPermission('canDeleteOwnLink') && !$this->link->userID) || !WCF::getUser()->getPermission('mod.business.canDeleteLinks')) {
			throw new PermissionDeniedException();
		}
		
		// delete link
		$this->link->delete();
		if (!$this->link->isDisabled) {
			// set links count
			$this->categoryEditor->updateLinks(-1);
		}
		
		// clear statistics cache
		WCF::getCache()->clearResource('businessStatistics');
		// reset cache
		WCF::getCache()->clearResource('businessCategory');
		// reset tag cloud cache
		WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.businessCategoryTagCloud-*', true);
		
		$this->executed();
		
		// forward
		HeaderUtil::redirect('index.php?page=BusinessCategory&categoryID='.$this->link->categoryID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>
