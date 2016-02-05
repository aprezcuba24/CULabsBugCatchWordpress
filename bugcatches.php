<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://bugcatches.com/
 * @since             1.0.0
 * @package           Bugcatches
 *
 * @wordpress-plugin
 * Plugin Name:       Bug Catches
 * Plugin URI:        http://bugcatches.com/
 * Description:       This plugin let you report errors to bugcatches.com so you can get a notification to your email and track all error in a single place.
 * Version:           1.0.0
 * Author:            BugCatches
 * Author URI:        http://bugcatches.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bugcatches
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bugcatches-activator.php
 */
function activate_bugcatches() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bugcatches-activator.php';
	Bugcatches_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bugcatches-deactivator.php
 */
function deactivate_bugcatches() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bugcatches-deactivator.php';
	Bugcatches_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bugcatches' );
register_deactivation_hook( __FILE__, 'deactivate_bugcatches' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bugcatches.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bugcatches() {

	$plugin = new Bugcatches();
	$plugin->run();

}
run_bugcatches();
