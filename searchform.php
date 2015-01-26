<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
	<input type="search" class="search-field" placeholder="<?php _e( 'Search', 'whitemap' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php _e( 'Search', 'whitemap' ) ?>" />
	<button type="submit" class="button search-submit"><i class="fa fa-search"></i></button>
</form>
