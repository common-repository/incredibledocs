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
if ($public_visitor and $kb_access_type == 'Internal') {

    ?>
    <div class="idocs-tag-view"> 
        <p><?php echo esc_html__( 'Access Denied', 'incredibledocs' );?></p>
    </div>
    <?php
}
/*--------------------------------------------*/
else {

    $design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
    $show_live_search = $design_settings['tag_view_show_live_search'];
    //$analytics_data_tag_view_visit = $design_settings['analytics_data_tag_view_visit'];
    /*--------------------------------------------*/
    ?>
    <div class="container-fluid idocs-tag-view"> 
    <?php
        /*--------------------------------------------*/
        // Display a search bar?
        if ( $show_live_search ) {
            
            require_once IDOCS_DIR_PATH . 'public/templates/idocs_live_search.php';

        }
        
        /*--------------------------------------------*/
        // Display categories cards 
        require_once IDOCS_DIR_PATH . 'public/templates/idocs_tag_content_items.php';
        /*--------------------------------------------*/
        ?>
    </div> <!-- "idocs-tag-view" --> 
    <?php
    /*--------------------------------------------*/
    //if ($analytics_data_tag_view_visit) {
        /* Analytics */
        if (isset($_SERVER['REMOTE_ADDR']) && array_key_exists('REMOTE_ADDR', $_SERVER)) 
            $current_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        else
            $current_ip = '';
        /*--------------------------------------------*/
        IDOCS_Save_Events::save_taxonomy_visit_event($tag_id, "idocs-tag-taxo", $current_ip, $kb_id );							
        /*--------------------------------------------*/
    //}
}
/*--------------------------------------------*/
