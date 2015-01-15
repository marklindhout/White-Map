<?php
/**
 * CMB Theme Options
 * @version 0.1.0
 */
class whitemap_Admin {

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	protected static $key = 'whitemap_options';

	/**
	 * Array of metaboxes/fields
	 * @var array
	 */
	protected static $theme_options = array();

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'Map Options', 'whitemap' );
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}

	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( self::$key, self::$key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page( $this->title, $this->title, 'manage_options', self::$key, array( $this, 'admin_page_display' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb_options_page <?php echo self::$key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb_metabox_form( self::option_fields(), self::$key ); ?>
		</div>
		<?php
	}

	/**
	 * Defines the theme option metabox and field configuration
	 * @since  0.1.0
	 * @return array
	 */
	public static function option_fields() {

		// Only need to initiate the array once per page-load
		if ( ! empty( self::$theme_options ) ) {
			return self::$theme_options;
		}

		self::$theme_options = array(
			'id'         => 'theme_options',
			'show_on'    => array( 'key' => 'options-page', 'value' => array( self::$key, ), ),
			'show_names' => true,
			'fields'     => array(

				// Site branding
				array(
					'name'    => __( 'Site Logo', 'whitemap' ),
					'desc'    => __( 'Will be resized to fit within 256 x 128 pixels.', 'whitemap' ),
					'default' => get_stylesheet_directory_uri() . '/library/img/default_logo.png',
					'id'      => 'site_logo',
					'type'    => 'file',
					'allow' => array( 'attachment' ),
				),
				array(
					'name'    => __( 'Favicon', 'whitemap' ),
					'default' => get_stylesheet_directory_uri() . '/library/img/default_favicon.ico',
					'desc'    => __( '16x16 pixels, windows ICO format.', 'whitemap' ),
					'id'      => 'favicon',
					'type'    => 'file',
					'allow' => array( 'attachment' ),
				),
				array(
					'name'    => __( 'Site Icon (32x32)', 'whitemap' ),
					'default' => get_stylesheet_directory_uri() . '/library/img/default_site_icon.png',
					'desc'    => __( '32x32 pixels, PNG format.', 'whitemap' ),
					'id'      => 'favicon_png',
					'type'    => 'file',
					'allow' => array( 'attachment' ),
				),
				array(
					'name'    => __( 'Apple Touch Icon', 'whitemap' ),
					'default' => get_stylesheet_directory_uri() . '/library/img/default_apple_touch_icon.png',
					'desc'    => __( '152x152 pixels, PNG format.', 'whitemap' ),
					'id'      => 'apple_touch_icon',
					'type'    => 'file',
					'allow' => array( 'attachment' ),
				),
				array(
					'name'    => __( 'Windows Tile Icon (270x270)', 'whitemap' ),
					'default' => get_stylesheet_directory_uri() . '/library/img/default_windows_tile_icon.png',
					'desc'    => __( '270x270 pixels, PNG format.', 'whitemap' ),
					'id'      => 'windows_tile_icon',
					'type'    => 'file',
					'allow' => array( 'attachment' ),
				),
				array(
					'name'    => __( 'Main Color', 'whitemap' ),
					'id'      => 'main_color',
					'type'    => 'colorpicker',
					'default' => '#fd0000',
				),

				// Map branding
				array(
					'name' => __( 'Map Options', 'whitemap' ),
					'type' => 'title',
					'id'   => 'map_branding'
				),
				array(
					'name'		=> 'Default Coordinates',
					'desc'		=> 'This is the centerpoint of the map if no location is found. Drag the marker to set the exact location',
					'id'		=> 'default_map_location',
					'type'		=> 'pw_map',
					'sanitization_cb' => 'pw_map_sanitise',
				),

			),
		);

		return self::$theme_options;
	}

	/**
	 * Make public the protected $key variable.
	 * @since  0.1.0
	 * @return string  Option key
	 */
	public static function key() {
		return self::$key;
	}

}

// Get it started
$whitemap_Admin = new whitemap_Admin();
$whitemap_Admin->hooks();


/**
 * Wrapper function around cmb_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function whitemap_get_option( $key = '' ) {
	$option_value = cmb_get_option( whitemap_Admin::key(), $key );
	return $option_value;
}



/***

	TO DO: SETTINGS API 

	Tuts:

	1. http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/

**/

/************************************************************
 ADMIN MENU
*************************************************************/

function whitemap_add_admin_menu() { 
	add_menu_page( 'White Map TEST SETTINGS', 'White Map TEST SETTINGS', 'manage_options', 'whitemap', 'whitemap_render_options_page' );
}
add_action( 'admin_menu', 'whitemap_add_admin_menu' );



/************************************************************
 INITIALIZE
*************************************************************/

function whitemap_settings_init() { 

	register_setting( 'whiteMap', 'whitemap_settings' );

	add_settings_section(
		'whitemap_settings_section_site_branding', 
		__( 'Site branding', 'whitemap' ), 
		'whitemap_render_section_site_branding', 
		'whiteMap'
	);

		add_settings_field( 
			'site_logo', 
			__( 'Site logo', 'whitemap' ), 
			'whitemap_render_field_site_logo', 
			'whiteMap', 
			'whitemap_settings_section_site_branding' 
		);

	// 	// array(
	// 	// 	'name'    => __( 'Favicon', 'whitemap' ),
	// 	// 	'default' => get_stylesheet_directory_uri() . '/library/img/default_favicon.ico',
	// 	// 	'desc'    => __( '16x16 pixels, windows ICO format.', 'whitemap' ),
	// 	// 	'id'      => 'favicon',
	// 	// 	'type'    => 'file',
	// 	// 	'allow' => array( 'attachment' ),
	// 	// ),

	// 	add_settings_field( 
	// 		'favicon', 
	// 		__( 'Favicon', 'whitemap' ), 
	// 		'whitemap_render_field_favicon', 
	// 		'whiteMap', 
	// 		'whitemap_settings_section_site_branding' 
	// 	);

	// 	// array(
	// 	// 	'name'    => __( 'Site Icon (32x32)', 'whitemap' ),
	// 	// 	'default' => get_stylesheet_directory_uri() . '/library/img/default_site_icon.png',
	// 	// 	'desc'    => __( '32x32 pixels, PNG format.', 'whitemap' ),
	// 	// 	'id'      => 'favicon_png',
	// 	// 	'type'    => 'file',
	// 	// 	'allow' => array( 'attachment' ),
	// 	// ),

	// 	add_settings_field( 
	// 		'favicon_png', 
	// 		__( 'Site Icon (32x32)', 'whitemap' ), 
	// 		'whitemap_render_field_favicon_png', 
	// 		'whiteMap', 
	// 		'whitemap_settings_section_site_branding' 
	// 	);

	// 	// array(
	// 	// 	'name'    => __( 'Apple Touch Icon', 'whitemap' ),
	// 	// 	'default' => get_stylesheet_directory_uri() . '/library/img/default_apple_touch_icon.png',
	// 	// 	'desc'    => __( '152x152 pixels, PNG format.', 'whitemap' ),
	// 	// 	'id'      => 'apple_touch_icon',
	// 	// 	'type'    => 'file',
	// 	// 	'allow' => array( 'attachment' ),
	// 	// ),

	// 	add_settings_field( 
	// 		'apple_touch_icon', 
	// 		__( 'Apple Touch Icon', 'whitemap' ), 
	// 		'whitemap_render_field_apple_touch_icon', 
	// 		'whiteMap', 
	// 		'whitemap_settings_section_site_branding' 
	// 	);

	// 	// array(
	// 	// 	'name'    => __( 'Windows Tile Icon (270x270)', 'whitemap' ),
	// 	// 	'default' => get_stylesheet_directory_uri() . '/library/img/default_windows_tile_icon.png',
	// 	// 	'desc'    => __( '270x270 pixels, PNG format.', 'whitemap' ),
	// 	// 	'id'      => 'windows_tile_icon',
	// 	// 	'type'    => 'file',
	// 	// 	'allow' => array( 'attachment' ),
	// 	// ),

	// 	add_settings_field( 
	// 		'windows_tile_icon', 
	// 		__( 'Windows Tile Icon (270x270)', 'whitemap' ), 
	// 		'whitemap_render_field_windows_tile_icon', 
	// 		'whiteMap', 
	// 		'whitemap_settings_section_site_branding' 
	// 	);

	// 	// array(
	// 	// 	'name'    => __( 'Main Color', 'whitemap' ),
	// 	// 	'id'      => 'main_color',
	// 	// 	'type'    => 'colorpicker',
	// 	// 	'default' => '#fd0000',
	// 	// ),

	// 	add_settings_field( 
	// 		'main_color', 
	// 		__( 'Main theme color', 'whitemap' ), 
	// 		'whitemap_render_field_main_color', 
	// 		'whiteMap', 
	// 		'whitemap_settings_section_site_branding' 
	// 	);



	// add_settings_section(
	// 	'whitemap_settings_section_map_options', 
	// 	__( 'Map Options', 'whitemap' ), 
	// 	'whitemap_render_section_map_options', 
	// 	'whiteMap'
	// );

	// 	// array(
	// 	// 	'name'		=> 'Default Coordinates',
	// 	// 	'desc'		=> 'This is the centerpoint of the map if no location is found. Drag the marker to set the exact location',
	// 	// 	'id'		=> 'default_map_location',
	// 	// 	'type'		=> 'pw_map',
	// 	// 	'sanitization_cb' => 'pw_map_sanitise',
	// 	// ),

	// 	add_settings_field( 
	// 		'default_map_location', 
	// 		__( 'Default Map Coordinates', 'whitemap' ), 
	// 		'whitemap_render_field_default_map_location', 
	// 		'whiteMap', 
	// 		'whitemap_settings_section_map_options' 
	// 	);

}
add_action( 'admin_init', 'whitemap_settings_init' );



/************************************************************
 FIELD RENDERING
*************************************************************/

function whitemap_render_field_site_logo() { 

	// Load scripts required for media library	
	wp_enqueue_media();

	// Get the option
	$options          = get_option('whitemap_settings');
	$site_logo        = ( !empty($options['site_logo']) && isset($options['site_logo']) ? $options['site_logo'] : false );
	$site_logo_src    = wp_get_attachment_image_src( $site_logo, 'medium' );
	$site_logo_url    = $site_logo_src[0];
	$default_logo_url = get_stylesheet_directory_uri() . '/library/img/default_logo.png';
	$show_logo        = false;
	$is_default_logo  = false;

		if ($site_logo !== 'nologo') {
			$show_logo = true; // Show the logo

			if ( !is_numeric($site_logo)) {
				$site_logo_url   = $default_logo_url;
				$is_default_logo = true;
			}
		}

	?>
		<p>
			<label>
				<input id="show_logo" type="checkbox" value="show_logo" <?php echo ($show_logo ? 'checked="checked"' : '' ); ?> />
				<?php _e('Show logo in header', 'whitemap'); ?>
			</label>
		</p>

		<input id="site_logo" type="hidden" name="whitemap_settings[site_logo]" value="<?php echo $site_logo; ?>" />
		<input id="site_logo_current" type="hidden" value="<?php echo $site_logo; ?>" />
		<input id="site_logo_default" type="hidden" value="<?php echo $default_logo_url; ?>" />

		<div id="site_logo_visibility_toggle" <?php echo (!$show_logo ? 'style="display: none;"': ''); ?>>
			<img id="site_logo_preview" src="<?php echo ($site_logo && $show_logo ? $site_logo_url : $default_logo_url); ?>" />
			<button id="upload_logo" class="button button"><?php _e('Pick another logo', 'whitemap'); ?></button>
		</div>

		<script type="text/javascript">
			var show_logo         = jQuery('#show_logo');
			var site_logo         = jQuery('#site_logo');
			var upload_logo       = jQuery('#upload_logo');
			var site_logo_current = jQuery('#site_logo_current');
			var site_logo_default = jQuery('#site_logo_default');
			var site_logo_vistog  = jQuery('#site_logo_visibility_toggle');

			jQuery(document).ready(function($) {

				show_logo.on('change', function(e){
					if( show_logo.is(':checked') ) {
						site_logo_vistog.show('fast');
						if ( site_logo_current.val() === 'nologo' ) {
							site_logo.val( site_logo_default.val() );
						}
						else {
							site_logo.val( site_logo_current.val() );
						}
					}
					else {
						site_logo_vistog.hide('fast');
						site_logo.val('nologo');
					}
				});

				upload_logo.on('click', function() {

					var send_attachment_bkp = wp.media.editor.send.attachment;
					
					wp.media.editor.send.attachment = function(props, attachment) {
						$('#site_logo_preview').attr('src', attachment.sizes.medium.url);
						console.log(attachment);
						// $('.custom_media_url').val(attachment.url);
						site_logo.val(attachment.id);
						wp.media.editor.send.attachment = send_attachment_bkp;
					}

					wp.media.editor.open();

					return false;       
				});

			});
		</script>

	<?php
}



/************************************************************
 SECTIONS
*************************************************************/

function whitemap_render_section_site_branding() { 
	echo __('Here you can add your own custom branding elements to fit the theme to your needs.', 'whitemap');
}

function whitemap_render_section_map_options() { 
	echo __('Here you can add your own custom branding elements to fit the map to your needs.', 'whitemap');
}



/************************************************************
 PAGE RENDERING
*************************************************************/

function whitemap_render_options_page() { 
	?>
		<form action="options.php" method="post" enctype="multipart/form-data">
			<h2>White Map</h2>
			<?php
				settings_fields( 'whiteMap' );
				do_settings_sections( 'whiteMap' );
				submit_button();
			?>
		</form>
	<?php
}







