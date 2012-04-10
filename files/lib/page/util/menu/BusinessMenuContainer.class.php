<?php

/**
 * The core class of applications that uses the Business menu should implement this interface.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.page.util.menu
 * @category 	WoltLab Community Framework 
 */
interface BusinessMenuContainer {
	/**
	 * Returns the active object of the Business menu.
	 * 
	 * @return	BusinessMenu
	 */
	public static function getBusinessMenu();
}
?>
