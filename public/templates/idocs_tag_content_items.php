<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
$category_content_item_icon_color = $design_settings['category_content_item_icon_color'];
$category_content_item_icon_size = intval($design_settings['category_content_item_icon_size']);
$show_documents_card = $design_settings['tag_content_box_show_documents'];
$show_links_card = $design_settings['tag_content_box_show_links'];
$show_videos_card = $design_settings['tag_content_box_show_videos'];
$show_faqs_card = $design_settings['tag_content_box_show_faqs'];
$tag_content_card_items_order_by = $design_settings['tag_content_card_items_order_by'];
$video_visit_event = $design_settings['analytics_data_video_view_visit'];
/*--------------------------------------------*/
// get the list of all terms under the 'idocs-content-type-taxo' taxanomy 
$content_types_terms = IDOCS_Taxanomies::get_content_types_terms_caching();
$content_types = array();
$content_types_old = array();
// reduce the result to the term id and name
foreach ($content_types_terms as $term) {

    $rec['name'] = $term->name ;
    $rec['term_id'] = $term->term_id;

    array_push($content_types_old, $rec);
}
/*--------------------------------------------*/
//do_action( 'qm/debug', $content_types_old );
foreach ($content_types_terms as $term) {

    $content_types[$term->name] = $term->term_id;
}
/*--------------------------------------------*/
// List of Documents
$args = array(
    'post_type' => 'idocs_content',
    'tax_query' => array(
        'relation' => 'AND',

        array(
            'taxonomy' => 'idocs-tag-taxo',
            'field' => 'term_id',
            'terms' => $tag_id,
        ),

        array(
            'taxonomy'         => 'idocs-content-type-taxo', 
            'field'            => 'term_id',
            'terms'            => $content_types['Document'], 
            'operator'         => 'IN',
        ),
    ),
);    
$docs_query = new WP_Query($args);
/*--------------------------------------------*/
// Lists of Links
$args = array(
    'post_type' => 'idocs_content',
    'tax_query' => array(
        'relation' => 'AND',

        array(
            'taxonomy' => 'idocs-tag-taxo',
            'field' => 'term_id',
            'terms' => $tag_id,
        ),

        array(
            'taxonomy'         => 'idocs-content-type-taxo', 
            'field'            => 'term_id',
            'terms'            => $content_types['Link'], 
            'operator'         => 'IN',
        ),
    ),
);    
$links_query = new WP_Query($args);
/*--------------------------------------------*/
// List of Videos
$args = array(
    'post_type' => 'idocs_content',
    'tax_query' => array(
        'relation' => 'AND',

        array(
            'taxonomy' => 'idocs-tag-taxo',
            'field' => 'term_id',
            'terms' => $tag_id,
        ),

        array(
            'taxonomy'         => 'idocs-content-type-taxo', 
            'field'            => 'term_id',
            'terms'            => array( $content_types['Internal-Video'], $content_types['YouTube-Video'] ), 
            'operator'         => 'IN',
        ),
    ),
);    
$videos_query = new WP_Query($args);
/*--------------------------------------------*/
// List of FAQs
$args = array(
    'post_type' => 'idocs_content',
    'tax_query' => array(
        'relation' => 'AND',

        array(
            'taxonomy' => 'idocs-tag-taxo',
            'field' => 'term_id',
            'terms' => $tag_id,
        ),

        array(
            'taxonomy'         => 'idocs-content-type-taxo', 
            'field'            => 'term_id',
            'terms'            => $content_types['FAQ'], 
            'operator'         => 'IN',
        ),
    ),
);    
$faqs_query = new WP_Query($args);
/*--------------------------------------------*/
?>
<section class="container-fluid row idocs-tag-content-box">

    <!------------------------------------>
    <div class="row">
        <div class="col-12 text-center">
            <h2 class="idocs-tag-content-title">Tag: <?php echo esc_html($tag_label);?> </h2>
        </div>
    </div>
    <!------------------------------------>
    <div class="row mb-4">
        <?php
        if ($show_documents_card && $docs_query->have_posts() ) {
            ?>
            <div class="col-4">
                <?php
                    ?>
                    <h3><?php echo esc_html__('Documents');?> </h3>
                    <div class="idocs-content-list">
                        <ul>
                            <?php
                            if ( $docs_query->have_posts() ) {

                                $video_counter = 0;
                                /*--------------------------------------------*/
                                while ($docs_query->have_posts()) {

                                    $docs_query->the_post();
                                    $newtab = false;
                                    $post_id = get_the_ID();
                                    $newtab = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-newtab-meta');
                                    $icon_size = $category_content_item_icon_size;
                                    $show_faqs = true;
                                    /*--------------------------------------------*/
                                    if ( empty ($newtab) ) {

                                        $newtab = 0;

                                    }
                                    /*--------------------------------------------*/	
                                    IDOCS_Shortcodes::display_content_item ( 'Document', $post_id, $icon_size, $category_content_item_icon_color, $video_counter, $show_faqs  );
                                }  
                            } 
                            wp_reset_postdata(); // Restore original post data
                            ?>
                        </ul>
                    </div>
            </div>
            <?php
        }
        ?>
        <!------------------------------------>
        <?php
        if ( $show_links_card && $links_query->have_posts()) {
            ?>
            <div class="col-4">
                <?php
                    /*--------------------------------------------*/
                    ?>
                    <h3><?php echo esc_html__('Links');?> </h3>
                    <div class="idocs-content-list">
                        <ul>
                            <?php
                            if ($links_query->have_posts()) {

                                $video_counter = 0;
                                /*--------------------------------------------*/
                                while ($links_query->have_posts()) {

                                    $links_query->the_post();
                                    $newtab = false;
                                    $post_id = get_the_ID();
                                    $newtab = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-newtab-meta');
                                    $icon_size = $category_content_item_icon_size;
                                    $show_faqs = true;
                                    if ( empty ($newtab) ) {
                                        $newtab = 0;
                                    };
                                    /*--------------------------------------------*/	
                                    IDOCS_Shortcodes::display_content_item ( 'Link', $post_id, $icon_size, $category_content_item_icon_color, $video_counter, $show_faqs );
                                }
                            } 
                            /*--------------------------------------------*/
                            wp_reset_postdata(); // Restore original post data
                            ?>
                        </ul>
                    </div>
            </div>
            <?php
        }
        ?>
        <!------------------------------------>
        <?php
        if ( $show_videos_card && $videos_query->have_posts()) {
            ?>
            <div class="col-4">
                <?php
                    
                    /*--------------------------------------------*/
                    ?>
                    <h3><?php echo esc_html__('Videos');?> </h3>
                    <div class="idocs-content-list">
                        <ul>
                            <?php
                            $video_counter = 0;
                            if ($videos_query->have_posts()) {

                                /*--------------------------------------------*/
                                while ($videos_query->have_posts()) {

                                    $videos_query->the_post();
                                    $newtab = false;
                                    $post_id = get_the_ID();
                                    $newtab = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-newtab-meta');
                                    $content_type_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-type-meta');
                                    $content_term = get_term_by('id', $content_type_id, 'idocs-content-type-taxo');
                                    $content_type_name = $content_term->name;
                                    
                                    $icon_size = $category_content_item_icon_size;
                                    $show_faqs = false;
                                    /*--------------------------------------------*/
                                    if ( empty ($newtab) ) {

                                        $newtab = 0;

                                    };
                                    /*--------------------------------------------*/    
                                    IDOCS_Shortcodes::display_content_item ( $content_type_name, $post_id, $icon_size, $category_content_item_icon_color, $video_counter, $show_faqs  );
                                }                                
                            } 
                            /*--------------------------------------------*/
                            wp_reset_postdata(); // Restore original post data
                            ?>
                            <!-- used by the javascript to create events on each video link -->
                            <div style="display:none" id="idocs-total-video-links" data-total_video_links="<?php echo esc_attr($video_counter);?>" data-current_ip="<?php echo esc_attr($current_ip);?>" data-kb_id="<?php echo esc_attr($kb_id);?>" data-visit_event="<?php echo esc_attr($video_visit_event);?>">
                            </div>
                        </ul>
                    </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    /*--------------------------------------------*/
    if ( $show_faqs_card && $faqs_query->have_posts()) {
        ?>
        <div class="row">
            <div class="col-8 mx-auto">
                <?php                    
                    /*--------------------------------------------*/
                    ?>
                    <h3><?php echo esc_html__('FAQs');?> </h3>
                    <div class="idocs-content-list">
                        <ul>
                            <?php
                            if ($faqs_query->have_posts()) {

                                $video_counter = 0;
                                while ($faqs_query->have_posts()) {

                                    $faqs_query->the_post();
                                    $newtab = false;
                                    $post_id = get_the_ID();
                                    $newtab = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-newtab-meta');
                                    $icon_size = $category_content_item_icon_size;
                                    $show_faqs = true;
                                    /*--------------------------------------------*/
                                    if ( empty ($newtab) ) {

                                        $newtab = 0;

                                    }	
                                    IDOCS_Shortcodes::display_content_item ( 'FAQ', $post_id, $icon_size, $category_content_item_icon_color, $video_counter, $show_faqs  );

                                }
                            } 
                            /*--------------------------------------------*/                            
                            wp_reset_postdata(); // Restore original post data
                            ?>
                        </ul>
                    </div>
            </div>
        </div>
        <?php
    }
    ?>
</section> <!-- "idocs-tag-content-view" --> 
<?php
/*--------------------------------------------*/
