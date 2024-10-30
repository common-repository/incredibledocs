<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/*
    A post meta field is a WordPress object used to store extra data about a post. 
    When registering the field, note the show_in_rest parameter is enabled.
    This ensures the data will be included in the REST API, which the block editor uses to load and save meta data.
*/ 
/*---------------------------------------------------------------------------------------*/
class IDOCS_PostMetaFields {

    public function validate_link_url ($link) {

        return esc_url_raw($link);

    }
    /*---------------------------------------*/
    // Registering the post meta data 
    public function register_post_meta_fields() {
        
        /*---------------------------------------*/
        // Content Knowlege-Base
        /*---------------------------------------*/ 
        register_post_meta(
            'idocs_content', // cpt
            'idocs-content-kb-meta', //field name 
            [
                'auth_callback' => '__return_true',
                'default'       => 0,
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'number',
            ]
        );
        /*---------------------------------------*/
        // Content Category 
        /*---------------------------------------*/
        register_post_meta(
            'idocs_content',
            'idocs-content-category-meta',
            [
                'auth_callback' => '__return_true',
                'default'       => 0,
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'number',
            ]
        );
        /*---------------------------------------*/
        // Content Parent-Category (used for the search process)
        /*---------------------------------------*/
        register_post_meta(
            'idocs_content',
            'idocs-parent-category-meta',
            [
                'auth_callback' => '__return_true',
                'default'       => 0,
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'number',
            ]
        );
        /*---------------------------------------*/
        // Content Link URL 
        /*---------------------------------------*/
        register_post_meta(
            'idocs_content',
            'idocs-content-link-meta',
            [
                'auth_callback' => '__return_true',
                'default'       => "",
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'string',
                'sanitize_callback' => array($this, 'validate_link_url')
            ]
        );
        /*---------------------------------------*/
        // Content Link URL - open in a new tab?
        /*---------------------------------------*/
        register_post_meta(
            'idocs_content',
            'idocs-content-newtab-meta',
            [
                'auth_callback' => '__return_true',
                'default'       => true,
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'boolean',
            ]
        );
        /*---------------------------------------*/
        // Content Type 
        /*---------------------------------------*/
        register_post_meta(
            'idocs_content',
            'idocs-content-type-meta',
            [
                'auth_callback' => '__return_true',
                'default'       => 0,
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'number',
            ]
        );
        /*---------------------------------------*/
        // FAQ Group
        /*---------------------------------------*/
        register_post_meta(
            'idocs_content',
            'idocs-faq-group-meta',
            [
                'auth_callback' => '__return_true',
                'default'       => 0,
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'number',
            ]
        );
        /*---------------------------------------*/
        // Tags
        /*---------------------------------------*/
        register_post_meta(
            'idocs_content',
            'idocs-tags-meta',
            [
                'auth_callback' => '__return_true',
                'default'       => [],
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'array',
                'show_in_rest'  => [
                    'schema' => [
                        'items' => [
                            'type' => 'number',
                        ],
                    ],
                ],
            ]
        );
        /*---------------------------------------*/
        // Video URL
        /*---------------------------------------*/
        register_post_meta(
            'idocs_content',
            'idocs-content-video-url-meta',
            [
                'auth_callback' => '__return_true',
                'default'       => "",
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'string',
                'sanitize_callback' => array($this, 'validate_link_url')

            ]
        );
        /*---------------------------------------*/
        // YouTube - Video ID
        /*---------------------------------------*/
        register_post_meta(
            'idocs_content',
            'idocs-content-video-yturl-meta',
            [
                'auth_callback' => '__return_true',
                'default'       => "",
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'string',
                'sanitize_callback' => array($this, 'validate_link_url')

            ]
        );
        /*---------------------------------------*/
        // Display Order 
        /*---------------------------------------*/
        register_post_meta(
            'idocs_content',
            'idocs-content-display-order-meta',
            [
                'auth_callback' => '__return_true',
                'default'       => 1,
                'show_in_rest'  => true, // so the meta field can be accessed by the block editor. 
                'single'        => true,
                'type'          => 'number',
            ]
        );
    }
    /*---------------------------------------------------------------------------------------*/
    // update the content post - cpt taxonomies fields after a new post was created/existing is updated. (5e5827093d)
    public function update_content_post( $post_id ) {

        // exit if it is not our cpt
        if ( ! get_post_type($post_id) == 'idocs_content' ) {

            exit;
            
        }
        /*---------------------------------------*/
        // copy the cpt metadata (saved by the block editor) to the cpt taxonomies fields so they will be available in the admin screen columns. 
        // don't use cache data here for the post meta-data
        $kb_id = get_post_meta($post_id, 'idocs-content-kb-meta', true);
        $cat_id = get_post_meta($post_id, 'idocs-content-category-meta', true);
        $content_id = get_post_meta($post_id, 'idocs-content-type-meta', true);
        $faqgroup_id = get_post_meta($post_id, 'idocs-faq-group-meta', true);
        $tags_ids = get_post_meta($post_id, 'idocs-tags-meta', true);
        /*--------------------------------------------*/
        // adding or updating a content item (cusom post type) --> removing the caching data on cpts
        delete_transient( 'idocs_transient_direct_content_flags');
        delete_transient( 'idocs_transient_posts_metadata');
        delete_transient( 'idocs_transient_total_content_types' );
		delete_transient( 'idocs_transient_faqs_schema_for_seo_' . $kb_id );
        delete_transient( 'idocs_transient_navigation_links' );
        delete_transient( 'idocs_transient_faqs_per_group');
        delete_transient( 'idocs_transient_attached_terms_' . 'idocs-category-taxo');
        delete_transient( 'idocs_transient_attached_terms_' . 'idocs-kb-taxo');
        delete_transient( 'idocs_transient_attached_terms_' . 'idocs-tag-taxo');
        delete_transient( 'idocs_transient_attached_terms_' . 'idocs-faq-group-taxo');
        /*---------------------------------------*/
        if ( (int) $kb_id != 0 ) {
            wp_set_post_terms($post_id, array( (int) $kb_id ), 'idocs-kb-taxo');
        }
        if ( (int) $cat_id != 0 ) {   
            wp_set_post_terms($post_id, array( (int) $cat_id ), 'idocs-category-taxo');
        }
        if ( (int) $content_id != 0 ) {   
            wp_set_post_terms($post_id, array( (int) $content_id ), 'idocs-content-type-taxo');
        }
        /*---------------------------------------*/
        if ( (int) $faqgroup_id != 0 ) {  

            wp_set_post_terms($post_id, array( (int) $faqgroup_id ), 'idocs-faq-group-taxo');
            // get the faq category id and save it as the faq post category id (if the faq group is located at the root of the kb, then there is no category)
            $faq_term = get_term_by('id', $faqgroup_id, 'idocs-faq-group-taxo');
            $faqgroup_category = IDOCS_Taxanomies::get_term_meta_caching(  $faq_term->term_id, 'idocs-faq-group-taxo-category', false);
            //$faqgroup_category =  get_term_meta( $faq_term->term_id, 'idocs-faq-group-taxo-category', true );
            wp_set_post_terms($post_id, array( (int) $faqgroup_category ), 'idocs-category-taxo');

            // getting the root category id of the faq-group category which was selected for that FAQ.
            $parent_id = $this->get_term_top_most_parent( $faqgroup_category, 'idocs-category-taxo');
            // save the root category id is a dedicated metabox attribute to be used by the searching engine.
            update_post_meta($post_id, 'idocs-parent-category-meta', $parent_id);

        }
        else { // editor faqgroup is is zero --> in case a FAQ content item was switched to another content item --> reset the FAQ Group. 

            wp_set_post_terms($post_id, array( 0 ), 'idocs-faq-group-taxo');

        }
        /*---------------------------------------*/
        if ( (int) $tags_ids != 0 ) {  
            //error_log(gettype($tags_ids));
            // the array cells are double --> must be converted to integer  
            $tags_ids = array_map('intval', $tags_ids);
            //error_log(gettype($tags_ids[0]));
            wp_set_post_terms($post_id, $tags_ids , 'idocs-tag-taxo');
        } 
        else { // list of tags is empty 
            wp_set_post_terms($post_id, [] , 'idocs-tag-taxo');
        }
        /*---------------------------------------*/
        // save the parent category id as a metadata field.
        if ( !empty($cat_id) and (int) $cat_id != 0 ) {
            // getting the root category id of the category which was selected for that document 
            $parent_id = $this->get_term_top_most_parent($cat_id, 'idocs-category-taxo');
            // save the root category id is a dedicated metabox attribute to be used by the searching engine.
            update_post_meta($post_id, 'idocs-parent-category-meta', $parent_id);
        };
    }
    /*---------------------------------------------------------------------------------------*/
    // Utility function to translate array of tags ids into labels 
    public function translate_tag_ids_to_labels($tag_ids) {

        $tag_labels = array();
        foreach ($tag_ids as $tag_id) {
            $tag = get_term($tag_id, 'post_tag');
            if ($tag && !is_wp_error($tag)) {
                $tag_labels[] = $tag->name;
            }
        }
        /*---------------------------------------*/
        return $tag_labels;
    }
    /*---------------------------------------------------------------------------------------*/
    // Utility function to determine the top-most parent id of a term
	public function get_term_top_most_parent( $term, $taxonomy ) {
		
        // Start from the current term
		$parent  = get_term( $term, $taxonomy );
        // if the category term does not exist anymore: 
        if ($parent == null ) return null;
        /*--------------------------------------------*/
		// Climb up the hierarchy until we reach a term with parent = '0'
		while ( $parent->parent != '0' ) {
			$term_id = $parent->parent;
			$parent  = get_term( $term_id, $taxonomy);
		}
        /*---------------------------------------*/
		return $parent->term_id;
	}
}
/*---------------------------------------------------------------------------------------*/
// https://developer.wordpress.org/block-editor/how-to-guides/metabox/
//https://developer.wordpress.org/block-editor/reference-guides/slotfills/plugin-document-setting-panel/
