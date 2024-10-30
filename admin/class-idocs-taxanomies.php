<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* Define the plugin taxonomies as a scheme of content classification

	 1. "idocs-kb-taxo" - knowledge-bases
	 2. "idocs-category-taxo" - categories for content cpts
	 3. "idocs-tag-taxo" - tags for content cpts
	 4. "idocs-faq-group-taxo" - groups for FAQs
	 5. "idocs-content-type-taxo" - content types 
	 
*/
/*---------------------------------------------------------------------------------------*/
class IDOCS_Taxanomies {

	/*---------------------------------------------------------------------------------------*/
	// modifying the main query on the frontend and only when using block-based theme 
	public function prioritize_second_taxonomy( &$query ) {

		// Ensure we're only modifying the main query on the frontend and only when using block-based theme 
		if ($query->is_main_query() && !is_admin() && wp_is_block_theme() ) {

			//error_log("this is a block-based theme query");
			//do_action( 'qm/debug', $query );
			/*******************************************/
			/*			
			// #1 - "category" - for category we are using the same kb template so nothing is needed to adjust. 

			/*--------------------------------------------*/
			// #2 - "tag" - check if both taxonomies are set in the query vars
			if (!empty($query->query_vars['idocs-kb-taxo']) && !empty($query->query_vars['idocs-tag-taxo'])) {
				
				// Get the current query vars
				$query_vars = $query->query_vars;
				// Remove both taxonomies from the query vars
				$kb_taxo = $query_vars['idocs-kb-taxo'];
				$tag_taxo = $query_vars['idocs-tag-taxo'];
				unset($query_vars['idocs-kb-taxo'], $query_vars['idocs-tag-taxo']);
				// Create a new array with the tag taxonomy and a custom field for the knowledge base taxonomy 
				$new_query_vars = array(
					
					'idocs-tag-taxo' => $tag_taxo,
					'custom-idocs-kb-taxo' => $kb_taxo,
				);
				// Merge the new array with the remaining query vars
				$query->query_vars = array_merge($new_query_vars, $query_vars);
				unset( $query->tax_query->queried_terms['idocs-kb-taxo'] );	
			}
			/*--------------------------------------------*/
			// #3 - "faq_group" - check if taxonomies are set in the query vars
			if (!empty($query->query_vars['idocs-kb-taxo']) &&
				!empty($query->query_vars['idocs-category-taxo']) && 
				!empty($query->query_vars['idocs-faq-group-taxo'])) {

				// Get the current query vars
				$query_vars = $query->query_vars;
				// Remove both taxonomies from the query vars
				$kb_taxo = $query_vars['idocs-kb-taxo'];
				$faq_group_taxo = $query_vars['idocs-faq-group-taxo'];

				unset($query_vars['idocs-kb-taxo'], $query_vars['idocs-category-taxo'], $query_vars['idocs-faq-group-taxo']);
				// Create a new array with the tag taxonomy and a custom field for the knowledge base taxonomy 
				$new_query_vars = array(
					'idocs-faq-group-taxo' => $faq_group_taxo,
					'custom-idocs-kb-taxo' => $kb_taxo,
				);
				// Merge the new array with the remaining query vars
				$query->query_vars = array_merge($new_query_vars, $query_vars);

				unset( $query->tax_query->queried_terms['idocs-kb-taxo'] );	
				unset( $query->tax_query->queried_terms['idocs-category-taxo'] );	
				//do_action( 'qm/debug', $query->query_vars );
			}
			/*--------------------------------------------*/
			// #4 - Document 
			if (!empty($query->query_vars['idocs_content']) ) {

				// Get the current query vars
				$query_vars = $query->query_vars;
				$kb_taxo = $query_vars['idocs-kb-taxo'];

				$new_query_vars = array(
					'custom-idocs-kb-taxo' => $kb_taxo,
				);
				$query->query_vars = array_merge($new_query_vars, $query_vars);
			}

			/*--------------------------------------------*/
			//do_action( 'qm/debug', $query->query_vars );
			//do_action( 'qm/debug', $query );
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public function prioritize_second_taxonomy_old( &$query ) {


		// Ensure we're only modifying the main query on the frontend and only when using block-based theme 
		if ($query->is_main_query() && !is_admin() && wp_is_block_theme() ) {

			//error_log("this is a block-based theme query");
			do_action( 'qm/debug', $query );
			/*******************************************/
			/*			
			// "category" - check if both taxonomies are set in the query vars
			if (!empty($query->query_vars['idocs-kb-taxo']) && 
				!empty($query->query_vars['idocs-category-taxo']) &&
				 empty($query->query_vars['idocs-faq-group-taxo']) 
				) {

					// Get the current query vars
					$query_vars = $query->query_vars;
					// Remove both taxonomies from the query vars
					$kb_taxo = $query_vars['idocs-kb-taxo'];
					$category_taxo = $query_vars['idocs-category-taxo'];
					
					unset($query_vars['idocs-kb-taxo'], $query_vars['idocs-category-taxo']);
					// Create a new array with the prioritized taxonomies at the start
					$new_query_vars = array(

						'idocs-kb-taxo' => $kb_taxo,
						'idocs-category-taxo' => $category_taxo,
						'custom-idocs-kb-taxo' => $kb_taxo,
					);
					// Merge the new array with the remaining query vars
					$query->query_vars = array_merge($new_query_vars, $query_vars);
					//unset( $query->tax_query->queried_terms['idocs-kb-taxo'] );	
			}
			/*--------------------------------------------*/
			// "tag" - check if both taxonomies are set in the query vars
			if (!empty($query->query_vars['idocs-kb-taxo']) && !empty($query->query_vars['idocs-tag-taxo'])) {
				
				//error_log("I was here - tag");
				// Get the current query vars
				$query_vars = $query->query_vars;
				// Remove both taxonomies from the query vars
				$kb_taxo = $query_vars['idocs-kb-taxo'];
				$tag_taxo = $query_vars['idocs-tag-taxo'];
				unset($query_vars['idocs-kb-taxo'], $query_vars['idocs-tag-taxo']);
				// Create a new array with the tag taxonomy and a custom field for the knowledge base taxonomy 
				$new_query_vars = array(
					'idocs-tag-taxo' => $tag_taxo,
					'custom-idocs-kb-taxo' => $kb_taxo,
				);
				// Merge the new array with the remaining query vars
				$query->query_vars = array_merge($new_query_vars, $query_vars);
				unset( $query->tax_query->queried_terms['idocs-kb-taxo'] );	
			}
			/*--------------------------------------------*/
			// "faq_group" - check if taxonomies are set in the query vars
			if (!empty($query->query_vars['idocs-kb-taxo']) &&
				!empty($query->query_vars['idocs-category-taxo']) && 
				!empty($query->query_vars['idocs-faq-group-taxo'])) {

				// Get the current query vars
				$query_vars = $query->query_vars;
				// Remove both taxonomies from the query vars
				$kb_taxo = $query_vars['idocs-kb-taxo'];
				$faq_group_taxo = $query_vars['idocs-faq-group-taxo'];

				unset($query_vars['idocs-kb-taxo'], $query_vars['idocs-category-taxo'], $query_vars['idocs-faq-group-taxo']);
				// Create a new array with the tag taxonomy and a custom field for the knowledge base taxonomy 
				$new_query_vars = array(
					'idocs-faq-group-taxo' => $faq_group_taxo,
					'custom-idocs-kb-taxo' => $kb_taxo,
				);
				// Merge the new array with the remaining query vars
				$query->query_vars = array_merge($new_query_vars, $query_vars);

				unset( $query->tax_query->queried_terms['idocs-kb-taxo'] );	
				unset( $query->tax_query->queried_terms['idocs-category-taxo'] );	
				//do_action( 'qm/debug', $query->query_vars );
			}

			//do_action( 'qm/debug', $query->query_vars );
			//do_action( 'qm/debug', $query );
		}
	}
	/*---------------------------------------------------------------------------------------*/
	// a callback function to add custom taxanomies to a custom post type  
	public function add_custom_taxonomies() {

		// get the root kbs slug (can be configured by the user)
		$idocs_kbs_root_slug = IDOCS_Database::get_plugin_settings('idocs_kbs_root_slug');
		/*--------------------------------------------*/
		$this->knowledge_bases_custom_taxonomy($idocs_kbs_root_slug);
		$this->categories_custom_taxonomy($idocs_kbs_root_slug);
		$this->tags_custom_taxonomy($idocs_kbs_root_slug);
		$this->faqs_custom_taxonomy($idocs_kbs_root_slug);
		$this->content_type_custom_taxonomy();
		/*--------------------------------------------*/
	}
	/*---------------------------------------------------------------------------------------*/
	// TAXONOMY for Knowledge-Bases: 'idocs-kb-taxo'
	public function knowledge_bases_custom_taxonomy($idocs_kbs_root_slug) {
	
		$labels = array( 
			'name' => __('Knowledge Bases', 'incredibledocs'),
			'singular_name' => __('Knowledgebase (idocs)', 'incredibledocs'),
			'search_items'               => __( 'Search Knowledge bases', 'incredibledocs' ),
			'popular_items'              => __( 'Popular Knowledge bases', 'incredibledocs' ),
			'all_items'                  => __( 'All Knowledge bases', 'incredibledocs' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Knowledge base', 'incredibledocs' ),
			'update_item'                => __( 'Update Knowledge base', 'incredibledocs' ),
			'add_new_item'               => __( 'Add a New Knowledge base', 'incredibledocs' ),
			'new_item_name'              => __( 'New Knowledge base Name', 'incredibledocs' ),
			'separate_items_with_commas' => __( 'Separate Knowledge bases with commas', 'incredibledocs' ),
			'add_or_remove_items'        => __( 'Add or remove Knowledge base', 'incredibledocs' ),
			'choose_from_most_used'      => __( 'Choose from the most used knowledge base', 'incredibledocs' ),
			'not_found'                  => __( 'No Knowledge base found.', 'incredibledocs' ),
			'menu_name'                  => __( 'Knowledge bases', 'incredibledocs' ),
		);
		/*--------------------------------------------*/
		$args = array(
			'labels'            => $labels,
			'public'			=> true,
			'hierarchical'      => false, // kbs are not hierarchical
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'has_archive'       => true,
			'rewrite'           => array( 'slug' => $idocs_kbs_root_slug . '-categories' , 
										  'with_front' => false, )
		);
		/*--------------------------------------------*/
		register_taxonomy( 'idocs-kb-taxo', 'idocs_content', $args );
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_kb_terms_caching() {

		$cached_data = get_transient( 'idocs_transient_kb_terms' );
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		if ( false === $cached_data ) {

			// get the list of knowledge-bases 
			$kb_terms = get_terms( array(
				'taxonomy'   => 'idocs-kb-taxo',
				'hide_empty' => false,
				'orderby' => 'name',
				'order' => 'ASC',
			) );
			//error_log('no cached data - kb terms');
			// Cache the data for 24 hours 
			set_transient( 'idocs_transient_kb_terms', $kb_terms, 10800 );
			return $kb_terms;	
		}
		/*--------------------------------------------*/
		//error_log('using cached data - kb terms');
		return $cached_data;
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_specific_kb_term_caching($required_kb_id) {

		$kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
		//do_action( 'qm/debug', $required_kb_id );
		//do_action( 'qm/debug', $kb_terms );
		/*--------------------------------------------*/
		foreach ($kb_terms as $kb_term) {
			//do_action( 'qm/debug', $kb_term );
			//do_action( 'qm/debug', $kb_term->term_id );
			if ($kb_term->term_id == $required_kb_id) {
				return $kb_term;
			}
		}
		/*--------------------------------------------*/
		return null; // Return null if the term with the specified $required_kb_id is not found
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_specific_kb_term_by_slug_caching($required_kb_slug) {

		$kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
		//do_action( 'qm/debug', $required_kb_id );
		//do_action( 'qm/debug', $kb_terms );
		/*--------------------------------------------*/
		foreach ($kb_terms as $kb_term) {
			//do_action( 'qm/debug', $kb_term );
			//do_action( 'qm/debug', $kb_term->term_id );
			if ($kb_term->slug == $required_kb_slug) {
				return $kb_term;
			}
		}
		/*--------------------------------------------*/
		return null; // Return null if the term with the specified $required_kb_id is not found
	}
	/*---------------------------------------------------------------------------------------*/
	// TAXONOMY for Categories: 'idocs-category-taxo' (5e5827093d)
	private function categories_custom_taxonomy($idocs_kbs_root_slug) {

		$labels = array( 

			'name' => __('Content Categories', 'incredibledocs'),
			'singular_name' => __('Category (idocs)', 'incredibledocs'),
			'search_items'               => __( 'Search Categories', 'incredibledocs' ),
			'popular_items'              => __( 'Popular Categories', 'incredibledocs' ),
			'all_items'                  => __( 'All Categories', 'incredibledocs' ),
			'parent_item'                => __( 'Parent Category', 'incredibledocs' ),
			'parent_item_colon'          => __( 'Parent Category:', 'incredibledocs' ),
			'edit_item'                  => __( 'Edit Category', 'incredibledocs' ),
			'update_item'                => __( 'Update Category', 'incredibledocs' ),
			'add_new_item'               => __( 'Add a New Category', 'incredibledocs' ),
			'new_item_name'              => __( 'New Category Name', 'incredibledocs' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'incredibledocs' ),
			'add_or_remove_items'        => __( 'Add or remove Categories', 'incredibledocs' ),
			'choose_from_most_used'      => __( 'Choose from the most used categories', 'incredibledocs' ),
			'not_found'                  => __( 'No categories found.', 'incredibledocs' ),
			'menu_name'                  => __( 'Select a Document Category', 'incredibledocs' ),
		);
		/*--------------------------------------------*/
		$args = array(
			'labels'            => $labels,
			'public'			=> true,
			'hierarchical'      => true, // document categories are hierarchical
			'show_ui'           => true,
			'show_admin_column' => true,			
			'query_var'         => true,
			'show_in_rest'      => true,
			'has_archive'       => true,
			'rewrite'           => array( 
										'slug' => $idocs_kbs_root_slug . '-categories' . '/%idocs-kb-taxo%',										
										'with_front' => false,
										// handling the hierarchical structure of categories.
										// this option is creating an issue to identify the cpt as a url resource,
										// therefore, adding unique prefix to the cpt base-slug and tax base-slug
										'hierarchical'      => true 
										)

		);
		/*--------------------------------------------*/
		register_taxonomy( 'idocs-category-taxo', 'idocs_content', $args );
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_categories_terms_no_caching($kb_id, $category_id, $hide_empty_categories, $category_cards_order_by) {

		if ( $category_cards_order_by == 'name' ) {

			// get all categories related to specific knowledge-base
			$category_list = get_terms( array(
		
				'taxonomy'   => 'idocs-category-taxo',
				'hide_empty' => $hide_empty_categories, // ignore categories without any content 
				'parent' => $category_id, // get only the direct sub-categories
				/*--------------------------------------------*/
				'meta_key'   => 'idocs-category-taxo-kb',
				'meta_value' => $kb_id,
				/*--------------------------------------------*/
				'orderby' => 'name',
				'order' => 'ASC', 

			) );
		}
		else {
			/*--------------------------------------------*/
			// get all categories related to specific knowledge-base
			//order by configured "category_order" 
			$category_list = get_terms( array(

				'taxonomy'   => 'idocs-category-taxo',
				'hide_empty' => $hide_empty_categories,
				'parent' => $category_id, // get only the direct sub-categories
				/*--------------------------------------------*/
				'meta_query' => array(
					[
						'key' => 'idocs-category-taxo-kb',
						'value' => $kb_id
					]
				),
				'orderby'       => 'meta_value_num',//Treat the meta value as numeric						
				'meta_key' => 'idocs-category-taxo-order',
				'order' => 'ASC', 
			) );
		}
		/*--------------------------------------------*/
		return $category_list;
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_root_categories_terms_caching($kb_id, $category_id, $hide_empty_categories, $category_cards_order_by) {

		$cached_data = get_transient( 'idocs_transient_root_categories_terms_' . $kb_id );
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		if ( false === $cached_data ) {

			$cached_array = [];
			// get all categories related to specific knowledge-base
			$category_list = get_terms( array(
	
				'taxonomy'   => 'idocs-category-taxo',
				'hide_empty' => $hide_empty_categories, // ignore categories without any content 
				'parent' => $category_id, // get only the direct sub-categories
				/*--------------------------------------------*/
				'meta_key'   => 'idocs-category-taxo-kb',
				'meta_value' => $kb_id,
				/*--------------------------------------------*/
				'orderby' => 'name',
				'order' => 'ASC', 

			) );
			$cached_array[$category_id][0] = $category_list;
			/*--------------------------------------------*/
			// get all categories related to specific knowledge-base
			//order by configured "category_order" 
			$category_list = get_terms( array(

				'taxonomy'   => 'idocs-category-taxo',
				'hide_empty' => $hide_empty_categories,
				'parent' => $category_id, // get only the direct sub-categories
				/*--------------------------------------------*/
				'meta_query' => array(
					[
						'key' => 'idocs-category-taxo-kb',
						'value' => $kb_id
					]
				),
				'orderby'       => 'meta_value_num',//Treat the meta value as numeric						
				'meta_key' => 'idocs-category-taxo-order',
				'order' => 'ASC', 
			) );
			$cached_array[$category_id][1] = $category_list;	
			/*--------------------------------------------*/
			//error_log('no cached data - categories');
			// Cache the data for 24 hours
			set_transient('idocs_transient_root_categories_terms_' . $kb_id , $cached_array, 10800 );
			$cached_data = $cached_array;
			//return $terms;	
		}
		else {

			//error_log('using cached data - categories');

		}

		if ($category_cards_order_by == 'name') {
			
			return $cached_data[$category_id][0];
				
		}
		else {

			return $cached_data[$category_id][1];

		}
	}	
	/*---------------------------------------------------------------------------------------*/
	// TAXONOMY for Tags: 'idocs-tag-taxo'
	private function tags_custom_taxonomy($idocs_kbs_root_slug) {

		$labels = array( 
			'name' => __('Content Tags', 'incredibledocs'),
			'singular_name' => __('Tag (idocs)', 'incredibledocs'),
			'search_items'               => __( 'Search Tags', 'incredibledocs' ),
			'popular_items'              => __( 'Popular Tags', 'incredibledocs' ),
			'all_items'                  => __( 'All Tags', 'incredibledocs' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Tag', 'incredibledocs' ),
			'update_item'                => __( 'Update Tag', 'incredibledocs' ),
			'add_new_item'               => __( 'Add a New Tag', 'incredibledocs' ),
			'new_item_name'              => __( 'New Tag Name', 'incredibledocs' ),
			'separate_items_with_commas' => __( 'Separate Tags with commas', 'incredibledocs' ),
			'add_or_remove_items'        => __( 'Add or remove Tags', 'incredibledocs' ),
			'choose_from_most_used'      => __( 'Choose from the most used tags', 'incredibledocs' ),
			'not_found'                  => __( 'No Tags', 'incredibledocs' ),
			'menu_name'                  => __( 'Tags', 'incredibledocs' ),

		);
		/*--------------------------------------------*/
		$args = array(
			'labels'            => $labels,
			'public'			=> true,
			'hierarchical'      => false, // tags are not hierarchical
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'has_archive'       => false,
			//'rewrite'           => array( 'slug' => 'idocs-tag-taxo' )

			'rewrite'           => array( 'slug' => $idocs_kbs_root_slug . '-tags' . '/%idocs-kb-taxo%', 
										  'with_front' => false, )

		);
		/*--------------------------------------------*/
		register_taxonomy( 'idocs-tag-taxo', 'idocs_content', $args );
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_tag_terms_caching() {

		$cached_data = get_transient( 'idocs_transient_tag_terms' );
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		if ( false === $cached_data ) {

			// get the list of tags
			$tag_terms = get_terms( array(
				'taxonomy'   => 'idocs-tag-taxo',
				'hide_empty' => false,
				'orderby' => 'name',
				'order' => 'ASC',
			) );
			//error_log('no cached data - tag terms');
			// Cache the data for 24 hours 
			set_transient( 'idocs_transient_tag_terms', $tag_terms, 10800 );
			return $tag_terms;	
		}
		/*--------------------------------------------*/
		//error_log('using cached data - tag terms');
		return $cached_data;
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_specific_tag_term_caching( $required_tag_id ) {

		$tag_terms = IDOCS_Taxanomies::get_tag_terms_caching();
		//do_action( 'qm/debug', $required_tag_id );
		//do_action( 'qm/debug', $tag_terms );
		/*--------------------------------------------*/
		foreach ($tag_terms as $tag_term) {
			//do_action( 'qm/debug', $tag_term );
			//do_action( 'qm/debug', $tag_term->term_id );
			if ($tag_term->term_id == $required_tag_id) {
				return $tag_term;
			}
		}
		/*--------------------------------------------*/
		return null; // Return null if the term with the specified $required_tag_id is not found
	}
	/*---------------------------------------------------------------------------------------*/
	// TAXONOMY for FAQ Groups: 'idocs-faq-group-taxo'
	private function faqs_custom_taxonomy( $idocs_kbs_root_slug ) {

		$labels = array( 
			'name' => __('FAQ Groups', 'incredibledocs'),
			'singular_name' => __('FAQ-Group (idocs)', 'incredibledocs'),
			'search_items'               => __( 'Search FAQ Groups', 'incredibledocs' ),
			'popular_items'              => __( 'Popular FAQ Groups', 'incredibledocs' ),
			'all_items'                  => __( 'All FAQ Groups', 'incredibledocs' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit FAQ Group', 'incredibledocs' ),
			'update_item'                => __( 'Update FAQ Group', 'incredibledocs' ),
			'add_new_item'               => __( 'Add a New FAQ Group', 'incredibledocs' ),
			'new_item_name'              => __( 'New FAQ Group Name', 'incredibledocs' ),
			'separate_items_with_commas' => __( 'Separate FAQ Group with commas', 'incredibledocs' ),
			'add_or_remove_items'        => __( 'Add or remove FAQ Group', 'incredibledocs' ),
			'choose_from_most_used'      => __( 'Choose from the most used FAQ Groups', 'incredibledocs' ),
			'not_found'                  => __( 'No FAQ Groups found.', 'incredibledocs' ),
			'menu_name'                  => __( 'FAQ Groups', 'incredibledocs' ),

		);
		/*--------------------------------------------*/
		$args = array(
			'labels'            => $labels,
			'public'			=> true,
			'hierarchical'      => false, // faq groups are not hierarchical
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'has_archive'       => false,

			'rewrite'           => array( 

				//'slug' => $idocs_kbs_root_slug . '-categories' . '/%idocs-kb-taxo%/%idocs-category-taxo%',										
				'slug' => $idocs_kbs_root_slug . '-faqgroups' . '/%idocs-kb-taxo%/%idocs-category-taxo%',
				'hierarchical'      => true 
										
				//'slug' => 'idocs-faq-group-taxo' 
				)
		);
		/*--------------------------------------------*/
		register_taxonomy( 'idocs-faq-group-taxo', 'idocs_content', $args );
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_groups_terms_per_category_caching($kb_id, $category_id) {

		$cached_data = get_transient( 'idocs_transient_faqgroups_terms_' . $kb_id );
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		if ( false === $cached_data || !(isset($cached_data[$category_id])) ) {

			$faq_groups = get_terms( array(

				'taxonomy'   => 'idocs-faq-group-taxo',
				'hide_empty' => false,
				//'parent' => $category_id, // get only the direct sub-categories
				/*--------------------------------------------*/
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'idocs-faq-group-taxo-kb',
						'value'   => $kb_id,
						'compare' => '=',
					),
					array(
						'key'     => 'idocs-faq-group-taxo-category',
						'value'   => $category_id, 
						'compare' => '=',
					),
				),
				/*--------------------------------------------*/
				'meta_key' => 'idocs-faq-group-taxo-order',
				'orderby'  => 'meta_value_num',
				'order' => 'ASC', 
			) );
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			};
			/*--------------------------------------------*/
			$cached_data[$category_id] = $faq_groups;
			//error_log('no cached data - setting faq groups terms_per_category');
			// Cache the data for 24 hours 
			set_transient('idocs_transient_faqgroups_terms_' . $kb_id, $cached_data, 10800 );
			return $faq_groups;	
		}
		/*--------------------------------------------*/
		else {
			//error_log('using cached data - faq groups_terms_per_category');
			return $cached_data[$category_id];
		}
	}
	/*---------------------------------------------------------------------------------------*/
	// Content Type taxonomy: 'idocs-content-type-taxo'
	public function content_type_custom_taxonomy(){

		$labels = array( 
			'all_items'                  => __( 'All Content Types', 'incredibledocs' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'			=> false,
			'hierarchical'      => false, 
			'show_ui'           => false,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'has_archive'       => false,
			
		);
		/*--------------------------------------------*/
		register_taxonomy( 'idocs-content-type-taxo', 'idocs_content' , $args );
		/*--------------------------------------------*/
		// check if one of the terms already exist 
		$term = term_exists( 'Document', 'idocs-content-type-taxo' );
		if ( ! $term ) {

			wp_insert_term( 'Document', 'idocs-content-type-taxo');
			wp_insert_term( 'FAQ', 'idocs-content-type-taxo');
			wp_insert_term( 'Link', 'idocs-content-type-taxo');
			delete_transient('idocs_transient_content_types_terms');

		}
		/*--------------------------------------------*/	
		// add the pro content types 
		do_action('idocspro_add_content_types');
		/*--------------------------------------------*/
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_content_types_terms_caching() {

		$cached_data = get_transient( 'idocs_transient_content_types_terms' );
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		if ( false === $cached_data ) {

			// get the list of content types
			$terms = get_terms( array(
				'taxonomy'   => 'idocs-content-type-taxo',
				'hide_empty' => false,
				'orderby' => 'id',
				'order' => 'ASC',
			) );

			//error_log('no cached data - content types');
			// Cache the data for 24 hours 
			set_transient('idocs_transient_content_types_terms', $terms, 10800 );
			return $terms;	
		}
		/*--------------------------------------------*/
		//error_log('using cached data - content types');
		return $cached_data;
	}
	/*---------------------------------------------------------------------------------------*/
	public function add_custom_fields_to_kb( $taxonomy ) {

		?>
		<!--------------------------------->
		<!-- KB Icon Selection-->
		<!--------------------------------->
		<div>
			<label style="font-weight:bold"><?php echo esc_attr__('Knowledge base Icon', 'incredibledocs'); ?></label>
			<button id="icon-picker-button" type="button" class="idocs-icon-type-button">Icon Picker</button>
			<button id="custom-icon-button" type="button" class="idocs-icon-type-button">Custom Icon</button>
		</div>
		<!--------------------------------->
		<!-- Icon Picker --> 
		<!--------------------------------->
		<div class="form-field" id="icon-picker-form-field">
			<!-- generate a nonce and include it as a hidden field. -->
			<?php wp_nonce_field( 'idocs_save_custom_fields_action', 'idocs_save_custom_fields_nonce' ); ?>

			<label><?php echo esc_attr__('Select an Icon', 'incredibledocs'); ?>:</label>

			<div class="idocs-icon-picker-search-container" id="idocs-icon-picker-search" style="width:250px">
				 <input class="idocs-icon-search-input-field" type="text" class="form-control" id="idocs-icon-search-term" placeholder="Search icon using a keyword"  autocomplete="off" style="width:100%"></input>
			</div>
			<input type="hidden" id="input-icon-key" name="idocs-kb-taxo-icon-picker" size="60" value="">
			<div class="idocs-icon-picker-container" id="icon-picker" style="padding-top:10px;">
			</div>		
		</div>
		<!--------------------------------->
		<!-- Custom Icon --> 
		<!--------------------------------->
		<div class="form-field" id="icon-upload-form-field" style = "display:none";>
			<label><?php echo esc_attr__('Upload an Icon', 'incredibledocs'); ?></label>

				<input class="category-icon-image-url" type="hidden" name="idocs-kb-taxo-icon-url" size="60" value="">
				<div>
					<input type="button" class="idocs-category-upload-remove" id="category-icon-upload" value="<?php echo esc_attr__( 'Add Icon', 'incredibledocs' )?>" /> 
					<input type="button" class="idocs-category-upload-remove" id="category-icon-remove" value="<?php echo esc_attr__( 'Remove Icon', 'incredibledocs' )?>" /> 
				</div>

				<div style="display: none" class="idocs-category-icon-image-container">
					<img  class="idocs-category-icon-image" src=""/>
				</div>
		</div>
		<!--------------------------------->
		<?php
	}
	/*---------------------------------------------------------------------------------------*/
	public function edit_custom_fields_to_kb( $term ) {

		$kb_icon_key = get_term_meta( $term->term_id, 'idocs-kb-taxo-icon-picker', true );
		$kb_icon =  get_term_meta( $term->term_id, 'idocs-kb-taxo-icon-url', true );
		//do_action( 'qm/debug', $kb_icon );
		/*--------------------------------------------*/
		if ( empty($kb_icon) ) {

			$icon_picker_display ="display:block;";
			$icon_upload_display ="display:none;";

		}
		else {

			$icon_picker_display ="display:none;";
			$icon_upload_display ="display:block;";

		}
		?>
		<!--------------------------------->
		<tr class="form-field">
			<!-- generate a nonce and include it as a hidden field. -->
			<?php wp_nonce_field( 'idocs_save_custom_fields_action', 'idocs_save_custom_fields_nonce' ); ?>
			<!--------------------------------->
			<!-- Category Icon -->
			<!--------------------------------->
			<tr>
				<th>
					<label><?php echo esc_attr__('Knowledge Base Icon', 'incredibledocs'); ?></label>
				</th>
				<!--------------------------------->
				<td>
					<div>
						<button id="icon-picker-button" type="button" class="idocs-icon-type-button">Icon Picker</button>
						<button id="custom-icon-button" type="button" class="idocs-icon-type-button">Custom Icon</button>
					</div>
					<!--------------------------------->
					<div id="icon-picker-form-field" style ="<?php echo esc_attr($icon_picker_display);?>">
						
						<div class="idocs-icon-picker-search-container" id="idocs-icon-picker-search" style="width:250px">
				 			<input class="idocs-icon-search-input-field" type="text" class="form-control" id="idocs-icon-search-term" placeholder="Search icon using a keyword"  autocomplete="off" style="width:100%"></input>
						</div>
						<input type="hidden" id="input-icon-key" name="idocs-kb-taxo-icon-picker" size="60" value="<?php echo esc_attr($kb_icon_key); ?>">
						<div class="idocs-icon-picker-container" id="icon-picker" style="padding-top:10px;">
						</div>	
					</div>	
					<!--------------------------------->
					<div id="icon-upload-form-field" style ="<?php echo esc_attr($icon_upload_display);?>">
						<input class="category-icon-image-url" type="hidden" name="idocs-kb-taxo-icon-url" size="60" value="<?php echo esc_url($kb_icon); ?>">
						<div>
							<input type="button" class="idocs-category-upload-remove" id="category-icon-upload" value="<?php echo esc_attr__( 'Add Icon', 'incredibledocs' )?>" /> 
							<input type="button" class="idocs-category-upload-remove" id="category-icon-remove" value="<?php echo esc_attr__( 'Remove Icon', 'incredibledocs' )?>" /> 
						</div>
						<div style="<?php echo esc_attr($icon_upload_display);?>" class="idocs-category-icon-image-container">
							<img  class="idocs-category-icon-image" src="<?php echo esc_attr($kb_icon);?>"/>
						</div>
					</div>
					<!--------------------------------->
				</td>
			</tr>
		</tr>
		<?php 
	}
	/*---------------------------------------------------------------------------------------*/
	// Add custom fields (metadata) to the category taxonomy. 
	// Action hook is {TAXONOMY}_add_form_fields.
	public function add_custom_fields_to_category( $taxonomy ) {

		/*--------------------------------------------*/
		$terms = IDOCS_Taxanomies::get_kb_terms_caching();
		//do_action( 'qm/debug', $terms );
		/*--------------------------------------------*/
		?>
		<div class="form-field" id="idocs-category-taxo-kb-div">
			<!--------------------------------->
			<?php wp_nonce_field( 'idocs_add_custom_fields_action', 'idocs_add_custom_fields_nonce' ); ?>
			<!--------------------------------->	
			<!-- Knowledge-base Field-->
			<!--------------------------------->
			<label for="idocs-category-taxo-kb"> <?php echo esc_attr__('Select a Knowledge Base', 'incredibledocs'); ?> </label>
			<select name="idocs-category-taxo-kb" id="idocs-category-taxo-kb">
			
				<?php
				foreach ($terms as $term) {
					?>
					<option value="<?php echo esc_attr($term->term_id);?>"><?php echo esc_attr($term->name);?>	
					</option>
					<?php
				}
				?>
			</select>
			<p id="knowledge-base-description">
				<?php echo esc_html__( 'Assign a knowledge base instance to the new category. If the list is empty, please create a knowledge base.	', 'incredibledocs' );?>
			</p>
		</div>
		<!--------------------------------->
		<!-- Category Order Field-->
		<div class="form-field">
			<label for="idocs-category-taxo-order"> <?php echo esc_attr__('Category Order', 'incredibledocs'); ?> </label>
			<input type="number" id="idocs-category-taxo-order" name="idocs-category-taxo-order" style="width:80px" value="0" />
			<p id="category-order-description">
				<?php echo esc_html__( 'Optional - assign a number to the category to be used when displaying categories in specific order (1 - highest).', 'incredibledocs' );?>
			</p>
		</div>
		<!-- Category Icon Selection-->
		<!--------------------------------->
		<div>
			<label><?php echo esc_attr__('Category Icon', 'incredibledocs'); ?></label>
			<button id="icon-picker-button" type="button" class="idocs-icon-type-button">Icon Picker</button>
			<button id="custom-icon-button" type="button" class="idocs-icon-type-button">Custom Icon</button>
		</div>
		<!-- Icon Picker -->
		<!---------------------------------> 
		<div class="form-field" id="icon-picker-form-field">
			<label><?php echo esc_attr__('Select an Icon:', 'incredibledocs'); ?></label>
			
				<div class="idocs-icon-picker-search-container" id="idocs-icon-picker-search" style="width:250px">
					<input class="idocs-icon-search-input-field" type="text" class="form-control" id="idocs-icon-search-term" placeholder="Search icon using a keyword"  autocomplete="off" style="width:100%"></input>
				</div>
				<input type="hidden" id="input-icon-key" name="idocs-category-taxo-icon-picker" size="60" value="">
				<div class="idocs-icon-picker-container" id="icon-picker" style="padding-top:10px;">
				</div>
					
		</div>
		<!-- Custom Icon -->
		<!---------------------------------> 
		<div class="form-field" id="icon-upload-form-field" style = "display:none";>
			<label><?php echo esc_attr__('Upload an Icon', 'incredibledocs'); ?></label>

				<input class="category-icon-image-url" type="hidden" name="idocs-category-taxo-icon-url" size="60" value="">
				<div>
					<input type="button" class="idocs-category-upload-remove" id="category-icon-upload" value="<?php echo esc_attr__( 'Add Icon', 'incredibledocs' )?>" /> 
					<input type="button" class="idocs-category-upload-remove" id="category-icon-remove" value="<?php echo esc_attr__( 'Remove Icon', 'incredibledocs' )?>" /> 
				</div>

				<div style="display: none" class="idocs-category-icon-image-container">
					<img  class="idocs-category-icon-image" src=""/>
				</div>
		</div>
		<!--------------------------------->	
		<?php
	}
	/*---------------------------------------------------------------------------------------*/
	// Handle the admin edit screen for the category custom taxonomy
	public function edit_custom_fields_to_category( $term ) { 
		
		// get the stored custom meta data fields of the category term 
		$category_order = get_term_meta( $term->term_id, 'idocs-category-taxo-order', true );
		$category_icon =  get_term_meta( $term->term_id, 'idocs-category-taxo-icon-url', true );
		//do_action( 'qm/debug', $category_icon );
		/*--------------------------------------------*/
		if ( empty($category_icon) ) {

			$icon_picker_display ="display:block;";
			$icon_upload_display ="display:none;";

		}
		else {

			$icon_picker_display ="display:none;";
			$icon_upload_display ="display:block;";

		}
		/*--------------------------------------------*/
		$category_kb =  get_term_meta( $term->term_id, 'idocs-category-taxo-kb', true );
		$category_icon_key = get_term_meta( $term->term_id, 'idocs-category-taxo-icon-picker', true );
	//	do_action( 'qm/debug', esc_attr($category_kb) );
		/*--------------------------------------------*/
		$terms = IDOCS_Taxanomies::get_kb_terms_caching();
		//do_action( 'qm/debug', esc_attr($category_order) );
		?>
		<!--------------------------------->
		<tr class="form-field">
			<?php wp_nonce_field( 'idocs_update_custom_fields_action', 'idocs_update_custom_fields_nonce' ); ?>
			<!--------------------------------->
			<!-- Knowledge-base -->
			<!--------------------------------->
			<tr>
				<th>
					<label for="idocs-category-taxo-kb"> <?php echo esc_html__('Knowledge Base', 'incredibledocs'); ?></label>
				</th>
				
				<td>
					<select disabled name="idocs-category-taxo-kb" id="idocs-category-taxo-kb">
						<?php
						foreach ($terms as $term) {

							$selected = selected( $category_kb == $term->term_id, true, false );
							?>
							<option value="<?php echo esc_attr($term->term_id);?>" <?php echo esc_attr($selected);?>><?php echo esc_attr($term->name);?></option>
							<?php
						}
						?>
					</select>
					<p>A knowledge-base can't be changed for an existing category. Create a new category if a different knowledge-base is needed.</p>
				</td>
			</tr>  
			<!--------------------------------->
			<!-- Category Order -->
			<!--------------------------------->
			<tr>
				<th>
					<label for="idocs-category-taxo-order"> <?php echo esc_attr__('Category Order', 'incredibledocs'); ?></label>
				</th>
				<td>
					<input type="number" id= "idocs-category-taxo-order" name="idocs-category-taxo-order" value="<?php echo esc_attr($category_order); ?>">
				</td>
			</tr>
			<!--------------------------------->
			<!-- Category Icon -->
			<!--------------------------------->
			<tr>
				<th>
					<label><?php echo esc_attr__('Category Icon', 'incredibledocs'); ?></label>
				</th>
				<td>
					<div>
						<button id="icon-picker-button" type="button" class="idocs-icon-type-button">Icon Picker</button>
						<button id="custom-icon-button" type="button" class="idocs-icon-type-button">Custom Icon</button>
					</div>
					<!--------------------------------->
					<div id="icon-picker-form-field" style ="<?php echo esc_attr($icon_picker_display);?>">
						<div class="idocs-icon-picker-search-container" id="idocs-icon-picker-search" style="width:250px">
							<input class="idocs-icon-search-input-field" type="text" class="form-control" id="idocs-icon-search-term" placeholder="Search icon using a keyword"  autocomplete="off" style="width:100%"></input>
						</div>
						<input type="hidden" id="input-icon-key" name="idocs-category-taxo-icon-picker" size="60" value="<?php echo esc_attr($category_icon_key); ?>">
						<div class="idocs-icon-picker-container" id="icon-picker" style="padding-top:10px;">
						</div>
					</div>	
					<!--------------------------------->
					<div id="icon-upload-form-field" style ="<?php echo esc_attr($icon_upload_display);?>">
						<input class="category-icon-image-url" type="hidden" name="idocs-category-taxo-icon-url" size="60" value="<?php echo esc_url($category_icon); ?>">
						<div>
							<input type="button" class="idocs-category-upload-remove" id="category-icon-upload" value="<?php echo esc_attr__( 'Add Icon', 'incredibledocs' )?>" /> 
							<input type="button" class="idocs-category-upload-remove" id="category-icon-remove" value="<?php echo esc_attr__( 'Remove Icon', 'incredibledocs' )?>" /> 
						</div>
						<div style="<?php echo esc_attr($icon_upload_display);?>" class="idocs-category-icon-image-container">
							<img  class="idocs-category-icon-image" src="<?php echo esc_attr($category_icon);?>"/>
						</div>
					</div>
					<!--------------------------------->
				</td>
			</tr>
		</tr>
		<?php  
	}
	/*---------------------------------------------------------------------------------------*/
	// save the custom fields when a new category is saved. 
	public function save_custom_fields_to_new_category( $term_id ) {

		// When checking a nonce using wp_verify_nonce you will need to sanitize the input using wp_unslash AND sanitize_text_field
		if ( isset( $_POST['idocs_add_custom_fields_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['idocs_add_custom_fields_nonce'])), 'idocs_add_custom_fields_action' ) ) {
			// Nonce is valid, process form data
			/*--------------------------------------------*/
			// delete the specific category transient 
			$kb_id = sanitize_text_field( $_POST[ 'idocs-category-taxo-kb' ]);
			// delete the top-level list of categories transient
			delete_transient( 'idocs_transient_root_categories_terms_' . $kb_id );
			// delete cache for terms meta-data 
			delete_transient( 'idocs_transient_terms_metadata' );
			delete_transient( 'idocs_transient_category_terms');
			delete_transient( 'idocs_transient_category_terms_slugs');
			/*--------------------------------------------*/
			// ORDER
			update_term_meta(
				$term_id,
				'idocs-category-taxo-order',
				sanitize_text_field( $_POST[ 'idocs-category-taxo-order' ] )
			);
			/*--------------------------------------------*/
			// ICON URL
			update_term_meta(
				$term_id,
				'idocs-category-taxo-icon-url',
				sanitize_text_field( $_POST[ 'idocs-category-taxo-icon-url' ] )
			);
			/*--------------------------------------------*/
			// KB
			update_term_meta(
				$term_id,
				'idocs-category-taxo-kb',
				sanitize_text_field( $_POST[ 'idocs-category-taxo-kb' ] )
			);
			/*--------------------------------------------*/
			// Selected Icon Key
			update_term_meta(
				$term_id,
				'idocs-category-taxo-icon-picker',
				sanitize_text_field( $_POST[ 'idocs-category-taxo-icon-picker' ] )
			);
			/*--------------------------------------------*/ 
			// check the access type of the knowledge-base
			$kb_id = sanitize_text_field($_POST[ 'idocs-category-taxo-kb' ]);
			$kb_access_type = get_term_meta( $kb_id, 'idocs-kb-taxo-access-type', true);
			/*--------------------------------------------*/
			switch ( $kb_access_type ) {

				case 'Public': $cat_access_type = 0;
					break;
				case 'Internal': $cat_access_type = 1;
					break;
				case 'Hybrid': $cat_access_type = 1;
					break;
				default: $cat_access_type = 0;

			}
			/*--------------------------------------------*/
			// Access Type 
			update_term_meta(
				$term_id,
				'idocs-category-taxo-access-type',
				$cat_access_type // default category access type is based on the kb
			);
			/*--------------------------------------------*/
		}		
	}
	/*---------------------------------------------------------------------------------------*/
	// save the custom fields when an existing category is saved. 
	public function save_custom_fields_to_existing_category( $term_id ) {

		if ( isset( $_POST['idocs_update_custom_fields_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['idocs_update_custom_fields_nonce']) ), 'idocs_update_custom_fields_action' ) ) {
			// Nonce is valid, process form data
			/*--------------------------------------------*/
			// delete the transient 
			$kb_id =  get_term_meta( $term_id, 'idocs-category-taxo-kb', true );
			delete_transient( 'idocs_transient_root_categories_terms_' . $kb_id );
			// delete cache for terms meta-data 
			delete_transient( 'idocs_transient_terms_metadata' );
			delete_transient( 'idocs_transient_category_terms');
			delete_transient( 'idocs_transient_category_terms_slugs');
			/*--------------------------------------------*/
			// ORDER
			update_term_meta(
				$term_id,
				'idocs-category-taxo-order',
				sanitize_text_field( $_POST[ 'idocs-category-taxo-order' ] )
			);
			/*--------------------------------------------*/
			// ICON URL
			update_term_meta(
				$term_id,
				'idocs-category-taxo-icon-url',
				sanitize_text_field( $_POST[ 'idocs-category-taxo-icon-url' ] )
			);
			/*--------------------------------------------*/
			// Selected Icon Key
			update_term_meta(
				$term_id,
				'idocs-category-taxo-icon-picker',
				sanitize_text_field( $_POST[ 'idocs-category-taxo-icon-picker' ] )
			);
			/*--------------------------------------------*/
		} 	
	}
	/*---------------------------------------------------------------------------------------*/
	public function category_is_deleted($term, $tt_id, $deleted_term, $object_ids) {

		// get the list of knowledge-bases 
        $kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
		/*------------------------------------------------*/
		foreach ($kb_terms as $kb_term) {

			$kb_id = $kb_term->term_id;
			// delete the top-level list of categories transient
			delete_transient( 'idocs_transient_root_categories_terms_' . $kb_id  );

		};
		/*------------------------------------------------*/
		// delete the top-level list of categories transient
		delete_transient( 'idocs_transient_terms_metadata' );
		delete_transient( 'idocs_transient_category_terms');
		delete_transient( 'idocs_transient_attached_terms_' . 'idocs-category-taxo' );
		/*--------------------------------------------*/
		
	}
	/*---------------------------------------------------------------------------------------*/
	public function delete_kb_comments($kb_id) {

		// Query comments with specific meta-data
		$args = array(
			'meta_query' => array(
				array(
					'key' => 'document-kb-id',
					'value' => $kb_id,
					'compare' => '='
				)
			)
		);
		$comments_query = new WP_Comment_Query;
		$comments = $comments_query->query( $args );
		/*------------------------------------------------*/
		// Loop through comments and delete them
		if ( $comments ) {
			foreach ( $comments as $comment ) {

				wp_delete_comment( $comment->comment_ID, true ); // Set second parameter to true to force delete (bypass trash)

			}
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public function kb_is_deleted($term, $tt_id, $deleted_term, $object_ids) {

		/*--------------------------------------------*/
		// delete the existing kb terms caching transient
		delete_transient('idocs_transient_kb_terms');
		/*--------------------------------------------*/
		// delete the custom design settings for the deleted kb
        delete_option('idocs_design_options_'. $tt_id );
		/*--------------------------------------------*/
		// delete the kb analytics data 
		IDOCS_Database::deleted_kb_db_cleanup( $tt_id );
		/*--------------------------------------------*/
		self::delete_kb_comments($tt_id);

		delete_transient( 'idocs_transient_attached_terms_' . 'idocs-kb-taxo' );
		
	}
	/*---------------------------------------------------------------------------------------*/
	// save meta-data fields for a new knowledge-base 
	public function save_custom_fields_to_kb( $term_id ) {

		if ( isset( $_POST['idocs_save_custom_fields_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['idocs_save_custom_fields_nonce'])), 'idocs_save_custom_fields_action' ) ) {

			/*--------------------------------------------*/
			// delete the existing kb terms caching transient
			delete_transient('idocs_transient_kb_terms');
			// delete cache for terms meta-data 
			delete_transient( 'idocs_transient_terms_metadata' );
			/*--------------------------------------------*/
			// save a default access-type metadata field for a new knowledge-base 
			update_term_meta(
				$term_id,
				'idocs-kb-taxo-access-type',
				sanitize_text_field( 'Public' ) // default kb access type is public 
			);
			/*--------------------------------------------*/  
			// save a default idocs-kb-taxo-custom-kb-page-flag metadata field for a new knowledge-base. 
			// idocs-kb-taxo-custom-kb-page-id will be added dynamically when the flag will be changed to ON (1).
			update_term_meta(
				$term_id,
				'idocs-kb-taxo-custom-kb-page-flag',
				0 // OFF 
			);
			/*--------------------------------------------*/
			// save a default idocs-kb-taxo-summary-report-flag metadata field for a new knowledge-base. 
			// other email settings will be added dynamically when the flag will be changed to ON (1).
			update_term_meta(
				$term_id,
				'idocs-kb-taxo-summary-report-flag',
				0 // OFF 
			);
			/*--------------------------------------------*/
			update_term_meta(
				$term_id,
				'idocs-kb-taxo-theme-id',
				1 // defualt theme is the first theme is the list 
			);
			/*--------------------------------------------*/
			update_term_meta(
				$term_id,
				'idocs-kb-taxo-theme-type',
				0 // 0 - the theme is from the plugin defualt themes, 1 - custom theme added by user
			);
			/*--------------------------------------------*/
			// ICON URL
			update_term_meta(
				$term_id,
				'idocs-kb-taxo-icon-url',
				sanitize_text_field( $_POST[ 'idocs-kb-taxo-icon-url' ] )
			);
			/*--------------------------------------------*/
			// Selected Icon Key
			update_term_meta(
				$term_id,
				'idocs-kb-taxo-icon-picker',
				sanitize_text_field( $_POST[ 'idocs-kb-taxo-icon-picker' ] )
			);
			/*--------------------------------------------*/
		}
	}
	/*---------------------------------------------------------------------------------------*/
	// callback function to update the idocs-category-taxo and idocs-tag-taxo links using the "term_link" filter. 
	public function taxanomies_permalink( $term_link, $term, $taxonomy )
	{
		
		//do_action( 'qm/debug', "term link before:" . $term_link );
		//do_action( 'qm/debug', $term );
		//do_action( 'qm/debug', $taxonomy );
		//error_log('I was called!');
		/*--------------------------------------------*/
		// if not related to the plugin, return the term link 
		if ( 'idocs-category-taxo' != $taxonomy &&  
			 'idocs-kb-taxo' != $taxonomy &&
			 'idocs-faq-group-taxo' != $taxonomy &&
			 'idocs-tag-taxo' != $taxonomy ) {

			return $term_link;

		}
		/*--------------------------------------------*/
		if ('idocs-category-taxo' == $taxonomy) { 

			// get the knowlege-base id stored as meta-data of category ('idocs-category-taxo-kb')
			//$category_kb = get_term_meta( $term->term_id, 'idocs-category-taxo-kb', true );
			$category_kb = IDOCS_Taxanomies::get_term_meta_caching(  $term->term_id, 'idocs-category-taxo-kb', false);
			/*--------------------------------------------*/
			if ( ! empty($category_kb) ){
				//do_action( 'qm/debug', $category_kb );
				// get the knowledge-base full term using the kb id
				//$kb_term = get_term($category_kb);
				$kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($category_kb);
				//do_action( 'qm/debug', $kb_term );
				if ( ! empty($kb_term) ) {
					// get the custom kb flag 
					$term_link = str_replace( '%idocs-kb-taxo%', $kb_term->slug , $term_link );
				}
			}
		}
		/*--------------------------------------------*/
		if ( 'idocs-kb-taxo' == $taxonomy ) {

			//$custom_kb_page_flag = get_term_meta( $term->term_id, 'idocs-kb-taxo-custom-kb-page-flag', true );
			$custom_kb_page_flag = IDOCS_Taxanomies::get_term_meta_caching(  $term->term_id, 'idocs-kb-taxo-custom-kb-page-flag', false);
			/*--------------------------------------------*/
			// check if custom kb page is ON
			if ($custom_kb_page_flag == 1) {
		
				//$custom_kb_page_id = get_term_meta( $term->term_id, 'idocs-kb-taxo-custom-kb-page-id', true );
				$custom_kb_page_id = IDOCS_Taxanomies::get_term_meta_caching(  $term->term_id, 'idocs-kb-taxo-custom-kb-page-id', false);

				// check if the custom kb page id is not zero 
				if ($custom_kb_page_id != 0 ) {
					
					$post = get_post($custom_kb_page_id); 
					
					// check if the custom kb page exist (can be removed)
					if ( $post ) {
						$term_link = get_site_url() . '/' . $post->post_name . '/';
						//do_action( 'qm/debug', $post->post_name );
					}
					else {
						$term_link = "#";
					}	
				}
				else {
					$term_link = "#";
				}	
			}
		}
		/*--------------------------------------------*/
		if ('idocs-faq-group-taxo' == $taxonomy) { 

			// get the knowlege-base id stored as meta-data of faq group ('idocs-category-taxo-kb')
			//$faqgroup_kb = get_term_meta( $term->term_id, 'idocs-faq-group-taxo-kb', true );
			$faqgroup_kb = IDOCS_Taxanomies::get_term_meta_caching(  $term->term_id, 'idocs-faq-group-taxo-kb', false);
			/*--------------------------------------------*/
			if ( ! empty($faqgroup_kb) ){
				//do_action( 'qm/debug', $faqgroup_kb );
				// get the knowledge-base full term using the kb id
				//$kb_term = get_term($faqgroup_kb);
				$kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($faqgroup_kb);

				if ( ! empty($kb_term) ) {
					// get the custom kb flag 
					$term_link = str_replace( '%idocs-kb-taxo%', $kb_term->slug , $term_link );
				}
			}
			/*--------------------------------------------*/
			//$faqgroup_category = get_term_meta( $term->term_id, 'idocs-faq-group-taxo-category', true );
			$faqgroup_category = IDOCS_Taxanomies::get_term_meta_caching(  $term->term_id, 'idocs-faq-group-taxo-category', false);

			if ( ! empty($faqgroup_category) ){
				
				// get the category full term using the category id
				//$cat_term = get_term($faqgroup_category);
				$cat_term = IDOCS_Taxanomies::get_specific_category_term_caching($faqgroup_category);
				/*--------------------------------------------*/
				if ( ! empty($cat_term) ) {
					
					$replacement = IDOCS_CPT::full_categories_slug($cat_term);
                    // Replace the placeholder with the hierarchical structure of the terms
					$term_link = str_replace( '%idocs-category-taxo%', $replacement , $term_link );
				}
			}
			else {

				$term_link = str_replace( '%idocs-category-taxo%/', 'root/' , $term_link );

			}
			/*--------------------------------------------*/
			// removing the last part of the url - faq term slug 
			//$parts = explode('/', rtrim($term_link, '/'));
			//$lastPart = array_pop($parts);
			//$term_link = implode('/', $parts) . '/';
			/*--------------------------------------------*/
			
		}
		/*--------------------------------------------*/
		if ('idocs-tag-taxo' == $taxonomy) { 

			// get the knowlege-base id stored as meta-data of tag ('idocs-category-taxo-kb')
			$tag_kb_id = IDOCS_Taxanomies::get_term_meta_caching(  $term->term_id, 'idocs-tag-taxo-kb', false);
			/*--------------------------------------------*/
			//do_action( 'qm/debug', $tag_kb_id );
			if ( ! empty($tag_kb_id) ){
				// get the knowledge-base full term using the kb id
				$kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($tag_kb_id);
				//do_action( 'qm/debug', $kb_term );
				if ( ! empty($kb_term) ) {
					
					$term_link = str_replace( '%idocs-kb-taxo%', $kb_term->slug , $term_link );
				}
			}
			/*--------------------------------------------*/
		}
		/*--------------------------------------------*/
		return $term_link;
	}
	/*---------------------------------------------------------------------------------------*/
	// Add custom fields (metadata) to the tag taxonomy. 
	// Action hook is {TAXONOMY}_add_form_fields.
	public function add_custom_fields_to_tag( $taxonomy ) {

		/*--------------------------------------------*/
		$terms = IDOCS_Taxanomies::get_kb_terms_caching();
		?>
		<!--------------------------------->
		<!-- KB Field-->
		<div class="form-field">
				
				<!-- Knowledge-base Field-->
				<!--------------------------------->
				<label for ="idocs-tag-taxo-kb"> <?php echo esc_attr__('Select a Knowledge Base', 'incredibledocs'); ?> </label>
				<select name="idocs-tag-taxo-kb" id="idocs-tag-taxo-kb" >
				
					<?php
					foreach ($terms as $term) {
						?>
						<option  value="<?php echo esc_attr($term->term_id);?>"><?php echo esc_attr($term->name);?></option>
						<?php
					}
					?>
				</select>
				<p id="knowledge-base-description">
					<?php echo esc_html__( 'Assign a knowledge base instance to the new tag. If empty, please create a knowledge base.', 'incredibledocs' );?>
				</p>
		</div>
		<!--------------------------------->
		<!-- Tag Color Field-->	
		<div class="form-field">
			<?php wp_nonce_field( 'idocs_save_custom_fields_action', 'idocs_save_custom_fields_nonce' ); ?>
			<label for ="idocs-tag-taxo-color"> <?php echo esc_attr__('Background Color', 'incredibledocs'); ?> </label>
			<input type="text" name="idocs-tag-taxo-color" class="wp-color-picker" value="#03c2fc" />
			<p id="tag-color-description">
				<?php echo esc_html__( 'Assign a background color to the tag to be used when displaying list of tags for a specific document.', 'incredibledocs' );?>
			</p>
		</div>
		<!--------------------------------->
		<?php
	}
	/*---------------------------------------------------------------------------------------*/
	// Handle the admin edit screen for the TAG custom taxonomy
	public function edit_custom_fields_to_tag( $term ) { 
		
		$terms = IDOCS_Taxanomies::get_kb_terms_caching();
		// get the stored custom meta data fields of the category term 
		$tag_color = get_term_meta( $term->term_id, 'idocs-tag-taxo-color', true );
		$tag_kb =  get_term_meta( $term->term_id, 'idocs-tag-taxo-kb', true );
	//	do_action( 'qm/debug', $tag_color );
		/*--------------------------------------------*/
		?>
		<tr class="form-field">
			<?php wp_nonce_field( 'idocs_save_custom_fields_action', 'idocs_save_custom_fields_nonce' ); ?>
			<th>
				<label for ="idocs-tag-taxo-kb"> <?php echo esc_attr__('Select a Knowledge Base', 'incredibledocs'); ?> </label>
			</th>
			<td>
				<select  name="idocs-tag-taxo-kb" disabled> 
						<?php
						foreach ($terms as $term) {

							$selected = selected( $tag_kb == $term->term_id, true, false );
							?>
							<option value="<?php echo esc_attr($term->term_id);?>" <?php echo esc_attr($selected);?>><?php echo esc_attr($term->name);?></option>
							<?php
						}
						?>
				</select>
				<p>A knowledge-base can't be changed for an existing tag. If needed, remove it and then create a new tag for a different knowledge-base.</p>
			</td>
		</tr>
		<tr class="form-field">
			<!--------------------------------->
			<th>
				<label for ="idocs-tag-taxo-color"> <?php echo esc_attr__('Tag Color', 'incredibledocs'); ?> </label>
			</th>
			<td>
				<input type="text" name="idocs-tag-taxo-color" class="wp-color-picker" value="<?php echo esc_attr($tag_color); ?>" />
				<p id="tag-color-description">
					<?php echo esc_html__( 'Assign a background color to the tag to be used when displaying list of tags for a specific document.', 'incredibledocs' );?>
				</p>
			</td>
		</tr>
		<?php  
	}
	/*---------------------------------------------------------------------------------------*/
	// save the custom fields when a new tag is saved. 
	public function save_custom_fields_new_tag( $term_id ) {

		if ( isset( $_POST['idocs_save_custom_fields_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['idocs_save_custom_fields_nonce'])), 'idocs_save_custom_fields_action' ) ) {
			/*--------------------------------------------*/
			// delete cache for terms meta-data 
			delete_transient( 'idocs_transient_terms_metadata' );
			delete_transient( 'idocs_transient_tag_terms_slugs' );
			delete_transient('idocs_transient_tag_terms');
			/*--------------------------------------------*/
			// Tag KB
			update_term_meta(
				$term_id,
				'idocs-tag-taxo-kb',
				sanitize_text_field( $_POST[ 'idocs-tag-taxo-kb' ] )
			);
			/*--------------------------------------------*/
			// Tag Color
			update_term_meta(
				$term_id,
				'idocs-tag-taxo-color',
				sanitize_text_field( $_POST[ 'idocs-tag-taxo-color' ] )
			);
			/*--------------------------------------------*/
		}
	}
	/*---------------------------------------------------------------------------------------*/
	// save the custom fields when an existing tag is saved. 
	public function save_custom_fields_existing_tag( $term_id ) {

		if ( isset( $_POST['idocs_save_custom_fields_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['idocs_save_custom_fields_nonce'])), 'idocs_save_custom_fields_action' ) ) {
			/*--------------------------------------------*/
			// delete cache for terms meta-data 
			delete_transient( 'idocs_transient_terms_metadata' );
			delete_transient( 'idocs_transient_tag_terms_slugs' );
			delete_transient('idocs_transient_tag_terms');
			/*--------------------------------------------*/
			// Tag Color
			update_term_meta(
				$term_id,
				'idocs-tag-taxo-color',
				sanitize_text_field( $_POST[ 'idocs-tag-taxo-color' ] )
			);
			/*--------------------------------------------*/
		}
	}
	/*---------------------------------------------------------------------------------------*/
	/* FAQ GROUP */
	/*---------------------------------------------------------------------------------------*/
	// Add custom fields (metadata) to the faqgroup taxonomy. 
	// Action hook is {TAXONOMY}_add_form_fields.
	public function add_custom_fields_to_faqgroup( $taxonomy ) {

		/*--------------------------------------------*/
		$terms = IDOCS_Taxanomies::get_kb_terms_caching();
		//do_action( 'qm/debug', $terms );
		/*--------------------------------------------*/
		?>
		<div class="form-field" id="idocs-faq-group-taxo-kb-div">
				<?php wp_nonce_field( 'idocs_save_custom_fields_action', 'idocs_save_custom_fields_nonce' ); ?>
				<!-- Knowledge-base Field-->
				<!--------------------------------->
				<label for ="idocs-faq-group-taxo-kb"> <?php echo esc_attr__('Select a Knowledge Base', 'incredibledocs'); ?> </label>
				<select name="idocs-faq-group-taxo-kb" id="idocs-faq-group-taxo-kb">
				
					<?php
					foreach ($terms as $term) {
						?>
						<option value="<?php echo esc_attr($term->term_id);?>"><?php echo esc_attr($term->name);?></option>
						<?php
					}
					?>
				</select>
				<p id="knowledge-base-description">
					<?php echo esc_html__( 'Assign a knowledge base instance to the new faq group. If empty, please create a knowledge base.', 'incredibledocs' );?>
				</p>
		</div>
		<!--------------------------------->			
		<div class="form-field" id="idocs-faq-group-taxo-category-div">
		<!-- Category Field-->
		<!--------------------------------->
				<label for ="idocs-faq-group-taxo-category"> <?php echo esc_attr__('Select a Category', 'incredibledocs'); ?> </label>
				<select name="idocs-faq-group-taxo-category" id="idocs-faq-group-taxo-category">
				
				</select>
				<p id="category-description">
					<?php echo esc_html__( 'Assign a category instance to the new faq group or place it under the knowledge base root. If empty, please create a category.', 'incredibledocs' );?>	
				</p>
		</div>
		<!--------------------------------->
		<!-- Category Order Field-->
		<!--------------------------------->
		<div class="form-field">
			<label for ="idocs-faq-group-taxo-order"> <?php echo esc_attr__('FAQ Group Order', 'incredibledocs'); ?> </label>
			<input type="number" id="idocs-faq-group-taxo-order" name="idocs-faq-group-taxo-order" style="width:80px" value="0" />
			<p id="faqgroup-order-description">
				<?php echo esc_html__( 'Optional - assign a number to the faq group to be used when displaying groups not alphabetically in specific order (1 - highest).', 'incredibledocs' );?>	
			</p>
		</div>
		<!--------------------------------->			
		<?php
	}
	/*---------------------------------------------------------------------------------------*/	
	// Handle the admin edit screen for the faqgroup custom taxonomy
	public function edit_custom_fields_to_faqgroup( $term ) { 

		/*--------------------------------------------*/
		// get the stored custom meta data fields of the category term 
		$faqgroup_order = get_term_meta( $term->term_id, 'idocs-faq-group-taxo-order', true );
		$faqgroup_kb =  get_term_meta( $term->term_id, 'idocs-faq-group-taxo-kb', true );
		$faqgroup_category =  get_term_meta( $term->term_id, 'idocs-faq-group-taxo-category', true );
	//	do_action( 'qm/debug', esc_attr($category_kb) );
		/*--------------------------------------------*/
		$terms = IDOCS_Taxanomies::get_kb_terms_caching();
		//do_action( 'qm/debug', esc_attr($category_order) );
		/*--------------------------------------------*/
		?>
		<tr class="form-field">
			<?php wp_nonce_field( 'idocs_save_custom_fields_action', 'idocs_save_custom_fields_nonce' ); ?>
			<!-- Knowledge-base -->
			<tr>
				<th>
					<label for="idocs-faq-group-taxo-kb"> <?php echo esc_html__('Knowledge Base', 'incredibledocs'); ?></label>
				</th>
				<!--------------------------------->
				<td>
					<select disabled name="idocs-faq-group-taxo-kb" id="idocs-faq-group-taxo-kb">
						<?php
						foreach ($terms as $term) {

							$selected = selected( $faqgroup_kb == $term->term_id, true, false );
							?>
							<option value="<?php echo esc_attr($term->term_id);?>" <?php echo esc_attr($selected);?>><?php echo esc_attr($term->name);?></option>
							<?php
						}
						?>
					</select>
					<p><?php echo esc_html__( "A knowledge base can't be changed for an existing faq-group. Create a new faq-group if a different knowledge base is needed.", 'incredibledocs' );?></p>							
				</td>
			</tr>
			<!--------------------------------->
			<!-- Category -->
			<tr>
				<th>
					<label for="idocs-faq-group-taxo-category"> <?php echo esc_html__('Category', 'incredibledocs'); ?></label>
				</th>
				<td>
					<select disabled name="idocs-faq-group-taxo-category" id="idocs-faq-group-taxo-category">
						<?php
						foreach ($terms as $term) {

							$selected = selected( $faqgroup_category == $term->term_id, true, false );
							?>
							<option value="<?php echo esc_attr($term->term_id);?>" <?php echo esc_attr($selected);?>><?php echo esc_attr($term->name);?></option>
							<?php
						}
						?>
					</select>
					<p><?php echo esc_html__( "A category can't be changed for an existing faq-group. Create a new faq-group if a different category is needed.", 'incredibledocs' );?></p>							
				</td>
			</tr>
			<!--------------------------------->
			<!-- FAQ-Group Order -->
			<tr>
				<th>
					<label for="idocs-faq-group-taxo-order"> <?php echo esc_attr__('FAQ Group Order', 'incredibledocs'); ?></label>
				</th>
				<td>
					<input type="number" id= "idocs-faq-group-taxo-order" name="idocs-faq-group-taxo-order" value="<?php echo esc_attr($faqgroup_order); ?>">
				</td>
			</tr>
			<!--------------------------------->
		</tr>
		<?php  

	}
	/*---------------------------------------------------------------------------------------*/	
	// save the custom fields when a new faq group is saved. 
	public function save_custom_fields_new_faqgroup( $term_id ) {

		if ( isset( $_POST['idocs_save_custom_fields_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['idocs_save_custom_fields_nonce'])), 'idocs_save_custom_fields_action' ) ) {

			/*--------------------------------------------*/
			$kb_id = sanitize_text_field( $_POST[ 'idocs-faq-group-taxo-kb' ]);
			$category_id = sanitize_text_field( $_POST[ 'idocs-faq-group-taxo-category' ] );
			delete_transient( 'idocs_transient_faqgroups_terms_' . $kb_id );
			// delete cache for terms meta-data 
			delete_transient( 'idocs_transient_terms_metadata' );
			// delete cache for faqs schema related to that kb
			delete_transient( 'idocs_transient_faqs_schema_for_seo_' . $kb_id );
			
			/*--------------------------------------------*/
			// ORDER
			update_term_meta(
				$term_id,
				'idocs-faq-group-taxo-order',
				sanitize_text_field( $_POST[ 'idocs-faq-group-taxo-order' ] )
			);
			/*--------------------------------------------*/
			// KB
			update_term_meta(
				$term_id,
				'idocs-faq-group-taxo-kb',
				sanitize_text_field( $_POST[ 'idocs-faq-group-taxo-kb' ] )
			);
			/*--------------------------------------------*/
			// Category
			update_term_meta(
				$term_id,
				'idocs-faq-group-taxo-category',
				sanitize_text_field( $_POST[ 'idocs-faq-group-taxo-category' ] )
			);
			/*--------------------------------------------*/
		}
	}	
	/*---------------------------------------------------------------------------------------*/
	// save the custom fields when an existing faq group is saved. 
	public function save_custom_fields_existing_faqgroup( $term_id ) {

		// When checking a nonce using wp_verify_nonce you will need to sanitize the input using wp_unslash AND sanitize_text_field
		if ( isset( $_POST['idocs_save_custom_fields_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ($_POST['idocs_save_custom_fields_nonce'])), 'idocs_save_custom_fields_action' ) ) {
			/*--------------------------------------------*/
			$kb_id =  get_term_meta( $term_id, 'idocs-faq-group-taxo-kb', true );
			//$category_id =  get_term_meta( $term_id, 'idocs-faq-group-taxo-category', true );
			//error_log($kb_id);
			//error_log($category_id);
			delete_transient( 'idocs_transient_faqgroups_terms_' . $kb_id );
			delete_transient( 'idocs_transient_faqgroup_terms_slugs' );
			
			// delete cache for terms meta-data 
			delete_transient( 'idocs_transient_terms_metadata' );
			// delete cache for faqs schema related to that kb
			delete_transient( 'idocs_transient_faqs_schema_for_seo_' . $kb_id );
			/*--------------------------------------------*/
			// ORDER
			update_term_meta(
				$term_id,
				'idocs-faq-group-taxo-order',
				sanitize_text_field( $_POST[ 'idocs-faq-group-taxo-order' ] )
			);
			/*--------------------------------------------*/
		}
	}	
	/*---------------------------------------------------------------------------------------*/
	public function faqgroup_is_deleted($term, $tt_id, $deleted_term, $object_ids) {

		//$faqgroup_kb =  get_term_meta( $tt_id, 'idocs-faq-group-taxo-kb', true );
		//$faqgroup_category =  get_term_meta( $tt_id, 'idocs-faq-group-taxo-category', true );

		// get the list of knowledge-bases 
        $kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
		/*------------------------------------------------*/
		foreach ($kb_terms as $kb_term) {

			$kb_id = $kb_term->term_id;
			delete_transient( 'idocs_transient_faqgroups_terms_' . $kb_id );

		};
		/*------------------------------------------------*/
		delete_transient( 'idocs_transient_attached_terms_' . 'idocs-faq-group-taxo' );

	}
	/*---------------------------------------------------------------------------------------*/
	public static function total_content_type_in_kb ( $content_type_name, $kb_id ) {

		$content_type_term = get_term_by('name', $content_type_name , 'idocs-content-type-taxo' );
		/*------------------------------------------------*/
		if ( $content_type_term != null ) {

			// get the list of all faqs related to specific knowledge-base
			$args = array(

				'post_type' => 'idocs_content',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
				/*--------------------------------------------*/
				'tax_query' => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'idocs-kb-taxo',
						'field' => 'term_id',
						'terms' => $kb_id,
						'operator' => 'IN',
						//'include_children' => false,
					),
					array(
						'taxonomy'         => 'idocs-content-type-taxo', 
						'field'            => 'term_id',
						'terms'            =>  $content_type_term->term_id, 
						'operator'         => 'IN',
					),
				),
			);
			/*--------------------------------------------*/
			$the_query = new WP_Query( $args );
			return $the_query->post_count;
		}
		/*--------------------------------------------*/
		return null;
	}
	/*---------------------------------------------------------------------------------------*/
	public static function total_faq_groups_in_kb ( $kb_id ) {

		// get all faq-groups related to specific knowledge-base
		$faq_groups = get_terms( array(

			'taxonomy'   => 'idocs-faq-group-taxo',
			'hide_empty' => false,
			'meta_query' => array(

				array(
					'key'     => 'idocs-faq-group-taxo-kb',
					'value'   => $kb_id,
					'compare' => '=',
				),
			),
			'orderby' => 'name',
			'order' => 'ASC', 
		) );
		/*--------------------------------------------*/
		//do_action( 'qm/debug', $faq_groups );
		return count($faq_groups);

	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_term_meta_caching( $term_id, $key, $key_is_array ) {

		// cache data is removed when any term metadata is added/updated
		//delete_transient('idocs_transient_terms_metadata');
		$cached_data =  get_transient( 'idocs_transient_terms_metadata');
		// If the cached data is not found, fetch it from the database
		// or that info for that term id is not available in the cache
		if ( false === $cached_data || !(isset($cached_data[$term_id])) ) {

			//error_log('no cache data or the info for that term not available');
			// get the complete term metadata object (not specific meta-data key)
			$term_metadata =  get_term_meta( $term_id );
			//do_action( 'qm/debug', $term_metadata[$key][0]);
			//do_action( 'qm/debug', $term_metadata);
			//$key_meta_data =  get_term_meta( $term_id, $key, true );

			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			// scenario #2 - cache data avialable but not on that term object
			//error_log('setting cache for that term');
			// add the complete term metadata to the cached_data array 
			$cached_data[$term_id] = $term_metadata;
			set_transient( 'idocs_transient_terms_metadata', $cached_data, 10800);
			// check if the key is available 
			if (isset($term_metadata[$key])) {

				// the $key is also an array so accessing the first item
				$key_value = $term_metadata[$key][0];
				//return $key_value;
				if ($key_is_array) {
					return unserialize($key_value);	
				}
				else {
					return $key_value;
				}
				
			}
			else {
				// empty array
				return [];
			}
		}
		/*--------------------------------------------*/
		// cached data found and also data is available for that term object
		else {

			//error_log('getting term meta-data from the cache');
			// check if the key is available 
			if (isset($cached_data[$term_id][$key])) {
				
				// the $key is also an array so accessing the first item
				$key_value = $cached_data[$term_id][$key][0];
				if ($key_is_array) {
					return unserialize($key_value);	
				}
				else {
					return $key_value;
				}
			}
			else {
				// empty array
				return [];
			}
		}
	}
	/*---------------------------------------------------------------------------------------*/					
	public static function get_specific_category_term_caching( $term_id ) {

		// cache data is removed when any category term is added/updated
		$cached_data =  get_transient( 'idocs_transient_category_terms');
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		// or that info for that term_id is not available in the cache
		if ( false === $cached_data || !(isset($cached_data[$term_id])) ) {

			//error_log('no cache data or the info for that term not available');
			// get the complete term object 
			//$category_term = get_term($term_id, 'idocs-category-taxo' );
			$category_term = get_term_by('id', $term_id, 'idocs-category-taxo');
			//do_action( 'qm/debug', $category_term);
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			// scenario #2 - cache data avialable but not on that term object
			//error_log('setting cache for that category term');
			// add the complete term object to the cached_data array
			$cached_data[$term_id] = $category_term;
			set_transient( 'idocs_transient_category_terms', $cached_data, 10800);
			//do_action( 'qm/debug', $category_term );
			return $category_term;
		}
		// cached data found and also data is available for that term object
		else {

			//error_log('getting term object from the cache');
			//do_action( 'qm/debug', $cached_data[$term_id] );
			return $cached_data[$term_id];
	
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_specific_category_term_by_slug_caching( $term_slug ) {

		// cache data is removed when any category term is added/updated
		$cached_data =  get_transient( 'idocs_transient_category_terms_slugs');
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		// or that info for that term_id is not available in the cache
		if ( false === $cached_data || !(isset($cached_data[$term_slug])) ) {

			//error_log('no cache data or the info for that term not available');
			// get the complete term object 
			$category_term = get_term_by('slug', $term_slug, 'idocs-category-taxo');
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			// scenario #2 - cache data avialable but not on that term object
			//error_log('setting cache for that category term');
			// add the complete term object to the cached_data array 
			$cached_data[$term_slug] = $category_term;
			set_transient( 'idocs_transient_category_terms_slugs', $cached_data, 10800);
			//do_action( 'qm/debug', $category_term );
			return $category_term;
		}
		/*--------------------------------------------*/
		// cached data found and also data is available for that term object
		else {

			//error_log('getting term object from the cache');
			//do_action( 'qm/debug', $cached_data[$term_slug] );
			return $cached_data[$term_slug];
	
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_specific_tag_term_by_slug_caching($term_slug) {

		// cache data is removed when any category term is added/updated
		$cached_data =  get_transient( 'idocs_transient_tag_terms_slugs');
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		// or that info for that term_id is not available in the cache
		if ( false === $cached_data || !(isset($cached_data[$term_slug])) ) {

			//error_log('no cache data or the info for that term not available');
			// get the complete term object 
			$tag_term = get_term_by('slug', $term_slug, 'idocs-tag-taxo');
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			// scenario #2 - cache data avialable but not on that term object
			//error_log('setting cache for that tag term');
			// add the complete term object to the cached_data array 
			$cached_data[$term_slug] = $tag_term;
			set_transient( 'idocs_transient_tag_terms_slugs', $cached_data, 10800);
			//do_action( 'qm/debug', $tag_term );
			return $tag_term;
		}
		/*--------------------------------------------*/
		// cached data found and also data is available for that term object
		else {

			//error_log('getting term object from the cache');
			//do_action( 'qm/debug', $cached_data[$term_slug] );
			return $cached_data[$term_slug];
	
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_specific_faqgroup_term_by_slug_caching($term_slug) {

		// cache data is removed when any category term is added/updated
		$cached_data =  get_transient( 'idocs_transient_faqgroup_terms_slugs');
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		// or that info for that term_id is not available in the cache
		if ( false === $cached_data || !(isset($cached_data[$term_slug])) ) {

			//error_log('no cache data or the info for that term not available');
			// get the complete term object 
			$tag_term = get_term_by('slug', $term_slug, 'idocs-faq-group-taxo');
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			// scenario #2 - cache data avialable but not on that term object
			//error_log('setting cache for that tag term');
			// add the complete term object to the cached_data array 
			$cached_data[$term_slug] = $tag_term;
			set_transient( 'idocs_transient_faqgroup_terms_slugs', $cached_data, 10800);
			//do_action( 'qm/debug', $tag_term );
			return $tag_term;
		}
		/*--------------------------------------------*/
		// cached data found and also data is available for that term object
		else {

			//error_log('getting term object from the cache');
			//do_action( 'qm/debug', $cached_data[$term_slug] );
			return $cached_data[$term_slug];
	
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public static function get_the_terms_caching($post_id, $taxonomy) {

		// cache data is removed when any cpt is added/updated (as related terms of that post can change)
		$cached_data =  get_transient( 'idocs_transient_attached_terms_' . $taxonomy );
		/*--------------------------------------------*/
		// If the cached data is not found, fetch it from the database
		// or that info for that post_id is not available in the cache
		if ( false === $cached_data || !( isset($cached_data[$post_id]) ) ) {

			//error_log('no cache data or the info for that post not available');
			// get the attached terms from that post
			$attached_terms = get_the_terms( $post_id, $taxonomy );
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			// scenario #2 - cache data available but not on that post
			//error_log('setting cache for that post - attached terms');
			// add the complete term object to the cached_data array 
			$cached_data[$post_id] = $attached_terms;
			set_transient( 'idocs_transient_attached_terms_' . $taxonomy, $cached_data, 10800);
			//do_action( 'qm/debug', $category_term );
			return $attached_terms;

		}
		/*--------------------------------------------*/
		// cached data found and also data is available for that post
		else {

			//error_log('getting attached terms of that post from the cache');
			//do_action( 'qm/debug', $cached_data[$term_slug] );
			return $cached_data[$post_id];
	
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public function prevent_term_deletion_if_posts_exist($term_id, $taxonomy) {

		// Check if the taxonomy is the one you want to restrict
		if ($taxonomy == 'idocs-faq-group-taxo') {
			// Check if there are any posts associated with this term
			$args = array(
				'post_type' => 'idocs_content', // You can specify a custom post type if needed
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $term_id,
					),
				),
				'fields' => 'ids', // Only get post IDs
				'posts_per_page' => 1, // Limit to 1 for performance
			);
	
			$posts = get_posts($args);
	
			if (! empty($posts) ) {

				
				update_option( 'idocs_admin_notice', array(
					'message' => __( 'Please remove any associated FAQs before removing the FAQs Group.', 'incredibledocs' ),
					'type' => 'warning'
				) );
				
			
				//wp_safe_redirect(add_query_arg(array('taxonomy' => $taxonomy, 'tag_ID' => $term_id, 'message' => 'term_deletion_error'), admin_url('edit-tags.php')));
				//wp_die(-1);
				wp_safe_redirect(wp_get_referer());
				wp_die();

			}
			
		}
	}
	/*---------------------------------------------------------------------------------------*/
	public function show_term_deletion_error_message() {

		$option      = get_option( 'idocs_admin_notice' );
		if (isset( $option['message'] ) ) {

			wp_admin_notice($option['message'],
				array(
					'type'				 => $option['type'],
					'dismissible'        => true,
				)
			);

		}
		delete_option('idocs_admin_notice');
	}
	
}
/*---------------------------------------------------------------------------------------*/
// https://wpsites.net/wordpress-admin/custom-permalink-structure-for-custom-post-type-taxonomies/
// https://wordpress.stackexchange.com/questions/39500/how-to-create-a-permalink-structure-with-custom-taxonomies-and-custom-post-types
// https://typerocket.com/ultimate-guide-to-custom-post-types-in-wordpress/
// https://metabox.io/custom-fields-vs-custom-taxonomies/#:~:text=their%20main%20purposes.-,What's%20the%20difference%20between%20custom%20fields%20and%20taxonomies%3F,and%20taxonomies%20here%20is%20grouping
// https://www.smashingmagazine.com/2014/08/customizing-wordpress-archives-categories-terms-taxonomies/
// https://www.smashingmagazine.com/2012/01/create-custom-taxonomies-wordpress/
// https://rudrastyh.com/wordpress/add-custom-fields-to-taxonomy-terms.html