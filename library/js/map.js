/*
 name: map.js
 Author: Mark P. Lindhout
 Date: 2014-11-22T17:59:27+01:00
*/

var WhiteMap = WhiteMap || {};

/********************************************************
 CENTER LOCATION
*********************************************************/
WhiteMap.center = new L.LatLng(WhiteMap.map_default_location.latitude, WhiteMap.map_default_location.longitude);


/********************************************************
 MAP
*********************************************************/
WhiteMap.map = L.map("wmap", {
	center: WhiteMap.center,
	zoom: 15
});


/********************************************************
 POPUP
*********************************************************/
WhiteMap.map_popup = L.Popup.extend({
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
 MAP LAYER
*********************************************************/
WhiteMap.map_layer = L.tileLayer(WhiteMap.map_layer.url, {
	attribution: WhiteMap.map_layer.attribution,
	opacity: WhiteMap.map_layer.opacity
})
.addTo(WhiteMap.map);


/********************************************************
 CENTER MARKER
*********************************************************/
WhiteMap.center_marker_icon = new L.divIcon({
	className: 'center_marker_icon',
});

/********************************************************
 NORMAL MARKER
*********************************************************/
WhiteMap.marker_normal = L.Icon.extend({
	options: {
		iconUrl:     WhiteMap.map_marker_normal.url,
		iconSize:    [WhiteMap.map_marker_normal.width, WhiteMap.map_marker_normal.height],
		iconAnchor:  [parseInt(WhiteMap.map_marker_normal.width/2, 10), WhiteMap.map_marker_normal.height],
	}
});

/********************************************************
 ACTIVE MARKER
*********************************************************/
WhiteMap.marker_active = L.Icon.extend({
	options: {
		iconUrl:     WhiteMap.map_marker_active.url,
		iconSize:    [WhiteMap.map_marker_active.width, WhiteMap.map_marker_active.height],
		iconAnchor:  [parseInt(WhiteMap.map_marker_active.width/2, 10), WhiteMap.map_marker_active.height],
	}
});


/********************************************************
 MARKERS ARRAY
*********************************************************/
WhiteMap.all_markers = [];


/********************************************************
Build map overlays and place markers
********************************************************/
WhiteMap.load_locations = function () {

	var posts = WhiteMap.locations.posts;
	
	for (var j = 0; j < posts.length; j++) {

		var location = false;
		var popup_text = "";
		var popup = new WhiteMap.map_popup();
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
			location = L.marker([lat, lng], { icon: new WhiteMap.marker_normal() });
		}
		
		popup.setContent(popup_text);
		location.bindPopup(popup);
		location.addTo(WhiteMap.map);

		WhiteMap.all_markers.push(location);
	}

};


/********************************************************
 GEOLOCATION
*********************************************************/

WhiteMap.get_geolocation = function() {
	WhiteMap.map.locate({setView: true, maxZoom: 16});
	WhiteMap.map.on('locationfound', WhiteMap.map_handle_locationfound);
	WhiteMap.map.on('locationerror', WhiteMap.map_handle_locationerror);
};

WhiteMap.map_handle_locationfound = function (e) {
	WhiteMap.set_geolocation_marker(e.latlng);
};

WhiteMap.map_handle_locationerror = function (e) {
	console.error('No geolocation found!');
};

WhiteMap.set_geolocation_marker = function (latlng) {
	var location = L.marker( latlng, { icon: WhiteMap.center_marker_icon });

	location.addTo(WhiteMap.map).setZIndexOffset(1000);
};

/********************************************************
 Reset all markers on map click
********************************************************/

WhiteMap.reset_all_markers = function () {
	for (var markeri = 0; markeri < WhiteMap.all_markers.length; markeri++) {
		var location = WhiteMap.all_markers[markeri];
		location.setIcon( new WhiteMap.marker_normal() );
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
			this.setIcon( new WhiteMap.marker_active() );
		});

	}
};

/********************************************************
 Load it all on DOM-Ready
********************************************************/

$(document).ready(function() {
	if ( $('body').hasClass('home') ) {
		if ( $('#map-container').length !== 0 ) {

			WhiteMap.get_geolocation();
			WhiteMap.load_locations();

			WhiteMap.markers_event_handlers();
			WhiteMap.reset_all_markers();

			WhiteMap.map.addEventListener('click', function () {
				WhiteMap.reset_all_markers();
			});
		}
	}
});