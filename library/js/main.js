var start_coordinates = new L.LatLng(52.501045, 13.442569);

var map = L.map('map', {
    center: start_coordinates,
    zoom: 15,
});

var locations_burger = [];
var locations_bbq    = [];

function mm_load () {

	for (var j = 0; j < mm_markers.length; j++) {

		var location = false;
		var popup_text = "";
		var lat = false;
		var len = false;
		var type = false;

		if ( mm_markers[j].hasOwnProperty('name') ) {
			popup_text += '<h2>' + mm_markers[j]['name'] + '</h2>\n';
		}

		if ( mm_markers[j].hasOwnProperty('comment') ) {
			popup_text += '<p>' + mm_markers[j]['comment'] + '</p>\n';
		}

		if ( mm_markers[j].hasOwnProperty('phone') ) {
			popup_text += '<p><strong>' + mm_markers[j]['phone'] + '</strong></p>\n';
		}

		if ( mm_markers[j].hasOwnProperty('email') ) {
			popup_text += '<p>' + mm_markers[j]['email'] + '</p>\n';
		}

		if ( mm_markers[j].hasOwnProperty('url') ) {
			popup_text += '<p><a href="' + mm_markers[j]['url'] + '" rel="nofollow" target="_blank">' + mm_markers[j]['url'] + '</a></p>\n';
		}

		if ( mm_markers[j].hasOwnProperty('lat') ) {
			lat = mm_markers[j]['lat'];
		}

		if ( mm_markers[j].hasOwnProperty('lng') ) {
			lng = mm_markers[j]['lng'];
		}

		if ( mm_markers[j].hasOwnProperty('type') ) {
			type = mm_markers[j]['type'];
			icont = new MMIcon({iconUrl: template_directory_uri + '/library/img/mm_marker_' + type + '.png'});

			location = L.marker([lat, lng], {icon: icont});

			if (type === 'burger' ) {
				locations_burger.push(location);
			}
			else if (type === 'bbq') {
				locations_bbq.push(location);
			}
		}
		
		location.bindPopup(popup_text);
	}

	locations_burger = L.layerGroup(locations_burger);
	locations_bbq    = L.layerGroup(locations_bbq);

}

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

$(document).ready(function() {

	mm_load();

	locations_burger.addTo(map);
	locations_bbq.addTo(map);
	L.control.layers(
		null,
		{
			"BBQ": locations_bbq,
			"Burgers": locations_burger
		},
		{
			collapsed: false
		})
	.addTo(map);

});