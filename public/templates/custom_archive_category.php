<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
get_header();
/*--------------------------------------------*/
global $wp_query;
$kb_id = get_queried_object('idocs-kb-taxo') != null ? get_queried_object()->term_id : '';
// getting the second taxonomy (category) from the wp_query object 
$category_slug = $wp_query->query_vars['idocs-category-taxo'];
// translate the slug to term_id 
$term = IDOCS_Taxanomies::get_specific_category_term_by_slug_caching($category_slug);
$cat_id = $term->term_id; 
/*--------------------------------------------*/
// Category taxonomy and knowledge-base taxonomies are not correlated to each other.
// Therefore, to inforce the correct url, we must check that the knowledge-base 
// coming from the url is correlated to category in the url (otherwise, any knowledge-base instance will work.)
/*--------------------------------------------*/
// get the knowledge-base id (stored in 'idocs-category-taxo-kb') of specific category term by id ($tax_id)
//$real_category_kb_id =  get_term_meta( $cat_id, 'idocs-category-taxo-kb', true );
$real_category_kb_id = IDOCS_Taxanomies::get_term_meta_caching( $cat_id, 'idocs-category-taxo-kb', false);
// check if the knowledge-base id (coming from the url) does not match the category id 
if ( $real_category_kb_id != $kb_id ) {

    global $wp_query;
    $wp_query->set_404(); // Set the query to 404
    status_header(404);   // Set 404 status header
    include( get_query_template( '404' ) ); // Load the 404 template
    exit(); // Stop execution

}
/*--------------------------------------------*/
$category_id = $cat_id; 
require_once IDOCS_DIR_PATH . 'public/templates/idocs_kb_view.php';
/*--------------------------------------------*/
get_footer();