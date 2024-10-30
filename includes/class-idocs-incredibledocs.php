<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* Main plugin class - load dependencies, define admin and public hooks.
/*---------------------------------------------------------------------------------------*/
class IDOCS_IncredibleDocs {

	protected $loader;       // Maintains and registers all required plugin hooks 
	protected static $plugin_instance = null; // The single instance of the class.
	/*---------------------------------------------------------------------------------------*/
	public function __construct() {
		
		if ( self::check_plugin_slug() ) {

			$this->load_dependencies();
			$this->set_locale();
			$this->define_admin_hooks();
			$this->define_public_hooks();
			
		}
	}
	/*---------------------------------------------------------------------------------------*/
	// Ensures only one instance of plugin is loaded or can be loaded.
	public static function instance() {

		$run = false;
		/*---------------------------------------*/
		if (is_null(self::$plugin_instance)) {

			self::$plugin_instance = new self();
			$run = true;

		}
		/*---------------------------------------*/
		return array(self::$plugin_instance, $run);
	}
	/*---------------------------------------------------------------------------------------*/
	// Load the required dependencies for this plugin.

	private function load_dependencies() {

		// loader, i18n, database, custom rest APIs, cron , and icons..
		require_once IDOCS_DIR_PATH . 'includes/class-idocs-loader.php';
		require_once IDOCS_DIR_PATH . 'includes/class-idocs-i18n.php';
		require_once IDOCS_DIR_PATH . 'includes/class-idocs-database.php';
		require_once IDOCS_DIR_PATH . 'includes/class-idocs-custom-rest-apis.php';
		require_once IDOCS_DIR_PATH . 'includes/class-idocs-cron.php';
		require_once IDOCS_DIR_PATH . 'includes/class-idocs-icons.php';
		require_once IDOCS_DIR_PATH . 'includes/class-idocs-plugin-update.php';
		require_once IDOCS_DIR_PATH . 'includes/class-idocs-blocks.php';
		require_once IDOCS_DIR_PATH . 'includes/class-idocs-block-template.php';
		require_once IDOCS_DIR_PATH . 'includes/class-idocs-templates-manager.php';
		/*---------------------------------------*/
		// admin menu, database setting, dashboard, custom post types, meta fields, taxanomies, and themes.
		require_once IDOCS_DIR_PATH . 'admin/class-idocs-admin-settings.php';
		require_once IDOCS_DIR_PATH . 'admin/class-idocs-admin-menu.php';
		require_once IDOCS_DIR_PATH . 'admin/class-idocs-custom-post-types.php';
		require_once IDOCS_DIR_PATH . 'admin/class-idocs-post-meta-fields.php';
		require_once IDOCS_DIR_PATH . 'admin/class-idocs-taxanomies.php';
		require_once IDOCS_DIR_PATH . 'admin/class-idocs-dashboard.php';
		require_once IDOCS_DIR_PATH . 'admin/class-idocs-themes.php';
		require_once IDOCS_DIR_PATH . 'admin/class-idocs-color-scheme.php';
		/*---------------------------------------*/
		// customizer setting - custom customizer controls are loaded only in the callback hook function 
		require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer.php';
		/*---------------------------------------*/

		/*---------------------------------------*/
		// public-facing, shortcodes, category tree, save events
		require_once IDOCS_DIR_PATH . 'public/class-idocs-public-frontend.php';
		require_once IDOCS_DIR_PATH . 'public/class-idocs-shortcodes.php';
		require_once IDOCS_DIR_PATH . 'public/class-idocs-category-tree.php';
		require_once IDOCS_DIR_PATH . 'public/class-idocs-access-control.php';
		require_once IDOCS_DIR_PATH . 'public/class-idocs-save-events.php';
		require_once IDOCS_DIR_PATH . 'public/class-idocs-page-info.php';
		/*---------------------------------------*/	
		// Added so the Site Editor will be able to RESET templates added by the plugin
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		/*---------------------------------------*/	

		// initiate a loader instance (5e5827093d)
		$this->loader = new IDOCS_Loader();
	}
	/*---------------------------------------------------------------------------------------*/
	// Define the locale for this plugin for internationalization.
	private function set_locale() {

		// initiate a i18n instance 
		$plugin_i18n = new IDOCS_i18n();
		$this->loader->add_filter('load_textdomain_mofile', $plugin_i18n, 'load_plugin_textdomain', 10, 2 );
		// tell WordPress that a specific JavaScript contains translation
		$this->loader->add_action('init', $plugin_i18n, 'javascript_set_script_translations' );
	
	}
	/*---------------------------------------------------------------------------------------*/	
	// Register all of the hooks related to the ADMIN area functionality
	private function define_admin_hooks() {

		/*---------------------------------------*/
		// for dynamic hooks 
		$my_custom_post_type = 'idocs_content';
		$cat_taxonomy = 'idocs-category-taxo';
		$tag_taxonomy = 'idocs-tag-taxo';
		$kb_taxonomy = 'idocs-kb-taxo';
		$faqgroup_taxonomy = 'idocs-faq-group-taxo';
		/*---------------------------------------*/
		$edit_doc_category_screen_id = "edit-idocs-category-taxo";
		$edit_doc_tag_screen_id = "edit-idocs-tag-taxo";
		$edit_faqgroup_screen_id = "edit-idocs-faq-group-taxo";
		$edit_kb_screen_id = "edit-idocs-kb-taxo";
		$edit_comments_screen_id = "edit-comments";
		$edit_idocs_content_screen_id = "edit-idocs_content";
		/*---------------------------------------*/
		// ADMIN SETTINGS
		/*---------------------------------------*/
		$plugin_admin = new IDOCS_Admin_Settings();
		
		// removes the adjacent post links
		$this->loader->add_action( 'init', $plugin_admin, 'remove_next_link', 1);
		// register admin styles and javascripts 
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' , 10);
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', 11);

		// register admin general settings
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_general_settings' );
		// displays all messages registered to 'your-settings-error-slug'
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'display_plugin_admin_notices' );
	
		/*---------------------------------------*/	
		// FSE - Templates 
		/*---------------------------------------*/
		if ( wp_is_block_theme() ) {

			$plugin_tc = new IDOCS_Templates_Manager();

			// #1 Filters the block template object BEFORE the query takes place.
			// First in the execution sequence since it influences block template retrieval before any actual discovery of theme files.
			// https://developer.wordpress.org/reference/hooks/pre_get_block_template/
			//$this->loader->add_filter( 'pre_get_block_template', $plugin_tc, 'get_block_template_fallback', 10, 3 );

			// #2 - Filters the block template object BEFORE the theme file discovery takes place.
			// This runs after pre_get_block_template because it's more specific to the actual file retrieval process within the theme.
			// https://developer.wordpress.org/reference/hooks/pre_get_block_file_template/
			$this->loader->add_filter( 'pre_get_block_file_template', $plugin_tc, 'get_block_file_template', 10, 3 );

			// #3 - Filters the array of queried block templates array AFTER they’ve been fetched.
			// This runs after the templates have been fetched, making it third in the sequence, as it deals with the result of the template query.
			// https://developer.wordpress.org/reference/hooks/get_block_templates/

			$this->loader->add_filter( 'get_block_templates', $plugin_tc, 'add_block_templates', 10, 3 );

			// #4 - This filter is part of the larger {$type}_template_hierarchy family of filters and is used to alter the list of template filenames 
			// that are searched when retrieving a template for a taxonomy.
			// {$type}_template_hierarchy - filters the list of template filenames that are searched for when retrieving a template to use.
			// This is applied as part of the template hierarchy determination, which happens after block templates have been fetched. 
			// https://developer.wordpress.org/reference/hooks/type_template_hierarchy/
			//$this->loader->add_filter( 'taxonomy_template_hierarchy', $plugin_tc, 'add_doc_archive_to_eligible_for_fallback_templates', 10, 1 );	
		}
		/*---------------------------------------*/	
		// Blocks (editor - backend)
		/*---------------------------------------*/
		$plugin_blocks = new IDOCS_Blocks();
		// register plugin blocks for the block editor 
		$this->loader->add_action( 'init', $plugin_blocks, 'register_myplugin_blocks' );
		// add a custom block category
		$this->loader->add_filter( 'block_categories_all', $plugin_blocks, 'custom_block_category', 10, 2 );

		// register assets for the Editor itself (i.e. not the user-generated content) - like the custom sidebar
		// it can be used also to style Editor content - but it is not the recommended approach 
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_blocks, 'enqueue_block_editor_assets' );
		
		// register assets for block editor and frontend 
		// This is the primary method you should use to enqueue assets for user-generated content (blocks)
		// There are instances where you may only want to add assets in the Editor and not on the front end. You can achieve this by using an is_admin() check.
		$this->loader->add_action( 'enqueue_block_assets', $plugin_blocks, 'enqueue_block_assets' );
		
		/*---------------------------------------*/	
		// ADMIN MENU
		/*---------------------------------------*/
		$plugin_admin_menu = new IDOCS_Admin_Menu();
		// register an admin menu page 
		$this->loader->add_action( 'admin_menu', $plugin_admin_menu, 'adding_admin_menu' );
		// register filters to highlight the selected admin menu and submenu 
		$this->loader->add_filter( 'parent_file', $plugin_admin_menu, 'highlight_selected_admin_menu' );
		$this->loader->add_filter( 'submenu_file', $plugin_admin_menu, 'highlight_selected_admin_submenu', 10, 2 );
		/*-------------------*/	
		// CPT ADMIN PANEL 
		/*-------------------*/	
		// update the columns in the cpt admin panel to display the taxonomy fields : manage_{$post_type}_posts_columns
		$this->loader->add_filter( "manage_{$my_custom_post_type}_posts_columns", $plugin_admin_menu, 'update_document_admin_columns', 99 , 1 );
		
		$this->loader->add_filter( "manage_{$edit_idocs_content_screen_id}_sortable_columns", $plugin_admin_menu, 'custom_columns_sortable', 99 , 1 );
		$this->loader->add_action( "pre_get_posts", $plugin_admin_menu, 'custom_sort_by_custom_field');

		// add the content for the new column/s: manage_{$post_type}_posts_custom_column
		$this->loader->add_filter( "manage_{$my_custom_post_type}_posts_custom_column", $plugin_admin_menu, 'custom_content_document', 99 , 2 );
		// add custom filter (knowledge-base selection) to list of documents admin panel
		// https://wordpress.stackexchange.com/questions/578/adding-a-taxonomy-filter-to-admin-list-for-a-custom-post-type
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin_menu, 'custom_admin_taxonomies_filters' );

		// manage_{$this->screen->taxonomy}_custom_column
		// https://gist.github.com/simongcc/8395576
		/*-------------------*/	
		// CATEGORY ADMIN SCREEN 
		/*-------------------*/
		// apply the filter based on the selected knowledge-base (used also for the faq-group)
		$this->loader->add_filter( 'get_terms_args', $plugin_admin_menu, 'filter_list_of_terms', 10, 2 );
		// adjust the columns header - add the new column header by hooking to: manage_{$screen->id}_columns
		$this->loader->add_filter( "manage_{$edit_doc_category_screen_id}_columns", $plugin_admin_menu, 'custom_header_doc_category', 99 , 1 );
		// add the content for the new column: manage_{$this->screen->taxonomy}_custom_column
		$this->loader->add_filter( "manage_{$cat_taxonomy}_custom_column", $plugin_admin_menu, 'custom_content_doc_category', 10, 3);
		/*-------------------*/	
		// COMMENTS ADMIN SCREEN 
		/*-------------------*/	
		$this->loader->add_filter( "manage_{$edit_comments_screen_id}_columns", $plugin_admin_menu, 'custom_header_comments', 99 , 1 );
		$this->loader->add_action( "manage_comments_custom_column", $plugin_admin_menu, 'custom_content_comments', 10, 3);
		/*-------------------*/	
		// KB ADMIN SCREEN 
		/*-------------------*/	
		// adjust the columns header - add the new column header : manage_{$screen->id}_columns
		$this->loader->add_filter( "manage_{$edit_kb_screen_id}_columns", $plugin_admin_menu, 'custom_header_kb', 99 , 1 );
		// add the content for the new column: manage_{$this->screen->taxonomy}_custom_column
		$this->loader->add_filter( "manage_{$kb_taxonomy}_custom_column", $plugin_admin_menu, 'custom_content_kb', 10, 3);
		/*-------------------*/	
		// TAG ADMIN SCREEN 
		/*-------------------*/	
		// adjust the columns header - add the new column header by hooking to: manage_{$screen->id}_columns
		$this->loader->add_filter( "manage_{$edit_doc_tag_screen_id}_columns", $plugin_admin_menu, 'custom_header_doc_tag', 99 , 1 );
		// add the content for the new column: manage_{$this->screen->taxonomy}_custom_column
		$this->loader->add_filter( "manage_{$tag_taxonomy}_custom_column", $plugin_admin_menu, 'custom_content_doc_tag', 10, 3);
		/*-------------------*/	
		// FAQ ADMIN SCREEN 
		/*-------------------*/	
		// adjust the columns header - add the new column header by hooking to: manage_{$screen->id}_columns
		$this->loader->add_filter( "manage_{$edit_faqgroup_screen_id}_columns", $plugin_admin_menu, 'custom_header_faqgroup', 99 , 1 );
		// add the content for the new column: manage_{$this->screen->taxonomy}_custom_column
		$this->loader->add_filter( "manage_{$faqgroup_taxonomy}_custom_column", $plugin_admin_menu, 'custom_content_faqgroup', 10, 3);
		// handle custom forms: admin_post_{$action}
		$action = 'idocs_custom_urls_form'; // defined as action in the form 
		$this->loader->add_action( "admin_post_{$action}", $plugin_admin_menu, 'process_admin_settings_custom_urls_form' );
		/*---------------------------------------*/
		// CUSTOMIZER (DESIGN)	
		/*---------------------------------------*/
		$plugin_customizer = new IDOCS_Customizer();

		// register customizer settings 
		$this->loader->add_action( 'customize_register', $plugin_customizer, 'custom_customize_register');
		/*---------------------------------------*/	
		// DATABASE 
		/*---------------------------------------*/	
		$plugin_db = new IDOCS_Database();

		// register callback function to check if the database structure should be updated after a plugin upgrade 
		$this->loader->add_action( 'plugins_loaded', $plugin_db, 'upgrade_plugin_db_check' );
		// during init, add action to the cron triggered db clean-up event
		$this->loader->add_action( 'init', $plugin_db, 'add_action_for_data_cleanup_event', 2000);
		/*---------------------------------------*/	
		// TAXANOMIES (knowledge-base, category, tag, )
		/*---------------------------------------*/	
		$plugin_taxo = new IDOCS_Taxanomies();
		// register taxanomy (knowledge base, idocs-category-taxo, idocs-tag-taxo)
		// please note! - the priority of adding a taxonomy MUST be higher (which is lower number) than other other action callbacks that are using it.
		$this->loader->add_action( 'init', $plugin_taxo, 'add_custom_taxonomies', 1000 );
		//$this->loader->add_action( 'pre_delete_term', $plugin_taxo, 'prevent_term_deletion_if_posts_exist', 10, 2 );
		//$this->loader->add_action( 'admin_notices', $plugin_taxo, 'show_term_deletion_error_message' );

		// as the url of category, tag and faq-group includes two taxonomies (one of them is kb) --> need to re-order the query to open the right template
		$this->loader->add_action( 'pre_get_posts', $plugin_taxo, 'prioritize_second_taxonomy'  );
		/*---------------------------------------*/
		// KB - Custom Fields
		/*---------------------------------------*/
		// step #1- add custom display fields (metadata) to a new term form. Action hook is {TAXONOMY}_add_form_fields.
		$this->loader->add_action( "{$kb_taxonomy}_add_form_fields", $plugin_taxo, 'add_custom_fields_to_kb' );
		// step #2 - add custom display fields (metadata) to the edit term form. 
		$this->loader->add_action( "{$kb_taxonomy}_edit_form_fields", $plugin_taxo, 'edit_custom_fields_to_kb' );
		// step #3 - save the term metadata field value (new form or edit form) to the database. “created_{taxonomy}” and “edited_{taxonomy}".
		// save the kb term metadata field default value (only for a new kb) to the database. “created_{taxonomy}”.
		$this->loader->add_action( "created_{$kb_taxonomy}", $plugin_taxo, 'save_custom_fields_to_kb' );
		$this->loader->add_action( "edited_{$kb_taxonomy}", $plugin_taxo, 'save_custom_fields_to_kb' );
		$this->loader->add_action( "delete_{$kb_taxonomy}", $plugin_taxo, 'kb_is_deleted', 10, 4 );
		/*---------------------------------------*/
		// Category - Custom Fields
		/*---------------------------------------*/
		// step #1- add custom display fields (metadata) to a new term form. Action hook is {TAXONOMY}_add_form_fields.
		$this->loader->add_action( "{$cat_taxonomy}_add_form_fields", $plugin_taxo, 'add_custom_fields_to_category' );
		// step #2 - add custom display fields (metadata) to the edit term form. 
		$this->loader->add_action( "{$cat_taxonomy}_edit_form_fields", $plugin_taxo, 'edit_custom_fields_to_category' );
		// step #3 - save the term metadata field value (new form or edit form) to the database. “created_{taxonomy}” and “edited_{taxonomy}".
		$this->loader->add_action( "created_{$cat_taxonomy}", $plugin_taxo, 'save_custom_fields_to_new_category' );
		$this->loader->add_action( "edited_{$cat_taxonomy}", $plugin_taxo, 'save_custom_fields_to_existing_category' );
		$this->loader->add_action( "delete_{$cat_taxonomy}", $plugin_taxo, 'category_is_deleted', 10, 4 );
		// save the kb term metadata field default value (only for a new kb) to the database. “created_{taxonomy}”.
		//$this->loader->add_action( "created_{$kb_taxonomy}", $plugin_taxo, 'save_custom_fields_to_kb' );

		// update the idocs-category-taxo and idocs-kb-taxo links
		$this->loader->add_filter( 'term_link', $plugin_taxo, 'taxanomies_permalink', 99 , 3 );
		/*---------------------------------------*/
		// TAG - Custom Fields
		/*---------------------------------------*/
		// custom fields for tags
		$this->loader->add_action( "{$tag_taxonomy}_add_form_fields", $plugin_taxo, 'add_custom_fields_to_tag' );
		$this->loader->add_action( "{$tag_taxonomy}_edit_form_fields", $plugin_taxo, 'edit_custom_fields_to_tag' );
		$this->loader->add_action( "created_{$tag_taxonomy}", $plugin_taxo, 'save_custom_fields_new_tag' );
		$this->loader->add_action( "edited_{$tag_taxonomy}", $plugin_taxo, 'save_custom_fields_existing_tag' );
		/*---------------------------------------*/
		// FAQ GROUP - Custom Fields
		/*---------------------------------------*/
		$this->loader->add_action( "{$faqgroup_taxonomy}_add_form_fields", $plugin_taxo, 'add_custom_fields_to_faqgroup' );
		$this->loader->add_action( "{$faqgroup_taxonomy}_edit_form_fields", $plugin_taxo, 'edit_custom_fields_to_faqgroup' );
		$this->loader->add_action( "created_{$faqgroup_taxonomy}", $plugin_taxo, 'save_custom_fields_new_faqgroup' );
		$this->loader->add_action( "edited_{$faqgroup_taxonomy}", $plugin_taxo, 'save_custom_fields_existing_faqgroup' );
		$this->loader->add_action( "delete_{$faqgroup_taxonomy}", $plugin_taxo, 'faqgroup_is_deleted', 10, 4 );
		/*---------------------------------------*/
		// CRON EVENTS
		/*---------------------------------------*/
		$plugin_cron = new IDOCS_Cron();
		
		/*
		// 1. This will enable WordPress to trigger events based on the required schedules.
		// 2. As the callback function for scheduling the hookes is using the kb taxonomy it must be called AFTER the register taxonomy hook (using priorities)
		// 3. Note! - The actions to process the events are added by other classes
		*/
		
		$this->loader->add_action( 'init', $plugin_cron, 'schedule_custom_wpcron_events', 2000 );
		// remove a schedule wpcron event when a kb is deleted: delete_{$taxonomy}
		$this->loader->add_action( "delete_{$kb_taxonomy}", $plugin_cron, 'remove_wp_cron_event_when_kb_is_deleted', 10, 4 );
		/*---------------------------------------*/
		// CUSTOM POST TYPE (idocs_content)
		/*---------------------------------------*/		
		$plugin_cpt = new IDOCS_CPT();

		// creates a custom post type (idocs_content)
		$this->loader->add_action( 'init', $plugin_cpt, 'add_custom_post_type_idocs_content', 1001 );
		// update the rewrite rules (permalinks) after adding the cpt and taxonomies 
		//$this->loader->add_action( 'init', $plugin_cpt, 'flush_rewrite_rules_after_new_cpt', 99999 );
		// update the idocs_content custom post type link  
		$this->loader->add_filter( 'post_type_link',    $plugin_cpt, 'content_posts_permalink', 99 , 2 );
		$this->loader->add_action( 'wp_trash_post', $plugin_cpt, 'clear_related_cache_for_content_items');
		/*---------------------------------------*/	
		// Meta Post Fields (replacing meta-boxes)
		/*---------------------------------------*/	
		$plugin_postmeta = new IDOCS_PostMetaFields();
		// register the required meta fields for the cpt. 
		$this->loader->add_action( 'init', $plugin_postmeta, 'register_post_meta_fields', 1002);
		// When the cpt is saved, update the taxonomies metadata so columns content will be filled in the list of documents admin screen.
		// fires actions after a post, its terms and also meta data!! has been saved.
		$this->loader->add_action( 'wp_after_insert_post', $plugin_postmeta, 'update_content_post');
		/*---------------------------------------*/
		// REST APIs 
		/*---------------------------------------*/	
		// register custom REST APIs endpoints (5e5827093d)
		$plugin_admin_restapi = new IDOCS_Custom_RestAPIs();

		// admin - categories per kb, list of kbs
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_categories_per_kb_with_levels' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_categories_per_kb_with_hierarchical' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_faq_groups_per_kb' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_tags_per_kb' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_kbs_list' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_color_schemes_list' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_color_schemes_list_detailed' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_block_default_settings' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_content_types' );
		$this->loader->add_action('rest_api_init', $plugin_admin_restapi, 'register_custom_route_check_category_name' );
		$this->loader->add_action('rest_api_init', $plugin_admin_restapi, 'register_custom_route_update_kb_global_color_scheme' );

		// analytics 
		// analytics - get search data 
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_most_popular_searches' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_search_success_rate' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_searches_per_day' );
		// analytics - get rating data 
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_rating_per_day' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_overall_ratings' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_rating_per_document' );
		// analytics - get visit data 
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_overall_visits' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_visits_per_document' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_overall_searches' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_top_visits_by_country' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_feedback_kpis' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_feedback_list' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_readability_score_list' );

		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_top_content_visits' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_top_visited_tags' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_top_visited_categories' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_top_countries_by_content_visits' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_content_visits_per_day' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_total_content_visits' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_top_countries_by_search_queries' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_top_countries_by_content_ratings' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_kb_view_visits_per_day' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin_restapi, 'register_custom_route_popular_searches_current_results' );
		$this->loader->add_action('wp_ajax_update_rating_score_kpi', $plugin_admin_restapi, 'update_rating_score_kpi'); 
	
		/*---------------------------------------*/
		// icons
		$this->loader->add_action('rest_api_init', $plugin_admin_restapi, 'register_custom_route_get_icons' );
		/*---------------------------------------*/
		// AJAX
		$this->loader->add_action('wp_ajax_update_div_kb_filter', $plugin_admin_restapi, 'update_div_kb_filter'); 
		/*---------------------------------------*/
		$plugin_update = new IDOCS_Plugin_Update();

		// clean up design settings cache after software upgrade 
		$this->loader->add_action('upgrader_process_complete', $plugin_update, 'plugin_upgrade_process', 10, 2); 
		/*---------------------------------------*/
		/*
		// for logged in users
		$this->loader->add_action('wp_ajax_update_div_content_cards', $plugin_admin_restapi, 'update_div_content_cards'); 
		// for non-logged in users
		$this->loader->add_action('wp_ajax_nopriv_update_div_content_cards', $plugin_admin_restapi, 'update_div_content_cards'); 
		$this->loader->add_action('wp_ajax_update_div_content_breadcrumb', $plugin_admin_restapi, 'update_div_content_breadcrumb'); 
		$this->loader->add_action('wp_ajax_nopriv_update_div_content_breadcrumb', $plugin_admin_restapi, 'update_div_content_breadcrumb'); 
		$this->loader->add_action('wp_ajax_update_div_content_faqs', $plugin_admin_restapi, 'update_div_content_faqs'); 
		$this->loader->add_action('wp_ajax_nopriv_update_div_content_faqs', $plugin_admin_restapi, 'update_div_content_faqs'); 
		*/
		/*---------------------------------------*/
		
	}
	/*---------------------------------------------------------------------------------------*/
	// Register all of the hooks related to the PUBLIC-facing functionality
	private function define_public_hooks() {

		/*---------------------------------------*/	
		// PUBLIC FRONTEND
		/*---------------------------------------*/	
		$plugin_public = new IDOCS_Public_Frontend();

		// register public styles, scripts and block scripts
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// register a filter hook to the path of the required custom archieve template 
		$this->loader->add_filter( 'archive_template', $plugin_public, 'load_custom_archive_template', 50);
		$this->loader->add_filter( 'single_template', $plugin_public, 'load_custom_single_template', 50);
		
		$this->loader->add_action( 'wp_after_insert_post', $plugin_public, 'reset_shortcodes_per_page_cache');

		// add FAQs schema (JSON-LD markup) for SEO 
		//$this->loader->add_action( 'wp_head', $plugin_public, 'add_faq_json_ld_to_head');
		
		// add Breadcrumbs schema (JSON-LD markup) for SEO (WIP)
		//$this->loader->add_action( 'wp_head', $plugin_public, 'add_breadcrumbs_json_ld_to_head');
		
		/*---------------------------------------*/	
		// Blocks (frontend)
		/*---------------------------------------*/
		$plugin_public_blocks = new IDOCS_Blocks();
		//$this->loader->add_action( 'enqueue_block_assets', $plugin_admin, 'enqueue_block_assets' );
		$this->loader->add_filter( 'render_block', $plugin_public_blocks, 'conditionally_enqueue_block_assets', 10, 2 );
		/*---------------------------------------*/	
		// SHORTCODES
		/*---------------------------------------*/	
		$plugin_public_sc = new IDOCS_Shortcodes();
		
		// register shortcodes - shortcode names should be globally unique! (using prefixing)
		$this->loader->add_shortcode( 'idocs_kb_view', $plugin_public_sc, 'idocs_kb_view' );
		$this->loader->add_shortcode( 'idocs_live_search', $plugin_public_sc, 'idocs_live_search' );
		$this->loader->add_shortcode( 'idocs_categories_cards', $plugin_public_sc, 'idocs_categories_cards' );
		$this->loader->add_shortcode( 'idocs_cards_root', $plugin_public_sc, 'idocs_cards_root' );
		$this->loader->add_shortcode( 'idocs_cards_category_with_docs', $plugin_public_sc, 'idocs_cards_category_with_docs' );
		$this->loader->add_shortcode( 'idocs_cards_category_no_docs', $plugin_public_sc, 'idocs_cards_category_no_docs' );
		$this->loader->add_shortcode( 'idocs_kb_faqs', $plugin_public_sc, 'idocs_kb_faqs' );
		$this->loader->add_shortcode( 'idocs_category_faqs', $plugin_public_sc, 'idocs_category_faqs' );
		$this->loader->add_shortcode( 'idocs_faqs_group', $plugin_public_sc, 'idocs_faqs_group' );
		$this->loader->add_shortcode( 'idocs_document_view', $plugin_public_sc, 'idocs_document_view' );
		$this->loader->add_shortcode( 'idocs_breadcrumbs', $plugin_public_sc, 'idocs_breadcrumbs' );
		$this->loader->add_shortcode( 'idocs_kb_breadcrumbs', $plugin_public_sc, 'idocs_kb_breadcrumbs' );
		$this->loader->add_shortcode( 'idocs_sidebar_navigator', $plugin_public_sc, 'idocs_sidebar_navigator' );
		$this->loader->add_shortcode( 'idocs_document_likes', $plugin_public_sc, 'idocs_document_likes' );
		$this->loader->add_shortcode( 'idocs_document_feedback', $plugin_public_sc, 'idocs_document_feedback' );
		$this->loader->add_shortcode( 'idocs_document_tags', $plugin_public_sc, 'idocs_document_tags' );
		$this->loader->add_shortcode( 'idocs_related_documents', $plugin_public_sc, 'idocs_related_documents' );
		/*---------------------------------------*/
		// REST APIs
		/*---------------------------------------*/
		// register custom REST APIs endpoints
		$plugin_public_restapi = new IDOCS_Custom_RestAPIs();
		
		// handle frontend search requests 
		$this->loader->add_action( 'rest_api_init', $plugin_public_restapi, 'register_custom_route_search_keyword' );
		$this->loader->add_action( 'rest_api_init', $plugin_public_restapi, 'register_custom_route_get_search_parameters' );

		// save users data - search
		$this->loader->add_action( 'rest_api_init', $plugin_public_restapi, 'register_custom_route_save_search_result' );
		/*---------------------------------------*/
	}
	/*---------------------------------------------------------------------------------------*/
	// Run the loader to execute all of the hooks with WordPress.
	public function run() {
		// the loader run method is performing a batch process for registering all actions, filters and shortcodes hooks 
		$this->loader->run();
	}
	/*---------------------------------------------------------------------------------------*/
	public static function check_plugin_slug() {

		// Get the directory of the current file
		$plugin_dir_path = plugin_dir_path(__FILE__);
		// Use plugin_basename to normalize the path and get the plugin slug
		$plugin_slug = plugin_basename($plugin_dir_path);
		// Extract the plugin slug using plugin_basename
		$plugin_slug = plugin_basename($plugin_dir_path);
		// Remove the ".php" extension if present
		$plugin_slug = str_replace('.php', '', $plugin_slug);
		// Get the directory name of the plugin file
		$directory_name = dirname($plugin_slug);
		// Extract just the plugin slug (last part of the path)
		$plugin_slug = basename($directory_name);
		//do_action( 'qm/debug', $plugin_slug);
		$hashed = hash('sha256', $plugin_slug);
		// Trim the hash to a fixed length (e.g., 8 characters)
		$trimmed_hash = substr($hashed, 0, 10);

		if ( $trimmed_hash == "1fb41bb74f" ) {
			return true;
		} else {
			return false;
		}

	}
	/*---------------------------------------------------------------------------------------*/
}
// 
/*5e5827093d------------------------------------------------------------------------------*/
// https://developer.wordpress.org/apis/security/escaping/
// https://codebriefly.com/how-to-create-taxonomy-term-meta-data-wordpress/
// https://kinsta.com/blog/wp-enqueue-scripts/


