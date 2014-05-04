var start_coordinates = new L.LatLng(52.501045, 13.442569);

var map = L.map('map', {
    center: start_coordinates,
    zoom: 15,
});

// var tiles = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
// 	attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
// }).addTo(map);

var tiles = L.tileLayer('https://{s}.tiles.mapbox.com/v3/marklindhout.hpk7ih6p/{z}/{x}/{y}.png', {
    attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
}).addTo(map);

var MMIcon = L.Icon.extend({
    options: {
		// shadowUrl: 'leaf-shadow.png',
		iconSize:     [56, 79],
		// shadowSize:   [50, 64],
		iconAnchor:   [28, 79],
		// shadowAnchor: [4, 62],
		popupAnchor:  [0, -79]
    }
});

var locations_burger   = [];
var locations_barbecue = [];

function mm_load(mm_markers, map) {

	for (var j = 0; j < mm_markers.length; j++) {

		var location = false;
		var popup_text = "";
		var lat = false;
		var len = false;
		var type = false;

		if ( mm_markers[j].hasOwnProperty('title') ) {
			popup_text += '<img src="' + mm_markers[j]['_mm_location_logo'] + '" />\n';
		}

		if ( mm_markers[j].hasOwnProperty('title') ) {
			popup_text += '<h2>' + mm_markers[j]['title'] + '</h2>\n';
		}

		if ( mm_markers[j].hasOwnProperty('_mm_location_description') ) {
			popup_text += '<p>' + mm_markers[j]['_mm_location_description'] + '</p>\n';
		}

		if ( mm_markers[j].hasOwnProperty('_mm_location_telephone_number') ) {
			popup_text += '<p><strong>' + mm_markers[j]['_mm_location_telephone_number'] + '</strong></p>\n';
		}

		if ( mm_markers[j].hasOwnProperty('_mm_location_email') ) {
			popup_text += '<p>' + mm_markers[j]['_mm_location_email'] + '</p>\n';
		}

		if ( mm_markers[j].hasOwnProperty('_mm_location_website_url') ) {
			popup_text += '<p><a href="' + mm_markers[j]['_mm_location_website_url'] + '" rel="nofollow" target="_blank">' + mm_markers[j]['_mm_location_website_url'] + '</a></p>\n';
		}

		if ( mm_markers[j].hasOwnProperty('_mm_location_location_latitude') ) {
			lat = mm_markers[j]['_mm_location_location_latitude'];
		}

		if ( mm_markers[j].hasOwnProperty('_mm_location_location_longitude') ) {
			lng = mm_markers[j]['_mm_location_location_longitude'];
		}

		if ( mm_markers[j].hasOwnProperty('_mm_location_location_type') ) {
			type = mm_markers[j]['_mm_location_location_type'];
			icont = new MMIcon({iconUrl: template_directory_uri + '/library/img/mm_marker_' + type + '.png'});

			if (lat || lng) {
				location = L.marker([lat, lng], {icon: icont});
			} else {
				console.log('(“' + mm_markers[j]['title'] + '”, ' + mm_markers[j]['id'] + ') Provided coordinates are not numbers.');
			}

			if (type === 'burger' ) {
				locations_burger.push(location);
			}
			else if (type === 'barbecue') {
				locations_barbecue.push(location);
			}
		}
		
		location.bindPopup(popup_text);
	}

	locations_burger   = L.layerGroup(locations_burger);
	locations_barbecue = L.layerGroup(locations_barbecue);

	locations_burger.addTo(map);
	locations_barbecue.addTo(map);

	L.control.layers(
		null,
		{
			"BBQ": locations_barbecue,
			"Burgers": locations_burger
		},
		{
			collapsed: false
		})
	.addTo(map);
}

$(document).ready(function() {
	var get_markers = $.get(template_directory_uri + '/library/mm_markers.php', function() {
		mm_load(get_markers.responseJSON['posts'], map);
	})
	.fail(function() {
		throw new Error('Markers could not be loaded.');
	});
});