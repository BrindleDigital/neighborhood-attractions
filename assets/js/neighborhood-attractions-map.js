window.initMap = function () {
	//...
};

jQuery(document).ready(function ($) {
	var map;
	var locationsArray = [];
	var markers = [];

	//* Vars from localization
	// grab the marker image from localization
	// var markerImage = options.marker_url;

	// grab the styles from localization and convert the php array to json
	var mapStyle = options.json_style;
	// mapStyle = JSON.stringify(mapStyle);

	// console.log(mapStyle);

	function renderMap() {
		var myLatlng = new google.maps.LatLng(
			39.8484006327939,
			-104.99522076837074
		);

		var mapOptions = {
			zoom: 8,
			minZoom: 5,
			maxZoom: 17,
			center: myLatlng,
			styles: mapStyle,
			disableDefaultUI: false, // removes the satellite/map selection (might also remove other stuff)
			// scaleControl: true,
			zoomControl: true,
			zoomControlOptions: {
				position: google.maps.ControlPosition.RIGHT_TOP,
			},
			fullscreenControl: true,
		};

		map = new google.maps.Map(
			document.getElementById('na-attractions-map'),
			mapOptions
		);
	}

	function getLocations() {
		// reset the array
		locationsArray = [];

		// get the positions
		// include both list items and hidden map-only items
		$('.na-attractions .type-attractions').each(function () {
			lat = $(this).attr('data-latitude');
			long = $(this).attr('data-longitude');
			title = $(this).find('h3').text();
			content = $(this).find('.map-markup').html();
			id = $(this).attr('data-id');
			markerImage = $(this).attr('data-marker');
			markerHeight = $(this).attr('data-marker-height');
			locationsArray.push([
				lat,
				long,
				title,
				content,
				id,
				markerImage,
				markerHeight,
			]);
		});
	}

	function addMarkers() {
		// clear existing markers (if any)
		if (markers.length) {
			for (var m = 0; m < markers.length; m++) {
				markers[m].setMap(null);
			}
		}
		markers = [];
		var bounds = new google.maps.LatLngBounds();

		for (var i = 0, len = locationsArray.length; i < len; i++) {
			var row = locationsArray[i];
			var latitude = row[0];
			var longitude = row[1];
			var content = row[3];
			var id = row[4];
			var markerImage = row[5];
			var markerHeight = row[6];
			var theposition = new google.maps.LatLng(latitude, longitude);

			var markerOpts = {
				position: theposition,
				map: map,
				title: row[2] || '',
			};
			// default height if not set
			var finalHeight = markerHeight ? parseInt(markerHeight, 10) : 40;

			if (markerImage) {
				// Set initial icon with default scaledSize to prevent native sizing
				markerOpts.icon = {
					url: markerImage,
					scaledSize: new google.maps.Size(finalHeight, finalHeight), // Assume square initially
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(
						Math.round(finalHeight / 2),
						finalHeight
					),
				};
			}
			var marker = new google.maps.Marker(markerOpts);
			bounds.extend(theposition);

			// If we have an image URL, load it to compute aspect ratio and then set a scaledSize icon
			if (markerImage) {
				var img = new Image();
				img.onload = (function (m, url, h) {
					return function () {
						var aspect =
							this.width && this.height
								? this.width / this.height
								: 1;
						var finalWidth = Math.max(1, Math.round(h * aspect));
						m.setIcon({
							url: url,
							scaledSize: new google.maps.Size(finalWidth, h),
							origin: new google.maps.Point(0, 0),
							anchor: new google.maps.Point(
								Math.round(finalWidth / 2),
								h
							),
						});
					};
				})(marker, markerImage, finalHeight);
				img.onerror = function () {
					// leave default icon (URL) if image fails to load
				};
				img.src = markerImage;
			}

			marker.infowindow = new google.maps.InfoWindow({
				content:
					'<div class="map-property-popup" id="overlay-' +
					id +
					'">' +
					content +
					'</div>',
				theMarkerID: id,
			});

			(function (marker) {
				google.maps.event.addListener(marker, 'click', function () {
					for (var j = 0; j < markers.length; j++) {
						markers[j].infowindow.close();
					}
					marker.infowindow.open(map, marker);
					var markerID = marker.infowindow.theMarkerID;
					$('.type-attractions').removeClass('active');
					$('.type-attractions[data-id=' + markerID + ']').addClass(
						'active'
					);
				});
			})(marker);

			markers.push(marker);
		}

		// fit once after loop (improves performance)
		if (!bounds.isEmpty()) {
			map.fitBounds(bounds);
		}
	}

	function resetMap() {
		renderMap();
		getLocations();
		addMarkers();
	}

	function openMarkerOnGridClick() {
		// console.log(markers);

		let markerID = $(this).attr('data-marker-id');

		// console.log(markerID);

		google.maps.event.trigger(markers[markerID], 'click');
	}

	function activeOnClick() {
		$('.type-attractions').removeClass('active');
		$(this).addClass('active');
	}

	$(document).on('click', '.type-attractions', openMarkerOnGridClick);
	$(document).on('click', '.type-attractions', activeOnClick);
	// initial load
	$(window).on('load', resetMap);
	// listen for custom update event when attractions list changes via AJAX filter
	$(document).on('naAttractionsUpdated', function () {
		resetMap();
	});
});
