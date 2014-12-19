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
		// autoPanPaddingTopLeft: new L.Point(32,32),
		autoPanPaddingBottomRight: new L.Point(0, 300),
		className: "wmap_popup"
	}
});


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
	
	}
};



/********************************************************
 Geolocation functions
*********************************************************/

WhiteMap.get_location = function() {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function (pos) {
			var ll = new L.LatLng(pos.coords.latitude, pos.coords.longitude);
			WhiteMap.set_map_to(ll, WhiteMap.wmap);
		}, function(pos) {
			throw new Error('Geolocation error.');
		});
	}
	else {
		throw new Error('Geolocation not supported by this browser.');
	}
};

WhiteMap.set_map_to = function (latlng, map) {
	if (map) {
		map.panTo(latlng);
	}
	else {
		throw new Error('No map specified');
	}
};

$(document).ready(function() {
	if ( $('#map-container').length !== 0 ) {
		WhiteMap.get_location();
	}
});


/********************************************************
 Load it all on DOM-Ready
********************************************************/

$(document).ready(function() {
	if ( $('#map-container').length !== 0 ) {
		var json = $.parseJSON(whitemap.locations);
		WhiteMap.mm_load(json, WhiteMap.wmap);
		WhiteMap.get_location();
	}
});