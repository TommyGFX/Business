/**
 * Handles link rating.
 * 
 * @author 	Rico P.
 * @copyright	2010 TommyGFX-Design
 * @license	Lesser General Public License <http://de.wikipedia.org/wiki/GNU_Lesser_General_Public_License>
 * @package	de.wcf.tommygfx.business
 * @subpackage	data.business.js
 * @category 	WoltLab Community Framework 
 */
var BusinessLinkRating = Class.create({
	/**
	 * Initialises a new rating option.
	 */
	initialize: function(elementName, currentRating) {
		this.elementName = elementName;
		this.currentRating = currentRating;

		var span = document.getElementById(this.elementName + 'Span');
		if (span) {
			// add stars
			for (var i = 1; i <= 5; i++) {
				var star = document.createElement('img');
				star.src = RELATIVE_WCF_DIR+'icon/businessNoRatingS.png';
				star.alt = '';
				star.rating = this;
				star.name = i;
				star.onmouseover = function() { this.style.cursor = 'pointer'; this.rating.showRating(parseInt(this.name)); };
				star.onclick = function() { this.rating.submitRating(parseInt(this.name)); };
				span.appendChild(star);
			}
		
			// add listener
			span.rating = this;
			span.onmouseout = function() { this.rating.showCurrentRating(); };
			
			// set visible
			span.className = '';
		}
		
		if (this.currentRating > 0) {
			this.showCurrentRating();
		}
	},
	
	/**
	 * Shows the current user rating.
	 */	
	showCurrentRating: function() {
		this.showRating(this.currentRating);
	},

	/**
	 * Shows given rating.
	 */	
	showRating: function(rating) {
		var span = document.getElementById(this.elementName + 'Span');
		if (span) {
			for (var i = 1; i <= rating; i++) {
				if (span.childNodes[i - 1]) {
					span.childNodes[i - 1].src = RELATIVE_WCF_DIR+'icon/businessRatingS.png';
				}
			}
			
			
			for (var i = rating + 1; i <= 5; i++) {
				if (span.childNodes[i - 1]) {
					span.childNodes[i - 1].src = RELATIVE_WCF_DIR+'icon/businessNoRatingS.png';
				}
			}
		}	
	},

	/**
	 * Submits given rating.
	 */	
	submitRating: function(rating) {
		var element = document.getElementById(this.elementName);
		var select = document.getElementById(this.elementName + 'Select');
		if (element) {
			this.currentRating = rating;
			element.value = rating;
			
			if (select) {
				select.selectedIndex = rating - 1;
			}
			
			element.form.submit();
		}
	}
});
