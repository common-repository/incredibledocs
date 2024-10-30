<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* Fired during plugin deactivation. */
/*---------------------------------------------------------------------------------------*/
class IDOCS_Deactivator {

	public static function deactivate() {
		
		if (! current_user_can('activate_plugins')) 
			return;
		
		// remove scheduled hook for db cleanup (cron job)
		IDOCS_Cron::wpcron_deactivation();
		// remove capabilities from the default wp_roles 
		self::remove_capabilities_from_wp_roles();
		// removes any plugin custom rewrite rules (permalinks) and then recreate rewrite rules. 
		flush_rewrite_rules();

	}
	/*---------------------------------------------------------------------------------------*/
	// https://learn.wordpress.org/tutorial/custom-post-types-and-capabilities/
	public static function remove_capabilities_from_wp_roles() {

		/*---------------------------------*/
		// Administrator
		$role = get_role( 'administrator');
		$capabilities = IDOCS_Admin_Settings::get_capabilities('KB Editor');

		foreach ( $capabilities as $cap) {
			$role->remove_cap ( $cap );
		};
		/*---------------------------------*/
		// Editor 
		$role = get_role( 'editor');
		$capabilities = IDOCS_Admin_Settings::get_capabilities('KB Editor');

		foreach ( $capabilities as $cap) {
			$role->remove_cap ( $cap );
		};
		/*---------------------------------*/	
		// Author
		$role = get_role( 'author');
		$capabilities = IDOCS_Admin_Settings::get_capabilities('KB Author');
		foreach ( $capabilities as $cap) {
			$role->remove_cap ( $cap );
		};	
	}
	/*---------------------------------------------------------------------------------------*/

}
/*---------------------------------------------------------------------------------------*/
