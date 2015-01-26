// remove all checked props from all stars within the rating
var reset_rating = function (rating) {
	rating.find('.stars .star').removeClass('checked');
	set_rating_state( rating );
};

// set the correct classes for display of the rating
var set_rating_state = function (rating, current_star) {
	var stars   = rating.find('.star');
	var checked = rating.find('.star.checked').length > 0;
	var fieldvalue = 0;
	
	// First we remove all active states
	stars.removeClass('active');

	// Only run if one of the checkboxes is actually checked
	if ( checked ) {

		var pos = rating.find('.star.checked').last().index() + 1;

		for (var i=0; i < pos; i++) {
			rating.find('.star:eq(' + i + ')').addClass('active');	
		}

		fieldvalue = pos;
	}

	rating.find('input[name="rating"]').val(fieldvalue);
};


(function ($) {

	"use strict";

	// Run this for all available ratings
	$('.rating-execute').each( function(index){

		// The rating to work with
		var rating = $(this);
	
		// Reset all ratings to 'unchecked' if this link is clicked	
		$(this).find('.reset_rating').on('click', function(e) {
			e.preventDefault();
			reset_rating( rating );
		});

		// Set star classes on form change
		$(this).find('.stars .star').on('click', function(e) {
			$(this).siblings('.star').removeClass('checked');
			$(this).addClass('checked');
			set_rating_state( rating );
		});

		// Initially set the classes for this rating
		set_rating_state( rating );
	});

})(jQuery);


