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
		$this->title = __( 'Site Options', 'whitemap' );
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
					'default' => get_stylesheet_directory_uri() . '/library/img/favicon.ico',
					'desc'    => __( '16x16 pixels, windows ICO format.', 'whitemap' ),
					'id'      => 'favicon',
					'type'    => 'file',
					'allow' => array( 'attachment' ),
				),
				array(
					'name'    => __( 'Site Icon (32x32)', 'whitemap' ),
					'default' => get_stylesheet_directory_uri() . '/library/img/favicon.png',
					'desc'    => __( '32x32 pixels, PNG format.', 'whitemap' ),
					'id'      => 'favicon_png',
					'type'    => 'file',
					'allow' => array( 'attachment' ),
				),
				array(
					'name'    => __( 'Apple Touch Icon', 'whitemap' ),
					// 'default' => get_stylesheet_directory_uri() . '/library/img/apple-touch-icon.png',
					'desc'    => __( '152x152 pixels, PNG format.', 'whitemap' ),
					'id'      => 'apple_touch_icon',
					'type'    => 'file',
					'allow' => array( 'attachment' ),
				),
				array(
					'name'    => __( 'Windows Tile Icon (270x270)', 'whitemap' ),
					// 'default' => get_stylesheet_directory_uri() . '/library/img/windows-tile-icon.png',
					'desc'    => __( '270x270 pixels, PNG format.', 'whitemap' ),
					'id'      => 'windows_tile_icon',
					'type'    => 'file',
					'allow' => array( 'attachment' ),
				),
				array(
					'name'    => __( 'Text Color', 'whitemap' ),
					'id'      => 'text_color',
					'type'    => 'colorpicker',
					'default' => '#333333',
				),
				array(
					'name'    => __( 'Link Color', 'whitemap' ),
					'id'      => 'link_color',
					'type'    => 'colorpicker',
					'default' => '#0074a2',
				),
				array(
					'name'    => __( 'Link Hover Color', 'whitemap' ),
					'id'      => 'link_hover_color',
					'type'    => 'colorpicker',
					'default' => '#2ea2cc',
				),
				array(
					'name'    => __( 'Header Background Color (top)', 'whitemap' ),
					'id'      => 'header_background_color_top',
					'type'    => 'colorpicker',
					'default' => '#ffffff',
				),
				array(
					'name'    => __( 'Header Background Color (bottom)', 'whitemap' ),
					'id'      => 'header_background_color_bottom',
					'type'    => 'colorpicker',
					'default' => '#eeeeee',
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
				array(
					'name' => __( 'Show Zoom Controls', 'whitemap' ),
					'id' => 'show_zoom_controls',
					'type' => 'checkbox'
				),
				array(
					'name' => __( 'Show Map Search', 'whitemap' ),
					'id' => 'show_map_search',
					'type' => 'checkbox'
				),
				array(
					'name' => __( 'Show Location Type Filter', 'whitemap' ),
					'id' => 'show_location_type_filter',
					'type' => 'checkbox'
				),

				// Variable amounts of map pins. Please do hook up to taxonomy!
				array(
					'id'          => 'map_pins',
					'type'        => 'group',
					'options'     => array(
						'group_title'   => __( 'Pin {#}', 'whitemap' ), // {#} gets replaced by row number
						'add_button'    => __( 'Add Another Pin', 'whitemap' ),
						'remove_button' => __( 'Remove Pin', 'whitemap' ),
						'sortable'      => true, // beta
					),
					// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
					'fields'      => array(
						array(
							'name'    => __( 'Image', 'whitemap' ),
							'id'      => 'map_pin_image',
							'type'    => 'file',
							'default' => get_stylesheet_directory_uri() . '/library/img/marker_blue.png',
							'allow' => array( 'attachment' ),
						),
					),
				),

				array(
					'id'          => 'map_layers',
					'type'        => 'group',
					'options'     => array(
						'group_title'   => __( 'Map Layer {#}', 'whitemap' ), // {#} gets replaced by row number
						'add_button'    => __( 'Add Another Map Layer', 'whitemap' ),
						'remove_button' => __( 'Remove Map Layer', 'whitemap' ),
						'sortable'      => true, // beta
					),
					// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
					'fields'      => array(
						array(
							'name' => __( 'Layer URL', 'whitemap' ),
							'id'   => 'layer_url',
							'type' => 'text',
						),
						array(
							'name' => __( 'Layer Opacity', 'whitemap' ),
							'desc' => __( '25% is almost transparent, 100% is fully opaque.', 'whitemap' ),
							'id'   => 'layer_opacity',
							'type'    => 'radio_inline',
							'options' => array(
								'0.25'  => __( '25%', 'whitemap' ),
								'0.50'  => __( '50%', 'whitemap' ),
								'0.75'  => __( '75%', 'whitemap' ),
								'1.00' => __( '100%', 'whitemap' ),
							),
						),
						array(
							'name' => __( 'Layer Attribution', 'whitemap' ),
							'id' => 'layer_attribution',
							'type' => 'textarea_code',
						),
					),
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
	return cmb_get_option( whitemap_Admin::key(), $key );
}

/***********************************************************
 Theme Options in Front-end
 **********************************************************/

function whitemap_theme_option_css() {

	$header_bg_top    = whitemap_get_option('header_background_color_top');
	$header_bg_bottom = whitemap_get_option('header_background_color_bottom');
	$text_color       = whitemap_get_option('text_color');
	$link_color       = whitemap_get_option('link_color');
	$link_hover_color = whitemap_get_option('link_hover_color');
	$site_logo        = whitemap_get_option('site_logo');

	// start the style block
	$output = '<style>' . "\n";

	if ( !empty($header_bg_top) && !empty($header_bg_bottom) ) {
		$output .= "\n" . '#header {' . "\n";
		$output .= 'background: ' . $header_bg_top . ';' . "\n";
		$output .= 'background: -moz-linear-gradient(top, ' . $header_bg_top . ' 0%, ' . $header_bg_bottom . ' 99%);' . "\n";
		$output .= 'background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, ' . $header_bg_top . '), color-stop(99%, ' . $header_bg_bottom . '));' . "\n";
		$output .= 'background: -webkit-linear-gradient(top, ' . $header_bg_top . ' 0%, ' . $header_bg_bottom . ' 99%);' . "\n";
		$output .= 'background: -o-linear-gradient(top, ' . $header_bg_top . ' 0%, ' . $header_bg_bottom . ' 99%);' . "\n";
		$output .= 'background: -ms-linear-gradient(top, ' . $header_bg_top . ' 0%, ' . $header_bg_bottom . ' 99%);' . "\n";
		$output .= 'background: linear-gradient(to bottom, ' . $header_bg_top . ' 0%, ' . $header_bg_bottom . ' 99%);' . "\n";
		$output .= 'filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="' . $header_bg_top . '", endColorstr="' . $header_bg_bottom . '", GradientType=0);' . "\n";
		$output .= '}' . "\n";
	}

	if ( !empty($site_logo) ) {
		$output .= "\n" . '#header .logo {' . "\n";
		$output .= 'background-image: url(' . $site_logo . ');' . "\n";
		$output .= '}' . "\n";
	}

	if ( !empty($text_color) ) {
		$output .= "\n" . 'body {' . "\n";
		$output .= 'color: ' . $text_color . ';' . "\n";
		$output .= '}' . "\n";
	}

	if ( !empty($link_color) && !empty($link_hover_color) ) {
		$output .= "\n" . 'a, a:link, a:visited {' . "\n";
		$output .= 'color: ' . $link_color . ';' . "\n";
		$output .= '}' . "\n";
		$output .= "\n" . 'a:hover, a:active {' . "\n";
		$output .= 'color: ' . $link_hover_color . ';' . "\n";
		$output .= '}' . "\n";
	}

	// end the style block
	$output .= '</style>' . "\n";

	// Echo all to the front end
	echo $output;

}
add_action('wp_head', 'whitemap_theme_option_css');


function whitemap_theme_option_js() {

	$current_theme = sanitize_title( wp_get_theme() );

	$default_map_location      = whitemap_get_option('default_map_location');
	$show_zoom_controls        = whitemap_get_option('show_zoom_controls');
	$show_map_search           = whitemap_get_option('show_map_search');
	$show_location_type_filter = whitemap_get_option('show_location_type_filter');
	$default_map_location      = whitemap_get_option('default_map_location');
	$map_pins                  = whitemap_get_option('map_pins');
	$map_layers                = whitemap_get_option('map_layers');

	// start the style block
	$output = '<script type="text/javascript">' . "\n";

	$output .= 'var WhiteMap = WhiteMap || {};';

	if ( !empty($default_map_location) ) {
		$output .= 'WhiteMap.wmap = L.map("wmap", { center: new L.LatLng(' . $default_map_location['latitude'] . ', ' . $default_map_location['longitude'] . '), zoom: 15 });' . "\n";
	}

	// add the map layers
	if ( !empty($map_layers) ) {
		
		$i = 0;

		foreach ($map_layers as $layer) {
			$output .= 'WhiteMap.wmap_layer_' . $i . ' = L.tileLayer("' . $layer['layer_url'] . '",' . "\n";
			$output .= '{' . "\n";
			if ( !empty($layer['layer_attribution']) ) {
				$output .= 'attribution: "' . addslashes($layer['layer_attribution']) . '",' . "\n";
			}
			$output .= 'opacity: ' . $layer['layer_opacity'];
			$output .= '}).addTo(WhiteMap.wmap);' . "\n";

			$i += 1;
		}
	}

	// register the icons
	if ( !empty($map_pins) ) {
		$i = 0;

		foreach ($map_pins as $pin) {
			$output .= 'WhiteMap.wmap_icon_' . $i . ' = L.Icon.extend({' . "\n";
			$output .= 'options: {' . "\n";
			$output .= 'iconUrl: "' . $pin['map_pin_image'] . '",' . "\n";
			$output .= 'iconSize:     [64, 64],' . "\n";
			$output .= 'iconAnchor:   [32, 64],' . "\n";
			$output .= 'popupAnchor:  [0, -64],' . "\n";
			if ( !empty($pin['shadow']) ) {
				$output .= 'shadowUrl: "' . $pin['shadow'] . '",' . "\n";
			}
			$output .= 'shadowSize:   [50, 64],' . "\n";
			$output .= 'shadowAnchor: [4, 62]' . "\n";
			$output .= '}' . "\n";
			$output .= '});' . "\n";

			$i += 1;
		}
	}

	// end the style block
	$output .= '</script>' . "\n";

	// Echo all to the front end
	echo $output;

}
add_action('wp_footer', 'whitemap_theme_option_js');