<?php

/**
 * The settings of the plugin.
 *
 * @link       www.lehelmatyus.com/
 * @since      1.0.0
 *
 * @package    Website_Compare
 * @subpackage Website_Compare/admin
 */

/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class Website_Compare_Admin_Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
    private $version;

    private $plgncmpr_general_options;
    private $plgncmpr_settings_manual_options;
    private $plgncmpr_settings_advanced_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * This function introduces the theme options into the 'Settings' menu and into a top-level
	 * 'Website Compare' menu.
	 */
	public function setup_plugin_options_menu() {
        add_submenu_page(
            'options-general.php',
			'Website Compare Settings', 					// The title to be displayed in the browser window for this page.
			'Website Compare',					        // The text to be displayed for this menu item
            'manage_options',					            // Which type of users can see this menu item
            'plugins_compare_options',			        // The unique ID - that is, the slug - for this menu item
			array( $this, 'render_settings_page_content')	// The name of the function to call when rendering this menu's page
		);
	}

	/**
	 * Provides default values for the Input Options.
	 *
	 * @return array
	 */
	public function default_general_options() {
		$defaults = array(
			'key'	=>	'',
			'url' => home_url(),
            'site_label' => '',
            'site_environment' => '',
            'site_color' => '#ccc',
		);
		return $defaults;
	}


	/**
	 * Provides default values for the Input Options.
	 *
	 * @return array
	 */
	public function default_manual_options() {
		$defaults = array(
			'site_1_plugins'	=>	'',
			'site_2_plugins'	=>	'',
			'site_3_plugins'	=>	'',
			'site_4_plugins'	=>	'',
		);
		return $defaults;
	}

	/**
	 * Renders a simple page to display for the theme menu defined above.
	 */
	public function render_settings_page_content( $active_tab = '' ) {

        $this->plgncmpr_general_options = get_option( 'plgncmpr_general_options' );
        $this->plgncmpr_settings_manual_options = get_option( 'plgncmpr_settings_manual_options' );
        $this->plgncmpr_settings_advanced_options = get_option( 'plgncmpr_settings_advanced_options' );

		?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

            <h2><?php esc_html_e( 'Website Compare Options', 'website-compare' ); ?></h2>

            <?php settings_errors(); ?>

            <?php if( isset( $_GET[ 'tab' ] ) ) {
                $active_tab = sanitize_text_field($_GET[ 'tab' ]);
			} else if( $active_tab == 'plgncmpr_settings_manual_options' ) {
                $active_tab = 'plgncmpr_settings_manual_options';
            } else {
				$active_tab = 'general_options';
			}

            ?>

            <h2 class="nav-tab-wrapper">
				<a href="?page=plugins_compare_options&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General Settings', 'website-compare' ); ?></a>
				<a href="?page=plugins_compare_options&tab=plgncmpr_settings_manual_options" class="nav-tab <?php echo $active_tab == 'plgncmpr_settings_manual_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Compare Plugins', 'website-compare' ); ?></a>
            </h2>

            <form method="post" action="options.php">
				<?php
				if ( $active_tab == 'plgncmpr_settings_manual_options' ) {

					$plugins = new Website_Compare_Plugin_Getter($this->plugin_name, $this->version);
					?>

						<div class="tg-outer">
							<table class="form-table" role="presentation">
								<tbody>
									<tr>
										<th scope="row"><?php esc_html_e( 'Plugins on this website', 'website-compare' ); ?></th>
										<td>
											<?php
												echo '<textarea id="plgns_cmpr_this" name="plgns_cmpr_this" rows="3" cols="77" readonly>';
												echo esc_textarea($plugins->__get_all_plugins_json());
												echo '</textarea>';
												echo '<p class="description">';
													echo 'Copy the plugins JSON of the above field into the other website. If you are comparing plugins there.';
												echo '</p>';
											?>
										</td>
									</tr>
								</tbody>
							</table>

							<?php

								settings_fields( 'plgncmpr_settings_manual_options' );
								do_settings_sections( 'plgncmpr_settings_manual_options' );
								submit_button();
								// $compressed = gzcompress($plugins->__get_all_plugins_json(), 9);
								echo wp_kses_post(
									$plugins->__get_compare_plugins_tables()
								);

							?>


						</div>

					<?php
                } else {
                    settings_fields( 'plgncmpr_general_options' );
                    do_settings_sections( 'plgncmpr_general_options' );
                    submit_button();
				}
                ?>
                </form>



                <br><hr /><br>
                <div class="plgncmpr__plugin-reviews">
                    <div class="plgncmpr__plugin-reviews-rate">
                        <?php echo __('If you enjoy our plugin, please give it 5 stars on WordPress it helps me a lot:','website-compare'); ?>
                        <a href="https://wordpress.org/support/plugin/website-compare/reviews/?filter=5" target="_blank" title="terms popup on user login review">Rate the plugin</a>
                    </div>
                    <div class="plgncmpr__plugin-reviews-support">
                        <?php echo __('If you have any questions on how to use the plugin, feel free to ask them:','website-compare'); ?>
                        <a href="https://www.lehelmatyus.com/question/question-category/website-compare" target="_blank" title="ask a question" >Support Questions</a>
                    </div>
                    <div class="plgncmpr__plugin-reviews-donate">

                        <?php echo __('Donations play an important role, please consider donating:','website-compare'); ?>
                        <span class="dashicons dashicons-carrot"></span>
                        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=EN83B8SEVVLX8&item_name=Help+Support+Terms+Popup+On+User+Login+Options&currency_code=USD&source=url" title="support the plugin" target="_blank" >Donate</a>
                    </div>
                </div>



            </div><!-- /.wrap -->
        <?php

    }

    /**---------------------------------------------------------------------
     * Settings fields for General Options
     ---------------------------------------------------------------------*/

	/**
	 * Initializes the theme's activated options
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function initialize_general_options(  ) {

        if( false == get_option( 'plgncmpr_general_options' ) ) {
			$default_array = $this->default_general_options();
			update_option( 'plgncmpr_general_options', $default_array );
        }

        /**
         * Add Section
         */
        add_settings_section(
            'plgncmpr_general_section',
            __( 'Settings', 'website-compare' ),
            array( $this, 'general_options_callback'),
            'plgncmpr_general_options'
        );

		// add_settings_field(
        //     'tplu_license_key',
        //     __( 'Enter License Key', 'website-compare' ),
        //     array( $this, 'general_options_callback'),
        //     'plgncmpr_general_options',
        //     'plgncmpr_general_section'
		// );

        add_settings_field(
            'site_label',
            __( 'Give this site a Label', 'website-compare' ),
            array( $this, 'site_label_callback'),
            'plgncmpr_general_options',
            'plgncmpr_general_section'
        );

        // add_settings_field(
        //     'site_color',
        //     __( 'Pick a color for this site', 'website-compare' ),
        //     array( $this, 'site_color_callback'),
        //     'plgncmpr_general_options',
        //     'plgncmpr_general_section'
        // );

        add_settings_field(
            'site_environment',
            __( 'Pick an Environment Descriptor', 'website-compare' ),
            array( $this, 'site_environment_callback'),
            'plgncmpr_general_options',
            'plgncmpr_general_section'
        );

        /**
         * Register Section
         */
        register_setting(
			'plgncmpr_general_options',
			'plgncmpr_general_options',
			array( $this, 'validate_general_options')
        );

    }

    /**
     * The Callback to assist with extra text
     */
    public function general_options_callback() {
		// echo '<p>' . esc_html__( '', 'website-compare' ) . '</p>';
    }
    /**
     * Validator Callback to assist in validation
     */
    public function validate_general_options( $input ) {

		// Create our array for storing the validated options
		$output = array();

		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
			} // end if
		} // end foreach

		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'validate_general_options', $output, $input );
	}

	function site_label_callback(  ) {
		$options = $this->plgncmpr_general_options;
		// var_dump()2
		if (empty($options['site_label'])){
            $options['site_label'] = '';
        }
        ?>
        <input type='text' class="regular-text" name='plgncmpr_general_options[site_label]' value='<?php echo esc_attr($options['site_label']); ?>'>
        <p class="description"> <?php echo __( 'Give a descriptive Label. This Label will be used when referencing this site in charts and tables.', 'website-compare' ); ?> </p>
        <?php
	}


    public function site_color_callback(){
		$options = $this->plgncmpr_general_options;

        $val = ( isset( $options['site_color'] ) ) ? $options['site_color'] : '#ccc';
		echo '<input type="text" name="plgncmpr_general_options[site_color]" value="' . esc_attr($val) . '" class="plgncmpr-color-picker" >';
		?>
		<p class="description"> <?php echo __( 'Makes things easier to identify at a glance', 'website-compare' ); ?> </p>
		<?php
    }

    public function site_environment_callback(){

        $options = $this->plgncmpr_general_options;
        if (empty($options['site_environment'])){
            $options['site_environment'] = 'default';
        }
        ?>

        <select name='plgncmpr_general_options[site_environment]'>
            <option value='' <?php selected( $options['site_environment'], '' ); ?>><?php _e('-','website-compare') ; ?></option>
            <option value='LOCAL' <?php selected( $options['site_environment'], 'LOCAL' ); ?>><?php _e('LOCAL','website-compare') ; ?></option>
            <option value='DEV' <?php selected( $options['site_environment'], 'DEV' ); ?>><?php _e('DEV','website-compare') ; ?></option>
            <option value='TEST' <?php selected( $options['site_environment'], 'TEST' ); ?>><?php _e('TEST','website-compare') ; ?></option>
			<option value='PROD' <?php selected( $options['site_environment'], 'PROD' ); ?>><?php _e('PROD','website-compare') ; ?></option>

        </select>
        <p class="description"><?php echo __( 'Setting an environment indicator can further help you to distinguish between similarly labeled sites', 'website-compare' ); ?> </p>

        <?php
    }

	/**--------------------------------------------------------------------
	 * Manual Compare options
	 *--------------------------------------------------------------------/

	/**
	 * Initializes the theme's activated options
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function initialize_manual_options(  ) {

        if( false == get_option( 'plgncmpr_settings_manual_options' ) ) {
			$default_array = $this->default_manual_options();
			update_option( 'plgncmpr_settings_manual_options', $default_array );
        }

        /**
         * Add Section
         */
        add_settings_section(
            'plgncmpr_manual_section',
            __( 'Compare to other plugin sets', 'website-compare' ),
            array( $this, 'manual_options_callback'),
            'plgncmpr_settings_manual_options'
        );

		add_settings_field(
            'site_1_plugins',
            __( 'Plugins from other website', 'website-compare' ),
            array( $this, 'site_1_plugins_render'),
            'plgncmpr_settings_manual_options',
            'plgncmpr_manual_section'
        );

        /**
         * Register Section
         */
        register_setting(
			'plgncmpr_settings_manual_options',
			'plgncmpr_settings_manual_options',
			array( $this, 'validate_manual_options')
        );

    }

    /**
     * The Callback to assist with extra text
     */
    public function manual_options_callback() {
		// echo '<p>' . esc_html__( '', 'website-compare' ) . '</p>';
    }
    /**
     * Validator Callback to assist in validation
     */
    public function validate_manual_options( $input ) {

		// Create our array for storing the validated options
		$output = array();

		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
			} // end if
		} // end foreach

		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'validate_manual_options', $output, $input );
	}

    function site_1_plugins_render( ){
        $options = $this->plgncmpr_settings_manual_options;
        printf(
			'<textarea class="" rows="3" cols="77" name="plgncmpr_settings_manual_options[site_1_plugins]" id="site_1_plugins">%s</textarea>',
			isset( $options['site_1_plugins'] ) ? esc_attr( $options['site_1_plugins']) : ''
        );
		echo '<p class="description">';
			_e('Copy the plugins JSON from the other website in this field if you are comparing plugins here.', 'website-compare');
		echo '</p>';

    }



}