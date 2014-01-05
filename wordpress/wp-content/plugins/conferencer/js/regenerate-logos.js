jQuery(function($) {
	// regenerate logos
	
	var logos = {
		ids: typeof(conferencer_logo_regeneration_ids) != 'undefined' ? conferencer_logo_regeneration_ids : [],
		ndx: 0
	};
	
	$('#conferencer_regenerate_logos_progress').progressbar({
		change: function() { $(this).addClass('animated'); },
		complete: function() { $(this).removeClass('animated'); }
	}).hide();

	$('#conferencer_regenerate_logos_button').click(function() {
		$('#conferencer_regenerate_logos_progress').show();
		$('#conferencer_regenerate_logos_console').html('');
		logos.ndx = 0;
		regenerateNextLogo();
	});

	function regenerateNextLogo() {
		$('#conferencer_regenerate_logos_progress').progressbar('option', 'value', Math.max(1, 100 * logos.ndx / logos.ids.length));
		var id = logos.ids[logos.ndx++];
		if (id) {
			$.post(ajaxurl, { action: "conferencer_logo_regenerate", id: id }, function(response) {
				$('#conferencer_regenerate_logos_console').append("<li>" + (response.success ? response.success : response.error) + "</li>");
				regenerateNextLogo();
			});
		} else {
			$.get(ajaxurl, { action: 'conferencer_logo_regeneration_done' }, function(response) {
				$('#conferencer_logo_regeneration_needed').slideUp();
			});
		}
	}
});