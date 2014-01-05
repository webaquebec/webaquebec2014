jQuery(function($) {
	// Sponsors Slideshow
	$('.widget_conferencer_sponsors_widget .sponsors').fadeshow();
	
	$('.conferencer_tabs').each(function() {
		var options = {
			selected: 0
		};
		
		var tabs = $('.tabs li', this);
		var allContent = $([]);
		
		tabs.each(function() {
			var tab = $(this);
			var content = $($('a', this).attr('href'));
			allContent = $(allContent).add(content);
			
			$('a', tab).click(function() {
				tabs.removeClass('current');
				tab.addClass('current');
				
				allContent.hide();
				content.show();
				
				return false;
			});
		});
		
		$('a', tabs[options.selected]).click();
	});

	// Toggles session tooltip vs display details
	$('.conferencer_session_detail_toggle').show().click(function() {
		var $agenda = $(this).closest('.conferencer_agenda');
		var $tooltips = $('.session-tooltip', $agenda);
		var $details = $('.session-details', $agenda);

		$agenda.toggleClass('show_session_details');

		$tooltips
			.addClass('session-details')
			.removeClass('session-tooltip');

		$details
			.addClass('session-tooltip')
			.removeClass('session-details');

		return false;
	});

	// TODO: session tooltip helper, keep them in the viewport
});