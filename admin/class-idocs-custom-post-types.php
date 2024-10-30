<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* Creates the 'idocs_content' custom post type, handle cpt permalinks 
/*---------------------------------------------------------------------------------------*/
class IDOCS_CPT {
    
    /*---------------------------------------------------------------------------------------*/
    // a callback function to flush the rewrite rules after adding the cpt and taxonomies 
    public function flush_rewrite_rules_after_new_cpt () {

        flush_rewrite_rules(); 
        //error_log('flush rewrite rules');
    }
    /*---------------------------------------------------------------------------------------*/
    // Creates the 'idocs_content' custom post type 
    public function add_custom_post_type_idocs_content() {

        // for checking all available custom post type lables: 
        // https://developer.wordpress.org/reference/functions/get_post_type_labels/
        
        $idocs_kbs_root_slug = IDOCS_Database::get_plugin_settings('idocs_kbs_root_slug');
        /*--------------------------------------------*/
        $labels = array(
            'name'                => __('Content Items', 'incredibledocs'),
            'singular_name'       => __('Content Item (idocs)', 'incredibledocs'),
            'menu_name'           => esc_html__('IncredibleDocs', 'incredibledocs'),
            'parent_item_colon'   => null,
            'all_items'           => __( 'All Content', 'incredibledocs'),
            'view_item'           => __( 'View Content', 'incredibledocs'),
            'add_new_item'        => __( 'Add New Content', 'incredibledocs'),
            'add_new'             => __( 'Add New Content', 'incredibledocs'),
            'edit_item'           => __( 'Edit Content', 'incredibledocs'),
            'update_item'         => __( 'Update Content', 'incredibledocs'),
            'search_items'        => __( 'Search Content', 'incredibledocs'),
            'not_found'           => __( 'Not found', 'incredibledocs'),
            'not_found_in_trash'  => __( 'Not found in trash', 'incredibledocs'),
        );
        /*--------------------------------------------*/
        // https://developer.wordpress.org/reference/functions/register_post_type/

        // Set other options for Custom Post Type - declare additional WP admin form fields 
        $supports = array( 'title', 'editor', 'excerpt', 'author', 
                            'thumbnail', 'comments', 'revisions', 'custom-fields' );
        /*--------------------------------------------*/
        $description = __( 'Description.', 'incredibledocs'); 
        /*--------------------------------------------*/
        $args = array(
            'label'               => 'Content',
            'labels'              => $labels,
            'description'         => $description,
            'public'              => true,
            'hierarchical'        => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'show_ui'             => true, // Whether to generate and allow a UI for managing this post type in the admin.
            'show_in_menu'        => false, // Where to show the post type in the admin menu. If true, the post type is shown in its own top level menu.
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'show_in_rest'        => true, // Enable the REST API- required for Gutenberg/block editor 
            'menu_position'       => 2,
            'menu_icon'           => 'dashicons-dashboard',
           
            // if this is true, WordPress will automatically map the necessary meta capabilities based on the provided capability_type.
            'map_meta_cap'        => true,
            //'map_meta_cap'        => false,  
            // defines the base capability that is used for handling access to the custom post type. 
            // We used the cpt name but it can be any prefix. 
            'capability_type'     => ['idocs_content', 'idocs_contents'],
            
            'supports'            => $supports,
            'taxonomies'          => array(  'idocs-kb-taxo', 'idocs-category-taxo', 'idocs-tag-taxo', 'idocs-faq-group-taxo'),
            'has_archive'         => true,
            
            'rewrite'            => array(
                // using a unique base slug for the cpt ("-content") vs taxonomies to avoid conflicts in the URL structure.
                'slug'          => $idocs_kbs_root_slug . '-content' . '/%idocs-kb-taxo%/%idocs-category-taxo%',
                'with_front'    => false,
               
                ), 

            'query_var'          => true,
            'can_export'          => true, // Whether to allow this post type to be exported. Default true.

        );
        /*--------------------------------------------*/
        // Registering the Custom Post Type (5e5827093d)
        register_post_type( 'idocs_content', $args );
        // Flush rewrite rules after registering the custom post type
        flush_rewrite_rules(); 

    }
    /*---------------------------------------------------------------------------------------*/
    public static function full_categories_slug ( $category_term ) {

        // Add the current term slug to the array
        $term_slugs[] = $category_term->slug;
        // Get the parent term if it exists
        while ( $category_term->parent ) {
            $category_term = get_term($category_term->parent, 'idocs-category-taxo' );
            //$cat_term = IDOCS_Taxanomies::get_specific_category_term_caching($category_term->parent);
           // do_action( 'qm/debug', $category_term );
           // do_action( 'qm/debug', $cat_term );
            $term_slugs[] = $category_term->slug;
        }
        /*--------------------------------------------*/
        // Reverse the array to get the hierarchical structure from parent to child
        $term_slugs = array_reverse( $term_slugs );
        $full_slug = implode( '/', $term_slugs );
        /*--------------------------------------------*/
        return $full_slug;

    }
    /*---------------------------------------------------------------------------------------*/
    public function content_posts_permalink( $post_link, $post = null ) {
        
        //error_log( $post_link );
        //error_log( $post->post_type );
        //error_log( $post->ID );
        /*--------------------------------------------*/
        //Return if this is not a 'idocs_content' custom post type
        if ( 'idocs_content' != $post->post_type ) {
            return $post_link;
        }
        /*--------------------------------------------*/
        // don't replace it with caching!
        $content_type_id = get_post_meta($post->ID, 'idocs-content-type-meta', true);
        // if this is a new temporary post - user clicked on "New Content" before saving the post
        if ($content_type_id == 0)  {
            
            return $post_link;
            
        }
		$content_term = get_term_by('id', $content_type_id, 'idocs-content-type-taxo');
		/*--------------------------------------------*/
        switch ( $content_term->name ) {

            /*--------------------------------------------*/
            case "Document":
                //error_log($post_link);
                // get the document category using the document id 
                //$category_terms = get_the_terms( $post->ID, 'idocs-category-taxo' );
                $category_terms = IDOCS_Taxanomies::get_the_terms_caching( $post->ID, 'idocs-category-taxo' );
                //do_action( 'qm/debug', $category_terms);
                // category term exist
                if ( $category_terms ) {
                    $category_term = $category_terms[0];
                    $replacement = IDOCS_CPT::full_categories_slug($category_terms[0]);
                    // Replace the placeholder with the hierarchical structure of the terms
                    $post_link = str_replace( '%idocs-category-taxo%', $replacement, $post_link );
                    //$post_link = str_replace( '%idocs-category-taxo%', $category_term->slug, $post_link );
                }
                //$kb_terms = get_the_terms( $post->ID, 'idocs-kb-taxo' );
                $kb_terms = IDOCS_Taxanomies::get_the_terms_caching( $post->ID, 'idocs-kb-taxo' );
                // kb term exist
                if ( $kb_terms)  {
                    $post_link = str_replace( '%idocs-kb-taxo%', $kb_terms[0]->slug, $post_link );
                }

                if ($category_terms == null || $kb_terms == null) {
                    $post_link = esc_url("kb_or_category_is_not_configured");
                }
                //error_log($post_link);
                break;
            /*--------------------------------------------*/
            case "Link":
                $link = IDOCS_CPT::get_post_meta_caching($post->ID, 'idocs-content-link-meta');
                //$link = get_post_meta($post->ID, 'idocs-content-link-meta', true);
                if (empty($link)) {
                    $post_link = esc_url("link_is_not_configured");
                }
                else {
                    $post_link = esc_url($link);
                }
                break;
            /*--------------------------------------------*/
            case "FAQ":

                //$faqgroup_id = IDOCS_CPT::get_post_meta_caching($post->ID, 'idocs-faq-group-meta');
                $faqgroup_terms = IDOCS_Taxanomies::get_the_terms_caching( $post->ID, 'idocs-faq-group-taxo' );
                //do_action( 'qm/debug', $faqgroup_terms);

                // faq group term exist
                if ( $faqgroup_terms ) {
                    $faqgroup_term = $faqgroup_terms[0];
                    $faqgroup_id = $faqgroup_term->term_id;
                    $post_link = get_term_link((int) $faqgroup_id, 'idocs-faq-group-taxo' );
                } else {

                    $post_link = esc_url("faq_group_is_not_configured");

                }
                /*--------------------------------------------*/
                break;
                
            /*--------------------------------------------*/               
            case "Internal-Video":
                //$video_url = get_post_meta($post->ID, 'idocs-content-video-url-meta', true);
                $video_url = IDOCS_CPT::get_post_meta_caching($post->ID, 'idocs-content-video-url-meta');

                if (empty( $video_url )) {
                    $post_link = esc_url("video_url_is_not_configured");
                }
                else {
                    $post_link = esc_url( $video_url);
                }
                break;
            /*--------------------------------------------*/               
            case "YouTube-Video":
               // $video_url = get_post_meta($post->ID, 'idocs-content-video-yturl-meta', true);
                $video_url = IDOCS_CPT::get_post_meta_caching($post->ID, 'idocs-content-video-yturl-meta');

                if (empty( $video_url )) {
                    $post_link = esc_url("video_url_is_not_configured");
                }
                else {
                    $post_link = esc_url( $video_url);
                }
                break;                
        }
        /*--------------------------------------------*/
        return $post_link;
    }
    /*---------------------------------------------------------------------------------------*/
    public static function get_post_meta_caching($post_id, $key) {

		// cache data is removed when any post metadata is added/updated
		$cached_data =  get_transient( 'idocs_transient_posts_metadata');
		// If the cached data is not found, fetch it from the database
		// or that info for that term id is not available in the cache
		if ( false === $cached_data || !(isset($cached_data[$post_id])) ) {

			//error_log('no cache data or the info for that post metadata not available');
			// get the complete term metadata object (not specific meta-data key)
			$post_metadata =  get_post_meta( $post_id );
			//do_action( 'qm/debug', $term_metadata[$key][0]);
			//$key_meta_data =  get_term_meta( $term_id, $key, true );
			// scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			// scenario #2 - cache data avialable but not on that term object
			//error_log('setting cache for that post metadata');
			// add the complete post metadata to the cached_data array 
			$cached_data[$post_id] = $post_metadata;
			set_transient( 'idocs_transient_posts_metadata', $cached_data, 10800);
			// check if the key is available 
			if (isset($post_metadata[$key])) {
				return $post_metadata[$key][0];
			}
			else {
				// empty array
				return [];
			}
			
		}
		// cached data found and also data is available for that term object
		else {

			//error_log('getting post meta-data from the cache');
			// check if the key is available 
			if (isset($cached_data[$post_id][$key])) {
				// the $key is also an array so accessing the first item
				return $cached_data[$post_id][$key][0];
			}
			else {
				// empty array
				return [];
			}
		}
	}
    /*---------------------------------------------------------------------------------------*/
    public static function document_navigation_links($post_id, $category_id ) {

        //delete_transient('idocs_transient_navigation_links');
        $cached_data =  get_transient( 'idocs_transient_navigation_links');

        if ( false === $cached_data || !(isset($cached_data[$post_id])) ) {

            $document_content_type_term = get_term_by('name', 'Document' , 'idocs-content-type-taxo' );
            $current_post_order = self::get_post_meta_caching($post_id, 'idocs-content-display-order-meta');
            /*--------------------------------------------*/
            $next_post = get_posts(array(
                
                'post_type' => 'idocs_content',
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'idocs-category-taxo',
                        'field' => 'id', 
                        'terms' => $category_id,
                        'include_children' => false, // Exclude posts from sub-categories
                    ),
                    array(
                        'taxonomy'         => 'idocs-content-type-taxo', 
                        'field'            => 'term_id',
                        'terms'            =>  $document_content_type_term->term_id, 
                        'operator'         => 'IN',
                    ),
                ),
                'meta_key' => 'idocs-content-display-order-meta',
                'meta_value' => $current_post_order,
                'meta_compare' => '>',
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'posts_per_page' => 1

            ));
            /*--------------------------------------------*/
            $previous_post = get_posts(array(
                'post_type' => 'idocs_content',
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'idocs-category-taxo',
                        'field' => 'id', 
                        'terms' => $category_id,
                        'include_children' => false, // Exclude posts from sub-categories
                    ),
                    array(
                        'taxonomy'         => 'idocs-content-type-taxo', 
                        'field'            => 'term_id',
                        'terms'            =>  $document_content_type_term->term_id, 
                        'operator'         => 'IN',
                    ),
                ),
                'meta_key' => 'idocs-content-display-order-meta',
                'meta_value' => $current_post_order,
                'meta_compare' => '<',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'posts_per_page' => 1
            ));
            /*--------------------------------------------*/
            $links_array = array ();
            
            if ($previous_post) {
                $links_array[0] = get_permalink($previous_post[0]->ID);    
            }
            else {
                $links_array[0] = null;
            }

            if ($next_post) {
                $links_array[1] = get_permalink($next_post[0]->ID);    
            }
            else {
                $links_array[1] = null;
            }

            // scenario #1 - no cache data 
			if ( false === $cached_data) {
				// create empty array
				$cached_data = []; 
			}
			/*--------------------------------------------*/
			// scenario #1 - no cache data 
			// scenario #2 - cache data avialable but not on that term object

			// add the navigation_links of that specific post to the cached_data array 
			$cached_data[$post_id] = $links_array;
            //error_log('storing navigation links');
			set_transient( 'idocs_transient_navigation_links', $cached_data, 10800);
            return $links_array;
        }
        // cached data found and also data is available for that specific post
        else {

            //error_log('access from cache - navigation links');
            return $cached_data[$post_id];
        }
    }
    /*---------------------------------------------------------------------------------------*/
    public function clear_related_cache_for_content_items ( $post_id ) {

        // exit if it is not our cpt
        if ( ! get_post_type($post_id) == 'idocs_content' ) {

            exit;
            
        }
        /*---------------------------------------*/
        // adding or updating a content item (cusom post type) --> removing the caching data on cpts
        delete_transient( 'idocs_transient_direct_content_flags');
        delete_transient( 'idocs_transient_posts_metadata');
        delete_transient( 'idocs_transient_total_content_types' );
        delete_transient( 'idocs_transient_navigation_links' );
        delete_transient( 'idocs_transient_faqs_per_group');
        delete_transient( 'idocs_transient_attached_terms_' . 'idocs-category-taxo');
        delete_transient( 'idocs_transient_attached_terms_' . 'idocs-kb-taxo');
        delete_transient( 'idocs_transient_attached_terms_' . 'idocs-tag-taxo');
        delete_transient( 'idocs_transient_attached_terms_' . 'idocs-faq-group-taxo');
        /*---------------------------------------*/

    }
    /*---------------------------------------------------------------------------------------*/
}

// https://typerocket.com/ultimate-guide-to-custom-post-types-in-wordpress/
// https://www.smashingmagazine.com/2015/04/extending-wordpress-custom-content-types/