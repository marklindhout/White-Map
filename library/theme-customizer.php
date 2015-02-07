<?php

function whitemap_theme_customize($wp_customize) {
	/*
		Customize Image Reloaded Class
		Extend WP_Customize_Image_Control allowing access to uploads made within the same context

		Taken from: https://gist.github.com/eduardozulian/4739075/
	*/

	class My_Customize_Image_Reloaded_Control extends WP_Customize_Image_Control {

		public function __construct( $manager, $id, $args = array() ) {
			parent::__construct( $manager, $id, $args );
		}

		public function tab_uploaded() {

			$my_context_uploads = get_posts( array(
					'post_type' => 'attachment',
					'meta_key' => '_wp_attachment_context',
					'meta_value' => $this->context,
					'orderby' => 'post_date',
					'nopaging' => true,
				)
			);
		
			echo '<div class="uploaded-target"></div>';

			if ( empty( $my_context_uploads ) ) {
				return;
			}
			
			foreach ( (array) $my_context_uploads as $my_context_upload ) {
				$this->print_tab_image( esc_url_raw( $my_context_upload->guid ) );
			}
		}
	}


	/*
		Map coordinate picker class
		Extend WP_Customize_Control to pick coordinates using OpenStreetMap

		Author: M.P. Lindhout
	*/
	class Map_Coordinate_Picker_Control extends WP_Customize_Control {
		
		// Styles and scripts
		public function enqueue() {

			// Leaflet JS
			wp_enqueue_script(
				'leaflet',
				get_stylesheet_directory_uri() . '/library/js/vendor/leaflet/leaflet.js',
				array('jquery'),
				false // in footer ?
			);

			// Leaflet CSS
			wp_enqueue_style(
				'leaflet',
				get_stylesheet_directory_uri() . '/library/css/leaflet.css',
				array(),
				'',
				'all'
			);
		}

		// Render in Theme Customizer
		public function render_content() {
			
			$map_id = $this->id . '_map';
		?>
				<label>
					<span class="map_coordinate_picker_label">
						<?php echo esc_html( $this->label ); ?>
					</span>

					<input type="text" id="<?php echo $this->id; ?>" name="<?php echo $this->id; ?>" value="<?php echo $this->value(); ?>" class="map_coordinate_picker" />
				</label>

				<br />

				<div id="<?php echo $map_id; ?>_toggle">
					<a class="toggle button"><?php _e('Select point on map', 'whitemap'); ?></a>
					<div class="map" id="<?php echo $map_id; ?>" style="width: 100%; height: 256px; display: none;"></div>
				</div>

				<script>
					jQuery(function($) {
						var map = L.map('<?php echo $map_id; ?>');
						var layer = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png');
						var coords = [<?php echo $this->value(); ?>];
						var icon = L.icon({
							iconUrl: '<?php echo get_stylesheet_directory_uri(); ?>/library/img/default_marker.png',
							iconRetinaUrl: '<?php echo get_stylesheet_directory_uri(); ?>/library/img/default_marker@2x.png',
							iconSize: [64, 64],
							iconAnchor: [32, 64],
						});
						var marker = L.marker(coords, {icon: icon}).addTo(map);

						var map_reset = function () {
							map.invalidateSize();
							map.setView(coords, 13);
						};

						var map_init = function () {
							layer.addTo(map);
							marker.addTo(map);
							map_reset();
						};

						map.on('click', function(e) {
							var lat = e.latlng.lat;
							var lng = e.latlng.lng;

							marker.setLatLng([lat, lng])

							wp.customize('<?php echo $this->id; ?>', function(obj) {
								var v = lat + ',' + lng;
								obj.set(v);
								coords = v;
								$('#<?php echo $this->id; ?>').val(v);
								wp.customize.trigger('change');
							});

							map_reset();

						});

						$('#<?php echo $map_id; ?>_toggle .toggle').on('click', function(e){
							$(this).siblings('.map').slideToggle('fast');
							map.invalidateSize();
						});

						map_init();

					});
				</script>
			<?php
		}
	}

	/**************************************************
	 SECTION: MAP SETTINGS
	**************************************************/

	$wp_customize->add_section( 'whitemap_map_settings', array(
			'title'    => __( 'Map settings', 'whitemap' ),
			'priority' => 35,
		)
	);

		/**************************************************
		 DEFAULT LATITUDE
		**************************************************/

		$wp_customize->add_setting( 'default_coordinates', array(
				'default'  => '52.51202,13.40891',
			)
		);

		$wp_customize->add_control( new Map_Coordinate_Picker_Control( $wp_customize, 'default_coordinates', array(
					'label'    => __('Default coordinates', 'whitemap'),
					'section'  => 'whitemap_map_settings',
					'settings' => 'default_coordinates',
				)
			)
		);


	/**************************************************
	 SECTION: SITE BRANDING
	**************************************************/

	$wp_customize->add_section( 'whitemap_site_branding', array(
			'title'    => __( 'Site branding', 'whitemap' ),
			'priority' => 35,
		)
	);

		/**************************************************
		 SITE COLOR
		**************************************************/

		$wp_customize->add_setting( 'site_color', array(
				'default'  => '#FF0000',
			)
		);

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_color', array(
					'label'    => __('Main color', 'whitemap'),
					'section'  => 'whitemap_site_branding',
					'settings' => 'site_color',
				)
			)
		);

		/**************************************************
		 LOGO
		**************************************************/

		$wp_customize->add_setting( 'site_logo', array(
				'capability' => 'edit_theme_options'
			)
		);

		$wp_customize->add_control( new My_Customize_Image_Reloaded_Control( $wp_customize, 'site_logo', array(
					'label'    => __('Logo', 'whitemap'),
					'section'  => 'whitemap_site_branding',
					'settings' => 'site_logo',
					'context'  => 'site_logo'
				)
			)
		);

		/**************************************************
		 FAVICON
		**************************************************/

		$wp_customize->add_setting( 'favicon', array(
				'capability' => 'edit_theme_options'
			)
		);

		$wp_customize->add_control( new WP_Customize_Upload_Control( $wp_customize, 'favicon', array(
					'label'    => __('Favicon', 'whitemap') . ' ' . '(.ico, 16x16)',
					'section'  => 'whitemap_site_branding',
					'settings' => 'favicon',
					'context'  => 'favicon'
				)
			)
		); 

		/**************************************************
		 SITE ICON PNG
		**************************************************/

		$wp_customize->add_setting( 'siteicon', array(
				'capability' => 'edit_theme_options'
			)
		);

		$wp_customize->add_control( new My_Customize_Image_Reloaded_Control( $wp_customize, 'siteicon', array(
					'label'    => __('Site Icon', 'whitemap') . ' ' . '(32x32)',
					'section'  => 'whitemap_site_branding',
					'settings' => 'siteicon',
					'context'  => 'siteicon'
				)
			)
		);

		/**************************************************
		 APPLE TOUCH ICON
		**************************************************/

		$wp_customize->add_setting( 'appletouchicon', array(
				'capability' => 'edit_theme_options'
			)
		);

		$wp_customize->add_control( new My_Customize_Image_Reloaded_Control( $wp_customize, 'appletouchicon', array(
					'label'    => __('Apple Touch Icon', 'whitemap') . ' ' . '(152x152)',
					'section'  => 'whitemap_site_branding',
					'settings' => 'appletouchicon',
					'context'  => 'appletouchicon'
				)
			)
		);

		/**************************************************
		 WINDOWS TILE ICON
		**************************************************/

		$wp_customize->add_setting( 'windowstileicon', array(
				'capability' => 'edit_theme_options'
			)
		);

		$wp_customize->add_control( new My_Customize_Image_Reloaded_Control( $wp_customize, 'windowstileicon', array(
					'label'    => __('Windows Tile Icon', 'whitemap') . ' ' . '(270x270)',
					'section'  => 'whitemap_site_branding',
					'settings' => 'windowstileicon',
					'context'  => 'windowstileicon'
				)
			)
		);


}
add_action('customize_register', 'whitemap_theme_customize');