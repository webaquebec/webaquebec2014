var Slideshow = ( function( $, window, document, undefined ) {

	// Default configurations, can be overwritten when the object is created
	// navigationType: 'page', 'arrow' or 'both'
	var defaultConfig = {
		containerSelector 		: '.js-slider-container',
		slideSelector 			: '.slide',
		pageBtnSelector 		: '.button-page',
		arrowBtnSelector 		: '.button-nav',

		generateNavigation  	: true,
		navigationType 			: 'page',
		pageBtnContainer 		: '.js-slider',
		arrowBtnContainer   	: '.js-slider',
		pageBtnContainerClass	: 'js-nav-pages',
		arrowBtnContainerClass 	: 'js-nav-arrows',
		arrowBtnPreviousClass   : 'prev',
		arrowBtnNextClass   	: 'next',
	};

	// Constructor
	function Slideshow( obj, config, app ) {
		// Get reference to main Project object
		this.app = app;

		// Overwrite the default configuration
		this.config = $.extend({}, defaultConfig, config );

		// Get menu object
		this.slideshow = $(obj);

		// Get slides container
		this.container = this.slideshow.find( this.config.containerSelector );

		// Get all slides
		this.slides = this.slideshow.find( this.config.slideSelector );

		// Get all slides number
		this.nbSlides = this.slides.length;

		// Get slides width
		this.slideWidth = this.slides.eq(0).outerWidth( true );

		// Get page buttons
		this.pageButtons = this.slideshow.find( '.blog-nav .button-page' );

		// Get arrow buttons
		this.arrowButtons = this.slideshow.find( '.blog-nav .button.prev, .blog-nav .button.next' );

		// Get current active page position
		this.activePage = 0;

		this.displayedSlides = 3;

		// Initialize default functions
		this.init();
	}

	Slideshow.prototype = {
		// Initialization
		init: function() {
			var self = this,
				fixLayout = false;

			self.changeSlide();

			if( $( document ).width() < 1024 ) {
				self.displayedSlides = 1;
				fixLayout = true;
			}

			$( window ).resize( function() {

				self.slideWidth = self.slides.eq(0).outerWidth( true );
				self.container.width( self.slideWidth * self.nbSlides );
				self.slides.css('left', self.activePage * self.slideWidth * -self.displayedSlides);

				if( $(document).width() < 1024 ){
					self.displayedSlides = 1;
					self.pageButtons.removeClass( WAQ.Constants.isActiveClass );

					if( ! fixLayout ){
						self.slides.css('left', 0);
						self.activePage = 0;
						self.arrowButtons.eq(0).hide();
					}

					if( self.nbSlides > 0 ){
						self.arrowButtons.eq(1).show();
					}

					fixLayout = true;
				}
				else {
					self.displayedSlides = 3;
					if( fixLayout ){
						self.slides.css('left', 0);
						self.pageButtons.removeClass( WAQ.Constants.isActiveClass );
						self.activePage = 0;
					}
					fixLayout = false;
				}
			});

			self.arrowButtons.eq(0).hide();
			if( self.nbSlides < 4 ){
				self.arrowButtons.eq(1).hide();
			}

			self.pageButtons.eq(0).addClass( WAQ.Constants.isActiveClass );
			self.container.width( self.slideWidth * self.nbSlides );

            self.slides.find('a, button').attr('tabindex', '-1');
            self.slides.eq( self.displayedSlides * self.activePage ).find('a, button').removeAttr('tabindex');
            self.slides.eq( self.displayedSlides * self.activePage).nextAll().slice(0, 2).find('a, button').removeAttr('tabindex');
		},
		changeSlide: function(){
			var self = this;

			self.pageButtons.on('click', function(){
				$this = $(this);

				var index  = $this.index() - 2,
					offset = self.slideWidth * index * -self.displayedSlides;

				self.activePage = index;

				self.pageButtons.removeClass( WAQ.Constants.isActiveClass );
				$this.addClass( WAQ.Constants.isActiveClass );

				if( index > 0 ){
					self.arrowButtons.eq(0).show();
				} else {
					self.arrowButtons.eq(0).hide();
				}

				if( index + 1 != Math.floor( self.nbSlides / self.displayedSlides ) ){
					self.arrowButtons.eq(1).show();
				} else {
					self.arrowButtons.eq(1).hide();
				}

				self.slides.animate({
					left: offset
				}, 500);

	            self.slides.find('a, button').attr('tabindex', '-1');
	            self.slides.eq( self.displayedSlides * self.activePage ).find('a, button').removeAttr('tabindex');
	            self.slides.eq( self.displayedSlides * self.activePage).nextAll().slice(0, 2).find('a, button').removeAttr('tabindex');

			});

			self.arrowButtons.on('click', function(){
				var $this = $(this),
					activePage = self.activePage,
					newActivePage = activePage;


				if( $this.hasClass('prev') ){
					newActivePage--;
				}

				else if( $this.hasClass('next') ){
					newActivePage++;
				}

				self.activePage = newActivePage;
				offset = self.slideWidth * newActivePage * -self.displayedSlides;

				if( newActivePage > 0 ){
					self.arrowButtons.eq(0).show();
				} else {
					self.arrowButtons.eq(0).hide();
				}

				if( newActivePage + 1 != Math.floor( self.nbSlides / self.displayedSlides ) ){
					self.arrowButtons.eq(1).show();
				} else {
					self.arrowButtons.eq(1).hide();
				}

				self.pageButtons.removeClass( WAQ.Constants.isActiveClass );
				self.pageButtons.eq( newActivePage ).addClass( WAQ.Constants.isActiveClass );

				self.slides.animate({
					left: offset
				}, 500);

	            self.slides.find('a, button').attr('tabindex', '-1');
	            self.slides.eq( self.displayedSlides * self.activePage ).find('a, button').removeAttr('tabindex');
	            self.slides.eq( self.displayedSlides * self.activePage).nextAll().slice(0, 2).find('a, button').removeAttr('tabindex');

			});
		}
	};

	return Slideshow;

})(jQuery, window, document);