<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
// get the kb access type 
$kb_access_type = IDOCS_Taxanomies::get_term_meta_caching( $kb_id, 'idocs-kb-taxo-access-type', false);
//$kb_access_type = get_term_meta( $kb_id, 'idocs-kb-taxo-access-type', true);
$interal_user = is_user_logged_in();
$public_visitor = ! $interal_user;
/*--------------------------------------------*/
// prevent access if it is a public visitor and an "Internal" KB
if ( $public_visitor and $kb_access_type == 'Internal' ) {

    ?>
    <div class="idocs-kb-view"> 
        <p><?php echo esc_html__( 'Access Denied', 'incredibledocs' );?></p>
    </div>
    <?php
    
}
/*--------------------------------------------*/
else {
    
    $design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
    //do_action( 'qm/debug', $design_settings );
    $show_live_search_kb = $design_settings['kb_view_show_live_search'];
    $kb_view_show_faqs = $design_settings['kb_view_show_faqs'];
    $show_breadcrumb_flag = $design_settings['kb_view_show_breadcrumb'];
    //$analytics_data_kb_view_visit = $design_settings['analytics_data_kb_view_visit'];
    //$analytics_data_category_view_visit = $design_settings['analytics_data_category_view_visit'];
    $show_star_feedback = $design_settings['document_content_box_show_document_star_rating'];
    /*--------------------------------------------*/
    ?>
    <div class="container-fluid idocs-kb-view"> 
    <?php
        /*--------------------------------------------*/
        // Display a search bar?
        if ( $show_live_search_kb ) {
            
            require_once IDOCS_DIR_PATH . 'public/templates/idocs_live_search.php';

        }
        /*--------------------------------------------*/
        // Display breadcrumb?
        if ($show_breadcrumb_flag) {

            require_once IDOCS_DIR_PATH . 'public/templates/idocs_kb_breadcrumbs.php';

        }
        /*--------------------------------------------*/
        // Display categories cards 
        require_once IDOCS_DIR_PATH . 'public/templates/idocs_categories_cards.php';
        /*--------------------------------------------*/
        //do_action( 'qm/debug', 'faqs flag:' . $kb_view_show_faqs );
        if ( $kb_view_show_faqs ) {
            			
            require_once IDOCS_DIR_PATH . 'public/templates/idocs_faqs.php';

        }
        /*--------------------------------------------*/
        if ( $show_star_feedback ) {

            if ($category_id == 0) {

                $taxonomy = "idocs-kb-taxo";
                $term_id = $kb_id;

            }
            else {

                $taxonomy = "idocs-category-taxo";
                $term_id = $category_id;
            }
            /*--------------------------------------------*/
            do_action('idocspro_five_stars_taxonomy_rating', $term_id, $taxonomy, $kb_id);
        }
    ?>
    </div> <!-- "idocs-kb-view" --> 
    <?php
    /*--------------------------------------------*/
    // KB View
    //if ( $analytics_data_kb_view_visit && $category_id == 0 ) {
    if ( $category_id == 0 ) {
        /* Analytics */
        if (isset($_SERVER['REMOTE_ADDR']) && array_key_exists('REMOTE_ADDR', $_SERVER)) 
            $current_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        else
            $current_ip = '';
        /*--------------------------------------------*/
        IDOCS_Save_Events::save_taxonomy_visit_event($kb_id, "idocs-kb-taxo", $current_ip, $kb_id );
    }
    /*--------------------------------------------*/
    // Category View
    //if ($analytics_data_category_view_visit && $category_id != 0) {
    if ( $category_id != 0 ) {
        
        /* Analytics */
        if (isset($_SERVER['REMOTE_ADDR']) && array_key_exists('REMOTE_ADDR', $_SERVER)) 
            $current_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        else
            $current_ip = '';
        /*--------------------------------------------*/
        IDOCS_Save_Events::save_taxonomy_visit_event($category_id, "idocs-category-taxo", $current_ip, $kb_id );
    }					
}
/*--------------------------------------------*/
