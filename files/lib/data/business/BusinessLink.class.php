<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/Message.class.php');

/**
 * Represents a link (container for links).
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework 
 */
class BusinessLink extends Message {	

	/**
	 * Creates a new Link object.
	 *
	 * @param	integer		$linkID		id of a link
	 * @param array 		$row		resultset with link data form database
	 */
	public function __construct($linkID, $row = null) {		
		if ($linkID !== null) {			
			$sql = "SELECT		business_link.*, link_rating.rating AS userRating
					FROM		wcf".WCF_N."_business_link business_link
					LEFT JOIN 	wcf".WCF_N."_business_link_rating link_rating
						ON 		(link_rating.linkID = business_link.linkID
									AND ".(WCF::getUser()->userID ? "link_rating.userID = ".WCF::getUser()->userID : "link_rating.ipAddress = '".escapeString(WCF::getSession()->ipAddress)."'").")
					WHERE		business_link.linkID = ".$linkID;
			$row = WCF::getDB()->getFirstRow($sql);			
		}
		parent::__construct($row);	
		$this->messageID = $row['linkID'];
	}
	
	/**
	 * Enters link and checks permission.
	 */
	public function enter() {		
		// check if link exists
		if (!$this->linkID) {
			throw new IllegalLinkException();
		}
		
		if ($this->age != 0) {
			// check if active user is younger than link age
			$this->user = new UserProfile(WCF::getUser()->userID);
			$this->userAge = $this->user->getAge();
		
			$isYoungerThanAge = ($this->userAge < $this->age && !WCF::getUser()->getPermission('mod.business.canEditLinks'));
		
			if ($isYoungerThanAge) {
				throw new NamedUserException(WCF::getLanguage()->get('wcf.business.link.isYoungerThanAge'));
			}
		}
		
		// check permission
		$canEnterLink = (!$this->isDisabled || WCF::getUser()->getPermission('mod.business.canEnableLinks')) && WCF::getUser()->getPermission('user.business.canEnterLink') || $this->isOwnLink();
		
		if (!$canEnterLink) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Checks if link is owned by active user.
	 */
	public function isOwnLink() {
		return WCF::getUser()->userID == $this->userID && WCF::getUser()->userID;
	}
	
	/**
	 * Returns true, if the active user can see this link.
	 * 
	 * @return	boolean
	 */
	public function isSeeable() {
		if (!$this->isDisabled || WCF::getUser()->getPermission('mod.business.canEnableLinks') || $this->isOwnLink()) {
			return true;
		}
		return false;
	}
	
	
	/**
	 * Returns true, if the active user can comment this link.
	 * 
	 * @return	boolean
	 */
	public function isCommentable() {
		if ($this->isClosed == 0 || WCF::getUser()->getPermission('mod.business.canEditComments')) {
			return true;
		}
		return false;
	}
	
	public function isYoungerThanAge() {
		
		return false;
	}			
	
	/**
	 * Returns true, if the active user can edit this link.
	 * 
	 * @return	boolean
	 */
	public function isEditable() {
		if (($this->userID == WCF::getUser()->userID && WCF::getUser()->userID && $this->isClosed == 0) || WCF::getUser()->getPermission('mod.business.canEditLinks')) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the tags of this link.
	 * 
	 * @return	array<Tag>
	 */
	public function getTags($languageIDArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(WCF_DIR.'lib/data/business/TaggedBusinessLink.class.php');
		
		// get tags
		return TagEngine::getInstance()->getTagsByTaggedObject(new TaggedBusinessLink(null, array(
			'linkID' => $this->linkID,
			'taggable' => TagEngine::getInstance()->getTaggable('de.wcf.tommygfx.business')
		)), $languageIDArray);
	}

	/**
	 * Returns the result of the rating of this link.
	 * 
	 * @return	mixed		result of the rating of this link
	 */
	public function getRating() {
		if ($this->ratings > 0 && $this->ratings >= BUSINESS_LINK_MIN_RATINGS) {
			return $this->rating / $this->ratings;
		}
		return false;
	}
}
?>
