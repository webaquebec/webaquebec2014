var WAQ = (function(WAQ, $, window, document, undefined) {

	var $body = $('body');

	WAQ.Common = {

		// Rudimentary check to see if the user navigates with the keyboard
		checkForKeyboardInput: function() {
			var self = this;
			$body.on('keydown', function(e) {
				var keyCode = (window.event) ? e.which : e.keyCode;
				if (keyCode === 9 || keyCode === 13 || keyCode === 37 || keyCode === 38 || keyCode === 39 || keyCode === 40) {
					WAQ.keyboardNavigation = true;
					$.event.trigger('keyboardIsActive');
					$( '.' + WAQ.Constants.onClickClass ).removeClass( WAQ.Constants.onClickClass );
					$( '.' + WAQ.Constants.onHoverClass ).removeClass( WAQ.Constants.onHoverClass );
				}
			});
		},

		// Remove the focus outline in Firefox and IE when the user click on a link
		betterFocus: function(arrElements) {
			var focusableElements = arrElements || ['a','button'];

			$body.on('mousedown mouseenter mouseleave', focusableElements.toString(), function(e) {
				var $this = $(this),
					className = (e.type === 'mousedown') ? WAQ.Constants.onClickClass : WAQ.Constants.onHoverClass;

				$($this.get(0).tagName.toLowerCase() + '.' + className).removeClass(className);

				if (e.type !== 'mouseleave') $this.addClass(className);
			});
		},

		// Go straight to page main content
		goToContent: function($obj) {
			$obj.on('click', function(e) {
				$('.go-to-content').eq(0).attr('tabindex', '-1').focus();
				$( window ).scrollTop( $('.go-to-content').offset().top - 64 - 15 );
				e.preventDefault();
			});
		},

		// Initial check of browser font size, attach the 'fontresize' event on the document
		checkFontSize: function(){
			var self = this;

			self.manageIsZoomedClass();
			$(document).on('fontresize', function() { self.manageIsZoomedClass(); });
		},

		// Toggle 'isZoomed' class on body tag when the user zoom or unzoom fonts. Works in Firefox only
		manageIsZoomedClass: function() {
			var bodyClasses = ($body.attr('class')) ? _.filter($body.attr('class').split(' '), function(x) { if (x.indexOf(WAQ.Constants.fontPrefixClass) === -1) return x; }) : '',
				fontsize = parseInt($body.css('font-size').replace('px', ''), 10);

			if (bodyClasses) $body.attr('class', '').addClass(bodyClasses.join(' '));

			if (fontsize > 16) {
				$body.removeClass('not-zoomed');
				$body.addClass(WAQ.Constants.onZoomClass + ' ' + WAQ.Constants.fontPrefixClass + fontsize);
			} else {
				$body.addClass('not-zoomed');
				$body.removeClass(WAQ.Constants.onZoomClass);
			}
		},

		// Set equal heights to a group of jQuery object
		equalHeights: function(obj) {
			var $obj = obj instanceof $ ? obj : $(obj),
				height = $(_.max($.makeArray($obj.map(function() { return $(this).height(); })))).eminize();

			$obj.each(function() { $(this).css('height', height); });
		},

		// Add 'extLinksClass' to external links
		externalLinks: function() {
			$('a[href^="http:"]:not([href*="' + window.location.host + '"])').addClass(WAQ.Constants.extLinksClass);
		},

		// Get class name that start with specific string
		getClassStartingWith: function(obj, className) {
			var result = $.grep( obj[0].className.split( " " ), function( v, i ){
                return v.indexOf( className ) === 0;
            }).join();

            return result;
		},

		// Sequential fade in objects
		sequentialFadeIn: function(selectorText, speed, display, callback) {
			display = typeof display !== 'undefined' ? display : 'block';

			var els = $(selectorText),
			    i   = 0;

			(function helper() {
			    els.eq(i++).fadeIn(speed, helper).css('display', display);
			    if (callback && i === els.length) {callback();}
			})();
		},

		// Sequential fade out objects
		sequentialFadeOut: function(selectorText, speed, display, callback) {
			display = typeof display !== 'undefined' ? display : 'none';

			var els = $(selectorText),
			    i   = els.length - 1;

			(function helper() {
				if( i == -1 ){return;}
			    els.eq(i--).fadeOut(speed, helper).css('display', display);
			    if (callback && i === -1) {callback();}
			})();
		},

		// Main function to create objects
		createObjects: function($obj, objName, config) {
			return $.makeArray($obj.map(function() { return new objName(this, config || {}, WAQ); }));
		},
	};

	return WAQ;

})(WAQ || {}, jQuery, window, document);