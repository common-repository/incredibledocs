<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* > Define the plugin admin main & sub menu, highlight the selected menu item
   > open the relevant HTML setting page 
   > Apply custom filters to different admin pages
*/	
/*---------------------------------------------------------------------------------------*/
class IDOCS_Admin_Menu {

	private $menu_slug;
	/*---------------------------------------------------------------------------------------*/	
	public function __construct( ) {
		
		$this->menu_slug = IDOCS_MENU_SLUG;
	
	}
	/*---------------------------------------------------------------------------------------*/
	/* Configure and return the properties of plugin menu items */
	public function plugin_menu_list() {
			
		/*--------------------------------------------*/
		$my_post_type = 'idocs_content'; 
		$my_kb_taxo = 'idocs-kb-taxo';
		$my_tag_taxo = 'idocs-tag-taxo';
		$my_cat_taxo = 'idocs-category-taxo';
		$my_faq_taxo = 'idocs-faq-group-taxo';
		$my_customizer_panel = 'idocs_customizer_panel';
		$plural = "idocs_contents";
		/*--------------------------------------------*/
		$admin_pages = array(

			/*--------------------------------------------*/
			// Plugin MAIN MENU option 
			/*--------------------------------------------*/
			'plugin_main_menu' => array(

				'page_title' => 'IncredibleDocs',
				'menu_title' => 'IncredibleDocs',
				'capability' => 'edit_' . $plural,
				'menu_slug'  => $this->menu_slug,
				'callback'   => array($this, 'dashboard_page_html'),
				'icon_url'   =>  'dashicons-image-filter',
				'position'   => 5,
			),
			/*--------------------------------------------*/
			// Plugin sub-menu options
			/*--------------------------------------------*/
			'dashboard' => array (

				'parent_slug' =>  $this->menu_slug,
				'menu_title'  => __( 'Dashboard', 'incredibledocs' ),
				'capability'  => 'edit_' . $plural,
				// using the main menu "menu-slug" value to prevent dedicated sub-menu with the main-menu name
				'menu_slug'  => $this->menu_slug,
				'position'   => 1,
			),
			/*--------------------------------------------*/
			'all_docs' => array(

				'parent_slug' =>  $this->menu_slug,
				'menu_title'  => __( 'Content', 'incredibledocs' ),
				'capability'  => 'edit_' . $plural,
				'menu_slug'   => 'edit.php?post_type=' . $my_post_type,
				'callback'    => null,
				'position'   => 2,
			),
			/*--------------------------------------------*/
			'categories' => array(

                'parent_slug' => $this->menu_slug,
                'menu_title'  => __('Categories', 'incredibledocs'),
                'capability'  => 'edit_' . $plural,
                'menu_slug'   => 'edit-tags.php?post_type='. $my_post_type . '&taxonomy=' . $my_cat_taxo,
				'callback'    => null,
				'position'   => 3,
            ),
			/*--------------------------------------------*/
			'tags' => array(

                'parent_slug' => $this->menu_slug,
                'menu_title'  => __('Tags', 'incredibledocs'),
                'capability'  => 'edit_' . $plural,
				'menu_slug'   => 'edit-tags.php?post_type='. $my_post_type . '&taxonomy=' . $my_tag_taxo,
				'callback'    => null,
				'position'   => 4,
            ),
			/*--------------------------------------------*/
			'faqs' => array(

                'parent_slug' => $this->menu_slug,
                'menu_title'  => __('FAQ Groups', 'incredibledocs'),
                'capability'  => 'edit_' . $plural,
				'menu_slug'   => 'edit-tags.php?post_type='. $my_post_type . '&taxonomy=' . $my_faq_taxo,
				'callback'    => null,
				'position'   => 5,
            ),
			
			/*--------------------------------------------*/
            'knowledge-bases' => array(
                'parent_slug' => $this->menu_slug,
                'menu_title'  => __('Knowledge Bases', 'incredibledocs'),
			  	'capability'  => 'edit_' . $plural,
				'menu_slug'   => 'edit-tags.php?post_type=' . $my_post_type . '&taxonomy=' . $my_kb_taxo,
				'callback'    => null,
				'position'   => 6,
            ),
			/*--------------------------------------------*/
            'settings' => array(
                'parent_slug' => $this->menu_slug,
                'menu_title'  => __('Settings', 'incredibledocs'),
                // "manage_options" capability is associated with the administrator role.
				// so it will limit the access to the setting just for admin users. 
				'capability'  => 'manage_options', // only for Admins
                'menu_slug'   => 'idocs-settings',
				// custom html page
                'callback'    => array($this, 'settings_page_html'),
				'position'   => 15,
            ),
			/*--------------------------------------------*/
			'analytics' => array(

				'parent_slug' => $this->menu_slug,
				'page_title'  => __('Analytics', 'incredibledocs'),
				'menu_title'  => __('Analytics', 'incredibledocs'),
				'capability'  => 'edit_' . $plural,
				'menu_slug'   => 'idocs-analytics',
				'callback'    => array($this, 'analytics_html'),
				'position'    => 8,
			),
			/*--------------------------------------------*/
			'upgrade_to_pro' => array(
                'parent_slug' => $this->menu_slug,
                'menu_title'  => __('Upgrade to Pro', 'incredibledocs'),
                'capability'  => 'edit_' . $plural,
                'menu_slug'   => 'https://incrediblewp.io/incredibledocs-pro/',
				'position'   => 20,
			),
			/*--------------------------------------------*/
		);
		/*--------------------------------------------*/
		// apply_filter - let the pro version add additional menu options
		return apply_filters('idocspro_menu_list', $admin_pages);
	}
	/*---------------------------------------------------------------------------------------*/
	// A callback function hooked to the admin_menu hook - add the configured menu options (5e5827093d)
	public function adding_admin_menu() {

		foreach( $this->plugin_menu_list() as $key => $menu ) {
			// only for the main menu option
			if( 'plugin_main_menu' === $key ) {

				add_menu_page(
					$menu['page_title'], 
					$menu['menu_title'],
					$menu['capability'], 
					$menu['menu_slug'], 
					$menu['callback'],
					$menu['icon_url'], 
					$menu['position'],
				);
			
			} 
			/*--------------------------------------------*/
			// all sub-menu options 
			else {

				$this->menu_slug = isset( $menu['parent_slug'] ) ? $menu['parent_slug'] : '';
				$page_title  = isset( $menu['page_title'] ) ? $menu['page_title'] : '';
				$menu_title  = isset( $menu['menu_title'] ) ? $menu['menu_title'] : '';
				$capability  = isset( $menu['capability'] ) ? $menu['capability'] : '';
				$menu_slug   = isset( $menu['menu_slug'] ) ? $menu['menu_slug'] : '';
				$callback    = isset( $menu['callback'] ) ? $menu['callback'] : '';
				$position    = isset( $menu['position'] ) ? $menu['position'] : '';
				/*--------------------------------------------*/
				add_submenu_page(
					$this->menu_slug, $page_title, $menu_title, 
					$capability, $menu_slug, $callback, $position
				);
				//do_action( 'qm/debug', $page_hook_suffix );
			}
		}
	}
	/*---------------------------------------------------------------------------------------*/	
	// callback - filters to highlight the selected MAIN admin menu when a sub-menu that is not using admin.php is selected. 
	public function highlight_selected_admin_menu( $parent_file ) {
		
		global $submenu_file, $current_screen;
		//do_action( 'qm/debug', $parent_file );
		//do_action( 'qm/debug', $current_screen->id );
		/*--------------------------------------------*/
		if ( $current_screen->post_type == 'idocs_content' )

			if( $current_screen->id === 'edit-idocs-category-taxo' || 
			    $current_screen->id === 'edit-idocs-tag-taxo' ||
				$current_screen->id === 'edit-idocs-kb-taxo' || 
				$current_screen->id === 'edit-idocs-faq-group-taxo' ) {
			
					$parent_file = $this->menu_slug;
			}
		/*--------------------------------------------*/
		return $parent_file;
	}
	/*---------------------------------------------------------------------------------------*/
	// callback - filters to highlight the selected admin submenu that is not using admin.php
	public function highlight_selected_admin_submenu( $submenu_file ) {

		global $current_screen, $pagenow;
		//do_action( 'qm/debug', $current_screen->id );
		/*--------------------------------------------*/
        if ( $current_screen->post_type == 'idocs_content' ) {

			//do_action( 'qm/debug', $pagenow );
            if ( $pagenow == 'edit.php' ) {
                $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            }
            /*--------------------------------------------*/
            if( $current_screen->id === 'edit-idocs-category-taxo' ) {
                $submenu_file = 'edit-tags.php?post_type=idocs_content&taxonomy=idocs-category-taxo';
            }
			/*--------------------------------------------*/
            if( $current_screen->id === 'edit-idocs-tag-taxo' ) {
                $submenu_file = 'edit-tags.php?post_type=idocs_content&taxonomy=idocs-tag-taxo';
            }
			/*--------------------------------------------*/
            if( $current_screen->id === 'edit-idocs-kb-taxo' ) {
                $submenu_file = 'edit-tags.php?post_type=idocs_content&taxonomy=idocs-kb-taxo';
            }
			/*--------------------------------------------*/
			if( $current_screen->id === 'edit-idocs-faq-group-taxo' ) {
                $submenu_file = 'edit-tags.php?post_type=idocs_content&taxonomy=idocs-faq-group-taxo';
            }
        }
		/*--------------------------------------------*/
        return $submenu_file;
	}
	/*---------------------------------------------------------------------------------------*/
	// callback function to display the admin main setting page 
	public function settings_page_html() {

		// return views
		require_once IDOCS_DIR_PATH . 'admin/pages/idocs-admin-page-settings.php';
	}
	/*---------------------------------------------------------------------------------------*/
	// callback function to display the dashboard page 
	public function dashboard_page_html() {
		// return views
		require_once IDOCS_DIR_PATH . 'admin/pages/idocs-admin-page-dashboard.php';
	}
	/*---------------------------------------------------------------------------------------*/
	// callback function to display the analytics page
	public function analytics_html() {

		// return views
		require_once IDOCS_DIR_PATH . 'admin/pages/idocs-admin-page-analytics.php';

	}
	/*---------------------------------------------------------------------------------------*/
	// update the list of documents header (hooked to a filter)
	public function update_document_admin_columns( $columns ) {

		//do_action( 'qm/debug', $columns );
		$new_columns = array(

			'cb' => '<input type="checkbox" />',
			'title' => 'Title',
			// the following fields were added to the cpt when registering the related taxonomies, each field with a prefix of "taxonomy-{taxonomy_name}"
			// therefore, no need to manually update the columns content during admin screen display.
			'taxonomy-idocs-content-type-taxo' => __('Content Type', 'incredibledocs'), 
			'taxonomy-idocs-kb-taxo' => __('Knowledge Base', 'incredibledocs'),
			'taxonomy-idocs-category-taxo' => __('Category', 'incredibledocs'),
			'taxonomy-idocs-faq-group-taxo' => __('FAQs Group', 'incredibledocs'),
			'taxonomy-idocs-tag-taxo' => __('Tags', 'incredibledocs'),
			'idocs-cpt-display-order' => __('Display Order', 'incredibledocs'),
			'author' => __('Author', 'incredibledocs'),
			'date' => __('Date', 'incredibledocs'),
			'idocs-cpt-configured' => __('Configured', 'incredibledocs'), 
		//	'comments' => '<span class="vers comment-grey-bubble" title="Comments" aria-hidden="true"></span><span class="screen-reader-text">Comments</span>'
		
		);
		/*--------------------------------------------*/
		return $new_columns;
	}
	/*---------------------------------------------------------------------------------------*/
	// add the content for the custom added column/s in list of content items 
	public function custom_content_document( $column_name, $post_id ) {

		//do_action( 'qm/debug', $column_name );
		switch ( $column_name ) {

			case 'idocs-cpt-display-order':
				//$display_order = get_post_meta($post_id, 'idocs-content-display-order-meta', true);
				$display_order = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-display-order-meta');
				if ($display_order != null) {

					echo esc_html($display_order);

				}
				break;
			/*--------------------------------------------*/
			case 'idocs-cpt-configured':

				$configured = true;
				$content_type_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-type-meta');
				if ( $content_type_id == null ) {

					$configured = false;
					
				}
				else {
					$content_term = get_term_by('id', $content_type_id, 'idocs-content-type-taxo');
					switch ( $content_term->name ) {

						/*--------------------------------------------*/
						case "Document":
							
							$category_terms = IDOCS_Taxanomies::get_the_terms_caching( $post_id, 'idocs-category-taxo' );
							$kb_terms = IDOCS_Taxanomies::get_the_terms_caching( $post_id, 'idocs-kb-taxo' );
				
							if ($category_terms == null || $kb_terms == null) {

								$configured = false;

							}
							break;
						/*--------------------------------------------*/
						case "Link":

							$link = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-link-meta');
							$category_terms = IDOCS_Taxanomies::get_the_terms_caching( $post_id, 'idocs-category-taxo' );
							$kb_terms = IDOCS_Taxanomies::get_the_terms_caching( $post_id, 'idocs-kb-taxo' );
							if (empty($link) || $category_terms == null || $kb_terms == null) {

								$configured = false;

							}
							break;
						/*--------------------------------------------*/
						case "FAQ":
							$faqgroup_terms = IDOCS_Taxanomies::get_the_terms_caching( $post_id, 'idocs-faq-group-taxo' );		
							if ( !$faqgroup_terms ) {
								
								$configured = false;
								
							}	
							break;
						/*--------------------------------------------*/
						case "Internal-Video":
							$video_url = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-video-url-meta');
							$category_terms = IDOCS_Taxanomies::get_the_terms_caching( $post_id, 'idocs-category-taxo' );
							$kb_terms = IDOCS_Taxanomies::get_the_terms_caching( $post_id, 'idocs-kb-taxo' );
							if (empty( $video_url ) || $category_terms == null || $kb_terms == null) {

								$configured = false;

							}
							break;
						/*--------------------------------------------*/
						case "YouTube-Video":
							$video_url = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-video-yturl-meta');
							$category_terms = IDOCS_Taxanomies::get_the_terms_caching( $post_id, 'idocs-category-taxo' );
							$kb_terms = IDOCS_Taxanomies::get_the_terms_caching( $post_id, 'idocs-kb-taxo' );
							if (empty( $video_url ) || $category_terms == null || $kb_terms == null) {

								$configured = false;

							}
							break;  
					}
				}
				/*--------------------------------------------*/	
				if ( $configured ) {
					IDOCS_ICONS::echo_icon_svg_tag('check', 20, 20, 0.7, 'green');
				}
				else {
					IDOCS_ICONS::echo_icon_svg_tag('circle-xmark', 20, 20, 0.7, 'red');
				}
				break;
			/*--------------------------------------------*/
		}
	}
	/*---------------------------------------------------------------------------------------*/
	// making the display order a sortable column in the header (it is not adding the query logic)
	public function custom_columns_sortable( $columns ) {

		/*--------------------------------------------*/
		// key is the column ID and the value is the column name that will be displayed in the table header.
		$columns['idocs-cpt-display-order'] = 'idocs-cpt-display-order'; 

		$columns['taxonomy-idocs-content-type-taxo'] = 'taxonomy-idocs-content-type-taxo'; 
		$columns['taxonomy-idocs-category-taxo'] = 'taxonomy-idocs-category-taxo'; 
		$columns['taxonomy-idocs-faq-group-taxo'] = 'taxonomy-idocs-faq-group-taxo'; 
		/*--------------------------------------------*/
		return $columns;
	}
	/*---------------------------------------------------------------------------------------*/
	// custom query to handle sorting by custom field
	public function custom_sort_by_custom_field( $query ) {

		/*--------------------------------------------*/
		if ( !is_admin() || !$query->is_main_query() || $query->get('post_type') != 'idocs_content' ) {
			return;
		}
		/*--------------------------------------------*/
		$orderby = $query->get('orderby');
		/*--------------------------------------------*/
		if ( 'idocs-cpt-display-order' === $orderby ) { // Replace 'custom_field_name' with your actual custom field name
			$query->set('meta_key', 'idocs-content-display-order-meta'); // Replace 'custom_field_name' with your actual custom field name
			$query->set('orderby', 'meta_value_num'); // Use 'meta_value_num' for numeric values
		}
		/*--------------------------------------------*/
		if ( 'taxonomy-idocs-content-type-taxo' === $orderby ) { // Replace 'custom_field_name' with your actual custom field name
			$query->set('meta_key', 'idocs-content-type-meta'); // Replace 'custom_field_name' with your actual custom field name
			$query->set('orderby', 'meta_value_num'); // Use 'meta_value_num' for numeric values
		}
		/*--------------------------------------------*/
		if ( 'taxonomy-idocs-category-taxo' === $orderby ) { // Replace 'custom_field_name' with your actual custom field name
			$query->set('meta_key', 'idocs-content-category-meta'); // Replace 'custom_field_name' with your actual custom field name
			$query->set('orderby', 'meta_value_num'); // Use 'meta_value_num' for numeric values
		}
		/*--------------------------------------------*/
		if ( 'taxonomy-idocs-faq-group-taxo' === $orderby ) { // Replace 'custom_field_name' with your actual custom field name
			$query->set('meta_key', 'idocs-faq-group-meta'); // Replace 'custom_field_name' with your actual custom field name
			$query->set('orderby', 'meta_value_num'); // Use 'meta_value_num' for numeric values
		}
		
	}
	/*---------------------------------------------------------------------------------------*/
	// add custom filters to the list of content table (hooked to a filter)
	public function custom_admin_taxonomies_filters( $post_type ) {
		
		// Apply this to a specific CPT
		if ( 'idocs_content' !== $post_type ) {
			return; 
		}
		/*------------------------------------------------*/
		$taxonomies_slugs = array(

			'idocs-content-type-taxo',
			'idocs-kb-taxo',
			'idocs-category-taxo',
			'idocs-faq-group-taxo',
		);
		/*------------------------------------------------*/
		// loop through the taxonomy filters array
		foreach( $taxonomies_slugs as $slug ){

			$taxonomy = get_taxonomy( $slug );
			$selected = '';
			// if the current page is already filtered, get the selected term slug
//			$selected = isset( $_REQUEST[ $slug ] ) ? sanitize_text_field($_REQUEST[ $slug ]) : '';
			$selected = get_query_var($slug, '');
			// update the relevant class name (for custom css)
			switch ($slug) {

				/*------------------------------------------------*/
				case 'idocs-content-type-taxo':
					$class = 'idocs-admin-filter-content-type';
				
					wp_dropdown_categories( array(

						'show_option_all' =>  $taxonomy->labels->all_items,
						'taxonomy'        =>  $slug,
						'name'            =>  $slug,
						'orderby'         =>  'id',
						'value_field'     =>  'slug',
						'selected'        =>  $selected,
						'hierarchical'    =>  false,
						'show_count'      =>  false, // Show number of post in parent term
						'hide_empty'      =>  false, // don't show posts w/o terms?
						'class'           =>  $class,
						'hide_if_empty'   =>  true,

					) );
					
					break;
				/*------------------------------------------------*/	
				case 'idocs-kb-taxo':
					$class = 'idocs-admin-filter-kb';
					// render a dropdown for this taxonomy's terms
					wp_dropdown_categories( array(

						'show_option_all' =>  $taxonomy->labels->all_items,
						'taxonomy'        =>  $slug,
						'name'            =>  $slug,
						'orderby'         =>  'name',
						'value_field'     =>  'slug',
						'selected'        =>  $selected,
						'hierarchical'    =>  true,
						'show_count'      =>  false, // Show number of post in parent term
						'hide_empty'      =>  false, // don't show posts w/o terms?
						'class'           =>  $class,
						'hide_if_empty'   => true,
					) );
					break;
				/*------------------------------------------------*/
				case 'idocs-category-taxo':
					$class = 'idocs-admin-filter-category';
					// if the current page is already filtered with a kb, get the selected kb term slug
					//$kb_slug = isset( $_REQUEST[ 'idocs-kb-taxo' ] ) ? sanitize_text_field($_REQUEST[ 'idocs-kb-taxo' ]) : null;
					$kb_slug = get_query_var('idocs-kb-taxo', null);
					if ( $kb_slug ) { // specific kb was already selected 

						//$kb_term = get_term_by('slug', $kb_slug, 'idocs-kb-taxo');
						$kb_term = IDOCS_Taxanomies::get_specific_kb_term_by_slug_caching($kb_slug);
						// render a dropdown for this taxonomy's terms
						wp_dropdown_categories( array(

							'show_option_all' =>  $taxonomy->labels->all_items,
							'taxonomy'        =>  $slug,
							'name'            =>  $slug,
							'orderby'         =>  'name',
							'value_field'     =>  'slug',
							'selected'        =>  $selected,
							'hierarchical'    =>  true,
							'show_count'      =>  false, // Show number of post in parent term
							'hide_empty'      =>  false, // don't show posts w/o terms?
							'class'           =>  $class,
							'hide_if_empty'   => true,
							// use the kb_term id to filter categories related to that kb
							'meta_key'   => 'idocs-category-taxo-kb',
							'meta_value' => $kb_term->term_id
						) );
					}
					else {
						wp_dropdown_categories( array(

							'show_option_all' =>  $taxonomy->labels->all_items,
							'taxonomy'        =>  $slug,
							'name'            =>  $slug,
							'orderby'         =>  'name',
							'value_field'     =>  'slug',
							'selected'        =>  $selected,
							'hierarchical'    =>  true,
							'show_count'      =>  false, // Show number of post in parent term
							'hide_empty'      =>  false, // don't show posts w/o terms?
							'class'           =>  $class,
							'hide_if_empty'   => true,
						) );
					}
					break;
				/*------------------------------------------------*/
				case 'idocs-faq-group-taxo':
						$class = 'idocs-admin-filter-faq-group';
						// if the current page is already filtered with a kb, get the selected kb term slug
						//$kb_slug = isset( $_REQUEST[ 'idocs-kb-taxo' ] ) ? sanitize_text_field($_REQUEST[ 'idocs-kb-taxo' ]) : null;
						$kb_slug = get_query_var('idocs-kb-taxo', null);
						if ( $kb_slug ) { // specific kb was already selected 
	
							//$kb_term = get_term_by('slug', $kb_slug, 'idocs-kb-taxo');
							$kb_term = IDOCS_Taxanomies::get_specific_kb_term_by_slug_caching($kb_slug);

							// render a dropdown for this taxonomy's terms
							wp_dropdown_categories( array(
	
								'show_option_all' =>  $taxonomy->labels->all_items,
								'taxonomy'        =>  $slug,
								'name'            =>  $slug,
								'orderby'         =>  'name',
								'value_field'     =>  'slug',
								'selected'        =>  $selected,
								'hierarchical'    =>  false,
								'show_count'      =>  false, // Show number of post in parent term
								'hide_empty'      =>  false, // don't show posts w/o terms?
								'class'           =>  $class,
								'hide_if_empty'   => true,
								// use the kb_term id to filter categories related to that kb
								'meta_key'   => 'idocs-faq-group-taxo-kb',
								'meta_value' => $kb_term->term_id
							) );
						}
						else {
							wp_dropdown_categories( array(
	
								'show_option_all' =>  $taxonomy->labels->all_items,
								'taxonomy'        =>  $slug,
								'name'            =>  $slug,
								'orderby'         =>  'name',
								'value_field'     =>  'slug',
								'selected'        =>  $selected,
								'hierarchical'    =>  false,
								'show_count'      =>  false, // Show number of post in parent term
								'hide_empty'      =>  false, // don't show posts w/o terms?
								'class'           =>  $class,
								'hide_if_empty'   => true,
							) );
						}
						break;
			};
		}
	}
	/*---------------------------------------------------------------------------------------*/
	// filter the list of categories or faq groups after the user selected a knowledge-base and click on filter.
	public function filter_list_of_terms ( $args, $taxonomies ) {
		
		/*--------------------------------------------*/
		// modify the query only if it is admin and main query.
		if( ! ( is_admin() ) AND is_main_query()) { 
			return $args;
		}
		/*------------------------------------------------*/
		$categories_filter = in_array('idocs-category-taxo', (array) $taxonomies);
		$faqgroups_filter = in_array('idocs-faq-group-taxo', (array) $taxonomies);
		$tags_filter = in_array('idocs-tag-taxo', (array) $taxonomies);
		/*------------------------------------------------*/
		// Check if "idocs-category-taxo" or "idocs-faq-group-taxo" is in the array of queried taxonomies
		if ( (! $categories_filter) && (! $faqgroups_filter) && (! $tags_filter)) {
			return $args;
		}
		//do_action( 'qm/debug', $taxonomies);
		/*------------------------------------------------*/
		//do_action( 'qm/debug', $_REQUEST['selected_kb']);
		//do_action( 'qm/debug', $kb_id);
		$kb_id = null;
		if( isset($_REQUEST['selected_kb'] )) {

				$kb_id =  sanitize_text_field( $_REQUEST['selected_kb'] );

			
		}
		// if a knowledge-base was selected (checking the super global $_REQUEST)
		//if( isset($_REQUEST['selected_kb']) &&  0 != $_REQUEST['selected_kb']) {
		/*------------------------------------------------*/
		if( $kb_id ) {

			//$kb_id =  sanitize_text_field( $_REQUEST['selected_kb'] );
			// update the arguments with a dedidcated meta query with the knowledge key value
			/*--------------------------------------------*/
			if ( $categories_filter ) {
				$args['meta_query'] =  array(

					[
						'key' => 'idocs-category-taxo-kb',
						'value' => $kb_id
					]
				);
			}
			/*--------------------------------------------*/
			if ( $faqgroups_filter ) {

				$args['meta_query'] =  array(

					[
						'key' => 'idocs-faq-group-taxo-kb',
						'value' => $kb_id
					]
				);
			}
			/*--------------------------------------------*/
			if ( $tags_filter ) {

				$args['meta_query'] =  array(

					[
						'key' => 'idocs-tag-taxo-kb',
						'value' => $kb_id
					]
				);
			}
		}
		/*------------------------------------------------*/
		return $args;
	}
	/*---------------------------------------------------------------------------------------*/
	// update the header table for the custom taxanomy - "idocs-category-taxo"
	public function custom_header_doc_category( $columns ) {

		// before changing the header
		// do_action( 'qm/debug', $columns );

		$new_columns = array(

			'cb' => '<input type="checkbox" />',
			'name' => __('Name', 'incredibledocs'),
			// custom column - content will be handled by the "custom_content_xxxx" method. 
			// please note - this is just the column name and it has nothing to do with the actual meta field name.
			'idocs-category-taxo-id' => __('ID', 'incredibledocs'),
			'idocs-category-taxo-icon' => __('Icon', 'incredibledocs'),
			'idocs-category-taxo-order' => __('Order', 'incredibledocs'),
			'idocs-category-taxo-kb' => __('KB', 'incredibledocs'), // custom meta-data
			'idocs-category-taxo-access-type' => __('Access Type', 'incredibledocs'), // custom meta-data
			'description' => __('Description', 'incredibledocs'),
			'slug' => 'Slug',
			'posts' => __('Count', 'incredibledocs'),

		);
		/*------------------------------------------------*/
		return $new_columns;
	}
	/*---------------------------------------------------------------------------------------*/
	// update the header table for the custom taxanomy - "idocs-tag-taxo"
	public function custom_header_doc_tag( $columns ) {

		// before changing the header
		//do_action( 'qm/debug', $columns );
		$new_columns = array(

			'cb' => '<input type="checkbox" />',
			'name' => __('Name', 'incredibledocs'),
			'idocs-tag-taxo-color' => __('Background Color', 'incredibledocs'),
			'idocs-tag-taxo-kb' => __('KB', 'incredibledocs'),
			'description' => __('Description', 'incredibledocs'),
			'slug' => 'Slug',
			'posts' => __('Count', 'incredibledocs'),

		);
		/*------------------------------------------------*/
		return $new_columns;
	}
	/*---------------------------------------------------------------------------------------*/
	// update the header table for the custom taxanomy - "idocs-kb-taxo"
	public function custom_header_kb( $columns ) {

		// before changing the header
		//do_action( 'qm/debug', $columns );
		$new_columns = array(

			'cb' => '<input type="checkbox" />',
			'name' => __('Name', 'incredibledocs'),
			'idocs-kb-taxo-id' => __('ID', 'incredibledocs'),
			'idocs-kb-taxo-icon' => __('Icon', 'incredibledocs'),
			'idocs-kb-taxo-access-type' => __('Access Type', 'incredibledocs'), // custom meta-data 
			'idocs-kb-url-type' => __('URL Type', 'incredibledocs'), // custom meta-data 
			'description' => __('Description', 'incredibledocs'),
			'slug' => 'Slug',
			'posts' => __('Count', 'incredibledocs'),

		);
		/*------------------------------------------------*/
		return $new_columns;
	}
	/*---------------------------------------------------------------------------------------*/
	// update the header table for the custom taxanomy - "idocs-faq-group-taxo"
	public function custom_header_faqgroup( $columns ) {

		// before changing the header
		//do_action( 'qm/debug', $columns );
		$new_columns = array(

			'cb' => '<input type="checkbox" />',
			'name' => __('Name', 'incredibledocs'),
			'idocs-faq-group-taxo-id' => __('ID', 'incredibledocs'),
			'idocs-faq-group-taxo-order' => __('Order', 'incredibledocs'), // custom meta-data
			'idocs-faq-group-taxo-kb' => __('KB', 'incredibledocs'), // custom meta-data
			'idocs-faq-group-taxo-category' => __('Category', 'incredibledocs'), // custom meta-data
		//	'description' => __('Description', 'incredibledocs'),
			'slug' => 'Slug',
			'posts' => __('Count', 'incredibledocs'),

		);
		/*------------------------------------------------*/
		return $new_columns;
	}
	/*---------------------------------------------------------------------------------------*/
	// add the content for the custom added column/s in list of categories 
	public function custom_content_doc_category( $value, $column_name, $tax_id ) {

		//do_action( 'qm/debug', $column_name );
		switch( $column_name ) {

			/*------------------------------------------------*/
			case 'idocs-category-taxo-id':
				$value = $tax_id;
				break;
			/*------------------------------------------------*/
			case 'idocs-category-taxo-icon':
				
				$category_icon_url = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-category-taxo-icon-url', false);
				//$category_icon_url =  get_term_meta( $tax_id, 'idocs-category-taxo-icon-url', true );
				/*------------------------------------------------*/
				if (empty ($category_icon_url)) {
					//$category_icon_key =  get_term_meta( $tax_id, 'idocs-category-taxo-icon-picker', true );
					$category_icon_key = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-category-taxo-icon-picker', false);

					if ( $category_icon_key != null ) {
						ob_start();
						?>
						<?php IDOCS_ICONS::echo_icon_svg_tag($category_icon_key, 20, 20, 0.7);?>
						<?php
						$output = ob_get_contents();
						ob_end_clean();
						$value = $output;
					}
				}
				else {

					ob_start();
					?>
						<img src="<?php echo esc_attr($category_icon_url);?>" height=20 width=20/>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
					$value = $output;

				}
			
				break;
			/*------------------------------------------------*/
			case 'idocs-category-taxo-kb':
				// get the knowledge-base id (stored in 'idocs-category-taxo-kb') of specific category term by id ($tax_id)
				//$category_kb_id =  get_term_meta( $tax_id, 'idocs-category-taxo-kb', true );
				$category_kb_id = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-category-taxo-kb', false);

				// use the knowledge-base id to get the knowledge-base term object and then access the name
				//$kb_term = get_term_by('id', $category_kb_id, 'idocs-kb-taxo');
				$kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($category_kb_id);
				/*------------------------------------------------*/
				if ($kb_term == null) {	
					
					ob_start();
					?>
						<span style="color:red">
							<?php echo esc_html__('Not Allocated', 'incredibledocs'); ?>	
						</span>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
				 	$value = $output;
					//$value = __( 'Not Allocated', 'incredibledocs' );
					
				}
				/*------------------------------------------------*/
				else {
					ob_start();
					$new_url = get_site_url() . '/wp-admin/edit-tags.php?taxonomy=idocs-category-taxo&post_type=idocs_content&selected_kb=' . $category_kb_id;
					?>
						<a href="<?php echo esc_url($new_url);?>" title = "Filter all categories related selected knowledge base"> 
						<?php echo esc_html($kb_term->name); ?>
						</a>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
				 	$value = $output;
				}
				break;
			/*------------------------------------------------*/	
			case 'idocs-category-taxo-order':
				$category_order = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-category-taxo-order', false);
				//$category_order =  get_term_meta( $tax_id, 'idocs-category-taxo-order', true );
				$value = $category_order;
				break;
			/*------------------------------------------------*/	
			case 'idocs-category-taxo-access-type':
				$cat_access_type = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-category-taxo-access-type', false);
				//$cat_access_type =  get_term_meta( $tax_id, 'idocs-category-taxo-access-type', true );
				
				switch ( $cat_access_type ) {

					case 0: $value = __('Public', 'incredibledocs');
						break;
					case 1: $value = __('All Logged-in Users', 'incredibledocs');
						break;
					case 2: $value = __('Groups', 'incredibledocs');
						break;
					default: $value = '';
				}
				break;
			/*------------------------------------------------*/	
			default:
			break;
	  } 
	  /*------------------------------------------------*/
	  return $value; 
	}
	/*---------------------------------------------------------------------------------------*/
	// add the content for the custom added column/s in list of tags
	public function custom_content_doc_tag( $value, $column_name, $tax_id ) {

		//do_action( 'qm/debug', $column_name );
		switch( $column_name ) {
			/*------------------------------------------------*/
			case 'idocs-tag-taxo-color':
				$tag_color = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-tag-taxo-color', false);				
				ob_start();
				?>
					<div style="width: 40px; height: 20px; border-radius: 5px; background-color:<?php echo esc_attr($tag_color);?>">
						
					</div>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
				$value = $output;
				break;
			/*------------------------------------------------*/	
			case 'idocs-tag-taxo-kb':
				// get the knowledge-base id of specific tag term by id ($tax_id)
				$tag_kb_id = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-tag-taxo-kb', false);				
				// use the knowledge-base id to get the knowledge-base term object and then access the name
				$kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($tag_kb_id);
				/*------------------------------------------------*/
				if ($kb_term == null) {	
					
					ob_start();
					?>
						<span style="color:red">
							<?php echo esc_html__('Not Allocated', 'incredibledocs'); ?>	
						</span>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
				 	$value = $output;
					
				}
				/*------------------------------------------------*/
				else {
					ob_start();
					$new_url = get_site_url() . '/wp-admin/edit-tags.php?taxonomy=idocs-tag-taxo&post_type=idocs_content&selected_kb=' . $tag_kb_id;
					?>
						<a href="<?php echo esc_url($new_url);?>" title = "Filter all tags related selected knowledge base"> 
						<?php echo esc_html($kb_term->name); ?>
						</a>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
				 	$value = $output;
				}
				break;
			default:
			break;
	  } 
	  /*------------------------------------------------*/
	  return $value; 
	}
	/*---------------------------------------------------------------------------------------*/
	// add the content for the custom added column/s in list of knowledge-bases 
	public function custom_content_kb( $value, $column_name, $tax_id ) {

		//do_action( 'qm/debug', $column_name );
		switch( $column_name ) {

			/*------------------------------------------------*/	
			case 'idocs-kb-taxo-id':
				$value = $tax_id;
				break;
			/*------------------------------------------------*/	
			case 'idocs-kb-taxo-icon':
				$kb_icon_url = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-kb-taxo-icon-url', false);
				//$kb_icon_url =  get_term_meta( $tax_id, 'idocs-kb-taxo-icon-url', true );
				/*------------------------------------------------*/
				if (empty ($kb_icon_url)) {
					$kb_icon_key = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-kb-taxo-icon-picker', false);
					//$kb_icon_key =  get_term_meta( $tax_id, 'idocs-kb-taxo-icon-picker', true );
					if ( $kb_icon_key != null ) {
						ob_start();
						?>
							<?php IDOCS_ICONS::echo_icon_svg_tag($kb_icon_key, 20, 20, 0.7);?>
						<?php
						$output = ob_get_contents();
						ob_end_clean();
						$value = $output;
					}
				}
				/*------------------------------------------------*/
				else {

					ob_start();
					?>
						<img src="<?php echo esc_attr($kb_icon_url);?>" height=20 width=20/>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
					$value = $output;

				}
				break;
			/*------------------------------------------------*/
			case 'idocs-kb-taxo-access-type':
				$access_type = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-kb-taxo-access-type', false);
				$value = $access_type;
				break;
			/*------------------------------------------------*/
			case 'idocs-kb-url-type':
				$red = 0;
				$custom_kb_flag = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-kb-taxo-custom-kb-page-flag', false);
				/*------------------------------------------------*/
				if ($custom_kb_flag == 1) {

					$page_id = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-kb-taxo-custom-kb-page-id', false);
					//$page_id =  get_term_meta( $tax_id, 'idocs-kb-taxo-custom-kb-page-id', true );

				}
				if  ($custom_kb_flag == 1 and $page_id != 0 ) {
					$post = get_post($page_id); 
					if ($post) {
						$value = 'Custom KB Page';
					}
					else {
						$value = 'Missing KB Page!';
						$red = 1;
					}
				}
				else {

					$value = 'Automatic';

				}
				/*------------------------------------------------*/
				ob_start();
				?>
				<span <?php if ( $red ) echo esc_attr("style=color:red");?>>
					<?php echo esc_html($value); ?>	
				</span>
				<?php
				$value = ob_get_contents();
				ob_end_clean();
				break;	
			default:
			break;
	  } 
	  /*------------------------------------------------*/
	  return $value; 
	}
	/*---------------------------------------------------------------------------------------*/
	// add the content for the custom added column/s in list of faq groups
	public function custom_content_faqgroup( $value, $column_name, $tax_id ) {

		//do_action( 'qm/debug', $column_name );
		switch( $column_name ) {
			/*------------------------------------------------*/
			case 'idocs-faq-group-taxo-id':
				$value = $tax_id;
				break;
			/*------------------------------------------------*/				
			case 'idocs-faq-group-taxo-kb':
				// get the knowledge-base id (stored in 'idocs-faq-group-taxo-kb') of specific faq group term by id ($tax_id)
				$faqgroup_kb_id = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-faq-group-taxo-kb', false);
				// use the knowledge-base id to get the knowledge-base term object and then access the name
				$kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($faqgroup_kb_id);
				/*------------------------------------------------*/
				if ($kb_term == null) {	
					
					ob_start();
					?>
						<span style="color:red">
							<?php echo esc_html__('Not Allocated', 'incredibledocs'); ?>	
						</span>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
				 	$value = $output;
					//$value = __( 'Not Allocated', 'incredibledocs' );
					
				}
				/*------------------------------------------------*/
				else {
					ob_start();
					$new_url = get_site_url() . '/wp-admin/edit-tags.php?taxonomy=idocs-faq-group-taxo&post_type=idocs_content&selected_kb=' . $faqgroup_kb_id;
					?>
						<a href="<?php echo esc_url($new_url);?>" title = "Filter all faq groups related selected knowledge base"> 
						<?php echo esc_html($kb_term->name); ?>
						</a>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
				 	$value = $output;
				}
				break;
			/*------------------------------------------------*/
			case 'idocs-faq-group-taxo-category':
				// get the category id (stored in 'idocs-faq-group-taxo-cat') of specific faq group term by id ($tax_id)
				$faqgroup_cat_id = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-faq-group-taxo-category', false);
				//error_log($faqgroup_cat_id);
				// use the cat id to get the category term object and then access the name
				$cat_term = get_term_by('id', $faqgroup_cat_id, 'idocs-category-taxo');
				//do_action( 'qm/debug', $cat_term);
				//do_action( 'qm/debug', $faqgroup_cat_id);
				// if category term not available and category id is zero --> KB Root 
				/*------------------------------------------------*/
				if ( $cat_term == false && $faqgroup_cat_id == 0 ) {	
					
					ob_start();
					?>
						<span>
							<?php echo esc_html__('KB Root', 'incredibledocs'); ?>	
						</span>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
				 	$value = $output;
					
				} 
				// if category term not available and category id is not zero --> category term not allocated (was deleted)
				else if ( $cat_term == false && $faqgroup_cat_id != 0 ) {

					ob_start();
					?>
						<span style="color:red">
							<?php echo esc_html__('Not Allocated', 'incredibledocs'); ?>	
						</span>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
				 	$value = $output;

				} 
				/*------------------------------------------------*/
				else {
					ob_start();
					$new_url = get_site_url() . '/wp-admin/edit-tags.php?taxonomy=idocs-faq-group-taxo&post_type=idocs_content&selected_cat=' . $faqgroup_cat_id;
					?>
						<a href="<?php echo esc_url($new_url);?>" title = "Filter all faq groups related selected category"> 
						<?php echo esc_html($cat_term->name); ?>
						</a>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
				 	$value = $output;
				}
				break;
			/*------------------------------------------------*/
			case 'idocs-faq-group-taxo-order':
				$faqgroup_order = IDOCS_Taxanomies::get_term_meta_caching(  $tax_id, 'idocs-faq-group-taxo-order', false);
				$value = $faqgroup_order;
				break;
			/*------------------------------------------------*/	
			default:
			break;
	  } 
	  /*------------------------------------------------*/
	  return $value; 
	}
	/*---------------------------------------------------------------------------------------*/
	// update the header table for the comments page
	public function custom_header_comments( $columns ) {

		// before changing the header
		//do_action( 'qm/debug', $columns );

		$new_columns = array(

			'cb' => '<input type="checkbox" />',
			'author' =>  __('Author', 'incredibledocs'),
			'comment' => __('Comment', 'incredibledocs'),
			'response' => __('In response to', 'incredibledocs'),
			'date' => __('Submitted on', 'incredibledocs'),
			'type' => __('Type', 'incredibledocs'),
			'document-kb-id' => __('Knowledge Base', 'incredibledocs'), // custom meta-data 

		);
		/*--------------------------------------------*/
		return $new_columns;
	}
	/*---------------------------------------------------------------------------------------*/
	// add the content for the custom added column/s in list of comments 
	public function custom_content_comments( $column_name, $comment_id ) {

		//do_action( 'qm/debug', $column_name );
		if ( 'type' == $column_name ) {

			$type = get_comment_type( $comment_id);
			if ( 'IncredibleDocs' == $type ) {

				ob_start();
				$new_url = get_site_url() . '/wp-admin/edit-comments.php?comment_type=IncredibleDocs';
				/*------------------------------------------------*/
				?>
					<a href="<?php echo esc_url($new_url);?>" title = "Filter all comments related to IncredibleDocs"> 
					<?php echo esc_html('IncredibleDocs'); ?>
					</a>
				<?php
				/*------------------------------------------------*/
				$output = ob_get_contents();
				ob_end_clean();
				echo wp_kses_post($output);

			}
			else
				echo esc_html($type);

		}
		/*------------------------------------------------*/	
		if ('document-kb-id' == $column_name) {

			// get the knowledge-base id (stored in 'idocs-category-taxo-kb') of specific comment by id ($comment_id)
			$category_kb_id =  get_comment_meta( $comment_id, 'document-kb-id', true );
			
			if ( !empty($category_kb_id) ) {
				// use the knowledge-base id to get the knowledge-base term object and then access the name
				//$kb_term = get_term_by('id', $category_kb_id, 'idocs-kb-taxo');
				$kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($category_kb_id);

				echo esc_html($kb_term->name);
			}
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public function process_admin_settings_custom_urls_form() {

		// check if the security nonce for that form is valid 
		if ( check_admin_referer( 'idocs_admin_settings_custom_urls_form_nonce' ) ) {

			$amount_of_kbs = sanitize_text_field($_POST['amount_of_kbs']);

			for ( $i = 0; $i < $amount_of_kbs; $i++ ) {

				$term_id = sanitize_text_field($_POST['term-id-select-' . $i]);
				//$current_custom_kb_page_flag = get_term_meta( $term_id, 'idocs-kb-taxo-custom-kb-page-flag', true );
				$current_custom_kb_page_flag = IDOCS_Taxanomies::get_term_meta_caching(  $term_id, 'idocs-kb-taxo-custom-kb-page-flag', false);
				$new_current_custom_kb_page_flag = sanitize_text_field($_POST['custom-kb-page-flag-select-' . $i]);
				/*------------------------------------------------*/
				if ($current_custom_kb_page_flag == 1)
					$kb_page_id = sanitize_text_field($_POST['custom-kb-page-id-select-' . $i]);
				else
					$kb_page_id = 0;
				/*------------------------------------------------*/
				// scenario #1 - flag was OFF->ON or ON->OFF - save the flag and default value for page-id
				if ($current_custom_kb_page_flag != $new_current_custom_kb_page_flag) {
					
					update_term_meta($term_id, 'idocs-kb-taxo-custom-kb-page-flag', $new_current_custom_kb_page_flag);					
					update_term_meta($term_id, 'idocs-kb-taxo-custom-kb-page-id', 0);	
					delete_transient( 'idocs_transient_terms_metadata');				

				}
				/*------------------------------------------------*/
				// scenario #2 - flag was ON, still ON ---> allow to save the kb page id
				if ($current_custom_kb_page_flag == 1 and $new_current_custom_kb_page_flag == 1) { 
				
					update_term_meta($term_id, 'idocs-kb-taxo-custom-kb-page-id', $kb_page_id);
					delete_transient( 'idocs_transient_terms_metadata');						
				}
				/*------------------------------------------------*/
			}
		}
		/*--------------------------------------------*/
		wp_redirect(wp_get_referer());
	}
}
/*---------------------------------------------------------------------------------------*/
// https://awhitepixel.com/blog/modify-add-custom-columns-post-list-wordpress-admin/



