<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.lehelmatyus.com
 * @since             1.0.0
 * @package           Website_Compare
 *
 * @wordpress-plugin
 * Plugin Name:       Website Compare
 * Plugin URI:        https://www.lehelmatyus.com/my-wordpress-plugins/website-compare
 * Description:       Allows you to easily compare plugins between two websites that have this plugin installed.
 * Version:           1.0.0
 * Author:            Lehel Mátyus
 * Author URI:        https://www.lehelmatyus.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       website-compare
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WBCMPR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-website-compare-activator.php
 */
function wbcmpr_activate_website_compare() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-website-compare-activator.php';
	Website_Compare_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-website-compare-deactivator.php
 */
function wbcmpr_deactivate_website_compare() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-website-compare-deactivator.php';
	Website_Compare_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wbcmpr_activate_website_compare' );
register_deactivation_hook( __FILE__, 'wbcmpr_deactivate_website_compare' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-website-compare.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wbcmpr_run_website_compare() {

	$plugin = new Website_Compare();
	$plugin->run();

}
wbcmpr_run_website_compare();
