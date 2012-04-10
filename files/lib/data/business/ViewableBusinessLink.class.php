<?php
// wcf imports
require_once(WCF_DIR.'lib/data/business/BusinessLink.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');
require_once(WCF_DIR.'lib/page/util/menu/BusinessMenuContainer.class.php');

/**
 * Represents a viewable link.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework 
 */
class ViewableBusinessLink extends BusinessLink implements BusinessMenuContainer {	

	protected static $businessMenuObj = null;

	/**
	 * Returns formatted message.
	 *
	 * @return	string
	 */	
	public function getFormattedMessage() {
		// require message parser
		require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');

		// parse message
		$parser = MessageParser::getInstance();
		$parser->setOutputType('text/html');

		// return parsed message
		return $parser->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, !$this->messagePreview);		
	}
	
	/**
	 * Initialises the Business menu.
	 */
	protected static function initBusinessMenu() {
		require_once(WCF_DIR.'lib/page/util/menu/BusinessMenu.class.php');
		self::$businessMenuObj = BusinessMenu::getInstance();
	}
	
	/**
	 * @see BusinessMenuContainer::getBusinessMenu()
	 */
	public static final function getBusinessMenu() {
		if (self::$businessMenuObj === null) {
			self::initBusinessMenu();
		}
		
		return self::$businessMenuObj;
	}
	
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
			// user profile
			$this->author = new UserProfile($this->userID);
			
			// user options
			require_once(WCF_DIR.'lib/data/user/option/UserOptions.class.php');
			$userOptions = new UserOptions('short');
			$this->userData = $userOptions->getOptionTree('profile', $this->author);
	}
	

	/**
	 * Gets the link rating result for template output.
	 *
	 * @return	string		link rating result for template output
	 */
	public function getRatingOutput() {
		$rating = $this->getRating();
		if ($rating !== false) $roundedRating = round($rating, 0);
		else $roundedRating = 0;
		$description = '';
		if ($this->ratings > 0) {
			$description = WCF::getLanguage()->get('wcf.business.vote.description', array('$votes' => StringUtil::formatNumeric($this->ratings), '$vote' => StringUtil::formatNumeric($rating)));
		}
		
		return '<img src="'.StyleManager::getStyle()->getIconPath('rating'.$roundedRating.'.png').'" alt="" title="'.$description.'" />';
	}
	
	/**
	 * Returns the filename of the link icon.
	 *
	 * @return	string		filename of the link icon
	 */
	public function getIconName() {
	
		$icon = 'businessLink';
		
		// important
		if ($this->isSticky == 1) $icon .= 'Sticky';
		
		// closed
		if ($this->isClosed) $icon .= 'Closed';
		
		return $icon;
	}
	
	/**
	 * Returns the flag icon for the link language.
	 * 
	 * @return	string
	 */
	public function getLanguageIcon() {
		$languageData = Language::getLanguage($this->languageID);
		if ($languageData !== null) {
			return '<img src="'.StyleManager::getStyle()->getIconPath('language'.ucfirst($languageData['languageCode']).'S.png').'" alt="" title="'.WCF::getLanguage()->get('wcf.global.language.'.$languageData['languageCode']).'" />';
		}
		return '';
	}
	
	/**
	 * Returns the user object.
	 * 
	 * @return	UserProfile
	 */
	public function getAuthor() {
		return $this->author;
	}
	
	
	/**
	 * Returns the user options object.
	 * 
	 * @return	UserOptions
	 */
	public function getUserOptions() {
		return $this->userData;
	}
	
}
?>
