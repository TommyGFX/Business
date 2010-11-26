<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
 
/**
 * Shows the latest 5 Firmen.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.system.event.listener
 * @category 	WoltLab Community Framework 
 */
class UserPageBusinessLinksListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (MODULE_BUSINESS == 1 && PROFILE_SHOW_LAST_BUSINESS_LINKS == 1 && WCF::getUser()->getPermission('user.business.canViewBusiness')) {
			// get links
			require_once(WCF_DIR.'lib/data/business/Business.class.php');
			$business = new Business();
			$business->sqlConditions = 'business_link.isDisabled = 0 AND business_link.userID = '.$eventObj->frame->getUserID();
			$count = $business->countObjects();
			if ($count > 0) {
				$business->sqlLimit = 5;
				$business->readObjects();
				
				WCF::getTPL()->assign(array(
					'user' => $eventObj->frame->getUser(),
					'links' => $business->getObjects(),
					'linkItems' => $count
				));
				WCF::getTPL()->append('additionalContent3', WCF::getTPL()->fetch('userProfileBusinessLinks'));
			}
		}
	}
}
?>
