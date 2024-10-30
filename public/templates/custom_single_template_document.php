<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*--------------------------------------------*/
function idocs_check_categories_slug ( $categories_slug, $real_category_id ) {

    $slug_segments = explode( '/', $categories_slug );
    $slug_segments = array_reverse( $slug_segments );
    /*--------------------------------------------*/
    foreach ($slug_segments as $slug) {
        //do_action( 'qm/debug', $slug );
       // $term = get_term_by('slug', $slug, 'idocs-category-taxo');
        $term = IDOCS_Taxanomies::get_specific_category_term_by_slug_caching($slug);

        if ($term == null ) {
            $required_category_id = 0;    
        } 
        else {
            $required_category_id = $term->term_id; 
        }
        if ($required_category_id != $real_category_id) {
            return false;
        }
        //$term = get_term_by('id', $real_category_id, 'idocs-category-taxo');
        $term = IDOCS_Taxanomies::get_specific_category_term_caching($real_category_id);
        $real_category_id = $term->parent;
    };
    /*--------------------------------------------*/
    return true;
}
/*--------------------------------------------*/
get_header();
global $wp_query;
// Get the current custom post type archive object
$queried_object = get_queried_object();
$post_id = $queried_object != null ? $queried_object->ID : '';
$real_kb_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-kb-meta');
$real_category_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-category-meta');
//do_action( 'qm/debug', $wp_query->query_vars );
/*--------------------------------------------*/
$required_kb_slug = $wp_query->query_vars['idocs-kb-taxo'];
$term = IDOCS_Taxanomies::get_specific_kb_term_by_slug_caching($required_kb_slug);
/*--------------------------------------------*/
if ($term == null ) {
    $required_kb_id = 0;    
} 
else {
    $required_kb_id = $term->term_id; 
}
/*--------------------------------------------*/
$required_category_slug = $wp_query->query_vars['idocs-category-taxo'];
$categories_slugs_ok = idocs_check_categories_slug($required_category_slug, $real_category_id);
/*--------------------------------------------*/
if ( $real_kb_id != $required_kb_id || !$categories_slugs_ok ) {

    global $wp_query;
    $wp_query->set_404(); // Set the query to 404
    status_header(404);   // Set 404 status header
    include( get_query_template( '404' ) ); // Load the 404 template
    exit(); // Stop execution

}
/*--------------------------------------------*/
$current_kb_id = $real_kb_id;
$current_document_id = $post_id;
$current_category_id = $real_category_id;
/*--------------------------------------------*/
require_once IDOCS_DIR_PATH . 'public/templates/idocs_document_view.php';
/*--------------------------------------------*/
get_footer();


