jQuery(document).ready(function ($) {
	// simple in-memory cache for filter responses keyed by category slug ('all' for no category)
	var attractionsCache = {};

	// helper to inject response HTML, re-apply limits, and notify map script
	function renderAttractions(html) {
		$('.na-attractions-wrap').html(html);
		applyAttractionsLimit();
		// trigger a custom event instead of relying on global ajaxComplete
		$(document).trigger('naAttractionsUpdated');
	}
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

	// filter on click (with caching)
	$('.attraction-type-button').on('click', function () {
		$('.attraction-type-button').removeClass('active');
		$(this).addClass('active');

		var slug = $(this).attr('data-slug') || 'all';

		// serve from cache if available
		if (attractionsCache.hasOwnProperty(slug)) {
			renderAttractions(attractionsCache[slug]);
			return;
		}

		$.ajax({
			type: 'POST',
			url: '/wp-admin/admin-ajax.php',
			dataType: 'html',
			data: {
				action: 'filter_attractions',
				category: slug === 'all' ? undefined : slug,
			},
			success: function (res) {
				// cache & render
				attractionsCache[slug] = res;
				renderAttractions(res);
			},
		});
	});

	// filter on load
	function filterAttractionsOnLoad() {
		var $first = $('.attraction-type-button').first();
		$first.addClass('active');
		var slug = $first.attr('data-slug') || 'all';

		// if cached (e.g., primed elsewhere) use it
		if (attractionsCache.hasOwnProperty(slug)) {
			renderAttractions(attractionsCache[slug]);
			return;
		}

		$.ajax({
			type: 'POST',
			url: '/wp-admin/admin-ajax.php',
			dataType: 'html',
			data: {
				action: 'filter_attractions',
			},
			success: function (res) {
				attractionsCache[slug] = res; // cache "all"
				renderAttractions(res);
			},
		});
	}

	$(window).on('load', filterAttractionsOnLoad);
});
