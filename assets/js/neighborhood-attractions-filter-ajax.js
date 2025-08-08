jQuery(document).ready(function ($) {
	// helper to apply initial limit & load more button
	function applyAttractionsLimit() {
		// localized via options.max_initial_attractions (comes from map script localization)
		var maxInitial =
			window.options &&
			parseInt(window.options.max_initial_attractions, 10) > 0
				? parseInt(window.options.max_initial_attractions, 10)
				: 0;

		// remove any prior button
		$('.na-attractions-load-more').remove();

		var $items = $('.na-attractions-wrap > .type-attractions');
		if (!maxInitial || $items.length <= maxInitial) {
			// show all if no limit
			$items.show();
			$('.na-attractions-wrap').removeClass('initial-hide');
			return;
		}

		// hide over-limit items
		$items.each(function (idx) {
			if (idx >= maxInitial) {
				$(this).hide().addClass('na-hidden-by-limit');
			} else {
				$(this).show();
			}
		});

		// inject load more button after grid
		if ($('.na-attractions-load-more').length === 0) {
			$(
				'<div class="na-attractions-load-more-wrap"><button type="button" class="na-attractions-load-more">Load more</button></div>'
			).insertAfter('.na-attractions');
		}

		// reveal the initial subset
		$('.na-attractions-wrap').removeClass('initial-hide');
	}

	// click handler for load more
	$(document).on('click', '.na-attractions-load-more', function () {
		$('.na-attractions-wrap > .na-hidden-by-limit')
			.slideDown(200)
			.removeClass('na-hidden-by-limit');
		$(this).closest('.na-attractions-load-more-wrap').remove();
	});

	// filter on click
	$('.attraction-type-button').on('click', function () {
		$('.attraction-type-button').removeClass('active');
		$(this).addClass('active');

		$.ajax({
			type: 'POST',
			url: '/wp-admin/admin-ajax.php',
			dataType: 'html',
			data: {
				action: 'filter_attractions',
				category: $(this).attr('data-slug'),
			},
			success: function (res) {
				$('.na-attractions-wrap').html(res);
				applyAttractionsLimit();
			},
		});
	});

	// filter on load
	function filterAttractionsOnLoad() {
		$('.attraction-type-button').first().addClass('active');

		$.ajax({
			type: 'POST',
			url: '/wp-admin/admin-ajax.php',
			dataType: 'html',
			data: {
				action: 'filter_attractions',
			},
			success: function (res) {
				$('.na-attractions-wrap').html(res);
				applyAttractionsLimit();
			},
		});
	}

	$(window).on('load', filterAttractionsOnLoad);
});
