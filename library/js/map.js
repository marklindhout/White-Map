/*
 name: Whitemap.LocationsLoader
 Author: Mark P. Lindhout
 Date: 2014-11-22T17:59:27+01:00
*/

var WhiteMap = WhiteMap || {};

/********************************************************
Build map overlays and place markers
********************************************************/
WhiteMap.mm_load = function (obj, map) {
	posts = obj.posts;
	for (var j = 0; j < posts.length; j++) {

		var location = false;
		var popup_text = "";
		var lat = false;
		var len = false;

		if ( posts[j].hasOwnProperty('title') ) {
			popup_text += '<h2>' + posts[j].title + '</h2>\n';
		}

		if ( posts[j].hasOwnProperty('description') ) {
			popup_text += '<p>' + posts[j].description + '</p>\n';
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
		
		location.bindPopup(popup_text);
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
		WhiteMap.mm_load( json, WhiteMap.wmap);
		WhiteMap.get_location();
	}
});