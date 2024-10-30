<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* Fired during plugin activation. */
/*---------------------------------------------------------------------------------------*/
class IDOCS_Activator {

	/*---------------------------------------------------------------------------------------*/
	public static function activate() {
	
		// please note  - the activation function registred to an activation hook is not called when a plugin is updated. 
		// We will handle a plugin update scenario by hooking to the "plugins_loaded" hook. 

		if ( ! current_user_can( 'activate_plugins' ) )
            return;

		// Capabilities that are added for roles are stored in the database.
		// Therefore, it is more optimized to add capabilities for default wp roles only one-time when the plugin is activated and not attached to a hook (e.g. admin_init).
		self::add_capabilities_to_wp_roles();
		
	}
	/*---------------------------------------------------------------------------------------*/
	public static function add_capabilities_to_wp_roles() {

		/*---------------------------------*/
		// Administrator
		$role = get_role( 'administrator');
		$capabilities = IDOCS_Admin_Settings::get_capabilities('KB Editor');

		foreach ( $capabilities as $cap) {
			$role->add_cap ( $cap );
		};
		/*---------------------------------*/
		// Editor 
		$role = get_role( 'editor');
		$capabilities = IDOCS_Admin_Settings::get_capabilities('KB Editor');

		foreach ( $capabilities as $cap) {
			$role->add_cap ( $cap );
		};
		/*---------------------------------*/	
		// Author
		$role = get_role( 'author');
		$capabilities = IDOCS_Admin_Settings::get_capabilities('KB Author');
		foreach ( $capabilities as $cap) {
			$role->add_cap ( $cap );
		};	
	}
}
/*---------------------------------------------------------------------------------------*/
// https://kinsta.com/blog/wordpress-user-roles/
// https://learn.wordpress.org/tutorial/custom-post-types-and-capabilities/
// https://learn.wordpress.org/tutorial/developing-with-user-roles-and-capabilities/
// https://developer.wordpress.org/plugins/users/roles-and-capabilities/#roles
// https://wordpress.org/documentation/article/roles-and-capabilities/#capability-vs-role-table
// https://www.unserialize.com/
