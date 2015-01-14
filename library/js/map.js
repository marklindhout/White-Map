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
// Only loaded if the container element is present
if ( document.getElementById('wmap') !== null ) {
	WhiteMap.map = L.map('wmap', {
		center: WhiteMap.center,
		zoom: 15
	});
}

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
});


/********************************************************
 CENTER MARKER
*********************************************************/
WhiteMap.center_marker_icon = new L.divIcon({
	iconSize: [16, 16],
	className: 'center_marker_icon',
	html: '<div class="pulsar"></div>' + '<div class="inner"></div>',
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
WhiteMap.add_markers = function () {

	var posts = WhiteMap.locations;
	
	for (var j = 0; j < posts.length; j++) {

		var location = false;
		var popup_text = '';
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

			var cut = false;

			if ( posts[j].hasOwnProperty('desc_is_cut') ) {
				cut = (posts[j].desc_is_cut ? ' ' + 'cut' : '');
			}

			popup_text += '<div class="description' + cut + '">' + posts[j].description + '</div>\n';
		}

		if ( posts[j].hasOwnProperty('tags') ) {
			popup_text += '<ul class="tags">';

			for (var tag = 0; tag < posts[j].tags.length; tag++) {
				popup_text += '<li class="tag">';
				popup_text += posts[j].tags[tag];
				popup_text += '</li>';
			}

			popup_text += '</ul>\n';
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

	WhiteMap.markers_event_handlers();
	WhiteMap.reset_all_markers();

	WhiteMap.map.addEventListener('click', function () {
		WhiteMap.reset_all_markers();
	});
};


/********************************************************
 GEOLOCATION
*********************************************************/

WhiteMap.get_geolocation = function() {
	WhiteMap.map.locate({setView: true});
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
	for (var i = 0; i < WhiteMap.all_markers.length; i++) {
		var location = WhiteMap.all_markers[i];
		location.setIcon( new WhiteMap.marker_normal() );
	}
};


/********************************************************
 Bind all markers event handlers
********************************************************/

WhiteMap.markers_event_handlers = function () {
	for (var i = 0; i < WhiteMap.all_markers.length; i++) {
		var location = WhiteMap.all_markers[i];

		location.addEventListener('click', function () {
			WhiteMap.reset_all_markers();
			this.setIcon( new WhiteMap.marker_active() );
		});

	}
};


/********************************************************
 INIT FUNCTION
********************************************************/

WhiteMap.setup_map = function () {
	WhiteMap.map_layer.addTo(WhiteMap.map);
};

/********************************************************
 INIT FUNCTION
********************************************************/

WhiteMap.init_home = function () {
	WhiteMap.setup_map();	
	WhiteMap.get_geolocation();
	WhiteMap.add_markers();
};

WhiteMap.init_single_location = function () {
	WhiteMap.setup_map();

	var marker = L.marker(WhiteMap.center, { icon: new WhiteMap.marker_normal() });
	marker.addTo(WhiteMap.map);
	WhiteMap.map.setView(WhiteMap.center);

	// disable dragging and zooming
	WhiteMap.map.dragging.disable();
	WhiteMap.map.doubleClickZoom.disable();
	WhiteMap.map.scrollWheelZoom.disable();
	WhiteMap.map.boxZoom.disable();
	WhiteMap.map.keyboard.disable();
	
	// remove zoom control
	WhiteMap.map.removeControl(WhiteMap.map.zoomControl);
};

/********************************************************
 Load it all on DOM-Ready
********************************************************/

$(document).ready(function() {
	if ( $('body').hasClass('home') ) {
		WhiteMap.init_home();
	} else if ( $('body').hasClass('single-location') ) {
		WhiteMap.init_single_location();
	}
});