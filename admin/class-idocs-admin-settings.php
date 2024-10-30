<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* 
	> enqueue styles and javascript on-demand
   	> register saved settings in the database 
/*---------------------------------------------------------------------------------------*/
class IDOCS_Admin_Settings {

	private $plugin_name;
	/*---------------------------------------------------------------------------------------*/
	// Initialize the class and set its properties.
	public function __construct() {

		$this->plugin_name = IDOCS_PLUGIN_NAME;

	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_settings_tabs () {

		$settings_tabs = array (
			/*---------------------------------*/
			
			/*---------------------------------*/
			'design' => array (
				'tab_active' => true,
				'tab_title' => __( 'Design', 'incredibledocs' ),
				'default_section' => 'customizer',
				/*---------------------------------*/
				'sections' => array (
		
					'customizer' => array (
						'section_active' => true,
						'section_title' => __( 'Customizer', 'incredibledocs' ),
						'section_page'  => 'design-customizer-page.php',
						'page_path' => IDOCS_ADMIN_DIR_PATH,
					),

					'site_editor' => array (
						'section_active' => false,
						'section_title' => __( 'Site Editor - Templates', 'incredibledocs' ),
						'section_page'  => 'design-site-editor-page.php',
						'page_path' => IDOCS_ADMIN_DIR_PATH,
					),
		
					'kb_theme' => array (
						'section_active' => true,
						'section_title' => __( 'Global Color Scheme', 'incredibledocs' ),
						'section_page'  => 'design-global-color-scheme-page.php',
						'page_path' => IDOCS_ADMIN_DIR_PATH,
					),
				
					'color_scheme_builder' => array (
						'section_active' => false,
						'section_title' => __( 'Color Scheme Builder', 'incredibledocs' ),
						'section_page'  => 'design-color-scheme-builder-page.php',
						'page_path' => '',
					),
				
					'design_tools' => array (
						'section_active' => false,
						'section_title' => __( 'Design Tools', 'incredibledocs' ),
						'section_page'  => 'design-tools-page.php',
						'page_path' => '',
					),
				)
			),
			/*---------------------------------*/
			'urls' => array (
				'tab_active' => true,
				'tab_title' => __( 'URLs', 'incredibledocs' ),
				// mark the default section for this tab. 
				'default_section' => 'automated',
				/*---------------------------------*/
				'sections' => array (
		
					'automated' => array (
						'section_active' => true,
						'section_title' =>  __( 'Automated', 'incredibledocs' ),
						'section_page'  => 'settings-general-urls.php',
						'page_path' => IDOCS_ADMIN_DIR_PATH,
					),

					'custom-kb-pages' => array (
						'section_active' => true,
						'section_title' =>  __( 'Custom KB Pages', 'incredibledocs' ),
						'section_page'  => 'settings-general-custom-kb-pages.php',
						'page_path' => IDOCS_ADMIN_DIR_PATH,
					),
				)
			),	
			/*---------------------------------*/
			'access-manager' => array (
				'tab_active' => false,
				'tab_title' => __( 'Access Control', 'incredibledocs' ),
				'default_section' => 'kbs',
				/*---------------------------------*/
				'sections' => array (
		
					'kbs' => array (
						'section_active' => true,
						'section_title' => __( 'Knowledge Bases', 'incredibledocs' ),
						'section_page'  => 'access-manager-kbs.php',
						'page_path' => '',
					),

					'categories' => array (
						'section_active' => true,
						'section_title' => __( 'Categories', 'incredibledocs' ),
						'section_page'  => 'access-manager-categories.php',
						'page_path' => '',
					),

					'groups' => array (
						'section_active' => true,
						'section_title' => __( 'Groups', 'incredibledocs' ),
						'section_page'  => 'access-manager-groups.php',
						'page_path' => '',
					),

					'users' => array (
						'section_active' => true,
						'section_title' => __( 'Users', 'incredibledocs' ),
						'section_page'   => 'access-manager-users.php',
						'page_path' => '',
					),

					'roles_mapping' => array (
						'section_active' => true,
						'section_title' => __( 'Role Mapping', 'incredibledocs' ),
						'section_page'   => 'access-manager-roles-mapping.php',
						'page_path' => '',
					),
				),
			),
			/*---------------------------------*/
			'tools' => array (
				'tab_active' => true,
				'tab_title' => __( 'Tools', 'incredibledocs' ),
				// mark the default section for this tab. 
				'default_section' => 'shortcodes',
				/*---------------------------------*/
				'sections' => array (
					
					'shortcodes' => array (
						'section_active' => true,
						'section_title' => __( 'Frontend Shortcodes', 'incredibledocs' ),
						'section_page'  => 'settings-general-shortcodes.php',
						'page_path' => IDOCS_ADMIN_DIR_PATH,
					),

					'analytics_data' => array (
						'section_active' => true,
						'section_title' => __( 'Analytics Data', 'incredibledocs' ),
						'section_page'  => 'settings-analytics-data.php',
						'page_path' =>  IDOCS_ADMIN_DIR_PATH,
					),

					'uninstall' => array (
						'section_active' => true,
						'section_title' =>  __( 'Uninstall', 'incredibledocs' ),
						'section_page'  => 'settings-general-uninstall.php',
						'page_path' => IDOCS_ADMIN_DIR_PATH,
					),

					
					
					/* ROADMAP 
					'Data Caching' => array (
						'section_active' => true,
						'section_title' =>  __( 'Data Caching', 'incredibledocs' ),
						'section_page'  => 'settings-general-data-caching.php',
						'page_path' => IDOCS_ADMIN_DIR_PATH,
					),
					*/

					'email_reporting' => array (
						'section_active' => false,
						'section_title' => __( 'Email Reporting', 'incredibledocs' ),
						'section_page'  => 'settings-general-email-reporting.php',
						'page_path' => '',
					),
					// roadmap item 
					
					
					

					'license' => array (
						'section_active' => false,
						'section_title' => __( 'License Key', 'incredibledocs' ),
						'section_page'  => 'settings-general-license.php',
						'page_path' => '',
					),
				),	
			),
			/*---------------------------------*/
			'license' => array (
				'tab_active' => false,
				'tab_title' => __( 'License', 'incredibledocs' ),
				'default_section' => 'license-key',
				/*---------------------------------*/
				'sections' => array (
		
					'license-key' => array (
						'section_active' => true,
						'section_title' => __( 'License Key', 'incredibledocs' ),
						'section_page'  => 'settings-general-license.php',
						'page_path' => '',
					),
				),
			),
		);
		/*---------------------------------*/
		// remove the "customizer" menu for block-based themes 
		if ( wp_is_block_theme() ) {

			//$settings_tabs['design']['tab_active'] = false;
			$settings_tabs['design']['sections']['site_editor']['section_active'] = true;
			$settings_tabs['design']['sections']['customizer']['section_active'] = false;
			$settings_tabs['design']['default_section'] = 'site_editor';

		};
		/*---------------------------------*/
		// apply_filter - let the pro-version turn on additional settings tabs
		return apply_filters('idocs_settings_tabs', $settings_tabs);
	}
	/*---------------------------------------------------------------------------------------*/
	// utility function to check all wp roles and capabilities 
	// adjust the $string_to_check
	public static function check_all_wp_roles() {

		// Get all roles
		$roles = wp_roles()->roles;
		/*---------------------------------*/
		// Loop through each role
		foreach ( $roles as $role_name => $role ) {

			// Check if the role has capabilities with the suffix "_idocs_documents"
			$filtered_capabilities = array_filter($role['capabilities'], function($capability, $key) {
				return strpos($key, 'idocs_document') !== false;
			}, ARRAY_FILTER_USE_BOTH);
			/*---------------------------------*/
			// If the role has capabilities with the suffix "_idocs_documents"
			if (!empty($filtered_capabilities)) {
				// Display role name
				?>
				<strong>Role: '<?php echo esc_html($role_name);?></strong><br>
				<?php

				// Display filtered capabilities
				foreach ($filtered_capabilities as $capability => $value) {
					echo esc_html($capability . ': ' . ($value ? 'true' : 'false') . '<br>');
				}
				?> <br> <?php
			}
		}
	}
	/*---------------------------------------------------------------------------------------*/
	// define the plugin knowledge-base capabilities (KB Editor, KB Author)
	public static function get_capabilities( $kb_role ) {

		$plural = "idocs_contents";
		//$plural = "idocs_documents";
		/*---------------------------------*/
		$kb_roles = array (
			/*---------------------------------*/
			'KB Editor' => array (
				// Edit
				'edit_' . $plural,
				'edit_others_' . $plural,
				'edit_private_' . $plural,
				'edit_published_' . $plural,
				
				// Delete
				'delete_' . $plural,
				'delete_others_' . $plural,
				'delete_private_' . $plural,
				'delete_published_' . $plural,
				
				// Publish 
				'publish_'. $plural,
			),
			/*---------------------------------*/
			'KB Author' => array (
				// Edit
				'edit_' . $plural,
				'edit_published_' . $plural,
				
				// Delete
				'delete_' . $plural,
				'delete_published_' . $plural,
				
				// Publish 
				'publish_'. $plural,

				/*
				//not relevant for author - will not be able to change other authors content 
				'edit_others_' . $plural,
				'edit_private_' . $plural,
				'delete_others_' . $plural,
				'delete_private_' . $plural,
				*/
				
			),
		);
		/*---------------------------------*/
		return $kb_roles[$kb_role];
	}
	/*---------------------------------------------------------------------------------------*/	
	public function remove_next_link() {
		
		/*
		Removes the adjacent post links:

		'wp_head': This is the hook from which the action is being removed. 
		The wp_head hook is often used to output elements in the <head> section of the HTML document.

		The adjacent_posts_rel_link function, by default, adds the rel links for the next and previous post in the head of the document.
		by using remove_action in this context can be useful if you want to prevent those links from being output in the <head> section of your site.
		
		*/
		remove_action( 'wp_head', 'adjacent_posts_rel_link', 1 );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 1 );	
			
	}
	/*---------------------------------------------------------------------------------------*/
	// callback function hooked to "admin_notices" action to display all messages registered to 'idocs-settings-error-slug'
	public function display_plugin_admin_notices() {

		// Displays settings errors registered by add_settings_error() .
		settings_errors( 'idocs_kbs_root_slug' );
		settings_errors( 'toc_title' );
		settings_errors( 'feedback_title' );
		settings_errors( 'checkbox_value' );
		settings_errors( 'min_amount_characters_for_search' );
		settings_errors( 'keystroke_delay_before_search' );

	}
	/*---------------------------------------------------------------------------------------*/
	// Register the stylesheets for the admin area.
	// Optimizing the CSS loading according to the relevant page. (5e5827093d)
	public function enqueue_styles() {

		global $current_screen;
		//do_action( 'qm/debug', $current_screen->id);
		/*--------------------------------------------*/
		// main admin css - must be loaded by default for creating the seperators in the menu
		wp_enqueue_style( 'idocs-admin-css', 
						  IDOCS_ADMIN_URL . 'css/idocs-admin.css', 
						  array(), 
						  IDOCS_VERSION, 
						  'all' 
						);
		/*--------------------------------------------*/
		/* idocs-settings & idocs-dashboard pages */
		if ( $current_screen->id == 'incredibledocs_page_idocs-settings' ||
			 $current_screen->id == 'toplevel_page_idocs-dashboard' ||
			 $current_screen->id == 'incredibledocs_page_idocs-design' ||
			 $current_screen->id == 'incredibledocs_page_idocs-analytics'
			 ) {

			// loading the bootrap css framework 
			wp_enqueue_style( 'idocs-bootstrap-css', 
							IDOCS_ADMIN_URL . 'css/vendor/bootstrap.min.css', 
							array(), 
							'5.3.3', 
							'all' 
							);
		};
		/*--------------------------------------------*/
		/* idocs-settings page */
		if ( $current_screen->id == 'incredibledocs_page_idocs-settings' ) {

			wp_enqueue_style( 'idocs-admin-settings-css', 
								IDOCS_ADMIN_URL . 'css/idocs-admin-settings.css', 
								array(), 
								IDOCS_VERSION, 
								'all' 
							);
		}
		/*--------------------------------------------*/
		/* idocs-dashboard page */
		if ( $current_screen->id == 'toplevel_page_idocs-dashboard' ) {

			wp_enqueue_style( 'idocs-admin-dashboard-css', 
								IDOCS_ADMIN_URL . 'css/idocs-admin-dashboard.css', 
								array(), 
								IDOCS_VERSION, 
								'all' 
							);
		}
		/*--------------------------------------------*/
		/* tags page */
		if ( $current_screen->id == 'edit-idocs-tag-taxo' ) {

			wp_enqueue_style('wp-color-picker');

		}
		/*--------------------------------------------*/
		// analytics page
		if (  $current_screen->id == 'incredibledocs_page_idocs-analytics' ) {
			/*--------------------------------------------*/
			
			/*--------------------------------------------*/
			// admin analytics css
			wp_enqueue_style( 'idocspro-admin-analytics-css', 
							IDOCS_ADMIN_URL . 'css/idocs-admin-analytics.css', 
							array(), 
							IDOCS_VERSION, 
							'all' );
		};
		/*--------------------------------------------*/
	}	
	/*---------------------------------------------------------------------------------------*/
	public function display_rewrite_rules() {

		
		$rules = get_option('rewrite_rules');
		//do_action( 'qm/debug', $rules );
		//error_log($rules);
		
	}
	/*---------------------------------------------------------------------------------------*/	
	// Register the JavaScript for the ADMIN area.
	public function enqueue_scripts( $hook_suffix ) {

		global $current_screen; // a global variable in admin screens.
		wp_enqueue_media();
		//do_action( 'qm/debug', $current_screen->id );			
		/*--------------------------------------------*/
		if ( $current_screen->id == 'incredibledocs_page_idocs-settings' ||
			 $current_screen->id == 'toplevel_page_idocs-dashboard' ||
			 $current_screen->id == 'incredibledocs_page_idocs-design' ||
			 $current_screen->id == 'incredibledocs_page_idocs-analytics'  
			 ) {

			// loading bootstrap js framework
			wp_enqueue_script( 'idocs-bootstrap-js',
				IDOCS_ADMIN_URL . 'js/vendor/bootstrap.min.js',
				array( 'jquery' ), 
				'5.3.3', 
				false 
			);
		}
		/*--------------------------------------------*/
		if ( $current_screen->id == 'incredibledocs_page_idocs-analytics' ) {
			
			/*--------------------------------------------*/
			wp_enqueue_script( 'class-idocs-analytics-js', 
				IDOCS_ADMIN_URL . 'js/class-idocs-analytics.min.js',  
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! (after the DOM was loaded)
				true
			);
			/*--------------------------------------------*/ 
			wp_localize_script( 'class-idocs-analytics-js', // the script name we will pass the data 
				'idocs_ajax_obj',      // Name of the JavaScript object
				array(
					// this is used by the JS to send GET requests to specific site url 
					'root_url' => get_site_url(),
					'ajax_url' => admin_url('admin-ajax.php'),
					'nonce'     => wp_create_nonce( 'wp_rest' ),
				)  
			 );
			/*--------------------------------------------*/
			 // graph.js framework (charts)
			wp_enqueue_script( 'graph-js',
				IDOCS_ADMIN_URL . 'js/vendor/chart.umd.min.js',
				array( 'jquery' ), 
				'4.4.1', 
				false 
			);
			/*--------------------------------------------*/
		}
		/*--------------------------------------------*/
		// is that the categories admin page?
		if ( $current_screen->id == 'edit-idocs-category-taxo' ) {

			wp_enqueue_script( 'class-idocs-categories-admin-js', 
				IDOCS_ADMIN_URL . 'js/class-idocs-categories-admin.min.js', 
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! (after the DOM was loaded)
				true
			 );

			wp_localize_script( 'class-idocs-categories-admin-js', // the script name we will pass the data 
				'idocs_ajax_obj',      // Name of the JavaScript object
				array(
					// this is used by the JS to send GET requests to specific site url 
					'root_url' => get_site_url(),
					'nonce'     => wp_create_nonce( 'wp_rest' ),
					'ajax_url' => admin_url('admin-ajax.php'),

				)  
			 );
			
			wp_enqueue_script( 'class-idocs-icon-upload.min.js', 
				IDOCS_ADMIN_URL . 'js/class-idocs-icon-upload.min.js', 
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! (after the DOM was loaded)
				true
			 );

			 wp_enqueue_script( 'class-idocs-icon-picker.min.js', 
				IDOCS_ADMIN_URL . 'js/class-idocs-icon-picker.min.js', 
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! (after the DOM was loaded)
				true
			 );
		}
		/*--------------------------------------------*/
		// is that the kb admin page?
		if ( $current_screen->id == 'edit-idocs-kb-taxo' ) {

			wp_enqueue_script( 'class-idocs-icon-picker.min.js', 
				IDOCS_ADMIN_URL . 'js/class-idocs-icon-picker.min.js', 
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! (after the DOM was loaded)
				true
			 );

			wp_localize_script( 'class-idocs-icon-picker.min.js', // the script name we will pass the data 
				'idocs_ajax_obj',      // Name of the JavaScript object
				array(
					// this is used by the JS to send GET requests to specific site url 
					'root_url' => get_site_url(),
					'nonce'     => wp_create_nonce( 'wp_rest' ),
				)  
			 );
			
			wp_enqueue_script( 'class-idocs-icon-upload.min.js', 
				IDOCS_ADMIN_URL . 'js/class-idocs-icon-upload.min.js', 
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! (after the DOM was loaded)
				true
			 );
			
			 wp_enqueue_script( 'class-idocs-kb-delete.min.js', 
				IDOCS_ADMIN_URL . 'js/class-idocs-kb-delete.min.js', 
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! (after the DOM was loaded)
				true
			 );
		}
		/*--------------------------------------------*/
		// is that the design admin page?
		if ( $current_screen->id == 'incredibledocs_page_idocs-design' ||
			 $current_screen->id == 'incredibledocs_page_idocs-settings' ) {

			if ( ! wp_is_block_theme() ) {	
				wp_enqueue_script( 'class-idocs-design-customizer-js', 
					IDOCS_ADMIN_URL . 'js/class-idocs-design-customizer.min.js', 
					array( 'jquery' ), 
					IDOCS_VERSION, 
					// Script will be loaded at the footer!!! (after the DOM was loaded)
					true
				);

				wp_localize_script( 'class-idocs-design-customizer-js', // the script name we will pass the data 
					'idocs_ajax_obj',      // Name of the JavaScript object
					array(
						// this is used by the JS to send GET requests to specific site url 
						'root_url' => get_site_url(),
						'nonce'     => wp_create_nonce( 'wp_rest' ),
					)  
				);
			}; 

			 wp_enqueue_script( 'class-idocs-global-color-scheme-js', 
				IDOCS_ADMIN_URL . 'js/class-idocs-global-color-scheme.min.js', 
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! (after the DOM was loaded)
				true
			 );
			 /*--------------------------------------------*/
			 wp_localize_script( 'class-idocs-global-color-scheme-js', // the script name we will pass the data 
				'idocs_ajax_obj',      // Name of the JavaScript object
				array(
					// this is used by the JS to send GET requests to specific site url 
					'root_url' => get_site_url(),
					'nonce'     => wp_create_nonce( 'wp_rest' ),
				)  
			 );

		}
		/*--------------------------------------------*/
		/* tags page */
		if ( $current_screen->id == 'edit-idocs-tag-taxo' ) {

			wp_enqueue_script('wp-color-picker');

			wp_enqueue_script( 'class-idocs-tag-color-js', 
				IDOCS_ADMIN_URL . 'js/class-idocs-tag-color.js', 
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! (after the DOM was loaded)
				true
			 );
		}
		/*--------------------------------------------*/
		// is that the faq groups admin page?
		if ( $current_screen->id == 'edit-idocs-faq-group-taxo' ) {

			wp_enqueue_script( 'class-idocs-faqgroup-admin-js', 
				IDOCS_ADMIN_URL . 'js/class-idocs-faqgroup-admin.min.js', 
				array( 'jquery' ), 
				IDOCS_VERSION, 
				// Script will be loaded at the footer!!! (after the DOM was loaded)
				true
			 );

			wp_localize_script( 'class-idocs-faqgroup-admin-js', // the script name we will pass the data 
				'idocs_ajax_obj',      // Name of the JavaScript object
				array(
					// this is used by the JS to send GET requests to specific site url 
					'root_url' => get_site_url(),
					'nonce'     => wp_create_nonce( 'wp_rest' ),
				)  
			 );	 
		}
	}
	/*---------------------------------------------------------------------------------------*/
	// translate drop-down selection options from a key to a label to display.
	public function get_selection_options ( $array_name ) {
	
		$selection_options = array (
			// left side is what saved in the option. Right side is for display. 
			'category_cards_order_by' => array(

					'name'   		   => 'Alphabetical by Name',
					'category_order'   => 'Configured Category Order',
			),

			'documents_order_by' => array (

					'title'   		     => 'Alphabetical by Title',
					'created_date' => 'Created Date',
					'last_modified_date' => 'Last Modified Date',
			
					
			),

			'kb_page_num_columns' => array (

					'1-col' => 1,
					'2-col' => 2,
					'3-col' => 3,
					'4-col' => 4,
					
			),

			'kb_page_category_layout' => array(

				'basic-layout' => 'Basic',
				'tabs-layout' => 'Tabs',
	
			),

			'toc_header_start' => array (

				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6'

			),

			'toc_header_end' => array (

				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6'
				
			),

			'email_report_frequency' => array (

				'f1' => 'Daily',
				'f2' => 'Weekly',
				'f3' => 'Monthly',

			),

			'email_report_day' => array (

				'd1' => 'Sunday',
				'd2' => 'Monday',
				'd3' => 'Tuesday',
				'd4' => 'Wednesday',
				'd5' => 'Thursday',
				'd6' => 'Friday',
				'd7' => 'Saturday',
 			),

		);
		/*---------------------------------*/
		return $selection_options[$array_name];
	}
	/*---------------------------------------------------------------------------------------*/
	// a callback function to register the plugin menu options
	public function register_general_settings() {
		
		// Registers a setting and its data - 
		// which then allows the option to be saved and updated automatically on the wp-admin/options.php page.
		/*--------------------------------------------*/
		// option group name - is used when displaying on a setting page 
		$my_options_group = $this->plugin_name . '_saved_options_group';
		// option name (database entry name) - must be a unique name in the options table 
		$my_option_name = $this->plugin_name . '_saved_options';
		// a callback function that sanitizes the option values.
		$args = array (
			'sanitize_callback' => array($this, 'validate_options_before_saving')
		);	
		register_setting($my_options_group, $my_option_name, $args);
		/*--------------------------------------------*/
		// register the setting sections 
		$this->register_setting_sections();
		// register the settings fields inside each section 
		$this->register_setting_fields();

	}
	/*---------------------------------------------------------------------------------------*/
	private function register_setting_sections() {
		
		// add_settings_section( string $id, string $title, callable $callback, string $page, array $args = array() )
		/*--------------------------------------------*/
		add_settings_section( 
			'general-tab-uninstall-section', 
			'', 
			'',
			'general-tab-uninstall-section'
		);
		/*--------------------------------------------*/
		add_settings_section( 
			'general-tab-urls-section', 
			'', 
			'',
			'general-tab-urls-section'
		);
		/*--------------------------------------------*/
		add_settings_section( 
			'analytics-data-section', 
			'', 
			'',
			'analytics-data-section'
		);	
	}
	/*---------------------------------------------------------------------------------------*/
	// register the required fields per each setting page
	private function register_setting_fields() {

		$this->register_setting_fields_general();
		$this->register_setting_fields_urls();
		$this->register_setting_fields_analytics_data();
	
	}
	/*---------------------------------------------------------------------------------------*/
	private function register_setting_fields_analytics_data() {

		add_settings_field(
			'idocs_search_query_event_enabled',
			'Search Query Event',
			array($this, 'callback_fill_form_field_flag'), 
			'analytics-data-section',
			'analytics-data-section',
			[ 'id' => 'idocs_search_query_event_enabled', 'label' => '' ]
		);

		add_settings_field(
			'idocs_kb_visit_event_enabled',
			'Visit Event - KB Page',
			array($this, 'callback_fill_form_field_flag'), 
			'analytics-data-section',
			'analytics-data-section',
			[ 'id' => 'idocs_kb_visit_event_enabled', 'label' => '' ]
		);

		add_settings_field(
			'idocs_category_visit_event_enabled',
			'Visit Event - Category Page',
			array($this, 'callback_fill_form_field_flag'), 
			'analytics-data-section',
			'analytics-data-section',
			[ 'id' => 'idocs_category_visit_event_enabled', 'label' => '' ]
		);

		add_settings_field(
			'idocs_tag_visit_event_enabled',
			'Visit Event - Tag Page',
			array($this, 'callback_fill_form_field_flag'), 
			'analytics-data-section',
			'analytics-data-section',
			[ 'id' => 'idocs_tag_visit_event_enabled', 'label' => '' ]
		);

		add_settings_field(
			'idocs_faq_group_visit_event_enabled',
			'Visit Event - FAQ Group Page',
			array($this, 'callback_fill_form_field_flag'), 
			'analytics-data-section',
			'analytics-data-section',
			[ 'id' => 'idocs_faq_group_visit_event_enabled', 'label' => '' ]
		);

		add_settings_field(
			'idocs_content_visit_event_enabled',
			'Visit Event - Content Page',
			array($this, 'callback_fill_form_field_flag'), 
			'analytics-data-section',
			'analytics-data-section',
			[ 'id' => 'idocs_content_visit_event_enabled', 'label' => '' ]
		);

		/*
		add_settings_field(
			'idocs_ignore_local_host_events',
			'Ignore Local Host Events',
			array($this, 'callback_fill_form_field_flag'), 
			'analytics-data-section',
			'analytics-data-section',
			[ 'id' => 'idocs_ignore_local_host_events', 'label' => '' ]
		);
		*/

	}
	/*---------------------------------------------------------------------------------------*/
	// setting fields for URL section 
	private function register_setting_fields_urls() {

		// Adds a new field to a section of a settings page.
		add_settings_field(
			'idocs_kbs_root_slug',
			'Custom Root Slug',
			array($this, 'callback_fill_form_field_text'), 
			'general-tab-urls-section',
			'general-tab-urls-section',
			// array of arguments that are sent to the callback function 
			[ 'id' => 'idocs_kbs_root_slug', 'label' => 'Using a generic slug can potentially conflict with other plugins or themes, so make sure it is unique in your website.' ]
		);

	}
	/*---------------------------------------------------------------------------------------*/
	// setting fields for GENERAL tab sections 
	private function register_setting_fields_general() {

		add_settings_field(
			'idocs_remove_configuration_after_uninstall',
			'Remove All Configuration',
			array($this, 'callback_fill_form_field_flag'), 
			'general-tab-uninstall-section',
			'general-tab-uninstall-section',
			[ 'id' => 'idocs_remove_configuration_after_uninstall', 'label' => 'remove any plugin custom configuration during uninstall.' ]
		);
		/*---------------------------------*/
		add_settings_field(
			'idocs_remove_analytics_after_uninstall',
			'Remove All Analytics Data',
			array($this, 'callback_fill_form_field_flag'), 
			'general-tab-uninstall-section',
			'general-tab-uninstall-section',
			[ 'id' => 'idocs_remove_analytics_after_uninstall', 'label' => 'remove any collected analytics data during uninstall.' ]
		);

	}
	/*---------------------------------------------------------------------------------------*/
	// FLAG Field 
	public function callback_fill_form_field_flag( $args ) {
				
		$saved_options_name = $this->plugin_name.'_saved_options';
		$option = IDOCS_Database::get_plugin_settings($args['id']);
		
		// check if the id and label (of a specific field) coming from the function arguments are set 
		$id    = isset( $args['id'] )    ? $args['id']    : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';
		
		// using the checked() function to check if the checked box should be selected 
		$checked_string = !empty( $option ) ? checked( $option, 1, false ) : '';		
		?>
			<!-- When the checkbox is left unchecked by the user, the hidden field’s value gets submitted. 
				 Otherwise, the value of the checked box will be sent. -->
			
			<input id= <?php echo esc_attr($saved_options_name . $id); ?> name=<?php echo esc_attr($saved_options_name . '['. $id . ']'); ?> 
				type="hidden" value='0' >

			<input id= <?php echo esc_attr($saved_options_name . $id); ?> name=<?php echo esc_attr($saved_options_name . '['. $id . ']'); ?> 
				type="checkbox" value=<?php echo esc_attr('1' . ' ' . $checked_string); ?> >
			<br />
			<label for= <?php echo esc_attr($saved_options_name . $id); ?> ><?php echo esc_attr($label); ?></label> 
		<?php
	}
	/*---------------------------------------------------------------------------------------*/
	// Number Field 
	public function callback_fill_form_field_number( $args ) {
					
		$saved_options_name = $this->plugin_name.'_saved_options';
		$option = IDOCS_Database::get_plugin_settings($args['id']);
		
		// check if the id and label (of a specific field) coming from the function arguments are set 
		$id    = isset( $args['id'] )    ? $args['id']    : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';
		
		$value = !empty( $option ) ? $option : '';
		?>

			<input id= <?php echo esc_attr($saved_options_name . $id); ?> name=<?php echo esc_attr($saved_options_name . '['. $id . ']'); ?> 
					type="number" value="<?php echo esc_attr($value); ?>" >
			<br />
			<label for= <?php echo esc_attr($saved_options_name . $id); ?> ><?php echo esc_attr($label); ?></label> 
		
		<?php
	}

	/*---------------------------------------------------------------------------------------*/
	// Text Field 
	public function callback_fill_form_field_text( $args ) {
		
		// name of the saved options - combined with the plugin name to be unique
		$saved_options_name = $this->plugin_name.'_saved_options';
		$option = IDOCS_Database::get_plugin_settings($args['id']);
		
		// check if the id and label (of a specific field) coming from the function arguments are set 
		$id    = isset( $args['id'] )    ? $args['id']    : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';
		
		//$value = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';
		$value = !empty( $option ) ? sanitize_text_field( $option ) : '';
		?>
	
			<input id= <?php echo esc_attr($saved_options_name . $id); ?> name=<?php echo esc_attr($saved_options_name . '['. $id . ']'); ?> 
				   type="text" size="40" value="<?php echo esc_attr($value); ?>" >
			<br />
			<label for= <?php echo esc_attr($saved_options_name . $id); ?> ><?php echo esc_attr($label); ?></label> 
		
		<?php
	}
	/*---------------------------------------------------------------------------------------*/
	// Email Field 
	public function callback_fill_form_field_email( $args ) {
			
		// name of the saved options - combined with the plugin name to be unique
		$saved_options_name = $this->plugin_name.'_saved_options';
		$option = IDOCS_Database::get_plugin_settings($args['id']);
		
		// check if the id and label (of a specific field) coming from the function arguments are set 
		$id    = isset( $args['id'] )    ? $args['id']    : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';
		
		//$value = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';
		$value = !empty( $option ) ? sanitize_email( $option ) : '';
		?>

			<input id= <?php echo esc_attr($saved_options_name . $id); ?> name=<?php echo esc_attr($saved_options_name . '['. $id . ']'); ?> 
				type="email" value="<?php echo esc_attr($value); ?>" >
			<br />
			<label for= <?php echo esc_attr($saved_options_name . $id); ?> ><?php echo esc_attr($label); ?></label> 
		
		<?php
	}
	/*---------------------------------------------------------------------------------------*/
	// URL Field 
	public function callback_fill_form_field_url( $args ) {
		
		// name of the saved options - combined with the plugin name to be unique
		$saved_options_name = $this->plugin_name.'_saved_options';
		$option = IDOCS_Database::get_plugin_settings($args['id']);
		
		// check if the id and label (of a specific field) coming from the function arguments are set 
		$id    = isset( $args['id'] )    ? $args['id']    : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';
		
		//$value = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';
		
		$value = !empty( $option ) ? sanitize_url( $option ) : '';
		?>
	
			<input id= <?php echo esc_attr($saved_options_name . $id); ?> name=<?php echo esc_attr($saved_options_name . '['. $id . ']'); ?> 
				   type="url" value="<?php echo esc_attr($value); ?>" >
			<br />
			<label for= <?php echo esc_attr($saved_options_name . $id); ?> ><?php echo esc_attr($label); ?></label> 
		
		<?php
	}
	/*---------------------------------------------------------------------------------------*/
	// Selection Field 
	/*
	public function callback_fill_form_field_multiple_selection( $args ) {

		// todo: add callback functionality..
		$saved_options = $this->plugin_name.'_saved_options';
		$option = IDOCS_Database::get_plugin_settings($args['id']);

		$id    = isset( $args['id'] )    ? $args['id']    : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';
		
		$selected_option = !empty( $option ) ? sanitize_text_field( $option ) : '';
		
		$select_options = $this->get_selection_options($args['id']);

	
		echo '<select id="incredibledocs_saved_options_'. esc_attr($id) .'" name="incredibledocs_saved_options['. esc_attr($id) .']">';
		
		foreach ( $select_options as $value => $option ) {
			// using the selected() function to check which radio option should be selected 
			$selected = selected( $selected_option === $value, true, false );
			
			echo '<option value="'. esc_attr($value) .'"'. $selected .'>'. $option .'</option>';
			
		}
		
		echo '</select> <label for="incredibledocs_saved_options_'. esc_attr($id) .'">'. esc_attr($label) .'</label>';
	}
	/*---------------------------------------------------------------------------------------*/
	public function validate_checkbox($checkboxValue) {

		if ($checkboxValue !== '0' && $checkboxValue !== '1') {
			
			$message = __("Invalid checkbox value.", 'incredibledocs');
			add_settings_error ('checkbox_value', '', $message, 'error'); 
			return '0';
		
		}
		return $checkboxValue;  
	}
	/*---------------------------------------------------------------------------------------*/
	// callback: validate options - when WP is calling that function, it will pass the values the user is trying to save ($input) 
	public function validate_options_before_saving( $input ) {
		
		// sanitize and validate checkboxes 
		if ( isset( $input['idocs_remove_configuration_after_uninstall'] ) ) {

		  $input['idocs_remove_configuration_after_uninstall'] = $this->validate_checkbox($input['idocs_remove_configuration_after_uninstall']);

		}
		/*----------------------------*/
		if ( isset( $input['idocs_remove_analytics_after_uninstall'] ) ) {

			$input['idocs_remove_analytics_after_uninstall'] = $this->validate_checkbox($input['idocs_remove_analytics_after_uninstall']);
  
		  }

		/*----------------------------*/
		if ( isset( $input['show_live_search_kb'] ) ) {

			$input['show_live_search_kb'] = $this->validate_checkbox($input['show_live_search_kb']);
  
		  }
		/*----------------------------*/
		if ( isset( $input['show_live_search_doc'] ) ) {

			$input['show_live_search_doc'] = $this->validate_checkbox($input['show_live_search_doc']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_document_title'] ) ) {

			$input['show_document_title'] = $this->validate_checkbox($input['show_document_title']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_last_updated_date'] ) ) {

			$input['show_last_updated_date'] = $this->validate_checkbox($input['show_last_updated_date']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_estimated_time_to_read'] ) ) {

			$input['show_estimated_time_to_read'] = $this->validate_checkbox($input['show_estimated_time_to_read']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_author'] ) ) {

			$input['show_author'] = $this->validate_checkbox($input['show_author']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_category_counter'] ) ) {

			$input['show_category_counter'] = $this->validate_checkbox($input['show_category_counter']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_related_documents'] ) ) {

			$input['show_related_documents'] = $this->validate_checkbox($input['show_related_documents']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_document_tags'] ) ) {

			$input['show_document_tags'] = $this->validate_checkbox($input['show_document_tags']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_search_sub_title'] ) ) {

			$input['show_search_sub_title'] = $this->validate_checkbox($input['show_search_sub_title']);
  
		}
		/*----------------------------*/
		if ( isset( $input['search_order_alphabetically'] ) ) {

			$input['search_order_alphabetically'] = $this->validate_checkbox($input['search_order_alphabetically']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_breadcrumbs'] ) ) {

			$input['show_breadcrumbs'] = $this->validate_checkbox($input['show_breadcrumbs']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_toc_navigator'] ) ) {

			$input['show_toc_navigator'] = $this->validate_checkbox($input['show_toc_navigator']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_like_feedback'] ) ) {

			$input['show_like_feedback'] = $this->validate_checkbox($input['show_like_feedback']);
  
		}
		/*----------------------------*/
		if ( isset( $input['show_improve_feedback'] ) ) {

			$input['show_improve_feedback'] = $this->validate_checkbox($input['show_improve_feedback']);
  
		}
		/*----------------------------*/

		if ( isset( $input['breadcrumbs_home_url'] ) ) {
			$input['breadcrumbs_home_url'] = sanitize_url( $input['breadcrumbs_home_url'] );	
		}
		/*----------------------------*/
		if ( isset( $input['breadcrumbs_home_text'] ) ) {
			$input['breadcrumbs_home_text'] = sanitize_text_field( $input['breadcrumbs_home_text'] );	
		}
		/*----------------------------*/
		if ( isset( $input['idocs_kbs_root_slug']) )  {

			
			if ( $input['idocs_kbs_root_slug'] == '' ) {

				$message = __("KBs Root Slug can't be empty.", 'incredibledocs');
				add_settings_error ('idocs_kbs_root_slug', esc_attr('kbs-root-slug-error'), $message, 'error'); 
				
			}
			else
				$input['idocs_kbs_root_slug'] = sanitize_text_field( $input['idocs_kbs_root_slug'] );
		}
		/*----------------------------*/
		if ( isset( $input['toc_title']) )  {

			
			if ( $input['toc_title'] == '' ) {

				$message = __("Table of Content Title can't be empty.", 'incredibledocs');
				add_settings_error ('toc_title', esc_attr('toc-title-error'), $message, 'error'); 
				
			}
			else
				$input['toc_title'] = sanitize_text_field( $input['toc_title'] );
		}
		
		/*----------------------------*/
	
		if ( isset( $input['feedback_title']) ) {
			if ( $input['feedback_title'] == '' ) {

				$message = __("Feedback Title can't be empty.", 'incredibledocs');
				add_settings_error ('feedback_title', esc_attr('feedback_title_error'),$message, 'error'); 
			}
			else
				$input['feedback_title'] = sanitize_text_field( $input['feedback_title'] );
		}
		/*----------------------------*/
		if ( isset( $input['search_placeholder'] ) ) {
		
			$input['search_placeholder'] = sanitize_text_field( $input['search_placeholder'] );
				
		}

		if ( isset( $input['no_result_feedback'] ) ) {
		
			$input['no_result_feedback'] = sanitize_text_field( $input['no_result_feedback'] );
				
		}

		if ( isset( $input['search_title'] ) ) {
		
			$input['search_title'] = sanitize_text_field( $input['search_title'] );
				
		}
		/*----------------------------*/
		if ( isset( $input['search_sub_title'] ) ) {
		
			$input['search_sub_title'] = sanitize_text_field( $input['search_sub_title'] );
				
		}
		/*----------------------------*/

		if (isset( $input['min_amount_characters_for_search']) and $input['min_amount_characters_for_search'] < 1) {
			
			$message = __('Minimum amount of characters for search must be bigger than 0.', 'incredibledocs');
			add_settings_error ('min_amount_characters_for_search', '' ,$message, 'error'); 
			$input['min_amount_characters_for_search'] = 1;

		}
		/*----------------------------*/
		if (isset( $input['keystroke_delay_before_search']) and $input['keystroke_delay_before_search'] < 100) {
			
			$message = __('Delay must be bigger or equal to 100ms.', 'incredibledocs');
			add_settings_error ('keystroke_delay_before_search', '' ,$message, 'error'); 

			$input['keystroke_delay_before_search'] = 100;

		}
		/*----------------------------*/

		return $input;
	}
}
/*---------------------------------------------------------------------------------------*/
// https://justintadlock.com/archives/2011/07/12/how-to-load-javascript-in-the-wordpress-admin


