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



function whitemap_add_admin_menu() { 
	add_menu_page( 'White Map TEST SETTINGS', 'White Map TEST SETTINGS', 'manage_options', 'whitemap', 'whitemap_render_options_page' );
}


function whitemap_settings_init() { 

	register_setting( 'whiteMap', 'whitemap_settings' );

	add_settings_section(
		'whitemap_settings_section_logo', 
		__( 'Custom branding elements', 'whitemap' ), 
		'whitemap_render_section_callback_logo', 
		'whiteMap'
	);

	add_settings_field( 
		'site_logo', 
		__( 'Site logo', 'whitemap' ), 
		'whitemap_render_field_site_logo', 
		'whiteMap', 
		'whitemap_settings_section_logo' 
	);

}


function whitemap_render_field_site_logo() { 
	$options = get_option( 'whitemap_settings' );
?>
	<input type="file" name="whitemap_settings[site_logo]" value="<?php echo $options['site_logo']; ?>">
<?php
}

function whitemap_render_section_callback_logo() { 
	echo __('Here you can add your own custom branding elements to make White Map look exaclty like you want it.', 'whitemap');
}

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
add_action( 'admin_menu', 'whitemap_add_admin_menu' );
add_action( 'admin_init', 'whitemap_settings_init' );







