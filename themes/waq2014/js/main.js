$(window).load(function(){

	$('.days-buttons button').eq(0).addClass('active');

	$('.days-buttons button').on('click', function(){

		$('.days-buttons button').removeClass('active');
		$(this).addClass('active');

		var index = $(this).index(),
			width = $('.slide').eq(0).outerWidth(true);

		$('.schedule .slide').animate({
			left: index * width * -1,
		}, 300);

		$('.schedule .slide').eq(0).find('th').animate({
			left: index * width,
		}, 300);

	});

});