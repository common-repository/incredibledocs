<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
get_header();
/*--------------------------------------------*/
global $wp_query;
// get the current knowledge-base id 
$kb_id = get_queried_object()->term_id;
// getting the second taxonomy (category) from the wp_query object 
$tag_slug = $wp_query->query_vars['idocs-tag-taxo'];
// translate the slug to term_id 
$term = IDOCS_Taxanomies::get_specific_tag_term_by_slug_caching($tag_slug);
$tag_id = $term->term_id; 
$tag_label = $term->name; 
/*--------------------------------------------*/
// tag taxonomy and knowledge-base taxonomies are not correlated to each other.
// Therefore, to inforce the correct url, we must check that the knowledge-base 
// coming from the url is correlated to tag in the url (otherwise, any knowledge-base instance will work.)
/*--------------------------------------------*/
// get the knowledge-base id (stored in 'idocs-category-taxo-kb') of specific tag term by id ($tax_id)
$real_tag_kb_id = IDOCS_Taxanomies::get_term_meta_caching( $tag_id, 'idocs-tag-taxo-kb', false);

// check if the knowledge-base id (coming from the url) does not match the category id 
if ( $real_tag_kb_id != $kb_id ) {

    global $wp_query;
    $wp_query->set_404(); // Set the query to 404
    status_header(404);   // Set 404 status header
    include( get_query_template( '404' ) ); // Load the 404 template
    exit(); // Stop execution

}
/*--------------------------------------------*/
require_once IDOCS_DIR_PATH . 'public/templates/idocs_tag_view.php';
/*--------------------------------------------*/
get_footer();