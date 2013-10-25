jQuery(function($) {
	// all for addition of options for multiple select
	
	$('#conference_options .add-another').click(function() {
		var list = $(this).prev();
		var item = $($('li:first', list).clone()).appendTo(list);
		$('select', item).val('');
		
		return false;
	});
	
	// date picker for post admin options
	
	$('#conference_options .date').each(function() {
		$(this).datepicker({
			defaultDate: $(this).attr('default'),
			showOtherMonths: true,
			onSelect: function(selectedDate) {
				var api = $(this).data('datepicker');
				var date = $.datepicker.parseDate(
					api.settings.dateFormat || $.datepicker._defaults.dateFormat,
					selectedDate,
					api.settings
				);
			
				if (this.id == 'conferencer_starts_date') {
					$('#conferencer_ends_date').datepicker('option', 'minDate', date);
				} else {
					$('#conferencer_starts_date').datepicker('option', 'maxDate', date);
				}
			}
		});
	});
});