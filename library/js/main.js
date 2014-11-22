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
Overlays
********************************************************/
function mm_load(mm_markers, map) {

	for (var j = 0; j < mm_markers.length; j++) {

			var location = false;
			var popup_text = "";
			var lat = false;
			var len = false;
			var type = false;

			if ( mm_markers[j].hasOwnProperty('title') ) {
				popup_text += '<h2>' + mm_markers[j].title + '</h2>\n';
			}

			if ( mm_markers[j].hasOwnProperty('_mm_location_description') ) {
				popup_text += '<p>' + mm_markers[j]._mm_location_description + '</p>\n';
			}

			if ( mm_markers[j].hasOwnProperty('_mm_location_location_latitude') ) {
				lat = mm_markers[j]._mm_location_location_latitude;
			}

			if ( mm_markers[j].hasOwnProperty('_mm_location_location_longitude') ) {
				lng = mm_markers[j]._mm_location_location_longitude;
			}
			
			location.bindPopup(popup_text);
		}

}

$(document).ready(function() {

	if ( $('#map-container').length !== 0 ) {
		/********************************************************
		 Load map markers
		********************************************************/
		var json = $.parseJSON(whitemap.locations);
		mm_load( json, WhiteMap.wmap);
		console.log(json);

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
