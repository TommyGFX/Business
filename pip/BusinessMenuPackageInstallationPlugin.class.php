<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');
 
/**
 * This PIP installs, updates or deletes Business menu items.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.acp.package.plugin
 * @category 	WoltLab Community Framework 
 */
class BusinessMenuPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	public $tagName = 'businessmenu';
	public $tableName = 'business_menu_item';
	
	/** 
	 * @see PackageInstallationPlugin::install()
	 */
	public function install() {
		parent::install();
		
		if (!$xml = $this->getXML()) {
			return;
		}
		
		// Create an array with the data blocks (import or delete) from the xml file.
		$businessMenuXML = $xml->getElementTree('data');
		
		// Loop through the array and install or uninstall business-menu items.
		foreach ($businessMenuXML['children'] as $key => $block) {
			if (count($block['children'])) {
				// Handle the import instructions
				if ($block['name'] == 'import') {
					// Loop through business-menu items and create or update them.
					foreach ($block['children'] as $businessMenuItem) {
						// Extract item properties.
						foreach ($businessMenuItem['children'] as $child) {
							if (!isset($child['cdata'])) continue;
							$businessMenuItem[$child['name']] = $child['cdata'];
						}
					
						// check required attributes
						if (!isset($businessMenuItem['attrs']['name'])) {
							throw new SystemException("Required 'name' attribute for business menu item is missing", 13023);
						}
						
						// default values
						$menuItemLink = $parentMenuItem = $menuItemIcon = $permissions = $options = '';
						$showOrder = null;
						
						// get values
						$menuItem = $businessMenuItem['attrs']['name'];
						if (isset($businessMenuItem['link'])) $menuItemLink = $businessMenuItem['link'];
						if (isset($businessMenuItem['parent'])) $parentMenuItem = $businessMenuItem['parent'];
						if (isset($businessMenuItem['icon'])) $menuItemIcon = $businessMenuItem['icon'];
						if (isset($businessMenuItem['showorder'])) $showOrder = intval($businessMenuItem['showorder']);
						$showOrder = $this->getShowOrder($showOrder, $parentMenuItem, 'parentMenuItem');
						if (isset($businessMenuItem['permissions'])) $permissions = $businessMenuItem['permissions'];
						if (isset($businessMenuItem['options'])) $options = $businessMenuItem['options'];
						
						// If a parent link was set and this parent is not in database 
						// or it is a link from a package from other package environment: don't install further.
						if (!empty($parentMenuItem)) {
							$sql = "SELECT	COUNT(*) AS count
								FROM 	wcf".WCF_N."_business_menu_item
								WHERE	menuItem = '".escapeString($parentMenuItem)."'";
							$menuItemCount = WCF::getDB()->getFirstRow($sql);
							if ($menuItemCount['count'] == 0) {
								throw new SystemException("For the menu item '".$menuItem."' no parent item '".$parentMenuItem."' exists.", 13011);
							}
						}
						
						// Insert or update items. 
						// Update through the mysql "ON DUPLICATE KEY"-syntax. 
						$sql = "INSERT INTO			wcf".WCF_N."_business_menu_item
											(packageID, menuItem, parentMenuItem, menuItemLink, menuItemIcon, showOrder, permissions, options)
							VALUES			(".$this->installation->getPackageID().",
											'".escapeString($menuItem)."',
											'".escapeString($parentMenuItem)."',
											'".escapeString($menuItemLink)."',
											'".escapeString($menuItemIcon)."',
											".$showOrder.",
											'".escapeString($permissions)."',
											'".escapeString($options)."')
							ON DUPLICATE KEY UPDATE 	parentMenuItem = VALUES(parentMenuItem),
											menuItemLink = VALUES(menuItemLink),
											menuItemIcon = VALUES(menuItemIcon),
											showOrder = VALUES(showOrder),
											permissions = VALUES(permissions),
											options = VALUES(options)";
						WCF::getDB()->sendQuery($sql);
					}
				}
				// Handle the delete instructions.
				else if ($block['name'] == 'delete') {
					if ($this->installation->getAction() == 'update') {
						// Loop through business-menu items and delete them.
						$itemNames = '';
						foreach ($block['children'] as $acpMenuItem) {
							// check required attributes
							if (!isset($businessMenuItem['attrs']['name'])) {
								throw new SystemException("Required 'name' attribute for 'businessmenuitem'-tag is missing.", 13023);
							}
							// Create a string with all item names which should be deleted (comma seperated).
							if (!empty($itemNames)) $itemNames .= ',';
							$itemNames .= "'".escapeString($acpMenuItem['attrs']['name'])."'";
						}
						// Delete items.
						if (!empty($itemNames)) {
							$sql = "DELETE FROM	wcf".WCF_N."_business_menu_item
								WHERE		menuItem IN (".$itemNames.")
										AND  packageID = ".$this->installation->getPackageID();
							WCF::getDB()->sendQuery($sql);
						}
					}
				}
			}
		}
	}
}
?>
