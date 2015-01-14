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

/***********************************************************
 Theme Options in Front-end
 **********************************************************/

function whitemap_theme_option_css() {

	$main_color = whitemap_get_option('main_color');
	$site_logo  = wp_get_attachment_image_src( whitemap_get_option('site_logo_id'), 'whitemap-logo' );

	// start the style file
	$css = '';

	if ( !empty($site_logo) ) {
		$css .= "\n" . '#container #header .logo {' . "\n";
		$css .= 'background-image: url(' . $site_logo[0] . ');' . "\n";
		$css .= 'width: ' . $site_logo[1] . 'px;' . "\n";
		$css .= 'height: ' . $site_logo[2] . 'px;' . "\n";
		$css .= 'text-indent: ' . $site_logo[1] . 'px;' . "\n";
		$css .= 'line-height: ' . $site_logo[2] . 'px;' . "\n";
		$css .= '}' . "\n";
	}

	if ( !empty($main_color) ) {
		$css .= "\n";
		$css .= 'a {' . "color: " . $main_color . "}";
		$css .= "\n";
		$css .= '.single-title {' . "color: " . $main_color . "}";
		$css .= "\n";
		$css .= '.button {' . "background-color: " . $main_color . "}";
		$css .= "\n";
		$css .= '.brand {' . "background-color: " . $main_color . "}";
	}

	// end the style block
	$output = "\n" . '<style>' . "\n" . $css . "\n" . '</style>';

	// Echo all to the front end if there is something there
	if ( !empty($css) ) {
		echo $output;
	}

}
add_action('wp_enqueue_scripts', 'whitemap_theme_option_css');
