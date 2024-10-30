<?php
/* Fired when the plugin is uninstalled. */
/*---------------------------------------------------------------------------------------*/
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
/*---------------------------------------------------------------------------------------*/
// get the user configured settings about uninstallation 
function idocs_get_plugin_settings_for_uninstall() {

	// get the plugin saved options array from the options table - will return false if options array does not exist 
	$settings = get_option( 'incredibledocs' . '_saved_options');
				
	// if options does not exist, use default settings; 
	if (! $settings) {

		$settings = array (
	
			'idocs_remove_configuration_after_uninstall' => 0,
			'idocs_remove_analytics_after_uninstall' => 0,
		);
	}

	if ( $settings AND ! isset($settings['idocs_remove_configuration_after_uninstall']) ) {
		$settings['idocs_remove_configuration_after_uninstall'] = 0;
	}

	if ( $settings AND ! isset($settings['idocs_remove_analytics_after_uninstall']) ) {
		$settings['idocs_remove_analytics_after_uninstall'] = 0;
	}

	return $settings;

}
/*---------------------------------------------------------------------------------------*/
// delete setting options, design options and analytics data  
$settings = idocs_get_plugin_settings_for_uninstall();
$idocs_remove_configuration_after_uninstall = $settings['idocs_remove_configuration_after_uninstall'];
$idocs_remove_analytics_after_uninstall = $settings['idocs_remove_analytics_after_uninstall'];
/*---------------------------------------*/
// delete plugin configuration
if ($idocs_remove_configuration_after_uninstall) {

	// delete setting options, design options
	$options = array (
			
		'incredibledocs' . '_saved_options',
		'idocs' . '_design_options',
		
	);

	foreach($options as $option_name) {
		delete_option( $option_name );
	};
}
/*---------------------------------------*/
// delete collected analytics data 
if ($idocs_remove_analytics_after_uninstall) {

	// remove analytics data
	global $wpdb;
	
	$analytic_tables = array (

		$wpdb->prefix . 'idocs' . '_visited',
		$wpdb->prefix . 'idocs' . '_search_logs',
		$wpdb->prefix . 'idocs' . '_ratings',
	//	$wpdb->prefix . 'idocs' . '_feedback',

	);
	
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}idocs_visited");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}idocs_search_logs");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}idocs_ratings");

	/*
	foreach($analytic_tables as $table_name) {

	//	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
		$wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS {$wpdb->prefix}"));

	};
	*/
}
/*---------------------------------------*/
