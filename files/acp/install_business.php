<?php
/**
 * Sets group options and write style file.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.
 * @category 	WoltLab Community Framework 
 */
$packageID = $this->installation->getPackageID();
$sql = "DELETE FROM	wcf".WCF_N."_package_installation_file_log
		WHERE		 filename = 'acp/install_business.php'";
		WCF::getDB()->sendQuery($sql);
		
		if ($this->installation->getAction() == 'install') {
			// refresh style files
			require_once(WCF_DIR.'lib/data/style/StyleEditor.class.php');
			$sql = "SELECT 	*
				FROM 	wcf".WCF_N."_style";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$style = new StyleEditor(null, $row);
				$style->writeStyleFile();
			}

			// user, mod and admin options
			$sql = "UPDATE 	wcf".WCF_N."_group_option_value
				SET	optionValue = 1
				WHERE	groupID IN (4,5,6)
					AND optionID IN (
					SELECT	optionID
					FROM	wcf".WCF_N."_group_option
					WHERE	optionName LIKE 'user.business.%'
							OR optionName LIKE 'mod.business.%'
							OR optionName LIKE 'admin.business.%'
						AND packageID IN (
							SELECT	dependency
							FROM	wcf".WCF_N."_package_dependency
							WHERE	packageID = ".$packageID."
						)
					)
				AND optionValue = '0'";
			WCF::getDB()->sendQuery($sql);
		}
		return;
?>
