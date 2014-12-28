/*
 name: Whitemap.LocationsLoader
 Author: Mark P. Lindhout
 Date: 2014-11-22T17:59:27+01:00
*/

var WhiteMap = WhiteMap || {};


/********************************************************
 Popup definition
*********************************************************/
WhiteMap.wmap_popup = L.Popup.extend({
	options: {
		// maxWidth: 256,
		minWidth: 320,
		maxHeight: 256,
		closeButton: true,
		offset: [128,128],
		autoPanPaddingTopLeft: new L.Point(32,0),
		autoPanPaddingBottomRight: new L.Point(0, 300),
		className: "wmap_popup"
	}
});


/********************************************************
 Center marker definition
*********************************************************/
WhiteMap.center_marker_icon = new L.divIcon({
	className: 'center_marker_icon',
});


/********************************************************
 Collect all markers into this array
*********************************************************/
WhiteMap.all_markers = [];


/********************************************************
Build map overlays and place markers
********************************************************/
WhiteMap.mm_load = function (obj, map) {

	posts = obj.posts;
	
	for (var j = 0; j < posts.length; j++) {

		var location = false;
		var popup_text = "";
		var popup = new WhiteMap.wmap_popup();
		var lat = false;
		var len = false;

		if ( posts[j].hasOwnProperty('title') ) {
			popup_text += '<h2 class="title">';
			popup_text += '<a href="' + posts[j].permalink + '">';
			popup_text += posts[j].title;
			popup_text += '</a>\n';
			popup_text += '</h2>\n';
		}

		if ( posts[j].hasOwnProperty('street') || posts[j].hasOwnProperty('postal') || posts[j].hasOwnProperty('city') ) {
			popup_text += '<div class="address">';

			if ( posts[j].hasOwnProperty('street') && posts[j].street ) {
				popup_text += posts[j].street + '<span class="divider"> </span>';
			}
			
			if ( posts[j].hasOwnProperty('postal') && posts[j].postal) {
				popup_text += posts[j].postal + '<span class="divider"> </span>';
			}

			if ( posts[j].hasOwnProperty('city') && posts[j].city ) {
				popup_text += posts[j].city;
			}

			popup_text += '</div>\n';
		}

		if ( posts[j].hasOwnProperty('description') ) {
			popup_text += '<div class="description">' + posts[j].description + '</div>\n';
		}

		if ( posts[j].hasOwnProperty('latitude') ) {
			lat = posts[j].latitude;
		}

		if ( posts[j].hasOwnProperty('longitude') ) {
			lng = posts[j].longitude;
		}
		
		if (lat || lng) {
			location = L.marker([lat, lng], { icon: new WhiteMap.wmap_icon_0() });
		}
		
		popup.setContent(popup_text);
		location.bindPopup(popup);
		location.addTo(map);

		WhiteMap.all_markers.push(location);
	}

};


/********************************************************
 Geolocation functions
*********************************************************/

WhiteMap.set_current_location = function(map) {
	// if (navigator.geolocation) {
	// 	navigator.geolocation.getCurrentPosition(function (pos) {
	// 			var cl = new L.LatLng(pos.coords.latitude, pos.coords.longitude);
	// 			WhiteMap.current_location = cl;
	// 			WhiteMap.set_map_to(cl, map);
	// 			WhiteMap.set_center_marker(cl, map);
	// 			return cl;
	// 		}, function(pos) {
	// 			throw new Error('Geolocation error.');
	// 		}
	// 	);
		
	// }
	// else {
	// 	throw new Error('Geolocation not supported by this browser.');
	// }

	map.locate();
};

WhiteMap.set_map_to = function (latlng, map) {
	if (map) {
		map.panTo(latlng);
	}
	else {
		throw new Error('No map specified');
	}
};



/********************************************************
 Set center marker
********************************************************/

WhiteMap.set_center_marker = function (latlng, map) {
	var location = L.marker( latlng, { icon: WhiteMap.center_marker_icon });
	if (map) {
		location.addTo(map).setZIndexOffset(1000);
	}
	else {
		throw new Error('No map specified');
	}
};


/********************************************************
 Reset all markers on map click
********************************************************/

WhiteMap.reset_all_markers = function () {
	for (var markeri = 0; markeri < WhiteMap.all_markers.length; markeri++) {
		var location = WhiteMap.all_markers[markeri];
		location.setIcon( new WhiteMap.wmap_icon_0() );
	}
};


/********************************************************
 Bind all markers event handlers
********************************************************/

WhiteMap.markers_event_handlers = function () {
	for (var markeri = 0; markeri < WhiteMap.all_markers.length; markeri++) {
		var location = WhiteMap.all_markers[markeri];

		location.addEventListener('click', function () {
			WhiteMap.reset_all_markers();
			this.setIcon( new WhiteMap.wmap_icon_1() );
		});

	}
};

/********************************************************
 Load it all on DOM-Ready
********************************************************/

$(document).ready(function() {
	if ( $('body').hasClass('home') ) {
		if ( $('#map-container').length !== 0 ) {
			var map    = WhiteMap.wmap;
			var json   = $.parseJSON(whitemap.locations);

			WhiteMap.set_current_location(map);
			WhiteMap.mm_load( json, map );

			WhiteMap.markers_event_handlers();
			WhiteMap.reset_all_markers();

			map.addEventListener('click', function () {
				WhiteMap.reset_all_markers(this);
			});
		}
	}
});