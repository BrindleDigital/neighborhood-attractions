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

    console.log(mapStyle);

    function renderMap() {
        var myLatlng = new google.maps.LatLng(
            39.8484006327939,
            -104.99522076837074
        );

        var mapOptions = {
            zoom: 8,
            minZoom: 5,
            maxZoom: 14,
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
        $('.na-attractions .type-attractions').each(function () {
            lat = $(this).attr('data-latitude');
            long = $(this).attr('data-longitude');
            title = $(this).find('h3').text();
            content = $(this).find('.map-markup').html();
            id = $(this).attr('data-id');
            locationsArray.push([lat, long, title, content, id]);
        });
    }

    function addMarkers() {
        markers = [];
        var bounds = new google.maps.LatLngBounds();

        for (let i = 0; i < locationsArray.length; i++) {
            var latitude = locationsArray[i][0];
            var longitude = locationsArray[i][1];
            var content = locationsArray[i][3];
            var id = locationsArray[i][4];
            var label = title;
            var theposition = new google.maps.LatLng(latitude, longitude);

            if (typeof markerImage !== 'undefined') {
                // if there's a custom marker set, use that
                var marker = new google.maps.Marker({
                    position: theposition,
                    map: map,
                    title: title,
                    icon: markerImage,
                });
            } else {
                // if there's no custom icon, just use the google default
                var marker = new google.maps.Marker({
                    position: theposition,
                    map: map,
                    title: title,
                });
            }

            bounds.extend(theposition);
            map.fitBounds(bounds);

            marker['infowindow'] = new google.maps.InfoWindow({
                content:
                    '<div class="map-property-popup" id="overlay-' +
                    id +
                    '">' +
                    content +
                    '</div>',
            });

            google.maps.event.addListener(marker, 'click', function () {
                for (let i = 0; i < markers.length; i++) {
                    markers[i]['infowindow'].close(map, this);
                }

                this['infowindow'].open(map, this);
            });

            markers.push(marker);
        }
    }

    function resetMap() {
        renderMap();
        getLocations();
        addMarkers();
    }

    function openMarkerOnClick() {
        console.log(markers);

        let markerID = $(this).attr('data-marker-id');

        console.log(markerID);

        google.maps.event.trigger(markers[markerID], 'click');
    }

    $(document).on('click mouseover', '.type-attractions', openMarkerOnClick);
    $(document).on('ajaxComplete load', resetMap);
});
