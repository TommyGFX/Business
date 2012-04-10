<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/data/business/BusinessLink.class.php');

/**
 * Checks the download permission for firmen attachments.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.system.event.listener
 * @category 	WoltLab Community Framework
 */
class AttachmentCheckBusinessLinkPermissionListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$attachment = $eventObj->attachment;
		if ($attachment['containerID'] && $attachment['containerType'] == 'businessLink') {
			// get link
			$link = new BusinessLink($attachment['containerID'], null);
			
			// check read permission
			$link->enter();
			
			// check download permission
			if (!WCF::getUser()->getPermission('user.business.canDownloadAttachment') && (!$eventObj->thumbnail || !WCF::getUser()->getPermission('user.business.canViewAttachmentPreview'))) {
				throw new PermissionDeniedException();
			}
		}
	}
}
?>
