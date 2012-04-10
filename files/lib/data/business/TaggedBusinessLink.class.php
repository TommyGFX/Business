<?php
// wcf imports
require_once(WCF_DIR.'lib/data/tag/Tagged.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');
require_once(WCF_DIR.'lib/data/business/ViewableBusinessLink.class.php');
 
/**
 * An implementation of Tagged to support the tagging of links.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework
 */
class TaggedBusinessLink extends ViewableBusinessLink implements Tagged {
	/**
	 * user object
	 * 
	 * @var	User
	 */
	protected $user = null;

	/**
	 * @see ViewableBusinessLink::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		
		// get user
		$this->user = new User(null, array('userID' => $this->userID, 'username' => $this->username));
	}

	/**
	 * @see Tagged::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}

	/**
	 * @see Tagged::getObjectID()
	 */
	public function getObjectID() {
		return $this->linkID;
	}

	/**
	 * @see Tagged::getTaggable()
	 */
	public function getTaggable() {
		return $this->taggable;
	}
	
	/**
	 * @see Tagged::getDescription()
	 */
	public function getDescription() {
		require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
		$parser = MessageParser::getInstance();
		$parser->setOutputType('text/html');
		return $parser->parse($this->message, true, false, true, false);
	}

	/**
	 * @see Tagged::getSmallSymbol()
	 */
	public function getSmallSymbol() {
		return StyleManager::getStyle()->getIconPath('businessLinkS.png');
	}

	/**
	 * @see Tagged::getMediumSymbol()
	 */
	public function getMediumSymbol() {
		return StyleManager::getStyle()->getIconPath('businessLinkM.png');
	}
	
	/**
	 * @see Tagged::getLargeSymbol()
	 */
	public function getLargeSymbol() {
		return StyleManager::getStyle()->getIconPath('businessLinkL.png');
	}

	/**
	 * @see Tagged::getUser()
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * @see Tagged::getDate()
	 */
	public function getDate() {
		return $this->time;
	}
	
	/**
	 * @see Tagged::getDate()
	 */
	public function getURL() {
		return 'index.php?page=BusinessLink&linkID='.$this->linkID;
	}
}
?>
