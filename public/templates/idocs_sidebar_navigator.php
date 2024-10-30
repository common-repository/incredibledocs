<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
if ($hide_empty_categories == '1') {
    $hide_empty_categories = true;
}
else {
    $hide_empty_categories = false;
};
/*--------------------------------------------*/
// get all top-level categories related to specific knowledge-base
$top_level_category_list = get_terms( array(

    'taxonomy'   => 'idocs-category-taxo',
    'hide_empty' => $hide_empty_categories,
    'meta_key'   => 'idocs-category-taxo-kb',
    'meta_value' => $current_kb_id,
    //'hierarchical' => true,
    'parent' => 0, // get only the top-level 
    'orderby' => 'name',
    'order' => 'ASC', 

) );
/*--------------------------------------------*/
$interal_user = is_user_logged_in();
$public_visitor = ! $interal_user;
$category_cards_order_by = $design_settings['categories_box_cards_order_by'];
$hide_empty_categories = $design_settings['categories_box_hide_empty_categories'];
$category_list = IDOCS_Taxanomies::get_categories_terms_no_caching($current_kb_id, 0, $hide_empty_categories, $category_cards_order_by );
// filter out categories based on access type and user/visitor type
$top_level_category_list = IDOCS_Access_Check::filter_out_categories($current_kb_id, $interal_user, $category_list);
//do_action( 'qm/debug', $top_level_category_list );
/*--------------------------------------------*/
$category_num = 1; 
// get the current post category (single doc template) 
$post_categories = IDOCS_Taxanomies::get_the_terms_caching( $current_doc_id, 'idocs-category-taxo' );

if ( $show_only_current_category ) {

    $top_level_category_list = $post_categories;
}

//do_action( 'qm/debug', $post_categories );
//do_action( 'qm/debug', $category_list );
/*--------------------------------------------*/
?>
<div class="accordion idocs-navigation-box" id="idocs-navigator-accordion">
    <?php	
    foreach ($top_level_category_list as $top_category) {
    
        $term_permalink = get_term_link ($top_category->slug, 'idocs-category-taxo');
        list($category_tree, $show_accordion) = IDOCS_CategoryTree::build_category_tree($top_category->term_id, $current_kb_id, $post_categories[0]->name, $hide_empty_categories );
        /*--------------------------------------------*/
        if ( $show_only_current_category ) {

            $category_tree = array ();
        }
        
        // check if the current category (for-loop) is equal to the current post category (single doc template)
        if 	($top_category->name == $post_categories[0]->name) {    

            $show_accordion = 'show'; 
            
        }
        /*--------------------------------------------*/
        // only content items from the type "Document" 
        $document_content_type_term = get_term_by('name', 'Document' , 'idocs-content-type-taxo' );

        // used for the category counter 
        // get all content items related to specific category including content items in sub-categories 
        $args = array(
            'post_type' => 'idocs_content',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'idocs-category-taxo',
                    'field' => 'term_id',
                    'terms' => $top_category->term_id,
                    'operator' => 'IN',
                    'include_children' => true,
                ),
                array(
                    'taxonomy'         => 'idocs-content-type-taxo', 
                    'field'            => 'term_id',
                    'terms'            =>  $document_content_type_term->term_id, 
                    'operator'         => 'IN',
                ),
                /*
                array(
                    'taxonomy'         => 'idocs-content-type-taxo', 
                    'field'            => 'term_id',
                    'terms'            =>  $content_type_term->term_id, 
                    'operator'         => 'NOT IN',
                ),
                */
            )
        );
        $the_query = new WP_Query( $args ); 
        $post_counts = $the_query->post_count; // save the number of content items 
        /*--------------------------------------------*/
        $category_card_documents_order_by = $design_settings['category_card_documents_order_by'];

        if ( $category_card_documents_order_by == 'custom_display_order' ) {

            $args = array(
                'post_type' => 'idocs_content',
                'posts_per_page' => -1,
                'orderby' => 'meta_value_num', // Order by a numeric value
                'meta_key' => 'idocs-content-display-order-meta', // Specify the meta key
                'order' => 'ASC',
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'idocs-category-taxo',
                        'field' => 'term_id',
                        'terms' => $top_category->term_id,
                        'operator' => 'IN',
                        'include_children' => false,
                    ),
                    array(
                        'taxonomy'         => 'idocs-content-type-taxo', 
                        'field'            => 'term_id',
                        'terms'            =>  $document_content_type_term->term_id, 
                        'operator'         => 'IN',
                    ),
                )
            );
        }
        else {
            // get all content items related to specific category excluding content items in sub-categories 
            $args = array(
                'post_type' => 'idocs_content',
                'posts_per_page' => -1,
                'orderby' => $category_card_documents_order_by,
                'order' => 'ASC',
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'idocs-category-taxo',
                        'field' => 'term_id',
                        'terms' => $top_category->term_id,
                        'operator' => 'IN',
                        'include_children' => false,
                    ),
                    array(
                        'taxonomy'         => 'idocs-content-type-taxo', 
                        'field'            => 'term_id',
                        'terms'            =>  $document_content_type_term->term_id, 
                        'operator'         => 'IN',
                    ),
                    /*
                    array(
                        'taxonomy'         => 'idocs-content-type-taxo', 
                        'field'            => 'term_id',
                        'terms'            =>  $content_type_term->term_id, 
                        'operator'         => 'NOT IN',
                    ),
                    */
                )
            );
        }
        $the_query = new WP_Query( $args ); 
        //do_action( 'qm/debug', $the_query );
        $icon_size = $design_settings['navigation_box_document_item_icon_width'];
        $documents_order_by = $design_settings['category_card_documents_order_by'];
        /*--------------------------------------------*/
        IDOCS_Shortcodes::navigator_accordion_item($documents_order_by, $category_num, $top_category, $show_category_counter, $post_counts, $show_accordion, $the_query, $category_tree, $post_categories, $top_category, $current_doc_id );
        $category_num++;
    }	
?>
</div> <!--accordion-->
<?php
/*--------------------------------------------*/
