var conferencer;

jQuery(function($) {
	// alert user when they change logo sizes to regenerate
	
	var sponsor_slideshow_logo_size_changed = false;

	$('body').delegate('.conferencer_widget_logo_size', 'change', function() {
		sponsor_slideshow_logo_size_changed = true;
		$.getJSON(ajaxurl, { action: 'conferencer_logo_regeneration_needed' });
	});
	
	$('body').ajaxSuccess(function(e, request, options) {
		if (
			options.data.search('action=save-widget') != -1 &&
			options.data.search('widget-conferencer_sponsors_widget') != -1 &&
			sponsor_slideshow_logo_size_changed
		) {
			$('#conferencer_logo_regeneration_needed').slideDown();
		}
	});
	
	conferencer = {
		tinymce_button: function(opts) {
			var options = {
				slug: false,
				title: '',
				image: false,
				onclick: false
			};

			if (opts) $.extend(options, opts);
			if (!options.slug) return false;
			options.slug = 'conferencer_' + options.slug;

			tinymce.create('tinymce.plugins.' + options.slug, {
				init: function(editor, url) {
					editor.addButton(options.slug, {
						title: options.title,
						image: url + '/' + options.image,
						onclick: function() {
							options.onclick(editor, url);
						}
					});
				},
				getInfo: function() {
					return {
						longname: options.title,
						author: "Loud Dog",
						authorurl: 'http://conferencer.louddog.com',
						infourl: '',
						version: "1.0"
					};
				},
				createControl: function(n, cm) {
					return null;
				}
			});

			tinymce.PluginManager.add(options.slug, tinymce.plugins[options.slug]);
		}
	};
});