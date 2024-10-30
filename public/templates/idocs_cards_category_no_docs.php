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
$category_card_detailed_layout = $design_settings['category_card_detailed_layout'];
$animated_categories = $design_settings['categories_box_animated_categories'];
/*--------------------------------------------*/
$category_list = IDOCS_Taxanomies::get_categories_terms_no_caching($kb_id, $category_id, $hide_empty_categories, $category_cards_order_by );
/*--------------------------------------------*/
// filter out categories based on access type and user/visitor type
$category_list = IDOCS_Access_Check::filter_out_categories($kb_id, $interal_user, $category_list);
// reindex the array as while using the "'hide_empty' => true" parameter (which filter out items)
$category_list = array_values( $category_list );
$total_categories = count($category_list);
$required_rows = ceil($total_categories/$categories_box_num_columns);
$max_in_a_row = $categories_box_num_columns;
$category_index = 0;
//do_action( 'qm/debug', $required_rows );
/*--------------------------------------------*/
// used by the javascript to create events on each category card
?>	
<div style="display: none;" id="idocs-total-categories" data-total_categories="<?php echo esc_attr($total_categories);?>" data-no_docs_flag="1"
    data-animated_categories="<?php echo esc_attr($animated_categories);?>">
</div>
<?php
/*--------------------------------------------*/
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
<?php
/*--------------------------------------------*/
//wp_reset_postdata();
