/*
*	jQuery.onfontresize
*	Copyright (c) 2008 Petr Staníček (pixy@pixy.cz)
*	February 2009
*
*	requires jQuery

Usage:

	jQuery(document).bind('fontresize',myHandler);

Optional:

// stop the observer
	jQuery.onFontResize.unwatch();
// start again
	jQuery.onFontResize.watch();
// start with different timeout
	jQuery.onFontResize.watch(1000);

*/

jQuery.onFontResize = {

	Delay : 250,
	Timer: null,
	on : true,
	Box : null,
	H : 0,

	init : function() {
		this.Box = document.createElement('DIV');
		jQuery(this.Box)
		.html('Détection du zoom')
		.css({
			position:'absolute',
			top:'-999px',
			left:'-9999px',
			display:'inline',
			lineHeight: 1
			})
		.appendTo('body');
		this.H = jQuery(this.Box).height();
		},

	watch : function(delay) {
		if(!this.Box) this.init();
		this.unwatch();
		if (delay) this.Delay = delay;
		this.on = true;
		this.check();
		},
	unwatch : function() {
		this.on = false;
		if (this.Timer) clearTimeout(this.Timer);
		},

	check : function() {
		var that = jQuery.onFontResize;
		var h = jQuery(that.Box).height();
		if (h!=that.H) {
			that.H = h;
			jQuery(document).triggerHandler('fontresize');
			}
		if (that.on) this.Timer = setTimeout(that.check,that.Delay);
		}

	}

jQuery(function(){
	jQuery.onFontResize.watch();
	});