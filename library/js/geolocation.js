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