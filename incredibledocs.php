<?php
/*
 * Plugin Name:       IncredibleDocs
 * Plugin URI:        https://incrediblewp.io/incredibledocs/
 * Description:       The easiest way to manage content for knowledge-bases and to provide incredible self-service support center for your customers. 
 * Version:           2.0.6
 * Requires at least: 6.4
 * Requires PHP:      7.4	
 * Author:            IncredibleWP
 * Author URI:        https://incrediblewp.io
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       incredibledocs
 * Domain Path:       /languages
 */
/*---------------------------------------------------------------------------------------*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/*---------------------------------------------------------------------------------------*/
// define a list of plugin constants 
define( 'IDOCS_VERSION', '2.0.6' );
define( 'IDOCS_DB_VERSION', '1.1.1');
define( 'IDOCS_PLUGIN_NAME', 'incredibledocs' );
define( 'IDOCS_SHORT_PLUGIN_NAME', 'idocs' );
define( 'IDOCS_MENU_SLUG', 'idocs-dashboard' ); 
/*--------------------------------------------*/
define('IDOCS_DIR_PATH', plugin_dir_path(__FILE__));
define('IDOCS_URL', plugin_dir_url(__FILE__));
define('IDOCS_PUBLIC_URL', IDOCS_URL . 'public/');
define('IDOCS_ADMIN_URL', IDOCS_URL . 'admin/');
define('IDOCS_FILE', __FILE__);
define('IDOCS_BASENAME', plugin_basename(__FILE__));
define('IDOCS_ADMIN_DIR_PATH', IDOCS_DIR_PATH . 'admin/');
define('IDOCS_PUBLIC_PATH', IDOCS_DIR_PATH . 'public/');
define('IDOCS_FSE_TEMPLATES_PATH', IDOCS_DIR_PATH . 'public/templates/fse' );
/*--------------------------------------------*/
/* The code that runs during plugin activation. */
function idocs_activate_incredibledocs() {
	
	require_once IDOCS_DIR_PATH . 'includes/class-idocs-activator.php';
	IDOCS_Activator::activate();

}
/*---------------------------------------------------------------------------------------*/
/* The code that runs during plugin deactivation. */
function idocs_deactivate_incredibledocs() {

	require_once IDOCS_DIR_PATH . 'includes/class-idocs-deactivator.php';
	IDOCS_Deactivator::deactivate();

}
/*---------------------------------------------------------------------------------------*/
/* Register callback functions for activation and deactivation hooks */
// runs before init & plugins_loaded actions.
register_activation_hook( __FILE__, 'idocs_activate_incredibledocs' );
// runs after init & plugins_loaded actions.
register_deactivation_hook( __FILE__, 'idocs_deactivate_incredibledocs' );
/*---------------------------------------------------------------------------------------*/
/* The core plugin class  */
if (! class_exists('IncredibleDocs')) {
    require_once IDOCS_DIR_PATH . 'includes/class-idocs-incredibledocs.php';
}
/*---------------------------------------------------------------------------------------*/
function idocs_run_incredibledocs() {

	list($plugin, $run) = IDOCS_IncredibleDocs::instance();
	if ($run) 
		$plugin->run();
}

idocs_run_incredibledocs();
/*---------------------------------------------------------------------------------------*/
// https://developer.wordpress.org/plugins/plugin-basics/header-requirements/

