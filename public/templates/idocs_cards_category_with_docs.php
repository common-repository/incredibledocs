<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
$interal_user = is_user_logged_in();
$public_visitor = ! $interal_user;
/*--------------------------------------------*/
$num_columns_options = array(

    'value1' => 1,
    'value2' => 2,
    'value3' => 3,
    'value4' => 4,
    'value5' => 5,
    
);
/*--------------------------------------------*/
$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
$key = $design_settings['categories_box_num_columns'];
$categories_box_num_columns = $num_columns_options[$key];
$spacing_between_cards = $design_settings['categories_box_spacing_between_cards'];
$hide_empty_categories = $design_settings['categories_box_hide_empty_categories'];
$category_cards_order_by = $design_settings['categories_box_cards_order_by'];
$category_card_documents_order_by = $design_settings['category_card_documents_order_by'];
$category_card_detailed_layout = $design_settings['category_card_detailed_layout'];
$category_content_item_icon_size = intval($design_settings['category_content_item_icon_size']);
$category_content_item_icon_color = $design_settings['category_content_item_icon_color'];
$animated_categories = $design_settings['categories_box_animated_categories'];
$video_visit_event = $design_settings['analytics_data_video_view_visit'];
$category_index = 0;
/*--------------------------------------------*/
$category_list = IDOCS_Taxanomies::get_categories_terms_no_caching($kb_id, $category_id, $hide_empty_categories, $category_cards_order_by );
/*--------------------------------------------*/
// filter out categories based on access type and user/visitor type
$category_list = IDOCS_Access_Check::filter_out_categories($kb_id, $interal_user, $category_list);
//do_action( 'qm/debug', $category_list );
// reindex the array as while using the "'hide_empty' => true" parameter (which filter out items)
$category_list = array_values( $category_list );
$total_categories = count($category_list);
$required_rows = ceil($total_categories/$categories_box_num_columns);
$max_in_a_row = $categories_box_num_columns;
//do_action( 'qm/debug', $required_rows );
/*--------------------------------------------*/
if ( $category_card_documents_order_by == 'custom_display_order' ) {

    $args = array(
        'post_type' => 'idocs_content',
        'posts_per_page' => -1,
        'orderby' => 'meta_value_num', // Order by a numeric value
        'meta_key' => 'idocs-content-display-order-meta', // Specify the meta key
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'idocs-category-taxo',
                'field' => 'term_id',
                'terms' => $category_id,
                'operator' => 'IN',
                'include_children' => false,
            )
        )
    );
    
}
else {

    // get all content items related to specific category excluding documents in sub-categories 
    $args = array(
        'post_type' => 'idocs_content',
        'posts_per_page' => -1,
        'orderby' => $category_card_documents_order_by,
        'order' => 'ASC',
        'tax_query' => array(
            /*--------------------------------------------*/
            array(
                'taxonomy' => 'idocs-category-taxo',
                'field' => 'term_id',
                'terms' => $category_id,
                'operator' => 'IN',
                'include_children' => false,
            )
            /*--------------------------------------------*/
        )
    );
}
$the_query = new WP_Query( $args );

if (isset($_SERVER['REMOTE_ADDR']) && array_key_exists('REMOTE_ADDR', $_SERVER)) 
        $current_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    else
        $current_ip = '';
/*--------------------------------------------*/ 
// used by the javascript to create events on each category card
/*--------------------------------------------*/
?>
<div style="display: none;" id="idocs-total-categories" data-total_categories="<?php echo esc_attr($total_categories);?>"
        data-animated_categories="<?php echo esc_attr($animated_categories);?>">
</div>
<!------------------------------------>
<div class="row">
        <!-- Column 1 -->
    <div class="col-md-4">
            <div class="idocs-content-list">
                <ul>
                    <?php
                        $video_counter = 0;                       
                        while ($the_query->have_posts()) {

                            $the_query->the_post();
                            $post_id = get_the_ID();
                            $content_type_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-type-meta');
                            //$content_type_id = get_post_meta($post_id, 'idocs-content-type-meta', true);
                            $content_type_term = get_term_by('id', $content_type_id , 'idocs-content-type-taxo' );
                            $icon_size = $category_content_item_icon_size;
                            $show_faqs = false;
                            IDOCS_Shortcodes::display_content_item ( $content_type_term->name, $post_id, $icon_size,$category_content_item_icon_color, $video_counter, $show_faqs  );
                            
                        }
                    ?>
                    <!-- used by the javascript to create events on each video link -->
                    <div style="display:none" id="idocs-total-video-links" data-total_video_links="<?php echo esc_attr($video_counter);?>" data-current_ip="<?php echo esc_attr($current_ip);?>" data-kb_id="<?php echo esc_attr($kb_id);?>" data-visit_event="<?php echo esc_attr($video_visit_event);?>">
                    </div>
                </ul>
            </div>
    </div>
    <?php
    /*--------------------------------------------*/
    // display the sub-categories box only if it is greater than zero.
    if ($total_categories) {
        ?>
        <div class="col-md-8">
            <div class="idocs-category-sub-categories">
                <?php
                for ($row_index = 0; $row_index < $required_rows; $row_index++) {
                ?>	
                <!-- gx-value: horizontal gutter - space between columns in a row-->
                <div class="row gx-<?php echo esc_attr($spacing_between_cards);?> mb-3 justify-content-center">
                    <?php
                    // if reaching the last row 
                    if ($row_index == ($required_rows-1)) {
    
                        $mod = $total_categories % $categories_box_num_columns;
                        if ($mod != 0) $max_in_a_row = $mod;
                        
                    }
                    /*--------------------------------------------*/
                    for ($col = 0; $col < $max_in_a_row; $col++) {
    
                        $wp_term = $category_list[$row_index*$categories_box_num_columns + $col];
                        $term_permalink = get_term_link ($wp_term->slug, 'idocs-category-taxo');
                        $category_icon_url = IDOCS_Taxanomies::get_term_meta_caching(  $wp_term->term_id, 'idocs-category-taxo-icon-url', false);
                        $total_docs = IDOCS_Shortcodes::total_content_type_in_category($wp_term->term_id, 'Document');
                        $total_links = IDOCS_Shortcodes::total_content_type_in_category($wp_term->term_id, 'Link');
                        $total_videos_internal = IDOCS_Shortcodes::total_content_type_in_category($wp_term->term_id, 'Internal-Video');
                        $total_videos_youtube = IDOCS_Shortcodes::total_content_type_in_category($wp_term->term_id, 'YouTube-Video');
                        $total_videos = $total_videos_internal + $total_videos_youtube;
                        $total_faqs = IDOCS_Shortcodes::total_content_type_in_category($wp_term->term_id, 'FAQ');

                        $col_size = floor(12 / $categories_box_num_columns);
                        /*--------------------------------------------*/
                        // display high-level card layout
                        if ( $category_card_detailed_layout ) {
                            
                            IDOCS_Shortcodes::detailed_card_layout($col_size, $category_index, $kb_id, $wp_term, $category_icon_url, $total_docs, $total_links, $total_videos, $total_faqs);

                        } 
                        // display detailed card layout 
                        else {

                            IDOCS_Shortcodes::high_level_card_layout($col_size, $category_index, $kb_id, $wp_term, $category_icon_url, $total_docs, $total_links, $total_videos, $total_faqs );

                        }
                        /*--------------------------------------------*/
                        $category_index++;
                    }
                ?>	
                </div> <!-- row -->
                <?php
                }
                ?>
            </div>		
        </div>
        <?php
    }
    ?>
</div>
<?php	
/*--------------------------------------------*/
wp_reset_postdata();
