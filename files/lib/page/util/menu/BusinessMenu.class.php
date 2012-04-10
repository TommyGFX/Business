<?php
// wcf imports
require_once(WCF_DIR.'lib/page/util/menu/TreeMenu.class.php');
 
/**
 * Builds the business menu.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page.util.menu
 * @category 	WoltLab Community Framework 
 */
class BusinessMenu extends TreeMenu {
	protected static $instance = null;
	public $linkID = 0;
	
	/**
	 * Returns an instance of the businessMenu class.
	 * 
	 * @return	businessMenu
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new BusinessMenu();
		}
		
		return self::$instance;
	}
	
	/**
	 * @see TreeMenu::loadCache()
	 */
	protected function loadCache() {
		parent::loadCache();
		
		WCF::getCache()->addResource('businessMenu-'.PACKAGE_ID, WCF_DIR.'cache/cache.businessMenu-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBusinessMenu.class.php');
		$this->menuItems = WCF::getCache()->get('businessMenu-'.PACKAGE_ID);
	}
	
	/**
	 * @see TreeMenu::parseMenuItemLink()
	 */
	protected function parseMenuItemLink($link, $path) {
		if (preg_match('~\.php$~', $link)) {
			$link .= SID_ARG_1ST; 
		}
		else {
			$link .= SID_ARG_2ND_NOT_ENCODED;
		}
		
		// insert link id
		$link = str_replace('%s', $this->linkID, $link);
		
		return $link;
	}
	
	/**
	 * @see TreeMenu::parseMenuItemIcon()
	 */
	protected function parseMenuItemIcon($icon, $path) {
		return StyleManager::getStyle()->getIconPath($icon);
	}
}
?>
