var WhiteMap = WhiteMap || {};

/********************************************************
 Geolocation functions
*********************************************************/
WhiteMap.get_location = function() {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function (pos) {
			var ll = new L.LatLng(pos.coords.latitude, pos.coords.longitude);
			WhiteMap.set_map_to(ll, WhiteMap.wmap);
		}, function(pos) {
			console.log(pos);
			throw new Error('Geolocation error.');
		});
	}
	else {
		throw new Error('Geolocation is not supported by this browser.');
	}
};

WhiteMap.set_map_to = function (latlng, map) {
	if (map) {
		map.panTo(latlng);
	}
	else {
		throw new Error('No map was specified');
	}
};

/********************************************************
 Meat Map - Overlays
********************************************************/
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
						// icont = new WhiteMap.wmap_icon_normal({iconUrl: template_directory_uri + '/library/img/mm_marker_' + type + '.png'});

						if (lat || lng) {
								location = L.marker([lat, lng], {icon: WhiteMap.wmap_icon_normal});
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

	if ( $('#map-container').length !== 0 ) {
		/********************************************************
		 Load map markers
		********************************************************/
		var get_markers = $.get(template_directory_uri + '/library/json-markers.php', function() {
			mm_load(get_markers.responseJSON['posts'], WhiteMap.wmap);
		})
		.fail(function() {
			throw new Error('Markers could not be loaded.');
		});

		/********************************************************
		 Load map markers
		********************************************************/
		WhiteMap.get_location();

	}

	/********************************************************
	 Menu logic
	********************************************************/
	var $menulink  = $('#menutoggle .button');
	var $container = $('#container');
	var showclass  = 'menu_shown';

	$menulink.click( function(event) {
			event.preventDefault();
			if ( $container.hasClass(showclass) ) {
				$container
					.animate({ left: 0 }, 'slow')
					.removeClass(showclass);
			} else {
				$container
					.animate({ left: 250 }, 'slow')
					.addClass(showclass);
			}
		}
	);

});
