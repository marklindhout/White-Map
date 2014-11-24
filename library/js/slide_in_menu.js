/*
 name: Slide-In-Menu
 Author: Mark P. Lindhout
 Date: 2014-11-22T17:59:27+01:00
*/

$(document).ready( function() {

	var $menulink  = $('#menutoggle .button');
	var $container = $('#container');
	var showclass  = 'menu_shown';

	$menulink.click( function(e) {
			e.preventDefault();
			if ( $container.hasClass(showclass) ) {
				$container
					.removeClass(showclass);
			} else {
				$container
					.addClass(showclass);
			}
		}
	);

});