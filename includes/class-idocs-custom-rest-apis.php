<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
// Registering a list of REST API Endpoints to be used across the plugin.
/*---------------------------------------------------------------------------------------*/
/*
Authentication - 
For developers making manual Ajax requests, the nonce will need to be passed with each request.
The API uses nonces with the action set to wp_rest. 
These can then be passed to the API via the _wpnonce data parameter (either POST data or in the query for GET requests), or via the X-WP-Nonce header. 

*/
/*---------------------------------------------------------------------------------------*/
class IDOCS_Custom_RestAPIs {
    
    protected $admin_capability = "manage_options";
    protected $edit_capability = "edit_idocs_contents";
    /*---------------------------------------------------------------------------------------*/
    /* SEARCHES */
    /*---------------------------------------------------------------------------------------*/
    // Register custom route for searching documents using a keyword
    // Public-Facing API
    public function register_custom_route_search_keyword () {

        register_rest_route( 
            'incredibledocs/v1', // $namespace:string
            'search',            // $route:string
            array(               // $args:array
                'methods'  => WP_REST_SERVER::READABLE,  // constant inside the wp class which is 'GET' 
                'callback' => array($this, 'search_results'),

                /*
                To restrict access to your Public API endpoint only to requests originating from your plugin's frontend page:

                1. Generate a nonce in your frontend page (When generating your plugin's frontend page, include a unique nonce.)
                2. Include the nonce in API requests (When making API requests from your frontend page, include the nonce in the request.)
                3. (only needed for public api) Verify the nonce on the server side: In your REST API endpoint callback function, verify that the nonce included in the request matches the one generated for your plugin's frontend page. If they match, proceed with the request; otherwise, reject it.

                */
                'permission_callback' => '__return_true',
                
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    // callback function for performing the search based on input parameters ($data) 
    public function search_results( $data ) {

        /*------------------------------------------------*/
        // for public API (no logged-in users), we need to manually verify the nonce
        if ( !$_SERVER['HTTP_X_WP_NONCE'] || !wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'], 'wp_rest' ) ) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        /*------------------------------------------------*/
        $kb_id = absint(sanitize_text_field($data ['kb_id']));   // knowledge base id number
        $cat_id = absint(sanitize_text_field($data ['cat_id'])); // category id number 
        $internal_user = absint(sanitize_text_field($data ['internal_user']));
        $user_id = absint(sanitize_text_field($data ['user_id']));
        $search_string = sanitize_text_field($data['keyword']);
        $order_by = sanitize_text_field($data['order_by']);
        $pro = absint(sanitize_text_field($data['pro']));
        /*------------------------------------------------*/
        // define the meta_query based on the cat_id 
        switch ($cat_id) {
            case 0: // Search documents in ALL categories 
                
                $meta_query = array(
                    array(
                        'key' => 'idocs-content-kb-meta',
                        'value' => $kb_id,
                        'compare' => '=',
                    ),
                );
                break;
            /*------------------------------------------------*/
            default: // Search documents in specific root category 
                $meta_query = array(
                    'relation' => 'AND',
                    array(
                        'key' => 'idocs-content-kb-meta',
                        'value' => $kb_id,
                        'compare' => '=',
                    ),
                    array(
                        // the following key is a meta-data added to a document to indicate the root category 
                        'key' => 'idocs-parent-category-meta',
                        'value' => $cat_id,
                        'compare' => '=',
                    )
                );
        };
        /*------------------------------------------------*/
        // search in all content items 
        $raw_query = new WP_Query(array(
           'post_type'  => array ('idocs_content'), 
            // filter the relevant searched keyword 
            's'         => $search_string,
            'search_columns' => ['post_title'],
            //'title' => $search_string,
            'exact'      => false, 
            // filter the documents from the relevant knowledge-base
            'meta_query' => $meta_query,
            'orderby'   => $order_by,
            'order'     => 'ASC' 
        ));
        /*------------------------------------------------*/
        $optimized_output = array(

          // init empty content array 
          'content' => array(),
          // add the required icons for each content type to be used in js
          'icons' => array(

            'Document' => IDOCS_ICONS::get_icon_svg_tag("rectangle-list", 0, 0),
            'Link' => IDOCS_ICONS::get_icon_svg_tag("link", 0, 0),
            'FAQ' => IDOCS_ICONS::get_icon_svg_tag("circle-question", 0, 0),
            'YouTube-Video' => IDOCS_ICONS::get_icon_svg_tag("youtube", 0, 0),
            'Internal-Video' => IDOCS_ICONS::get_icon_svg_tag("video", 0, 0),
            'Tag' => IDOCS_ICONS::get_icon_svg_tag("tags", 0, 0),

          ),

          'counters' => array(

            'Document' => 0,
            'Link' => 0,
            'FAQ' => 0,
            'Video' => 0,
            'Internal-Video' => 0,
            'YouTube-Video' => 0,
            'Tag' => 0,

          ),
        );
        /*------------------------------------------------*/
        // reduce the output of the query to title and permalink 
        while ( $raw_query->have_posts() ) {

            $raw_query->the_post();
            $post_id = get_the_ID();
            /*------------------------------------------------*/
            
            /*------------------------------------------------*/
            // check the access for each document 
            $access_allowed = IDOCS_Access_Check::check_access_to_content_item( $kb_id, $post_id, $internal_user, $user_id );
            //error_log("access_allowed:" . $access_allowed);
            if ( $access_allowed ) {

                $category_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-category-meta');
                //error_log($category_id);
                if ((int) $category_id != 0) {
                    //$category_name = get_term_by('id', $category_id, 'idocs-content-type-taxo');
                    $category_term = IDOCS_Taxanomies::get_specific_category_term_caching($category_id);
                    $category_name = $category_term->name;
                }
                else {
                    $category_name = '';
                }
                /*------------------------------------------------*/
                $content_type_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-type-meta');
                $content_term = get_term_by('id', $content_type_id, 'idocs-content-type-taxo');
                $content_type_name = $content_term->name;
                /*------------------------------------------------*/
                $permalink = get_the_permalink();

                $video_type = 0;
                $video_id ='';
                /*------------------------------------------------*/
                if ( $content_term->name == 'Internal-Video' ) {

                        $content_type_name = 'Internal-Video';
                        $video_type = 1;
                        // remove the permalink link from the vidoe item 
                        $permalink = "#";
                        
                };
                /*------------------------------------------------*/
                if ( $content_term->name == 'YouTube-Video' ) {

                        $content_type_name = 'YouTube-Video';
                        $video_type = 2;
                        $video_id = self::get_youtube_video_id( $permalink );
                        $permalink = "#";
                        
                };
                /*------------------------------------------------*/
                if ( $content_term->name == 'FAQ' ) {

                    // get the faq group id of that faq post 
                    $faq_group_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-faq-group-meta');
                    $faq_group_term = get_term_by('id', $faq_group_id, 'idocs-faq-group-taxo');
                    //$faq_group_term = IDOCS_Taxanomies::get_specific_faqgroup_term_by_slug_caching($faq_group_slug);
                    $category_name= $faq_group_term->name;
                    
                };
                /*------------------------------------------------*/
                array_push( $optimized_output['content'], array(

                    'content_type' => $content_type_name,
                    'title'  => get_the_title(),
                    'category' => $category_name,
                    'permalink' =>  $permalink,
                    'video_type' => $video_type,
                    'video_id' => $video_id,
                    'video_link' => get_the_permalink(),
                    'content_id' => $post_id,
                    'kb_id' => $kb_id,
                    
                    )
                );
            }
        }
        wp_reset_postdata();    
        /*------------------------------------------------*/
        if ( $pro ) { 
            
            $optimized_output = apply_filters('idocspro_search_tags', $optimized_output,  $search_string, $order_by, $kb_id );
            /*------------------------------------------------*/
            // Iterate over the content array and count occurrences of each content_type
            foreach ($optimized_output['content'] as $item) {

                $content_type = $item['content_type'];
                if ($content_type == "Internal-Video" || $content_type == "YouTube-Video" ) {

                    // global video counter
                    $optimized_output['counters']['Video']++;
                    // specific video type counter 
                    $optimized_output['counters'][$content_type]++;  
            
                }
                else {
                    $optimized_output['counters'][$content_type]++;
                }
            }
        }
        /*------------------------------------------------*/        
        // WP will automatically convert the PHP Array structure into a JSON object
        return $optimized_output;
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_youtube_video_id( $url ) {

		$pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
		preg_match($pattern, $url, $matches);
	
		if (isset($matches[1])) {
			return $matches[1];
		}
		/*--------------------------------------------*/
		return false;
	}
    /*---------------------------------------------------------------------------------------*/
    /* SAVE SEARCH RESULT */
    /*---------------------------------------------------------------------------------------*/
    // register custom route for saving search result (5e5827093d)
    // Public-facing API
    public function register_custom_route_save_search_result () {
       
        register_rest_route( 
            'incredibledocs/v1', 
            'save_search',
            array(
                'methods'  => WP_REST_SERVER::READABLE, 
                'callback' => array($this, 'save_search_results'),
                'permission_callback' => '__return_true',
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function save_search_results( $data ) {
        
        /*------------------------------------------------*/
        // for public API (no logged-in users), we need to manually verify the nonce
        if ( !$_SERVER['HTTP_X_WP_NONCE'] || !wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'], 'wp_rest' ) ) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        /*------------------------------------------------*/
        // save a search query event 
        IDOCS_Save_Events::save_search_query_event(

            sanitize_text_field($data['keyword']),      // keyword content 
            absint($data['found_flag']),   // boolean value: 0 - not found, 1 - found
            sanitize_text_field($data['current_ip']),      // keyword content 
            absint($data['kb_id'])        // knowledge-base id number
        );
    }
    /*---------------------------------------------------------------------------------------*/
    /* GET SEARCH PARAMETERS 
    /*---------------------------------------------------------------------------------------*/
    // register custom route for getting search parameters
    // Public-Facing API 
    public function register_custom_route_get_search_parameters () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'search_parameters',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_search_parameters'),
                'permission_callback' => '__return_true',

            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_search_parameters( $data ) {

        /*------------------------------------------------*/
        // for public API (no logged-in users), we need to manually verify the nonce
        if ( !$_SERVER['HTTP_X_WP_NONCE'] || !wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'], 'wp_rest' ) ) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        /*------------------------------------------------*/
        $kb_id = absint(sanitize_text_field($data["kb_id"]));
        $design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
        /*------------------------------------------------*/
        $result = array (
            'min_amount_characters_for_search' => $design_settings['live_search_min_amount_characters_for_search'],
			'keystroke_delay_before_search'    => $design_settings['live_search_keystroke_delay_before_search'],
            'search_order_alphabetically'      => $design_settings['live_search_result_order_alphabetically'],
            'no_result_feedback'               => $design_settings['live_search_no_result_feedback']
        );
        /*------------------------------------------------*/
        return $result;
    }     
    /*---------------------------------------------------------------------------------------*/
    /*---------------------------------------------------------------------------------------*/
    // Logged-in User API End Points (For Admin Screens)
    /*---------------------------------------------------------------------------------------*/

    /*---------------------------------------------------------------------------------------*/
    /* GET CATEGORIES PER KB */
    /*---------------------------------------------------------------------------------------*/
    // Admin-facting API
    public function register_custom_route_get_categories_per_kb_with_hierarchical () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'categories_per_kb_with_hierarchical',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_categories_per_kb_with_hierarchical'),
                'permission_callback' => function () {    
                    // for non-public api -->  you do not need to verify that the nonce is valid inside your custom end point. 
                    // This is automatically done for you in rest_cookie_check_errors().
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_categories_per_kb_with_hierarchical ($data) {

        $categories = array();
        $kb_id = absint(sanitize_text_field($data['kb_id']));
        /*------------------------------------------------*/
        // get all categories related to specific knowledge-base
        $terms = get_terms( array(
            'taxonomy'   => 'idocs-category-taxo',
            'hide_empty' => false,
            'meta_key'   => 'idocs-category-taxo-kb',
            'meta_value' => $kb_id
        ) );
        /*------------------------------------------------*/
        if ($terms === null ) {
            return $categories;
        }
        /*------------------------------------------------*/
        $taxonomy = get_taxonomy( 'idocs-category-taxo' );
        $html = wp_dropdown_categories( array(

            'echo'            => 0,
            'show_option_all' =>  $taxonomy->labels->all_items,
            'taxonomy'        =>  'idocs-category-taxo',
            'name'            =>  'idocs-category-taxo',
            'orderby'         =>  'name',
            'value_field'     =>  'slug',
            //'selected'        =>  $selected,
            'hierarchical'    =>  true,
            'hide_empty'      =>  false, 
            // use the kb_term id to filter categories related to that kb
			'meta_key'   => 'idocs-category-taxo-kb',
			'meta_value' => $kb_id
            
        ) );
        /*------------------------------------------------*/
        // Use DOMDocument to parse the HTML
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        // Find all option elements
        $options = $dom->getElementsByTagName('option');
        /*------------------------------------------------*/
        // reduce the result to specifc two fields 
        foreach ($options as $option) {

            $target_slug = $option->getAttribute('value');

            if ($target_slug != 0) {

                $term_id = null;
                foreach ($terms as $term) {
                    if ($term->slug === $target_slug) {
                        // Term found
                        $term_id = $term->term_id;
                        break; // Exit the loop once the term is found
                    }
                }
                $category = array (
                    'term_id' => $term_id,
                    'name'    => trim($option->nodeValue),
                );

                array_push($categories, $category );
            }
        }
        /*------------------------------------------------*/
        return  $categories;
    }
    /*---------------------------------------------------------------------------------------*/
    // Admin-facting API
    public function register_custom_route_get_faq_groups_per_kb () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'faq_groups_per_kb',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_faq_groups_per_kb'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    // get all faq-groups for a specific knowledge-base 
    public function get_faq_groups_per_kb ( $data ) {

        $kb_id = absint(sanitize_text_field($data['kb_id']));
        /*------------------------------------------------*/
        // get all faq groups related to specific knowledge-base
        $terms = get_terms( array(
            'taxonomy'   => 'idocs-faq-group-taxo',
            'hide_empty' => false,
            'meta_key'   => 'idocs-faq-group-taxo-kb',
            'meta_value' => $kb_id
        ) );
        /*------------------------------------------------*/
        $groups = array();
        // reduce the result to specifc two fields 
        foreach ($terms as $term) {
            array_push($groups, array (
                'term_id' => $term->term_id,
                'name'    => $term->name
            ) );
        }
        /*------------------------------------------------*/
        return  $groups; 
    }
    /*---------------------------------------------------------------------------------------*/
    // Admin-facting API
    public function register_custom_route_get_tags_per_kb () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'tags_per_kb',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_tags_per_kb'),
                'permission_callback' => function () {
                
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    // get all tags for a specific knowledge-base 
    public function get_tags_per_kb ( $data ) {

        $kb_id = absint(sanitize_text_field($data['kb_id']));
        /*------------------------------------------------*/
        // get all tags related to specific knowledge-base
        $terms = get_terms( array(
            'taxonomy'   => 'idocs-tag-taxo',
            'hide_empty' => false,
            'meta_key'   => 'idocs-tag-taxo-kb',
            'meta_value' => $kb_id
        ) );
        /*------------------------------------------------*/
        $tags = array();
        // reduce the result to specifc two fields 
        foreach ($terms as $term) {
            array_push($tags, array (
                'term_id' => $term->term_id,
                'name'    => $term->name
            ) );
        }
        /*------------------------------------------------*/
        return  $tags;  
    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_categories_per_kb_with_levels () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'categories_per_kb_with_levels',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_categories_per_kb_with_levels'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    // get all categories for a specific knowledge-base 
    public function get_categories_per_kb_with_levels ( $data ) {

        $kb_id = absint(sanitize_text_field($data['kb_id']));
        /*------------------------------------------------*/
        // get all top-level categories related to specific knowledge-base
        $terms = get_terms( array(
            'taxonomy'   => 'idocs-category-taxo',
            'hide_empty' => false,
            'meta_key'   => 'idocs-category-taxo-kb',
            'meta_value' => $kb_id,
            'parent' => 0, // Get top-level terms
        ) );
        /*------------------------------------------------*/
        $optionsArray = self::generate_term_options($terms);
         /*------------------------------------------------*/        
        return  $optionsArray;
    }
    /*---------------------------------------------------------------------------------------*/
    // Recursive function to build the hierarchical structure
    public static function generate_term_options($terms, $indent = 0) {

        $options = array();
        /*------------------------------------------------*/
        foreach ($terms as $term) {
            // Store term information in the array
            $options[] = array(
                'term_id' => $term->term_id,
                'name' => $term->name,
                'level' => $indent,
            );
            /*------------------------------------------------*/
            // Check if the term has child terms
            $child_terms = get_terms(array(
                'taxonomy' => $term->taxonomy,
                'hide_empty' => false,
                'parent' => $term->term_id,
            ));
            /*------------------------------------------------*/
            // Recursively call the function for child terms
            if (!empty($child_terms)) {
                $options = array_merge($options, self::generate_term_options($child_terms, $indent + 1));
            }
        }
        /*------------------------------------------------*/
        return $options;
    }
    /*---------------------------------------------------------------------------------------*/
    /* GET LIST OF KBs
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_kbs_list () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'kbs_list',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_kbs_list'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    // get the list of knowledge-bases 
    public function get_kbs_list ( $data ) {

        // get the list of all terms (knowledge-bases) under the 'idocs-kb-taxo' taxanomy 
        $terms = IDOCS_Taxanomies::get_kb_terms_caching();
        /*------------------------------------------------*/
        $kbs = array();
        // reduce the result to specifc two fields 
        foreach ($terms as $term) {
            array_push($kbs, array (
                'term_id' => $term->term_id,
                'name'    => $term->name
            ) );
        }
        /*------------------------------------------------*/
        return  $kbs;  
    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_color_schemes_list () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'color_schemes_list',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_color_schemes_list'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    // get the list of themes (theme name and theme id)
    public function get_color_schemes_list ( $data ) {

        $themes_object_list = IDOCS_Themes::get_all_themes();
        /*------------------------------------------------*/
        $themes = array();
        // reduce the result to specifc two fields 
        foreach ($themes_object_list as $key => $theme_object) {
            
            array_push($themes, array (
                'color_scheme_id'     => $key,
                'color_scheme_name'   => $theme_object['name'],
                'custom_schema' => $theme_object['custom_theme'],
            ) );
        }
        /*------------------------------------------------*/
        return $themes;  
    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_color_schemes_list_detailed () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'get_color_schemes_list_detailed',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_color_schemes_list_detailed'),

                'permission_callback' => function () {                    
                    return current_user_can( $this->admin_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_color_schemes_list_detailed() { 

        return IDOCS_Themes::get_all_themes();

    }
    /*---------------------------------------------------------------------------------------*/

    public function register_custom_route_get_block_default_settings () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'block_default_settings',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'block_default_settings'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function block_default_settings ( $data ) {
        
        $color_scheme_id = $data['color_scheme_id'];
        $block_name = $data['block_name'];
        $kb_id = $data['kb_id'];
        
        return IDOCS_Blocks::block_default_design_settings($kb_id, $color_scheme_id, $block_name);

    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_content_types () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'get_content_types',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_content_types'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    // get the content types 
    public function get_content_types ( $data ) {

        // get the list of all terms under the 'idocs-content-type-taxo' taxanomy 
        $terms = IDOCS_Taxanomies::get_content_types_terms_caching();
        /*------------------------------------------------*/
        $types = array();
        // reduce the result to specifc two fields 
        foreach ($terms as $term) {
            array_push($types, array (
                'term_id' => $term->term_id,
                'name'    => $term->name
            ) );
        }
        /*------------------------------------------------*/
        return  $types;  
    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_check_category_name () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'check_category_name',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'check_category_name'),

                'permission_callback' => function () {                    
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function check_category_name( $data ) {
		
		$term_name = sanitize_text_field($data['term_name']);
		$term_slug = sanitize_text_field($data['term_slug']);
        $kb_id = absint(sanitize_text_field($data['kb_id']));
        $cat_taxonomy = 'idocs-category-taxo';
        /*------------------------------------------------*/
        // scenario #1 - user is trying to add a category with the same name in the same kb.
        // in that case, don't generate a slug. 
        /*------------------------------------------------*/
        // get the list of all terms (categories) with the same name and the same kb
		$terms = get_terms( array(
			'taxonomy'   => $cat_taxonomy,
            'name' => $term_name, 
            'meta_key'   => 'idocs-category-taxo-kb',
			'meta_value' => $kb_id, 
			'hide_empty' => false,
		) );
        // found a category with the same name in the same kb --> mark not to create a slug.
        if($terms) {

            $response['generate_slug'] = false;
            return $response;

        };
        /*------------------------------------------------*/
        // scenario #2 - user is trying to add a category with the the same name like other kbs.
        // in that case, mark to generate a slug and return how many kbs are having the same category name. 
        /*------------------------------------------------*/ 
        // get the list of all categories with the same name (coming from different kbs - same kb already dropped in scenario #1).
        $terms = get_terms( array(
			'taxonomy'   => $cat_taxonomy,
            'name' => $term_name, 
			'hide_empty' => false,
		) );
        
		if ($terms) {

			$response['generate_slug'] = true;
			$response['count'] = count($terms);
		} else {
			$response['generate_slug'] = false;
		}
        /*------------------------------------------------*/
		return $response;
	}
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_icons () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'get_icons',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_icons'),
                'permission_callback' => function () {                    
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_icons( $data ) { 

        return IDOCS_ICONS::get_all_icons(); 
    }
    /*---------------------------------------------------------------------------------------*/
    public function update_div_kb_filter( $data ) {

        /*------------------------------------------------*/
        $cat_taxonomy = 'idocs-category-taxo';
		$cpt = 'idocs_content';
        $terms = IDOCS_Taxanomies::get_kb_terms_caching();
		$url = get_site_url() . '/wp-admin/edit-tags.php?taxonomy='. $cat_taxonomy . '&post_type=' . $cpt;
        /*------------------------------------------------*/
        ob_start();
        ?>
        <div>
            <select class="idocs-admin-filter-kb" id="kb-selection-field" name="kb-selection-field">

                <option value=''>Select Knowledge Base...</option>
				<option disabled>────────────────────────</option>
				<option value=0>All Knowledge Bases</option>
                <?php
                    
                    foreach ($terms as $term) {
                        ?>
                        <option value="<?php echo esc_attr($term->term_id);?>"><?php echo esc_attr($term->name);?>	
                        </option>
                        <?php
                    }
                    
                ?>
            </select>
            <a id="url-filter-link" href=<?php esc_url($url); ?>>
                <button id="filter-selected-kb" class="button action" type="button">Filter</button>
            </a>
		</div>
        <?php
        /*------------------------------------------------*/
        $output = ob_get_contents();
        ob_end_clean();
        $response = array(
            'content' => $output, 
        );
        wp_send_json($response);
        exit();
    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_update_kb_global_color_scheme () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'update_kb_global_color_scheme',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'update_kb_global_color_scheme'),

                'permission_callback' => function () {                    
                    return current_user_can( $this->admin_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function update_kb_global_color_scheme( $data ) { 

        $kb_id = absint(sanitize_text_field($data['kb_id'])); 
        $theme_id = absint(sanitize_text_field($data['theme_id']));
        $custom_theme = sanitize_text_field($data['custom_theme']);
        /*------------------------------------------------*/
        // error_log($custom_theme);
        //error_log($theme_id);
        if ( $custom_theme ) {

            // add a large constant delta to identify a custom theme. 
           //error_log("updated kb theme");
           //error_log($theme_id + 1000);
           $success = update_term_meta( $kb_id, 'idocs-kb-taxo-theme-id', $theme_id + 1000);	
           delete_transient( 'idocs_transient_terms_metadata');

        }
        else {

            //update_term_meta( $kb_id, 'idocs-kb-taxo-theme-type', 0);	
            $success = update_term_meta( $kb_id, 'idocs-kb-taxo-theme-id', $theme_id);	
            delete_transient( 'idocs_transient_terms_metadata');

        }
        //error_log($success);
        /*------------------------------------------------*/
        // remove data caching of that kb
        delete_transient( 'idocs_transient_design_settings_' . $kb_id);
        return $success;
    }
    /*---------------------------------------------------------------------------------------*/
    /*---------------------------------------------------------------------------------------*/
    /*---------------------------------------------------------------------------------------*/
    /* SEARCH ANALYTICS */
    /*---------------------------------------------------------------------------------------*/
    public static function get_kbs_list_string() {

        $kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
        $kbs_list = array ();
        foreach ( $kb_terms as $term) {
            $kbs_list[] = $term->term_id;
        }
        /*--------------------------------------------*/
        return implode(',', array_map('intval', $kbs_list));

    }
    /*---------------------------------------------------------------------------------------*/
    public static function get_kbs_list_array() {

        $kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
        $kbs_list = array ();
        foreach ( $kb_terms as $term) {
            $kbs_list[] = $term->term_id;
        }
        /*--------------------------------------------*/
        return $kbs_list;
    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_popular_searches_current_results () {

        register_rest_route( 
            'incredibledocs/v1', 
            'popular_searches_current_results',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'popular_searches_current_results'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function popular_searches_current_results( $data ) {
        
        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $TopX = absint(sanitize_text_field($data['top']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*--------------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );
        /*--------------------------------------------*/
        if ( $kb_id == '0') { // all kbs 

            $sql_query = "SELECT search_query, count(*) as total_searches
                    FROM {$wpdb->prefix}idocs_search_logs
                    WHERE 
                        date_add(search_time, INTERVAL %d day) >= CURDATE()
                    AND
                        kb_id IN ($kbs_list_placeholders)   
                    GROUP BY search_query
                    ORDER BY total_searches DESC
                    LIMIT %d
            ";
        }
        else { // specific kb

            $sql_query = "SELECT search_query, count(*) as total_searches
                    FROM {$wpdb->prefix}idocs_search_logs
                    WHERE 
                        date_add(search_time, INTERVAL %d day) >= CURDATE()
                    AND
                        kb_id = %d
                    GROUP BY search_query
                    ORDER BY count(*) DESC
                    LIMIT %d
            ";
        }
        /*--------------------------------------------*/
        if ($kb_id == '0') {

            $top_searches = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );

        }
        else
            $top_searches = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX), 
                ARRAY_A
            );
        /*--------------------------------------------*/
        foreach ($top_searches as $key => $row) {

            $search_string = $row['search_query'];
            /*--------------------------------------------*/
            $order_by = sanitize_text_field($data['order_by']);
            if ( $kb_id == '0') { // all kbs 
            
                $meta_query = array();

            }
            else { // specific kb
                $meta_query = array(
                    array(
                        'key' => 'idocs-content-kb-meta',
                        'value' => $kb_id,
                        'compare' => '=',
                    ),
                );
            }
            // search in all content items 
            $raw_query = new WP_Query(array(
                'post_type'  => array ('idocs_content'), 
                // filter the relevant searched keyword 
                's'         => $search_string,
                'search_columns' => ['post_title'],
                //'title' => $search_string,
                'exact'      => false, 
                // filter the documents from the relevant knowledge-base
                'meta_query' => $meta_query,
                'orderby'   => $order_by,
                'order'     => 'ASC' 
            ));
            /*--------------------------------------------*/
            $optimized_output = array(

                'content' => array(),
                'counters' => array(
      
                  'Document' => 0,
                  'Link' => 0,
                  'FAQ' => 0,
                  'Video' => 0,
                  'Internal-Video' => 0,
                  'YouTube-Video' => 0,
                  'Tag' => 0,
      
                ),
            );
            /*--------------------------------------------*/
            // reduce the output of the query to title and permalink 
            while ( $raw_query->have_posts() ) {

                $raw_query->the_post();
                $post_id = get_the_ID();
                $content_type_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-type-meta');
                $content_term = get_term_by('id', $content_type_id, 'idocs-content-type-taxo');
                $content_type_name = $content_term->name;
                /*--------------------------------------------*/
                $video_type = 0;
                if ( $content_term->name == 'Internal-Video' ) {

                        $content_type_name = 'Internal-Video';
                        $video_type = 1;  
                };

                if ( $content_term->name == 'YouTube-Video' ) {

                        $content_type_name = 'YouTube-Video';
                        $video_type = 2;     
                };

                array_push( $optimized_output['content'], array(

                    'content_type' => $content_type_name,
                    'video_type' => $video_type,
                   
                    )
                );
            }
            wp_reset_postdata();    
            
            $optimized_output = apply_filters('idocspro_search_tags', $optimized_output,  $search_string, $order_by, $kb_id );

            // Iterate over the content array and count occurrences of each content_type
            foreach ($optimized_output['content'] as $item) {

                $content_type = $item['content_type'];
                if ($content_type == "Internal-Video" || $content_type == "YouTube-Video" ) {

                    // global video counter
                    $optimized_output['counters']['Video']++;
                    // specific video type counter 
                    $optimized_output['counters'][$content_type]++;  
            
                }
                else {
                    $optimized_output['counters'][$content_type]++;
                }

            }
            /*--------------------------------------------*/
            $top_searches[$key]['total_docs'] = $optimized_output['counters']['Document'];
            $top_searches[$key]['total_links'] = $optimized_output['counters']['Link'];
            $top_searches[$key]['total_faqs'] = $optimized_output['counters']['FAQ'];
            $top_searches[$key]['total_videos'] = $optimized_output['counters']['Internal-Video'] + $optimized_output['counters']['YouTube-Video'];
            $top_searches[$key]['total_tags'] = $optimized_output['counters']['Tag'];
            /*--------------------------------------------*/
        }
        /*--------------------------------------------*/
        return $top_searches; 
    }
    /*---------------------------------------------------------------------------------------*/
    // register custom route for getting the most popular searches 
    public function register_custom_route_get_most_popular_searches () {

        register_rest_route( 
            'incredibledocs/v1', 
            'popular_searches',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_most_popular_searches'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_most_popular_searches( $data ) {
        
        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $search_type = absint(sanitize_text_field($data ['search_type'])); // 1-overall, 2-with_results, 3-without_results
        $TopX = absint(sanitize_text_field($data['top']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*--------------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );

        //$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_search_logs';
        /*--------------------------------------------*/
        switch ($search_type) {
            /*--------------------------------------------*/
            case '1':
               
                //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();
 
                if ( $kb_id == '0') { // all kbs 

                    $sql_query = "SELECT search_query, count(*) as total_searches, sum(found_flag) as total_found, ROUND((sum(found_flag)/count(*))*100,0) as success_rate
                            FROM {$wpdb->prefix}idocs_search_logs
                            WHERE 
                                date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                                kb_id IN ($kbs_list_placeholders)   
                            GROUP BY search_query
                            ORDER BY total_searches DESC
                            LIMIT %d
                    ";
                }
                else { // specific kb

                    $sql_query = "SELECT search_query, count(*) as total_searches, sum(found_flag) as total_found, ROUND((sum(found_flag)/count(*))*100,0) as success_rate
                            FROM {$wpdb->prefix}idocs_search_logs
                            WHERE 
                                date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                                kb_id = %d
                            GROUP BY search_query
                            ORDER BY count(*) DESC
                            LIMIT %d
                    ";
                }
                break;
            /*--------------------------------------------*/    
            case '2':
                if ( $kb_id == '0') { // all kbs 
                    //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list(); 
                    $sql_query = "SELECT search_query, count(*) as count 
                                FROM {$wpdb->prefix}idocs_search_logs
                                WHERE 
                                    date_add(search_time, INTERVAL %d day) >= CURDATE()
                                AND
                                    found_flag = 1
                                AND
                                    kb_id IN ($kbs_list_placeholders)  
                                GROUP BY search_query
                                ORDER BY count(*) DESC
                                LIMIT %d
                    "; 
                }
                else {

                    $sql_query = "SELECT search_query, count(*) as count 
                                FROM {$wpdb->prefix}idocs_search_logs
                                WHERE 
                                    date_add(search_time, INTERVAL %d day) >= CURDATE()
                                AND
                                    found_flag = 1
                                AND
                                    kb_id = %d
                                GROUP BY search_query
                                ORDER BY count(*) DESC
                                LIMIT %d
                    "; 
                }
                break;
            /*--------------------------------------------*/    
            case '3':
                if ( $kb_id == '0') { // all kbs  
                    //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list(); 
                    $sql_query = "SELECT search_query, count(*) as count 
                        FROM {$wpdb->prefix}idocs_search_logs
                        WHERE 
                            date_add(search_time, INTERVAL %d day) >= CURDATE()
                        AND
                            found_flag = 0
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                        GROUP BY search_query
                        ORDER BY count(*) DESC
                        LIMIT %d
                    "; 
                }
                else {
                    $sql_query = "SELECT search_query, count(*) as count 
                            FROM {$wpdb->prefix}idocs_search_logs
                            WHERE 
                                date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                                found_flag = 0
                            AND
                                kb_id = %d
                            GROUP BY search_query
                            ORDER BY count(*) DESC
                            LIMIT %d
                    "; 
                }
                break;
        }
        // $wpdb->prepare() - prepares a SQL query for safe execution.
        // $wpdb->get_results - retrieves an entire SQL result set from the database (i.e., many rows).
        if ($kb_id == '0') {

            $top_searches = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );

        }
        else
            $top_searches = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX), 
                ARRAY_A
            );
        /*--------------------------------------------*/
        return $top_searches; 
    }
    /*---------------------------------------------------------------------------------------*/
    // register custom route for getting the overall search success rate 
    public function register_custom_route_get_search_success_rate () {
    
        register_rest_route( 
            'incredibledocs/v1', 
            'search_success_rate',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_search_success_rate'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_search_success_rate( $data ) {
        
        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        //$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_search_logs';
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );
        /*---------------------------------------*/
        // all knowlege-bases 
        if ( $kb_id == '0' ) {
            //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();
            $sql_query = "SELECT count(*) as success_count
                      FROM {$wpdb->prefix}idocs_search_logs
                      WHERE found_flag = 1
                        AND
                            date_add(search_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders)  
            ";
        }
        else {
            // specific kb 
            // count the amount of search queires with successful result 
            $sql_query = "SELECT count(*) as success_count
                        FROM {$wpdb->prefix}idocs_search_logs
                        WHERE found_flag = 1
                            AND
                                date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                                kb_id = %d
            ";
        }
        /*--------------------------------------------*/
        if ( $kb_id == '0' ) {

            $result_1 = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else
            $result_1 = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id), 
                ARRAY_A
            );

        if (empty($result_1)) {

            $result_1[0]["success_count"] = 0;

        }
        /*---------------------------------------*/
        // count the amount of search queires without any successful result 
        if ( $kb_id == '0' ) { 
            //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();
            $sql_query = "SELECT count(*) as no_success_count
                        FROM {$wpdb->prefix}idocs_search_logs
                        WHERE found_flag = 0
                            AND
                                date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                                kb_id IN ($kbs_list_placeholders)       
            ";
        }
        else {

            $sql_query = "SELECT count(*) as no_success_count
                        FROM {$wpdb->prefix}idocs_search_logs
                        WHERE found_flag = 0
                            AND
                                date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                                kb_id = %d
            ";

        };
        /*--------------------------------------------*/
        if ( $kb_id == '0' ) {
           
            $result_2 = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else
            $result_2 = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id), 
                ARRAY_A
            );

        if (empty($result_2)) {
            $result_2[0]["no_success_count"] = 0;
        }
        /*---------------------------------------*/
        // convert to integer value 
        $success_count = intval($result_1[0]["success_count"]);
        $overall_count = $success_count + intval($result_2[0]["no_success_count"]);
        /*--------------------------------------------*/
        if ($overall_count != 0 ) {
             // return the success rate in percent, success count, overall count 
            return [round(100*($success_count/$overall_count)),$success_count, $overall_count];
        }
        else {
            return [0, 0, 0];
        }
     }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_overall_searches () {
        //register_rest_route( $namespace:string, $route:string, $args:array, $override:boolean )
        register_rest_route( 
            'incredibledocs/v1', 
            'overall_searches',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_overall_searches'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_overall_searches( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        //$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_search_logs';
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );
        /*---------------------------------------*/
        if ( $kb_id == '0') {
            $kbs_string = IDOCS_Custom_RestAPIs::get_kbs_list_string();
            $sql_query = "SELECT count(*) as total_searches, sum(found_flag) as total_found_result, 
                             (count(*)-sum(found_flag)) as total_no_found_result
                      FROM {$wpdb->prefix}idocs_search_logs
                      WHERE date_add(search_time, INTERVAL %d day) >= CURDATE()
                      AND
                            kb_id IN ($kbs_list_placeholders)  

            ";
            
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values ), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT count(*) as total_searches, sum(found_flag) as total_found_result, 
                             (count(*)-sum(found_flag)) as total_no_found_result
                      FROM {$wpdb->prefix}idocs_search_logs
                      WHERE date_add(search_time, INTERVAL %d day) >= CURDATE()
                        AND
                          kb_id = %d   
            ";

            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        if (empty($result)) {
            $result[0]['total_searches'] = 0; 
            $result[0]['total_found_result'] = 0;
            $result[0]['total_no_found_result'] = 0; 
        }
        /*--------------------------------------------*/
        if ( $result[0]['total_searches'] == NULL )
	        $result[0]['total_searches'] = 0; 
        if ( $result[0]['total_found_result'] == NULL )
	        $result[0]['total_found_result'] = 0; 
        if ( $result[0]['total_no_found_result'] == NULL )
	        $result[0]['total_no_found_result'] = 0; 
        
        $delta = $days_back * 2; 
        $prepare_values = array_merge( array( $delta ), array( $days_back ), $kbs_array );
        /*---------------------------------------*/
        if ( $kb_id == '0') {
            //kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();
            $sql_query = "SELECT count(*) as total_searches, sum(found_flag) as total_found_result, 
                            (count(*)-sum(found_flag)) as total_no_found_result
                      FROM {$wpdb->prefix}idocs_search_logs
                      WHERE date_add(search_time, INTERVAL %d day) >= CURDATE()
                        AND
                          date_add(search_time, INTERVAL %d day) < CURDATE()
                        AND
                          kb_id IN ($kbs_list_placeholders)  
            ";

            $result_previous = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );

        }
        else {

            $sql_query = "SELECT count(*) as total_searches, sum(found_flag) as total_found_result, 
                                (count(*)-sum(found_flag)) as total_no_found_result
                        FROM {$wpdb->prefix}idocs_search_logs
                        WHERE date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                            date_add(search_time, INTERVAL %d day) < CURDATE()
                            AND
                            kb_id = %d
            ";

            $result_previous = $wpdb->get_results(
                $wpdb->prepare($sql_query, $delta, $days_back, $kb_id), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        if (empty($result_previous)) {
            $result_previous[0]['total_searches'] = 0; 
            $result_previous[0]['total_found_result'] = 0;
            $result_previous[0]['total_no_found_result'] = 0; 
        }
        /*--------------------------------------------*/
        if ( $result_previous[0]['total_searches'] == NULL )
	        $result_previous[0]['total_searches'] = 0; 
        if ( $result_previous[0]['total_found_result'] == NULL )
	        $result_previous[0]['total_found_result'] = 0; 
        if ( $result_previous[0]['total_no_found_result'] == NULL )
	        $result_previous[0]['total_no_found_result'] = 0; 

        return array_merge($result, $result_previous);
     }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_searches_per_day () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'searches_per_day',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_searches_per_day'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_searches_per_day( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        //$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_search_logs';
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*--------------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );

        /*---------------------------------------*/
        if ( $kb_id == '0' ) { // all kbs
            //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();
            $sql_query = "SELECT day(search_time) as day, DATE_FORMAT(search_time, '%e-%m') AS month_day, count(*) as count
                        FROM {$wpdb->prefix}idocs_search_logs
                        WHERE date_add(search_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                        GROUP BY day(search_time), 2
            ";
            
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values ), 
                ARRAY_A
            );
        } 
        else { // specific kb

            $sql_query = "SELECT day(search_time) as day, DATE_FORMAT(search_time, '%e-%m') AS month_day, count(*) as count
                        FROM {$wpdb->prefix}idocs_search_logs
                        WHERE date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                            kb_id = %d
                        GROUP BY day(search_time), 2
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        $recNew = [];
        foreach($result as $key => $row) {
            foreach($row as $field => $value) {
                $recNew[$field][] = $value;
            }
        }
        /*--------------------------------------------*/
        return $recNew;
    }
    /*---------------------------------------------------------------------------------------*/
    /* RATING ANALYTICS */
    /*---------------------------------------------------------------------------------------*/     
    // register custom route for getting the overall search success rate per day          
    public function register_custom_route_get_rating_per_day () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'rating_per_day',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_rating_per_day'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_rating_per_day( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        //$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_ratings';
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*--------------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );
        /*---------------------------------------*/
        if ( $kb_id == '0' ) {
            //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();
            $sql_query = "SELECT day(rating_time) as day, DATE_FORMAT(rating_time, '%e-%m') AS month_day, sum(like_type) as likes, (count(*)-sum(like_type)) as dislikes
                        FROM {$wpdb->prefix}idocs_ratings
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                        GROUP BY day(rating_time), 2
            ";
   
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT day(rating_time) as day, DATE_FORMAT(rating_time, '%e-%m') AS month_day, sum(like_type) as likes, (count(*)-sum(like_type)) as dislikes
                        FROM {$wpdb->prefix}idocs_ratings
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                            AND
                            kb_id = %d
                        GROUP BY day(rating_time), 2
            ";

            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id), 
                ARRAY_A
            ); 
        }
        /*--------------------------------------------*/
        foreach ($result as $key => $row) {
            $result[$key]['likes_rating'] = round($row['likes']/($row['dislikes']+$row['likes'])*100);
        }
        
        $recNew = [];
        foreach($result as $key => $row) {
            foreach($row as $field => $value) {
                $recNew[$field][] = $value;
            }
        }
        /*--------------------------------------------*/
        return $recNew;
     }
    //*---------------------------------------------------------------------------------------*/
    // register custom route for getting the rating per document 
     public function register_custom_route_get_rating_per_document () {
        //register_rest_route( $namespace:string, $route:string, $args:array, $override:boolean )
        register_rest_route( 
            'incredibledocs/v1', 
            'rating_per_document',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_rating_per_document'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_rating_per_document( $data ) {

        // /wp-json/incredibledocs/v1/rating_per_document?days_back=30&&order_by=likes_rating&&desc=true

        global $wpdb;
        /*--------------------------------------------*/
        //$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_ratings';
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        $TopX = absint(sanitize_text_field($data ['limit']));
        /*--------------------------------------------*/
        switch ($data['order_by'])  {

            case 'rating_score': $order_by = 5; break;
            default: $order_by = 5;         
        }
        /*--------------------------------------------*/
        if ( ($data['desc']) == 1 )
            $order = 'DESC';
        else
            $order = 'ASC';
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );
        /*--------------------------------------------*/
        if ( $kb_id == '0') {

            $sql_query = "SELECT kb_id, content_id, t2.post_title, t2.post_author, round( sum(rating_score)/ count(*) , 1) as rating_score, (count(*)) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_content
                        JOIN 
                            ( 
                                SELECT id, post_title, post_author
                                FROM wp_posts
                                WHERE post_type = 'idocs_content'
                            ) as t2
                            ON {$wpdb->prefix}idocs_ratings_content.content_id = t2.id 
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders) 
                        GROUP BY content_id
                        ORDER BY $order_by $order
                        LIMIT %d
            ";
            
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT kb_id, content_id, t2.post_title, t2.post_author, round( sum(rating_score)/ count(*) , 1) as rating_score, (count(*)) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_content
                        JOIN 
                            ( 
                                SELECT id, post_title, post_author
                                FROM wp_posts
                                WHERE post_type = 'idocs_content'
                            ) as t2
                            ON {$wpdb->prefix}idocs_ratings_content.content_id = t2.id 
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                            AND
                            kb_id = %d
                        GROUP BY content_id
                        ORDER BY $order_by $order
                        LIMIT %d
            ";
            
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        foreach ($result as $key => $row) {

            // translate each kb id to the kb title. 
            //$kb_term = get_term($row['kb_id'], 'idocs-kb-taxo');
            $kb_id = $row['kb_id'];
            $kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($kb_id);

            $result[$key]['kb_title'] = $kb_term->name;

            //$post_categories = get_the_terms($result[$key]['document_id'], 'idocs-category-taxo');
            $post_categories = IDOCS_Taxanomies::get_the_terms_caching( $result[$key]['content_id'], 'idocs-category-taxo' );
            $result[$key]['post_permalink'] = get_permalink($result[$key]['content_id']);
            $result[$key]['post_author']= ucfirst(get_the_author_meta( 'display_name', $result[$key]['post_author'] ));

            //do_action( 'qm/debug', $result[$key]['document_id']);
            //do_action( 'qm/debug', $post_categories);
            $result[$key]['category_title'] = $post_categories[0]->name;
        }
        return $result;
     }
    /*---------------------------------------------------------------------------------------*/
     // register custom route for getting the rating per document 
     public function register_custom_route_get_overall_ratings () {

        register_rest_route( 
            'incredibledocs/v1', 
            'overall_ratings',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_overall_ratings'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_overall_ratings( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        //$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_ratings';
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );
        /*--------------------------------------------*/
        if ( $kb_id == '0' ) {

            $sql_query = "SELECT sum(rating_score) as total_ratings, count(*) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_content
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders)         
            ";

            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT sum(rating_score) as total_ratings, count(*) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_content
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE() 
                        AND
                            kb_id = %d        
            ";

            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id), 
                ARRAY_A
            );

        }
        /*--------------------------------------------*/
        if (empty($result)) {

            $result[0]['number_ratings'] = 0;
            $result[0]['total_ratings'] = 0;
            $result[0]['stars_score'] = 0;
            
        }
        /*--------------------------------------------*/
        // result of ratings is empty 
        if ( intval($result[0]['number_ratings']) == 0 ) {

            $result[0]['number_ratings'] = 0;
            $result[0]['total_ratings'] = 0;
            $result[0]['stars_score'] = 0;

        }
        else {

            $result[0]['number_ratings'] = intval($result[0]['number_ratings']);
            $result[0]['total_ratings'] = intval($result[0]['total_ratings']);
            $result[0]['stars_score'] = round($result[0]['total_ratings']/($result[0]['number_ratings']),2);

        }
        /*---------------------------------------*/
        $delta = $days_back * 2; 
        $prepare_values = array_merge( array( $delta ), array( $days_back ), $kbs_array );

        if ( $kb_id == '0' ) {

            $sql_query = "SELECT sum(rating_score) as total_ratings, count(*) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_content
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                        AND
                            date_add(rating_time, INTERVAL %d day) < CURDATE() 
                        AND
                            kb_id IN ($kbs_list_placeholders)         
            ";

            $result_previous = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT sum(rating_score) as total_ratings, count(*) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_content
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                            AND
                            date_add(rating_time, INTERVAL %d day) < CURDATE() 
                            AND
                            kb_id = %d
            ";

            $result_previous = $wpdb->get_results(
                $wpdb->prepare($sql_query, $delta, $days_back, $kb_id), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        if (empty($result_previous)) {

            $result_previous[0]['number_ratings'] = 0;
            $result_previous[0]['total_ratings'] = 0;
            $result_previous[0]['stars_score'] = 0;
            
        }
        /*--------------------------------------------*/
        // result of ratings is empty 
        if ( intval($result_previous[0]['number_ratings']) == 0 ) {

            $result_previous[0]['number_ratings'] = 0;
            $result_previous[0]['total_ratings'] = 0;
            $result_previous[0]['stars_score'] = 0;

        }
        else {

            $result_previous[0]['number_ratings'] = intval($result_previous[0]['number_ratings']);
            $result_previous[0]['total_ratings'] = intval($result_previous[0]['total_ratings']);
            $result_previous[0]['stars_score'] = round($result_previous[0]['total_ratings']/($result_previous[0]['number_ratings']),2);

        }
        
        /*--------------------------------------------*/
        $content_rating = array_merge($result, $result_previous);
        /*--------------------------------------------*/
        /* KB View Taxonomy Ratings */
        /*--------------------------------------------*/
        $prepare_values = array_merge( array( $days_back ), $kbs_array );
        if ( $kb_id == '0' ) {

            $sql_query = "SELECT sum(rating_score) as total_ratings, count(*) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_taxonomy
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                        AND 
                            taxonomy = 'idocs-kb-taxo'
                        AND
                            kb_id IN ($kbs_list_placeholders)         
            ";

            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT sum(rating_score) as total_ratings, count(*) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_taxonomy
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE() 
                        AND 
                            taxonomy = 'idocs-kb-taxo'
                        AND
                            kb_id = %d        
            ";

            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id), 
                ARRAY_A
            );

        }
        /*--------------------------------------------*/
        if (empty($result)) {

            $result[0]['number_ratings'] = 0;
            $result[0]['total_ratings'] = 0;
            $result[0]['stars_score'] = 0;
            
        }
        /*--------------------------------------------*/
        // result of ratings is empty 
        if ( intval($result[0]['number_ratings']) == 0 ) {

            $result[0]['number_ratings'] = 0;
            $result[0]['total_ratings'] = 0;
            $result[0]['stars_score'] = 0;

        }
        else {

            $result[0]['number_ratings'] = intval($result[0]['number_ratings']);
            $result[0]['total_ratings'] = intval($result[0]['total_ratings']);

            $result[0]['stars_score'] = round($result[0]['total_ratings']/($result[0]['number_ratings']),2);

        }
        /*---------------------------------------*/
        $delta = $days_back * 2; 
        $prepare_values = array_merge( array( $delta ), array( $days_back ), $kbs_array );

        if ( $kb_id == '0' ) {

            $sql_query = "SELECT sum(rating_score) as total_ratings, count(*) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_taxonomy
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                        AND
                            date_add(rating_time, INTERVAL %d day) < CURDATE() 
                        AND 
                            taxonomy = 'idocs-kb-taxo'
                        AND
                            kb_id IN ($kbs_list_placeholders)         
            ";

            $result_previous = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT sum(rating_score) as total_ratings, count(*) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_taxonomy
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                        AND
                            date_add(rating_time, INTERVAL %d day) < CURDATE() 
                        AND 
                            taxonomy = 'idocs-kb-taxo'
                        AND
                            kb_id = %d
            ";

            $result_previous = $wpdb->get_results(
                $wpdb->prepare($sql_query, $delta, $days_back, $kb_id), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        if (empty($result_previous)) {

            $result_previous[0]['number_ratings'] = 0;
            $result_previous[0]['total_ratings'] = 0;
            $result_previous[0]['stars_score'] = 0;
            
        }
        /*--------------------------------------------*/
        // result of ratings is empty 
        if ( intval($result_previous[0]['number_ratings']) == 0 ) {

            $result_previous[0]['number_ratings'] = 0;
            $result_previous[0]['total_ratings'] = 0;
            $result_previous[0]['stars_score'] = 0;

        }
        else {

            $result_previous[0]['number_ratings'] = intval($result_previous[0]['number_ratings']);
            $result_previous[0]['total_ratings'] = intval($result_previous[0]['total_ratings']);

            $result_previous[0]['stars_score'] = round($result_previous[0]['total_ratings']/($result_previous[0]['number_ratings']),2);

        }
        /*--------------------------------------------*/
        $kb_view_ratings = array_merge($result, $result_previous);

        return array_merge($content_rating, $kb_view_ratings);

     }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_total_content_visits () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'total_content_visits',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'total_content_visits'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function total_content_visits( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        $content_type = sanitize_text_field($data ['content_type']); 

        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array($days_back), array($content_type), $kbs_array );
        /*--------------------------------------------*/
        if ( $kb_id == '0') {

            $sql_query = "SELECT count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visits_content
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                        AND
                            content_type = %s
                        AND
                            kb_id IN ($kbs_list_placeholders) 
            ";
           
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visits_content
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                        AND
                            content_type = %s
                        AND
                            kb_id = %d
            ";
                
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $content_type, $kb_id), 
                ARRAY_A
            );

        }
        /*--------------------------------------------*/
        if ( empty($result) ) {

            $result[0]['total_visits'] = 0;

        };
        /*--------------------------------------------*/
        if ( $result[0]['total_visits'] == NULL )
	        $result[0]['total_visits'] = 0; 
        
        /*---------------------------------------*/
        $delta = $days_back * 2; 
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $delta), array( $days_back ), array($content_type), $kbs_array );
        /*--------------------------------------------*/

        if ( $kb_id == '0') { 
            $sql_query = "SELECT count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visits_content
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                            AND
                                date_add(visit_time, INTERVAL %d day) < CURDATE()
                            AND 
                                content_type = %s
                            AND
                                kb_id IN ($kbs_list_placeholders)    
            ";
            $result_previous = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visits_content
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                            AND
                                date_add(visit_time, INTERVAL %d day) < CURDATE()
                            AND 
                                content_type = %s
                            AND
                                kb_id = %d
            ";

            $result_previous = $wpdb->get_results(
                $wpdb->prepare($sql_query, $delta, $days_back, $content_type, $kb_id), 
                ARRAY_A
            );

        }
        /*--------------------------------------------*/
        if ( empty($result_previous) ) {

            $result_previous[0]['total_visits'] = 0;

        };
        /*--------------------------------------------*/
        if ( $result_previous[0]['total_visits'] == NULL )
	        $result_previous[0]['total_visits'] = 0; 
        /*--------------------------------------------*/
        return array_merge($result, $result_previous);
    }
    /*---------------------------------------------------------------------------------------*/
    // register custom route for getting the rating per document 
    public function register_custom_route_get_overall_visits () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'overall_visits',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_overall_visits'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_overall_visits( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        //$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_visited';
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );
        /*--------------------------------------------*/
        if ( $kb_id == '0') {
            $kbs_string = IDOCS_Custom_RestAPIs::get_kbs_list_string();
            $sql_query = "SELECT count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visited
                        WHERE date_add(visited_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders) 
            ";
           
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visited
                        WHERE date_add(visited_time, INTERVAL %d day) >= CURDATE()
                            AND
                            kb_id = %d
            ";
                
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id), 
                ARRAY_A
            );

        }
        /*--------------------------------------------*/
        if ( empty($result) ) {

            $result[0]['total_visits'] = 0;

        };
        /*--------------------------------------------*/
        if ( $result[0]['total_visits'] == NULL )
	        $result[0]['total_visits'] = 0; 
        
        /*---------------------------------------*/
        $delta = $days_back * 2; 
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $delta ), array( $days_back ), $kbs_array );
        /*--------------------------------------------*/

        if ( $kb_id == '0') { 
            //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();
            $sql_query = "SELECT count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visited
                        WHERE date_add(visited_time, INTERVAL %d day) >= CURDATE()
                            AND
                                date_add(visited_time, INTERVAL %d day) < CURDATE()
                            AND
                                kb_id IN ($kbs_list_placeholders) 
                    
            ";
            $result_previous = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visited
                        WHERE date_add(visited_time, INTERVAL %d day) >= CURDATE()
                            AND
                                date_add(visited_time, INTERVAL %d day) < CURDATE()
                            AND
                                kb_id = %d
                    
            ";

            $result_previous = $wpdb->get_results(
                $wpdb->prepare($sql_query, $delta, $days_back, $kb_id), 
                ARRAY_A
            );

        }
        /*--------------------------------------------*/
        if ( empty($result_previous) ) {

            $result_previous[0]['total_visits'] = 0;

        };
        /*--------------------------------------------*/
        if ( $result_previous[0]['total_visits'] == NULL )
	        $result_previous[0]['total_visits'] = 0; 
        /*--------------------------------------------*/
        return array_merge($result, $result_previous);
    }
    /*---------------------------------------------------------------------------------------*/
    // register custom route for getting the rating per document 
    public function register_custom_route_get_top_visits_by_country () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'top_visits_by_country',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_top_visits_by_country'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_top_visits_by_country( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        //$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_visited';
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        $TopX = 7;
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );
        /*--------------------------------------------*/

        if ( $kb_id == '0') {

            //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();

            $sql_query = "SELECT country, count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visited
                        WHERE date_add(visited_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders) 
                        GROUP BY country
                        HAVING country IS NOT NULL
                        ORDER BY total_visits DESC
                        LIMIT %d
            ";

            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values), 
                ARRAY_A
            );
        }
        else {

            $sql_query = "SELECT country, count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visited
                        WHERE date_add(visited_time, INTERVAL %d day) >= CURDATE()
                            AND
                            kb_id = %d
                        GROUP BY country
                        HAVING country IS NOT NULL
                        ORDER BY total_visits DESC
                        LIMIT %d
            ";
                
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX), 
                ARRAY_A
            );

        }
        /*--------------------------------------------*/
        return array_merge($result);
     }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_top_visited_tags () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'top_visited_tags',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'top_visited_tags'),

                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function top_visited_tags( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        $TopX = absint(sanitize_text_field($data ['top_x']));
        /*--------------------------------------------*/
        if ( ($data['desc']) == 1 )
            $order = 'DESC';
        else
            $order = 'ASC';
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );
        /*--------------------------------------------*/
        if ( $kb_id == '0' ) {
           
            $sql_query = "SELECT kb_id, term_id, count(*) as total_visits
                      FROM {$wpdb->prefix}idocs_visits_taxonomy
                      
                      WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                        AND 
                            taxonomy = 'idocs-tag-taxo'
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                      GROUP BY term_id
                      ORDER BY total_visits $order
                      LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query,  $prepare_values ), 
                ARRAY_A
            );

        }
        else {

            $sql_query = "SELECT kb_id, term_id, count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visits_taxonomy
                        
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                            AND 
                                taxonomy = 'idocs-tag-taxo'
                            AND
                                kb_id = %d
                        GROUP BY term_id
                        ORDER BY total_visits $order
                        LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        foreach ($result as $key => $row) {

            // translate each kb id to the kb title. 
            $kb_id = $row['kb_id'];
            $kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($kb_id);
            //$kb_term = get_term($row['kb_id'], 'idocs-kb-taxo');
            $result[$key]['kb_title'] = $kb_term->name;
            $tag_term = IDOCS_Taxanomies::get_specific_tag_term_caching($result[$key]['term_id']);
            $result[$key]['tag_title'] = $tag_term->name;

        };
        /*--------------------------------------------*/
        return $result;

    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_top_visited_categories () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'top_visited_categories',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'top_visited_categories'),

                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function top_visited_categories( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        $TopX = absint(sanitize_text_field($data ['top_x']));
        /*--------------------------------------------*/
        if ( ($data['desc']) == 1 )
            $order = 'DESC';
        else
            $order = 'ASC';
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );
        /*--------------------------------------------*/
        if ( $kb_id == '0' ) {
           
            $sql_query = "SELECT kb_id, term_id, count(*) as total_visits
                      FROM {$wpdb->prefix}idocs_visits_taxonomy
                      
                      WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                        AND 
                            taxonomy = 'idocs-category-taxo'
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                      GROUP BY term_id
                      ORDER BY total_visits $order
                      LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query,  $prepare_values ), 
                ARRAY_A
            );

        }
        else {

            $sql_query = "SELECT kb_id, term_id, count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visits_taxonomy
                        
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                            AND 
                                taxonomy = 'idocs-category-taxo'
                            AND
                                kb_id = %d
                        GROUP BY term_id
                        ORDER BY total_visits $order
                        LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        foreach ($result as $key => $row) {

            // translate each kb id to the kb title. 
            $kb_id = $row['kb_id'];
            $kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($kb_id);
            //$kb_term = get_term($row['kb_id'], 'idocs-kb-taxo');
            $result[$key]['kb_title'] = $kb_term->name;
            $cat_term = IDOCS_Taxanomies::get_specific_category_term_caching($result[$key]['term_id']);
            $result[$key]['category_title'] = $cat_term->name;

        };
        /*--------------------------------------------*/
        return $result;

    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_top_countries_by_content_visits () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'top_countries_by_content_visits',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'top_countries_by_content_visits'),

                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function top_countries_by_content_visits( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        $TopX = absint(sanitize_text_field($data ['top_x']));
        /*--------------------------------------------*/
        if ( ($data['desc']) == 1 )
            $order = 'DESC';
        else
            $order = 'ASC';
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );
        /*--------------------------------------------*/
        if ( $kb_id == '0' ) {
           
            $sql_query = "SELECT country, count(*) as total_visits
                      FROM {$wpdb->prefix}idocs_visits_content
                      
                      WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                      GROUP BY country
                      ORDER BY total_visits $order
                      LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query,  $prepare_values ), 
                ARRAY_A
            );

        }
        else {

            $sql_query = "SELECT country, count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visits_content
                        
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                            AND
                                kb_id = %d
                        GROUP BY country
                        ORDER BY total_visits $order
                        LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        return $result;

    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_top_countries_by_content_ratings () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'top_countries_by_content_ratings',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'top_countries_by_content_ratings'),

                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function top_countries_by_content_ratings( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        $TopX = absint(sanitize_text_field($data ['top_x']));
        /*--------------------------------------------*/
        if ( ($data['desc']) == 1 )
            $order = 'DESC';
        else
            $order = 'ASC';
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );
        /*--------------------------------------------*/
        if ( $kb_id == '0' ) {
           
            $sql_query = "SELECT country, count(*) as total_ratings
                      FROM {$wpdb->prefix}idocs_ratings_content
                      
                      WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                      GROUP BY country
                      ORDER BY total_ratings $order
                      LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query,  $prepare_values ), 
                ARRAY_A
            );

        }
        else {

            $sql_query = "SELECT country, count(*) as total_ratings
                        FROM {$wpdb->prefix}idocs_ratings_content
                        
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE()
                            AND
                                kb_id = %d
                        GROUP BY country
                        ORDER BY total_ratings $order
                        LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        return $result;

    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_top_countries_by_search_queries () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'top_countries_by_search_queries',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'top_countries_by_search_queries'),

                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function top_countries_by_search_queries( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        $TopX = absint(sanitize_text_field($data ['top_x']));
        /*--------------------------------------------*/
        if ( ($data['desc']) == 1 )
            $order = 'DESC';
        else
            $order = 'ASC';
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );
        /*--------------------------------------------*/
        if ( $kb_id == '0' ) {
           
            $sql_query = "SELECT country, count(*) as total_visits
                      FROM {$wpdb->prefix}idocs_search_logs
                      
                      WHERE date_add(search_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                      GROUP BY country
                      ORDER BY total_visits $order
                      LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query,  $prepare_values ), 
                ARRAY_A
            );

        }
        else {

            $sql_query = "SELECT country, count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_search_logs
                        
                        WHERE date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                                kb_id = %d
                        GROUP BY country
                        ORDER BY total_visits $order
                        LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        return $result;

    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_kb_view_visits_per_day () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'kb_view_visits_per_day',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'kb_view_visits_per_day'),

                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function kb_view_visits_per_day( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*--------------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );
        /*---------------------------------------*/
        if ( $kb_id == '0' ) { // all kbs
            //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();
            $sql_query = "SELECT day(visit_time) as day, DATE_FORMAT(visit_time, '%e-%m') AS month_day, count(*) as count
                        FROM {$wpdb->prefix}idocs_visits_taxonomy
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                        AND
                            taxonomy = 'idocs-kb-taxo'
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                        GROUP BY day(visit_time), 2
            ";
            
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values ), 
                ARRAY_A
            );
        } 
        else { // specific kb

            $sql_query = "SELECT day(visit_time) as day, DATE_FORMAT(visit_time, '%e-%m') AS month_day, count(*) as count
                        FROM {$wpdb->prefix}idocs_visits_taxonomy
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                        AND
                            taxonomy = 'idocs-kb-taxo'
                        AND
                            kb_id = %d
                        GROUP BY day(visit_time), 2
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        $recNew = [];
        foreach($result as $key => $row) {
            foreach($row as $field => $value) {
                $recNew[$field][] = $value;
            }
        }
        /*--------------------------------------------*/
        return $recNew;
    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_content_visits_per_day () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'content_visits_per_day',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'content_visits_per_day'),

                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function content_visits_per_day( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*--------------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );
        /*---------------------------------------*/
        if ( $kb_id == '0' ) { // all kbs
            //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();
            $sql_query = "SELECT day(visit_time) as day, DATE_FORMAT(visit_time, '%e-%m') AS month_day, count(*) as count
                        FROM {$wpdb->prefix}idocs_visits_content
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                        GROUP BY day(visit_time), 2
            ";
            
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $prepare_values ), 
                ARRAY_A
            );
        } 
        else { // specific kb

            $sql_query = "SELECT day(visit_time) as day, DATE_FORMAT(visit_time, '%e-%m') AS month_day, count(*) as count
                        FROM {$wpdb->prefix}idocs_visits_content
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                            AND
                            kb_id = %d
                        GROUP BY day(visit_time), 2
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        $recNew = [];
        foreach($result as $key => $row) {
            foreach($row as $field => $value) {
                $recNew[$field][] = $value;
            }
        }
        /*--------------------------------------------*/
        return $recNew;
    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_top_content_visits () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'top_content_visits',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'top_content_visits'),

                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/ 
    public function top_content_visits( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        $TopX = 10;
        /*--------------------------------------------*/
        if ( ($data['desc']) == 1 )
            $order = 'DESC';
        else
            $order = 'ASC';
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );
        /*--------------------------------------------*/
        if ( $kb_id == '0' ) {
           
            $sql_query = "SELECT kb_id, content_id, content_type, t2.post_title, t2.post_author, t2.post_status, count(*) as total_visits
                      FROM {$wpdb->prefix}idocs_visits_content
                      JOIN 
                        ( 
                            SELECT id, post_title, post_author, post_status 
                            FROM wp_posts
                            WHERE post_type = 'idocs_content'
                        ) as t2
                        ON {$wpdb->prefix}idocs_visits_content.content_id = t2.id 
                      WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                      GROUP BY content_id
                      ORDER BY total_visits $order
                      LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query,  $prepare_values ), 
                ARRAY_A
            );

        }
        else {

            $sql_query = "SELECT kb_id, content_id, content_type, t2.post_title, t2.post_author, t2.post_status, count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visits_content
                        JOIN 
                            ( 
                                SELECT id, post_title, post_author, post_status 
                                FROM wp_posts
                                WHERE post_type = 'idocs_content'
                            ) as t2
                            ON {$wpdb->prefix}idocs_visits_content.content_id = t2.id 
                        WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                            AND
                            kb_id = %d
                        GROUP BY content_id
                        ORDER BY total_visits $order
                        LIMIT %d
            ";
                
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        foreach ($result as $key => $row) {

            // translate each kb id to the kb title. 
            $kb_id = $row['kb_id'];
            $kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($kb_id);
            //$kb_term = get_term($row['kb_id'], 'idocs-kb-taxo');
            $result[$key]['kb_title'] = $kb_term->name;

            //$post_categories = get_the_terms($result[$key]['document_id'], 'idocs-category-taxo');
            $post_categories = IDOCS_Taxanomies::get_the_terms_caching( $result[$key]['content_id'], 'idocs-category-taxo' );
            $result[$key]['post_permalink'] = get_permalink($result[$key]['content_id']);
            //do_action( 'qm/debug', $result[$key]['document_id']);
            //do_action( 'qm/debug', $post_categories);
            
            $result[$key]['post_author']= get_the_author_meta( 'display_name', $result[$key]['post_author'] );

            if ( $post_categories != NULL )
                $result[$key]['category_title'] = $post_categories[0]->name;
            else 
                $result[$key]['category_title']= '';
        };
        /*--------------------------------------------*/
        return $result;

    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_visits_per_document () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'visits_per_document',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_visits_per_document'),

                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_visits_per_document( $data ) {

        global $wpdb;
        /*--------------------------------------------*/
        //$table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_visited';
        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        $TopX = 10;
        /*--------------------------------------------*/
        if ( ($data['desc']) == 1 )
            $order = 'DESC';
        else
            $order = 'ASC';
        /*---------------------------------------*/
        $kbs_array = IDOCS_Custom_RestAPIs::get_kbs_list_array(); 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
         // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array, array( $TopX ) );
        /*--------------------------------------------*/
        if ( $kb_id == '0' ) {
            //$kbs_string = IDOCSPRO_Custom_RestAPIs::get_kbs_list();
           
            $sql_query = "SELECT kb_id, document_id, t2.post_title, t2.post_author, t2.post_status, count(*) as total_visits
                      FROM {$wpdb->prefix}idocs_visited
                      JOIN 
                        ( 
                            SELECT id, post_title, post_author, post_status 
                            FROM wp_posts
                            WHERE post_type = 'idocs_content'
                        ) as t2
                        ON {$wpdb->prefix}idocs_visited.document_id = t2.id 
                      WHERE date_add(visited_time, INTERVAL %d day) >= CURDATE()
                        AND
                            kb_id IN ($kbs_list_placeholders)  
                      GROUP BY document_id
                      ORDER BY total_visits $order
                      LIMIT %d
            ";
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query,  $prepare_values ), 
                ARRAY_A
            );

        }
        else {

            $sql_query = "SELECT kb_id, document_id, t2.post_title, t2.post_author, t2.post_status, count(*) as total_visits
                        FROM {$wpdb->prefix}idocs_visited
                        JOIN 
                            ( 
                                SELECT id, post_title, post_author, post_status 
                                FROM wp_posts
                                WHERE post_type = 'idocs_content'
                            ) as t2
                            ON {$wpdb->prefix}idocs_visited.document_id = t2.id 
                        WHERE date_add(visited_time, INTERVAL %d day) >= CURDATE()
                            AND
                            kb_id = %d
                        GROUP BY document_id
                        ORDER BY total_visits $order
                        LIMIT %d
            ";
                
            $result = $wpdb->get_results(
                $wpdb->prepare($sql_query, $days_back, $kb_id, $TopX ), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        foreach ($result as $key => $row) {

            // translate each kb id to the kb title. 
            $kb_id = $row['kb_id'];
            $kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($kb_id);
            //$kb_term = get_term($row['kb_id'], 'idocs-kb-taxo');
            $result[$key]['kb_title'] = $kb_term->name;

            //$post_categories = get_the_terms($result[$key]['document_id'], 'idocs-category-taxo');
            $post_categories = IDOCS_Taxanomies::get_the_terms_caching( $result[$key]['document_id'], 'idocs-category-taxo' );


            //do_action( 'qm/debug', $result[$key]['document_id']);
            //do_action( 'qm/debug', $post_categories);
            
            $result[$key]['post_author']= get_the_author_meta( 'display_name', $result[$key]['post_author'] );

            if ( $post_categories != NULL )
                $result[$key]['category_title'] = $post_categories[0]->name;
            else 
                $result[$key]['category_title']= '';
        };
        /*--------------------------------------------*/
        return $result;

    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_feedback_kpis () {
      
        register_rest_route( 
            'incredibledocs/v1', 
            'feedback_kpis',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_feedback_kpis'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    } 
    /*---------------------------------------------------------------------------------------*/
    public function get_feedback_kpis ( $data ) {

        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*--------------------------------------------*/
        if ($kb_id != '0' ) {

            $meta_query = array(
                array(
                    'key' => 'document-kb-id', 
                    'value' => $kb_id, 
                    'compare' => '=' // Use '=' for exact match
                ));
        }
        else {

            $kbs_string = IDOCS_Custom_RestAPIs::get_kbs_list_string();
            $meta_query = array(
                array(
                    'key' => 'document-kb-id', 
                    'value' => $kbs_string, 
                    'compare' => 'IN' // Use '=' for exact match
                ));
        
        };
        /*--------------------------------------------*/
        $comments_args = array(

            'post_type' => 'idocs_content',
            'meta_query' => $meta_query,
            'date_query' => array(
                array(
                    'after' => $days_back . ' days ago', // Comments submitted in the last 30 days
                    'inclusive' => true, // include comments from the exact date X days ago
                ),
            ),

            
        );
        /*--------------------------------------------*/
        $comments = get_comments($comments_args);

        if ($comments != null) {
            $output['total_feedback'] = count($comments);
        }
        else {
            $output['total_feedback'] = 0;
        }
        /*--------------------------------------------*/
        $comments_args = array(

            'post_type' => 'idocs_content',
            'status' => 'hold', // Retrieves comments marked as pending moderation.
            'meta_query' => $meta_query,
            'date_query' => array(
                array(
                    'after' => $days_back . ' days ago', // Comments submitted in the last 30 days
                    'inclusive' => true, // include comments from the exact date X days ago
                ),
            ),
            
        );
        /*--------------------------------------------*/
        $comments = get_comments($comments_args);

        if ($comments != null) {
            $output['pending_feedback'] = count($comments);
        }
        else {
            $output['pending_feedback'] = 0;
        }
        /*--------------------------------------------*/
        return $output;
    } 
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_feedback_list () {
      
        register_rest_route( 
            'incredibledocs/v1', 
            'feedback_list',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_feedback_list'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_feedback_list( $data ) {

        $days_back = absint(sanitize_text_field($data ['days_back']));
        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*--------------------------------------------*/
        if ($kb_id != '0' ) {

            $meta_query = array(
                array(
                    'key' => 'document-kb-id', 
                    'value' => $kb_id, 
                    'compare' => '=' // Use '=' for exact match
                ));
        }
        else {

            $kbs_string = IDOCS_Custom_RestAPIs::get_kbs_list_string();
            $meta_query = array(
                array(
                    'key' => 'document-kb-id', 
                    'value' => $kbs_string, 
                    'compare' => 'IN' // Use '=' for exact match
                ));
    
        };
        $comments_args = array(

            'post_type' => 'idocs_content',
            'meta_query' => $meta_query,
            'date_query' => array(
                array(
                    'after' => $days_back . ' days ago', // Comments submitted in the last 30 days
                    'inclusive' => true, // include comments from the exact date X days ago
                ),
            ),
            
        );
        /*--------------------------------------------*/
        $comments = get_comments($comments_args);
        $output = array ();

        // Loop through the comments
        foreach ($comments as $comment) {

            $comment_rec = array();
            $comment_rec['document_title'] = get_the_title($comment->comment_post_ID);            
            $comment_rec['comment_approved'] = $comment->comment_approved;
            $comment_rec['comment_author'] = $comment->comment_author;
            /*
            $date_time = new DateTime($comment->comment_date);
            $date_without_time = $date_time->format('Y-m-d');
            */

            $comment_rec['comment_date'] = $comment->comment_date;
            $comment_rec['comment_content'] = $comment->comment_content;

            // translate each kb id to the kb title.
            // get the kb id of that specific comment by id          
            $kb_id = get_comment_meta($comment->comment_ID, 'document-kb-id', true);
            //$kb_term = get_term($kb_id, 'idocs-kb-taxo');
            $kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($kb_id);
    
            if ( $kb_term != NULL )
                $comment_rec['kb_title'] = $kb_term->name;
            else 
                $comment_rec['kb_title'] = '';

            // get the category name of that post id associated to that comment
            //$post_categories = get_the_terms($comment->comment_post_ID, 'idocs-category-taxo');
            $post_categories = IDOCS_Taxanomies::get_the_terms_caching( $comment->comment_post_ID, 'idocs-category-taxo' );
        
            //do_action( 'qm/debug', $result[$key]['document_id']);
            //do_action( 'qm/debug', $post_categories);
                    
            if ( $post_categories != NULL )
                $comment_rec['category_title'] = $post_categories[0]->name;
            else 
                $comment_rec['category_title']= '';

            $output[] = $comment_rec;

        };
        /*--------------------------------------------*/
        // Extract the 'document_title' column
        $titles = array_column($output, 'document_title');
        $dates = array_column($output, 'comment_date');
        array_multisort($titles, $dates, $output);  
        /*--------------------------------------------*/
        return $output;
        
     }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_get_readability_score_list () {
      
        register_rest_route( 
            'incredibledocs/v1', 
            'readability_score_list',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'get_readability_score_list'),
                'permission_callback' => function () {
                    return current_user_can( $this->edit_capability );
                }
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function get_readability_score_list( $data ) {

        $kb_id = absint(sanitize_text_field($data ['kb_id']));
        /*--------------------------------------------*/
        if ($kb_id != '0' ) {

            $meta_query = array(
                array(
                    'key' => 'idocs-document-kb-meta',
                    'value' => $kb_id,
                    'compare' => '=',
                ),
            );    

        }
        else {

            $meta_query = array();
        }
        /*--------------------------------------------*/
        // Set up the query arguments
        $args = array(

            'post_type' => 'idocs_content',  
            'meta_query' => $meta_query,

        );
        // Create a new WP_Query
        $query = new WP_Query($args);
        $output = array();
        /*--------------------------------------------*/
        while ( $query->have_posts() ) {

            $query->the_post();
            //error_log(get_the_title());
            $document_rec['kb_title'] = '';
            $document_rec['category_title'] = '';
            $document_rec['document_title'] = sanitize_text_field(get_the_title());
            $document_rec['total_words'] = 0;
            $document_rec['readability_score'] =  0;
            /*--------------------------------------------*/
            $output[] = $document_rec;
            
        }
        /*--------------------------------------------*/
        // Restore original post data
        wp_reset_postdata();
        return $output; 
    }
    /*---------------------------------------------------------------------------------------*/
    // Calculate the Flesch-Kincaid Reading Ease score for a given text.
    public function calculate_reading_ease_score($text) {

        // Count the number of words, sentences, and syllables in the text
        $word_count = str_word_count($text);
        $sentence_count = preg_match_all('/[.!?]+/', $text, $matches);
        $syllable_count = preg_match_all('/[aeiouy]+/', preg_replace('/(?:[^aeiouy]es|ed|[^aeiouy]e)$/', '', strtolower($text)), $matches);
    
        // Calculate the Flesch-Kincaid Reading Ease score
        $reading_ease = 206.835 - 1.015 * ($word_count / $sentence_count) - 84.6 * ($syllable_count / $word_count);
        /*--------------------------------------------*/
        return $reading_ease;
    
    }
    /*---------------------------------------------------------------------------------------*/

    
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_save_content_rating () {

        register_rest_route( 
            'incredibledocs/v1', 
            'save_content_rating',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'save_content_rating'),
                'permission_callback' => '__return_true',

            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function save_content_rating( $data ) {
      
        /*------------------------------------------------*/
         // for public API (no logged-in users), we need to manually verify the nonce
         if ( !$_SERVER['HTTP_X_WP_NONCE'] || !wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'], 'wp_rest' ) ) {
             header('HTTP/1.0 403 Forbidden');
             exit;
         }
         /*------------------------------------------------*/
         // save a content rating event                  
        IDOCSPRO_Save_Rating_Events::save_content_rating_event(
 
             absint(sanitize_text_field($data['content_id'])),  // content id number (post id)
             sanitize_text_field($data['content_type']),  // content type
             absint(sanitize_text_field($data['rating_score'])),  // provided rating 
             sanitize_text_field($data['current_ip']),  // provided rating 
             absint(sanitize_text_field($data['kb_id']))    // knowledge-base id number
 
         );
         // remove the stars ratings cache 
         delete_transient('idocs_transient_stars_rating_per_document');
         return true;  
     }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_save_taxonomy_rating () {

        register_rest_route( 
            'incredibledocs/v1', 
            'save_taxonomy_rating',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'save_taxonomy_rating'),
                'permission_callback' => '__return_true',

            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function save_taxonomy_rating( $data ) {
       
        /*------------------------------------------------*/
         // for public API (no logged-in users), we need to manually verify the nonce
         if ( !$_SERVER['HTTP_X_WP_NONCE'] || !wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'], 'wp_rest' ) ) {
             header('HTTP/1.0 403 Forbidden');
             exit;
         }
 
         /*------------------------------------------------*/
         // save a rating event                  
         IDOCSPRO_Save_Rating_Events::save_taxonomy_rating_event(
 
             absint(sanitize_text_field($data['term_id'])),  // content id number (post id)
             sanitize_text_field($data['taxonomy']),  // content type
             absint(sanitize_text_field($data['rating_score'])),  // provided rating 
             sanitize_text_field($data['current_ip']),  // provided rating 
             absint(sanitize_text_field($data['kb_id']))    // knowledge-base id number
 
         );
         // remove the stars ratings cache 
         //delete_transient('idocs_transient_stars_rating_per_document');
         return true;  
     }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_save_feedback_result () {

        register_rest_route( 
            'incredibledocs/v1', 
            'save_feedback',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'save_feedback_results'),
                'permission_callback' => '__return_true',

            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function save_feedback_results( $data ) {
        
        /*------------------------------------------------*/
        // for public API (no logged-in users), we need to manually verify the nonce
        if ( !$_SERVER['HTTP_X_WP_NONCE'] || !wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'], 'wp_rest' ) ) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        /*------------------------------------------------*/
        // save the fedback event as a wordpress comment 
        $comment_data = array (

            'comment_content'      =>  sanitize_text_field($data['feedback_comment']),
            'comment_post_ID'      =>  absint($data['document_id']),
            'comment_author_email' => sanitize_text_field($data['email']),
            'comment_author'       =>  sanitize_text_field($data['name']),
            'comment_type'         => 'IncredibleDocs',
            'comment_approved'     => 0, // Set to 0 for unapproved status
            'comment_meta'         => array(
                'document-kb-id' => absint($data['kb_id']),
            ),
        );

        $comment_id = wp_insert_comment( $comment_data );
        return true;        
    }
    /*---------------------------------------------------------------------------------------*/
    public function register_custom_route_save_video_visit_event () {
        
        register_rest_route( 
            'incredibledocs/v1', 
            'save_video_visit_event',
            array(
                'methods'  => WP_REST_SERVER::READABLE,  
                'callback' => array($this, 'save_video_visit_event'),
                'permission_callback' => '__return_true',
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function save_video_visit_event( $data ) { 

        /*------------------------------------------------*/
        // for public API (no logged-in users), we need to manually verify the nonce
        if ( !$_SERVER['HTTP_X_WP_NONCE'] || !wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'], 'wp_rest' ) ) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        /*------------------------------------------------*/
        IDOCS_Save_Events::save_content_visit_event(

            absint(sanitize_text_field($data['content_id'])),  // post id number
            sanitize_text_field($data['content_type']),  
            sanitize_text_field($data['current_ip']),  
            absint(sanitize_text_field($data['kb_id'])),    // knowledge-base id number
            sanitize_text_field($data['ignore_local_host_events']),  

        );
        // remove the stars ratings cache 
        //delete_transient('idocs_transient_stars_rating_per_document');
        return true;  
        
    }
    /*---------------------------------------------------------------------------------------*/

    /*---------------------------------------------------------------------------------------*/
    public function update_rating_score_kpi ( $data ) {

        $stars_score = isset($_POST['stars_score']) ? sanitize_text_field($_POST['stars_score']) : '';
        /*------------------------------------------------*/
        ob_start();
        ?>
        <?php echo esc_html(number_format($stars_score,1));?>
        <span class="idocs-star-rating"> 
            <?php
            
                for ($star=1 ; $star <= 5; $star ++) {

                    if ($stars_score >= $star) {

                        IDOCS_ICONS::echo_icon_svg_tag("star", 14, 14);

                    }
                    else if ($stars_score + 0.5 >= $star) {

                        IDOCS_ICONS::echo_icon_svg_tag("star-half-stroke", 14, 14);

                    }
                    
                    else {
                        IDOCS_ICONS::echo_icon_svg_tag("empty-star", 14, 14);
                    }
                }
            ?> 
        </span> 
        <?php
        /*------------------------------------------------*/
        $output = ob_get_contents();
        ob_end_clean();
        
        //echo $output;
        //wp_die();
        
        $response = array(
            'content' => $output, 
        );
        wp_send_json($response);
        exit();
               
    }
    /*---------------------------------------------------------------------------------------*/
    
    /*---------------------------------------------------------------------------------------*/
    
    /*---------------------------------------------------------------------------------------*/
    /*---------------------------------------------------------------------------------------*/

    
}

/* Additional Info */
// https://developer.wordpress.org/rest-api/
// https://deliciousbrains.com/comparing-wordpress-rest-api-performance-admin-ajax-php/
// https://sabrinazeidan.com/wp-rest-api-search-with-autocomplete-with-vanilla-js/
// https://github.com/andrewatts85/wp-notes/blob/master/notes/how-to-setup-live-search-with-wp-rest-api.md
// https://code.tutsplus.com/tutorials/wp-rest-api-retrieving-data--cms-24694