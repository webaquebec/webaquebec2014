jQuery(function($) {
	
	// all for post sorting on reordering admin page
	
	$('#conferencer_reordering .items').sortable({
		axis: 'y',
		tolerance: 'pointer',
		containment: 'parent',
		opacity: 0.8
	});
});