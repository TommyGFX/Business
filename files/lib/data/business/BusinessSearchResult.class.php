<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/util/SearchResultTextParser.class.php');
require_once(WCF_DIR.'lib/data/business/ViewableBusinessLink.class.php');

/**
 * This class extends the viewable link by functions for a search result output.
 * 
 * @author 	Rico P.
 * @copyright	2012 Rico P.
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business
 * @category 	WoltLab Community Framework
 */
class BusinessSearchResult extends ViewableBusinessLink {
	/**
	 * @see ViewableLink::handleData();
	 */
	protected function handleData($data) {
		$data['messagePreview'] = true;
		parent::handleData($data);
	}

	/**
	 * @see ViewableBusinessLink::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		return SearchResultTextParser::parse(parent::getFormattedMessage());
	}
}
?>
