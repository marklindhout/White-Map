$(document).ready(function() {

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