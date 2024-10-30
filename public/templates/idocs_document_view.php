<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
// filter out categories based on access type and user/visitor type
$access_allowed = IDOCS_Access_Check::check_access_to_content_item($current_kb_id, $current_document_id, is_user_logged_in(), get_current_user_id());
//do_action( 'qm/debug', $access_allowed );
if (! $access_allowed) {

    ?>
    <div class="idocs-document-view"> 
        <p><?php echo esc_html__( 'Access Denied', 'incredibledocs' );?></p>
    </div>
    <?php
    
}
/*--------------------------------------------*/
else {

    $design_settings = IDOCS_Database::get_plugin_design_settings($current_kb_id, null);
    /*--------------------------------------------*/
    $show_live_search_doc = $design_settings['document_view_show_live_search'];
    $show_category_counter = $design_settings['navigation_box_category_show_counter'];
    $show_breadcrumb_flag = $design_settings['document_view_show_breadcrumb'];
    $show_document_title = $design_settings['document_metadata_show_document_title'];
    $show_last_updated_date = $design_settings['document_metadata_show_last_updated_date'];
    $show_estimated_time_to_read = $design_settings['document_metadata_show_estimated_time_to_read'];
    $show_author = $design_settings['document_metadata_show_author'];
    $show_document_tags = $design_settings['right_sidebar_box_show_document_tags'];
    $show_document_tags_below_content = $design_settings['document_content_box_show_tags'];
    $show_related_documents = $design_settings['document_content_box_show_related_documents'];
    $show_like_feedback = $design_settings['document_content_box_show_document_like_rating'];
    $show_star_feedback = $design_settings['document_content_box_show_document_star_rating'];
    $show_improve_feedback = $design_settings['document_content_box_show_document_feedback'];
    $show_toc_navigator = $design_settings['right_sidebar_box_show_toc'];
    $show_print_icon = $design_settings['document_metadata_show_print_icon'];
    $show_tags_headline = $design_settings['document_metadata_show_tags'];
    $show_visits_counter = $design_settings['document_metadata_show_visits_counter'];
    $show_rating_score = $design_settings['document_metadata_show_rating_score'];
    $show_left_sidebar = $design_settings['content_and_sidebars_box_show_left_sidebar'];
    $show_right_sidebar = $design_settings['content_and_sidebars_box_show_right_sidebar'];
    $hide_empty_categories = $design_settings['navigation_box_hide_empty_categories'];
    $show_only_current_category = $design_settings['navigation_box_only_current_category'];
    $show_tag_background_color = $design_settings['document_tags_item_show_background_color'];
    $left_sidebar = $design_settings['left_sidebar_box_width'];
    $right_sidebar = $design_settings['right_sidebar_box_width'];
    $feedback_probability = $design_settings['feedback_collection_probability'];
    //$analytics_data_document_view_visit = $design_settings['analytics_data_document_view_visit'];
    if ($show_tag_background_color == null) {
        $show_tag_background_color= '0';
    }
    /*--------------------------------------------*/
    $document = get_post ($current_document_id);
    //do_action( 'qm/debug', $document->post_content );
    $current_doc_title = $document->post_title; // used later on
    $current_doc_content = $document->post_content; // used later on 
    $current_doc_id = $current_document_id;
    //do_action( 'qm/debug', get_post_meta($current_document_id) );
    /*--------------------------------------------*/
    ?>
    <!-- Main Layout  -->
    <div class="container-fluid idocs-document-view exclude-from-print">     
        <?php
            /*--------------------------------------------*/
            // Display a search bar?
            if ( $show_live_search_doc ) {
                
                //do_action( 'qm/debug', $show_live_search_kb );
                //echo do_shortcode( '[idocs_live_search kb_id='. $current_kb_id . ']' );
                $kb_id = $current_kb_id;
                require_once IDOCS_DIR_PATH . 'public/templates/idocs_live_search.php';

            }
            /*--------------------------------------------*/
            if ($show_breadcrumb_flag) {

                $kb_id = $current_kb_id;
                $doc_id = $current_doc_id;
                //echo do_shortcode( '[idocs_breadcrumbs kb_id=' . $current_kb_id . ' document_id='. $current_doc_id . ']' );
                require_once IDOCS_DIR_PATH . 'public/templates/idocs_breadcrumbs.php';

            }
        ?>
        <!------------------------------------->
        <div class="row idocs-content-and-sidebars">
            <!--------------------------------------->
            <!-- List of Categories --> 
            <?php       
            if ( $show_left_sidebar ) {

                ?>
                <aside class="idocs-left-sidebar-box col-<?php echo esc_attr($left_sidebar);?> d-none d-lg-block">
                <!-- when reaching the screen lg size, the left sibebar will be disabled -->
                        <?php
                        if (! $show_category_counter) {

                            $show_category_counter = 0;
                        }

                        require_once IDOCS_DIR_PATH . 'public/templates/idocs_sidebar_navigator.php';

                        ?>
                    </aside>
                <?php
            }
            ?>
            <!------------------------------->
            <!-- Document Content <div class="col overflow-auto" style="height: 600px;" > --> 
            <?php
            /*--------------------------------------------*/
            // calculating the size of the content_box using the left and right sidebars 
            $content_box = 12; // maximum size (columns in bootstrap)
            if ($show_left_sidebar) {

                $content_box = $content_box -  $left_sidebar;

            }
            if ($show_right_sidebar) {
                
                $content_box = $content_box -  $right_sidebar;

            }
            /*--------------------------------------------*/
            ?>
            <main class="idocs-document-content-box col-12 col-md-<?php echo esc_attr(12 - $right_sidebar);?> col-lg-<?php echo esc_attr(12 - $right_sidebar - $left_sidebar);?>">
            <!-- by default the size of the middle content box will be maximum (12 columns) for mobile view, when reaching the md breakpoint the right side bar 
                 will be added so the size will be reduced to (12 - $right_sidebar).
                 When reaching the md breakpoint the left size bar will be added so the size will be reduced to (12 - $right_sidebar - $left_sidebar) -->
                    <?php require_once IDOCS_DIR_PATH . 'public/templates/idocs_document_content.php'; ?>
            </main>
            <!------------------------------->
            <!-- ToC --> 
            <?php
            if ( $show_right_sidebar ) {
                ?>
                <aside class="idocs-right-sidebar-box col-<?php echo esc_attr($right_sidebar);?> d-none d-md-block">
                <!-- when reaching the md size, the right sibebar will be disabled -->
                        <?php
                        if ($show_toc_navigator) {
                            
                            // display the toc that was generated 
                            echo wp_kses_post($toc); 
                        }
                                
                        if ($show_document_tags) {
                            ?>
                            <hr>
                            <?php require IDOCS_DIR_PATH . 'public/templates/idocs_document_tags.php'; ?>
                            <div class="sperator">
                                <hr>
                            </div>
                            <?php
                        }
                        ?> 
                </aside>
                <?php
            }
            ?>
        </div> <!--row-->
        <!------------------------------------->
        
    <!--</div> container-->
    </div>
    <?php
    /*--------------------------------------------*/
    //if ($analytics_data_document_view_visit) {
        /* Analytics */
        if (isset($_SERVER['REMOTE_ADDR']) && array_key_exists('REMOTE_ADDR', $_SERVER)) 
            $current_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        else
            $current_ip = '';
        /*--------------------------------------------*/
        // IDOCS_Save_Events::save_document_visit_event($current_doc_id, $current_ip, $current_kb_id);
        IDOCS_Save_Events::save_content_visit_event( $current_doc_id, "Document", $current_ip, $current_kb_id );											
    //}
    /*--------------------------------------------*/
}    