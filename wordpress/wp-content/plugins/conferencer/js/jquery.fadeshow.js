(function($) {
	$.fn.fadeshow = function(opts) {
		var options = {
			interval: 5000,
			fadespeed: 1000
		};
		
		if (opts) $.extend(options, opts);
		
		return this.each(function() {
			var container = $(this);
			var slides = $(container).children();

			var height = 0;
			$.each(slides, function() {
				height = Math.max(height, $(this).height());
			});
			
			$.each(slides, function() {
				$(this).css({
					'margin-top': Math.floor((height - $(this).height())/2) + 'px'
				});
			})
			
			container.height(height);
			slides.first().css({ opacity: 1 }).addClass('active');
			if (slides.length <= 1) return;
			
			setInterval(function() {
				active = $('.active', container);
				next = active.next().length ? active.next() : slides.first();
				
				$(active).animate({ opacity: 0 }, options.fadespeed, function() {
					$(this).removeClass('active');
				});
				
				$(next).animate({ opacity: 1 }, options.fadespeed, function() {
					$(this).addClass('active');
				});
			}, options.interval);
		});
	};
})(jQuery);