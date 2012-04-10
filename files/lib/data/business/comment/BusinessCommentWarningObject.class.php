<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/infraction/warning/object/WarningObject.class.php');
require_once(WCF_DIR.'lib/data/business/comment/ViewableBusinessComment.class.php');
 
/**
 *  An implementation of WarningObject to support the usage of a Business comment as a warning object.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.comment
 * @category 	WoltLab Community Framework 
 */
class BusinessCommentWarningObject extends ViewableBusinessComment implements WarningObject {
	/**
	 * @see WarningObject::getTitle()
	 */
	public function getTitle() {
		WCF::getCache()->addResource('bbcodes', WCF_DIR.'cache/cache.bbcodes.php', WCF_DIR.'lib/system/cache/CacheBuilderBBCodes.class.php');
		return $this->getExcerpt();
	}
	
	/**
	 * @see WarningObject::getURL()
	 */
	public function getURL() {
		return 'index.php?page=BusinessCommentsList&linkID='.$this->linkID.'&commentID='.$this->commentID.'#comment'.$this->commentID;
	}
}
?>
