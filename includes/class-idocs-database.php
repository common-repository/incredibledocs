<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/*
 > define default values for admin settings 
 > get\update the plugin setting/s or design from the database or/and defualt values 
 > create or update plugin tables when a new db version is released
*/
/*---------------------------------------------------------------------------------------*/
class IDOCS_Database {

	/*---------------------------------------------------------------------------------------*/
	// Initialize the class and set its properties.
	public function __construct() {

	}
	/*---------------------------------------------------------------------------------------*/
	public static function clear_all_kbs_design_transients() {

		// get the list of knowledge-bases 
        $kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
		 /*------------------------------------------------*/
		foreach ($kb_terms as $kb_term) {

			$kb_id = $kb_term->term_id;
			delete_transient( 'idocs_transient_design_settings_' . $kb_id);	

		}
	}
	/*---------------------------------------------------------------------------------------*/
	public static function suspend_design_settings_caching($kb_id) {

		//error_log('setting a suspend caching flag');
		//error_log("delete the existing data caching");
		// every action in the customizer is re-creating the flag so the transient time can be short.  	
		set_transient( 'idocs_transient_suspend_design_caching_flag', 1 , 300 );
		// delete the existing data caching so any change in the customizer will be reflected
		delete_transient( 'idocs_transient_design_settings_' . $kb_id);
		
	}
	/*---------------------------------------------------------------------------------------*/
	// utility method to define default values for the plugin admin setting 
	public static function default_values_for_settings () {
		
		return array (
			
			// remove the plugin configuration & analytics data after uninstall - default in no.
			'idocs_remove_configuration_after_uninstall' => 0,
			'idocs_remove_analytics_after_uninstall' => 0,
			// default slug for the KBs root. 
			'idocs_kbs_root_slug' => 'idocs',

			'idocs_search_query_event_enabled' => 1,
			'idocs_kb_visit_event_enabled' => 1,
			'idocs_category_visit_event_enabled' => 1,
			'idocs_tag_visit_event_enabled' => 1,
			'idocs_faq_group_visit_event_enabled' => 1,
			'idocs_content_visit_event_enabled' => 1,
			'idocs_ignore_local_host_events' => 0,

		);
	}
	/*---------------------------------------------------------------------------------------*/
	// get the plugin setting/s from the database or/and defualt settings 
	public static function get_plugin_settings ( $option_name = '' ) {

		// get the plugin saved options array from the options table - will return false if options array does not exist 
		$settings = get_option( IDOCS_PLUGIN_NAME.'_saved_options');
		 /*------------------------------------------------*/
		// get the plugin default settings 
		$default_settings = self::default_values_for_settings ();
		// if options does not exist, use default settings; 
		if (! $settings) {

			$settings = $default_settings;

		}
		 /*------------------------------------------------*/
		// if the requested option_name is empty return the complete array 
		if ( empty( $option_name) )	{
			// return the combined array of the default settings (full list) with the stored settings (partial list). 
			// any stored settings will overwrite the default settings. 
			return array_merge($default_settings, $settings);
		}
		 /*------------------------------------------------*/
		// requesting a specific setting (option_name) 
		// check the value is set in the DB for that requested option
		if ( isset( $settings [ $option_name ]) && strlen($settings [ $option_name ])) {

			return $settings [ $option_name ];

		}
		/*------------------------------------------------*/	
		// check the value is set in the default array for that requested option
		if ( isset ( $default_settings [ $option_name ])) {

			return $default_settings [ $option_name ];
		}
			
		else 
			return [];
	}
	/*---------------------------------------------------------------------------------------*/
	// get the plugin design setting/s
	// note - in PHP, required parameters must come before optional parameters in the function declaration.
	public static function get_plugin_design_settings ( $kb_id, $option_name = '') {

		// get the suspend caching flag
		$idocs_transient_suspend_design_caching_flag = get_transient( 'idocs_transient_suspend_design_caching_flag' );
		// set the default availability of cached data to false 
		$cached_data = false;
		 /*------------------------------------------------*/
		// if suspend caching is OFF, try to get cached data 
		if ( false === $idocs_transient_suspend_design_caching_flag ) {
			
			// Attempt to retrieve the cached data
			//error_log("suspend caching is OFF - checking for cached data");
			$cached_data = get_transient( 'idocs_transient_design_settings_' . $kb_id );
			
		} 
		// suspend caching is ON
		else {
			
			//error_log("suspend caching is ON - not using cached data");
		
		}
		 /*------------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		if ( false === $cached_data ) {

			// get the plugin saved options from the options table - will return false if options does not exist 
			// settings array will hold the subset of settings that were changed and saved in the database.   
			$settings = get_option( IDOCS_SHORT_PLUGIN_NAME.'_design_options_' . $kb_id);		
			// get the plugin default settings
			//error_log('no cached data for the kb, getting the data from db');
			$default_settings = IDOCS_Customizer::default_design_options ($kb_id);
			// if options does not exist, use default settings; 
			if (! $settings) {

				$settings = $default_settings;

			}

			// if the requested option_name is empty ("give me all settings") return the merged array of default settins and settings 
			// second array will overide duplicated keys - all settings that were changed. 
			if ( empty( $option_name) )	{

				$cached_data = array_merge($default_settings, $settings);
				// no need to cache data when the suspend caching is still ON
				if ( false === $idocs_transient_suspend_design_caching_flag ) {
					// Cache the data for 24 hours
					//error_log("creating a new transient data caching");
					set_transient( 'idocs_transient_design_settings_' . $kb_id, $cached_data, 10800 );
				}
				return $cached_data;
			}
			
			// check the value is set in the DB for that requested option
			if ( isset( $settings [ $option_name ])) {

				return $settings [ $option_name ];

			}
			
			// check the value is set in the default array for that requested option
			if ( isset ( $default_settings [ $option_name ])) {
				return $default_settings [ $option_name ];
			}
			else 
				return [];
		}
		/*------------------------------------------------*/
		//error_log("cache data is available - using it!");
		return $cached_data;
	}
	/*---------------------------------------------------------------------------------------*/
	// update a specific setting option with a new value. 
	public static function update_plugin_settings ( $option_name, $new_value ) {

		// check if option_name is empty
		if ( empty( $option_name) ) 
			return false;

		// as far WordPress is concerned - the plugin multi-dimensional array is ONE option.
		// meaning you can't get\change a single value inside an option which is saved as an array.   
		// we will get the full array, update it and save it back. 

		$my_options_array = self::get_plugin_settings();

		// if such option exist it will be update the it. Othwerise a new key-value pair will be added. 
		$my_options_array[$option_name] = $new_value;
		$success = update_option (IDOCS_PLUGIN_NAME . "_saved_options", $my_options_array);

		//do_action( 'qm/debug',  $success);
		return $success;
	}
	/*---------------------------------------------------------------------------------------*/
	// call back function called during 'plugins_loaded' hook to check the db structure when the plugin version is updated 
	// https://codex.wordpress.org/Creating_Tables_with_Plugins
	public function upgrade_plugin_db_check() {

		global $wpdb;

		// get the current installed plugin db version 
		$installed_db_ver = self::get_plugin_settings( "IDOCS_DB_VERSION" );
		//error_log("checking db");
		// check if one table if it does not exist
		$tables_available = false; 
		/*-----------------------------------------------*/
		$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_search_logs';
		//$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
		//error_log("checking db");
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) == $table_name ) {
			$tables_available = true;  
			//error_log("tables available");
		};
		/*-----------------------------------------------*/
		// check if the existing plugin db structure is not up-to-date or the first table does not exist
		if ( $installed_db_ver != IDOCS_DB_VERSION || ! $tables_available ) {

			//error_log("updating ");
			self::update_db_tables();
		
		};
	}
	/*---------------------------------------------------------------------------------------*/
	// create or update plugin tables when a new db version is released. 
	public static function update_db_tables() {
		
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		/*-----------------------------------------------*/
		// Search Logs Table 
		/*-----------------------------------------------*/
		$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_search_logs';
		//do_action( 'qm/debug',  $table_name);
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$table_name} (

				id INT UNSIGNED AUTO_INCREMENT,
				search_time  datetime NOT NULL default CURRENT_TIMESTAMP,
				search_query varchar(100) NOT NULL,
				kb_id INT NOT NULL,
				found_flag  boolean NOT NULL,
				country varchar(70),
				PRIMARY KEY  (id),
				KEY search_query_index  (search_query),
				KEY search_time_index  (search_time),
				KEY kb_id_index  (kb_id)
				
			) $charset_collate;";
		dbDelta( $sql );
		/*-----------------------------------------------*/
		// Content Ratings Table 
		/*-----------------------------------------------*/
		$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_ratings_content';

		$sql = "CREATE TABLE {$table_name} (

			id INT UNSIGNED AUTO_INCREMENT,
			rating_time  datetime NOT NULL default CURRENT_TIMESTAMP,
			content_id INT NOT NULL,
			content_type VARCHAR(20) NOT NULL,  
			kb_id INT NOT NULL,
			country varchar(70),
			rating_score TINYINT NOT NULL CHECK (rating_score BETWEEN 1 AND 5),
			PRIMARY KEY  (id),
			KEY rating_time_index  (rating_time),
			KEY content_id_index  (content_id),
			KEY content_type_index  (content_type),
			KEY kb_id_index  (kb_id)
			
		) $charset_collate;";
	
		dbDelta( $sql );	
		/*-----------------------------------------------*/
		// Taxonomy Ratings Table 
		/*-----------------------------------------------*/
		$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_ratings_taxonomy';

		$sql = "CREATE TABLE {$table_name} (

			id INT UNSIGNED AUTO_INCREMENT,
			rating_time  datetime NOT NULL default CURRENT_TIMESTAMP,
			term_id INT NOT NULL,
			taxonomy VARCHAR(30) NOT NULL,
			kb_id INT NOT NULL,
			country varchar(70),
			rating_score TINYINT NOT NULL CHECK (rating_score BETWEEN 1 AND 5),
			PRIMARY KEY  (id),
			KEY rating_time_index  (rating_time),
			KEY term_id_index  (term_id),
			KEY taxonomy_index  (taxonomy(30)),
			KEY kb_id_index  (kb_id)
			
		) $charset_collate;";
		dbDelta( $sql );
		/*-----------------------------------------------*/
		// Content Items Visits Table 
		/*-----------------------------------------------*/
		$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_visits_content';

		$sql = "CREATE TABLE {$table_name} (

			id INT UNSIGNED AUTO_INCREMENT,
			visit_time  datetime NOT NULL default CURRENT_TIMESTAMP,
			content_id INT NOT NULL,
			content_type VARCHAR(20) NOT NULL,  
			kb_id INT NOT NULL,
			country varchar(70),
			PRIMARY KEY  (id),
			KEY visit_time_index  (visit_time),
			KEY content_id_index  (content_id),
			KEY content_type_index  (content_type),
			KEY kb_id_index  (kb_id)

		) $charset_collate;";
		dbDelta( $sql );
		/*-----------------------------------------------*/
		// Taxonomy Visits Table (KB View, Category View, Tag View, FAQ Group View) 
		/*-----------------------------------------------*/
		$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_visits_taxonomy';

		$sql = "CREATE TABLE {$table_name} (

			id INT UNSIGNED AUTO_INCREMENT,
			visit_time  datetime NOT NULL default CURRENT_TIMESTAMP,
			term_id INT NOT NULL,
			taxonomy VARCHAR(30) NOT NULL,
			kb_id INT NOT NULL, 
			country varchar(70),
			PRIMARY KEY  (id),
			KEY visit_time_index  (visit_time),
			KEY term_id_index  (term_id),
			KEY taxonomy_index  (taxonomy(30)),
			KEY kb_id_index  (kb_id)

		) $charset_collate;";
		dbDelta( $sql );
		/*-----------------------------------------------*/	
		// update the stored plugin db version to the latest version 
		self::update_plugin_settings( "IDOCS_DB_VERSION", IDOCS_DB_VERSION );		
	
	}
	/*---------------------------------------------------------------------------------------*/
	// during init, add an action hook to cleanup the database. Hook will be triggred by a cron event. 
    public function add_action_for_data_cleanup_event() {
        
		// Hook the scheduled_db_cleanup method of the current class instance ($this) to the $db_cleanup_hook action hook 
		add_action(IDOCS_Cron::$db_cleanup_hook, array($this, 'scheduled_db_cleanup'), 10, 1);

    }
	/*---------------------------------------------------------------------------------------*/
	// perform a database cleanup based on the provided max_days parameters 
	// Any data older than the maximum days back will be deleted. (5e5827093d)
	public function scheduled_db_cleanup( ) {

		global $wpdb;
		$max_days = 180;  // 6 months
		/*-----------------------------------------------*/	
		// WordPress supports generic SQL queries via the $wpdb->query() method. 
		$wpdb->query($wpdb->prepare(
			"DELETE 
			FROM {$wpdb->prefix}idocs_search_logs
			WHERE date_add(search_time, INTERVAL %d day) < CURRENT_TIMESTAMP",
			$max_days
		));
		/*-----------------------------------------------*/	
		$wpdb->query($wpdb->prepare(
			"DELETE 
				FROM {$wpdb->prefix}idocs_ratings_content
				WHERE date_add(rating_time, INTERVAL %d day) < CURRENT_TIMESTAMP",
			$max_days
		));	
		/*-----------------------------------------------*/
		$wpdb->query($wpdb->prepare(
			"DELETE 
			  FROM {$wpdb->prefix}idocs_ratings_taxonomy
			  WHERE date_add(rating_time, INTERVAL %d day) < CURRENT_TIMESTAMP",
			$max_days
		));	
		/*-----------------------------------------------*/		
		$wpdb->query($wpdb->prepare(
			"DELETE 
            FROM {$wpdb->prefix}idocs_visits_content
            WHERE date_add(visit_time, INTERVAL %d day) < CURRENT_TIMESTAMP", 
			$max_days
		));
		/*-----------------------------------------------*/
		$wpdb->query($wpdb->prepare(
			"DELETE 
            FROM {$wpdb->prefix}idocs_visits_taxonomy
            WHERE date_add(visit_time, INTERVAL %d day) < CURRENT_TIMESTAMP", 
			$max_days
		));	
		/*-----------------------------------------------*/	
	}
	/*---------------------------------------------------------------------------------------*/
	public static function deleted_kb_db_cleanup( $kb_id ) {

		global $wpdb;
		/*-----------------------------------------------*/	
		// WordPress supports generic SQL queries via the $wpdb->query() method. 
		$wpdb->query($wpdb->prepare(
			"DELETE 
             FROM {$wpdb->prefix}idocs_search_logs
             WHERE kb_id = %d",
			$kb_id));
		/*-----------------------------------------------*/	
		$wpdb->query($wpdb->prepare(
			"DELETE 
             FROM {$wpdb->prefix}idocs_ratings_content
             WHERE kb_id = %d",
			$kb_id));
		/*-----------------------------------------------*/	
		$wpdb->query($wpdb->prepare(
			"DELETE 
             FROM {$wpdb->prefix}idocs_ratings_taxonomy
			 WHERE kb_id = %d",
			$kb_id));
		/*-----------------------------------------------*/	
		$wpdb->query($wpdb->prepare(
			"DELETE 
             FROM {$wpdb->prefix}idocs_visits_content
			 WHERE kb_id = %d",
			$kb_id));
		/*-----------------------------------------------*/	
		$wpdb->query($wpdb->prepare(
			"DELETE 
             FROM {$wpdb->prefix}idocs_visits_taxonomy
			 WHERE kb_id = %d",
			$kb_id));
		/*-----------------------------------------------*/	
	}
	/*---------------------------------------------------------------------------------------*/
}
/*---------------------------------------------------------------------------------------*/
// https://www.smashingmagazine.com/2013/01/using-wp_query-wordpress/
// https://www.smashingmagazine.com/2016/03/advanced-wordpress-search-with-wp_query/
// https://codex.wordpress.org/Creating_Tables_with_Plugins