<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* DASHBOARD ADMIN PAGE */
/*---------------------------------------------------------------------------------------*/
//wp_delete_post(670);
// get the list of all terms (knowledge-bases) under the 'idocs-kb-taxo' taxanomy 
$kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();

$kbs_list = array ();
foreach ( $kb_terms as $term) {
    $kbs_list[] = $term->term_id;
}
/*--------------------------------------------*/
$total_visits = IDOCS_Dashboard::get_overall_content_visits(7 , $kbs_list);
$total_searches = IDOCS_Dashboard::get_overall_searches(7, $kbs_list);
$total_ratings = IDOCS_Dashboard::get_overall_ratings(7, $kbs_list);
//do_action( 'qm/debug', $total_ratings );
$search_success_rate = IDOCS_Dashboard::get_search_success_rate(7, $kbs_list);
$total_pending_comments = IDOCS_Dashboard::total_pending_comments(7, $kbs_list);
//global $wp_filter;
//do_action( 'qm/debug', $wp_filter['customize_register'] );
/*---------------------------------------------------------------------------------------*/
/*
// get the list of all configured 
$role_mapping_terms = get_terms( array(
			'taxonomy'   => 'idocs-role-mapping-taxo',
			'hide_empty' => false,
			'orderby' => 'name',
			'order' => 'ASC',
) );

do_action( 'qm/debug', $role_mapping_terms);
$role = get_role( 'subscriber' );
do_action( 'qm/debug', $role->capabilities);
*/
/*---------------------------------------------------------------------------------------*/
// number of categories per each kb
foreach ( $kb_terms as $term) {
	
    $cat_per_kb = wp_count_terms( array(
        'taxonomy'   => 'idocs-category-taxo',
        'hide_empty' => false,
        'meta_key'   => 'idocs-category-taxo-kb',
        'meta_value' => $term->term_id
    ) );
    // save the number of categories for that kb in a new attribute. 
    $term->categories = $cat_per_kb;
    /*--------------------------------------------*/    
    $tags_per_kb = wp_count_terms( array(
        'taxonomy'   => 'idocs-tag-taxo',
        'hide_empty' => false,
        'meta_key'   => 'idocs-tag-taxo-kb',
        'meta_value' => $term->term_id
    ) );
    // save the number of categories for that kb in a new attribute. 
    $term->tags = $tags_per_kb;

}
/*---------------------------------------------------------------------------------------*/
?>
<div class="container-fluid">
    <div class="row">
        <!-- Column 1 -->
        <div class="col-md-8">
            <!---------------------------------------------->
            <!-- Row 1: Performance Overview -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card idocs-dashboard-card">
                        <div class="card-body" >
                            <h4><?php echo esc_html__( 'Performance Overview', 'incredibledocs' );?> <span style="font-size: 1.2rem" >(<?php echo esc_html__( 'last 7 days', 'incredibledocs' );?>)</span></h4>
                            <hr>
                            <!---------------------------------------------->
                            <div class="row"> 
                                <div class="col">
                                    <div class="kpi-card">
                                        <div class="kpi-card-box-1">
                                            <?php IDOCS_ICONS::echo_icon_svg_tag("users", 30, 30);?>
                                        </div>
                                        <div class="kpi-card-box-2">
                                            <div class="kpi-card-title">
                                                <?php echo esc_html__( 'Total Content Visits', 'incredibledocs' );?>
                                            </div>
                                            <div class="kpi-value"><?php echo esc_html($total_visits); ?></div>  
                                        </div>
                                    </div>			
                                </div>
                                 <!---------------------------------------------->
                                <div class="col">
                                    <div class="kpi-card">
                                        <div class="kpi-card-box-1">
                                            <?php IDOCS_ICONS::echo_icon_svg_tag("magnifying-glass", 30, 30);?>
                                        </div>
                                        <div class="kpi-card-box-2">
                                            <div class="kpi-card-title"><?php echo esc_html__( 'Total Searches', 'incredibledocs' );?></div>
                                            <div class="kpi-value"><?php echo esc_html($total_searches[0]['total_searches']); ?></div>
                                        </div>   
                                    </div>			
                                </div>
                                 <!---------------------------------------------->
                                <div class="col">
                                    <div class="kpi-card">
                                        <div class="kpi-card-box-1">
                                            <?php IDOCS_ICONS::echo_icon_svg_tag("rocket", 30, 30);?>
                                        </div>
                                        <div class="kpi-card-box-2">
                                            <div class="kpi-card-title"><?php echo esc_html__( 'Search Success Rate', 'incredibledocs' );?></div>
                                            <div class="kpi-value"><?php echo esc_html($search_success_rate[0] . "%"); ?></div>
                                        </div>
                                    </div>			
                                </div>
                            </div> 
                            <!---------------------------------------------->
                            <div class="row">
                                <div class="col">
                                    <div class="kpi-card">
                                        <div class="kpi-card-box-1">
                                            <?php IDOCS_ICONS::echo_icon_svg_tag("comment", 30, 30);?>
                                        </div>                                        
                                        <div class="kpi-card-box-2">    
                                                <div class="kpi-card-title"><?php echo esc_html__( 'Pending Comments', 'incredibledocs' );?></div>
                                                <div class="kpi-value"><?php echo esc_html($total_pending_comments['total_comments']); ?></div>
                                        </div>
                                    </div>			
                                </div>
                                 <!---------------------------------------------->
                                <div class="col">
                                    <div class="kpi-card">
                                        <div class="kpi-card-box-1">
                                            <?php IDOCS_ICONS::echo_icon_svg_tag("star", 30, 30);?>
                                        </div>
                                        <div class="kpi-card-box-2">
                                            <div class="kpi-card-title">
                                                <?php echo esc_html__( 'Total Ratings', 'incredibledocs' );?>
                                                <span class="kpi-card-sub-title"><?php echo esc_html__( '(Content)', 'incredibledocs' );?></span>
                                            </div>
                                            <div class="kpi-value"><?php echo esc_html($total_ratings[0]['total_ratings']); ?></div>
                                        </div>
                                    </div>			
                                </div> 
                                <!---------------------------------------------->
                                <div class="col">
                                    <div class="kpi-card">
                                        <div class="kpi-card-box-1">
                                            <?php IDOCS_ICONS::echo_icon_svg_tag("thumbs-up", 30, 30);?>
                                        </div>
                                        <div class="kpi-card-box-2">
                                            <div class="kpi-card-title">
                                                <?php echo esc_html__( 'Content Rating Score', 'incredibledocs' );?>
                                            </div>
                                            <div class="kpi-value">
                                                <span id="idocs-content-rating-score"> <?php echo esc_html(number_format($total_ratings[0]['stars_score'],1));?>
                                                    <span class="idocs-star-rating">
                                                        <?php
                                                            for ($star=1 ; $star <= 5; $star ++) {

                                                                if ($total_ratings[0]['stars_score'] >= $star) {

                                                                    IDOCS_ICONS::echo_icon_svg_tag("star", 14, 14);

                                                                }
                                                                /*--------------------------------------------*/
                                                                else if ($total_ratings[0]['stars_score'] + 0.5 >= $star) {

                                                                    IDOCS_ICONS::echo_icon_svg_tag("star-half-stroke", 14, 14);

                                                                }
                                                                /*--------------------------------------------*/
                                                                else {
                                                                    IDOCS_ICONS::echo_icon_svg_tag("empty-star", 14, 14);
                                                                }
                                                            }
                                                        ?>  
                                                    </span>
                                            </div>
                                        </div>
                                    </div>			
                                </div> 
                            </div>
                            <!---------------------------------------------->    
                        </div>
                    </div>
                </div>
            </div>
            <!---------------------------------------------->
            <!-- Row 2: Content Summary -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card idocs-dashboard-card">
                        <div class="card-body" >
                            <h4><?php echo esc_html__( 'Content Summary', 'incredibledocs' );?></h4>
                            <hr>
                            <table class="table">
                                <thead>
                                    <tr class="table-primary">
                                        <th scope="col" class="table-secondary"><?php echo esc_html__( 'Knowledge Base', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-primary text-center"><?php echo esc_html__( 'Documents', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-primary text-center"><?php echo esc_html__( 'Links', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-primary text-center"><?php echo esc_html__( 'FAQs', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-primary text-center"><?php echo esc_html__( 'Videos', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-warning text-center"><?php echo esc_html__( 'Categories', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-warning text-center"><?php echo esc_html__( 'Tags', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-warning text-center"><?php echo esc_html__( 'FAQ Groups', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-success text-center"><?php echo esc_html__( 'Last Content Update', 'incredibledocs' );?></th>
                                    </tr>
                                </thead>
                                <!---------------------------------------------->
                                <tbody>
                                <?php 
                                foreach ( $kb_terms as $term) {
                                    ?>
                                    <tr scope="row">
                                        <!-- KB -->
                                        <!---------------------------------------------->
                                        <td style="font-weight: bold;"> 
                                            <a href="<?php echo esc_url(get_term_link($term->term_id, 'idocs-kb-taxo'));?>"><?php echo esc_html($term->name); ?></a>
                                        </td>
                                        <!-- Total Documents -->
                                        <!---------------------------------------------->
                                        <td class="text-center"> 
                                            <?php 
                                                //echo esc_html($term->count);
                                                echo esc_html(IDOCS_Taxanomies::total_content_type_in_kb("Document", $term->term_id));
                                            ?> 
                                        </td>
                                        <!-- Total Links -->
                                        <!---------------------------------------------->
                                        <td class="text-center"> 
                                            <?php 
                                                //echo esc_html($term->count);
                                                echo esc_html(IDOCS_Taxanomies::total_content_type_in_kb("Link", $term->term_id));
                                            ?> 
                                        </td>
                                        <!-- Total FAQs -->
                                        <!---------------------------------------------->
                                        <td class="text-center"> 
                                            <?php 
                                                //echo esc_html($term->count);
                                                echo esc_html(IDOCS_Taxanomies::total_content_type_in_kb("FAQ", $term->term_id));
                                            ?> 
                                        </td>
                                        <!-- Total Videos -->
                                        <!---------------------------------------------->
                                        <td class="text-center"> 
                                            <?php 
                                                //echo esc_html($term->count);
                                                $total_internal = IDOCS_Taxanomies::total_content_type_in_kb("Internal-Video", $term->term_id);
                                                $total_yt = IDOCS_Taxanomies::total_content_type_in_kb("YouTube-Video", $term->term_id);
                                                echo esc_html($total_internal +  $total_yt);
                                            ?> 
                                        </td>
                                        <!-- Total Categories -->
                                        <!---------------------------------------------->
                                        <td class="text-center"> <?php echo esc_html($term->categories); ?> </td>
                                        <!-- Total Tags -->
                                        <!---------------------------------------------->
                                        <td class="text-center"> <?php echo esc_html($term->tags); ?> </td>
                                        <!-- Total FAQs -->
                                        <!---------------------------------------------->
                                        <td class="text-center"> 
                                            <?php 
                                                echo esc_html(IDOCS_Taxanomies::total_faq_groups_in_kb($term->term_id)); 
                                            ?> 
                                        </td>
                                        <!-- Last Content Update -->
                                        <!---------------------------------------------->
                                        <td class="text-center"> 
                                            <?php 
                                                $days_ago = IDOCS_Dashboard::amount_days_last_content_update($term->term_id);
                                                //do_action( 'qm/debug', $days_ago );

                                                if ( $days_ago === null) {
                                                    echo esc_html__("No Content.", 'incredibledocs');
                                                }
                                                else  {

                                                    // The _n function is a translation function that picks the first string if the $days_ago (third parameter to _n) is one, or the second one if it’s more than one. 
                                                    // We still have to use the sprintf to replace the placeholder with the actual number, but now the pluralization can be translated separately, and as part of the whole phrase. Note that the last argument to _n is still the plugin text domain to be used.
                                                    $message = sprintf( _n('%d day ago', '%d days ago', $days_ago, 'incredibledocs'), $days_ago );
                                                    echo esc_html($message);
                                                    
                                                }
                                            ?> 
                                        </td>
                                    </tr>
                                    <!---------------------------------------------->
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
            <!---------------------------------------------->
            <!-- Row 3: Configuration Summary -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card idocs-dashboard-card">
                        <div class="card-body" >
                            <h4><?php echo esc_html__( 'Configuration Summary', 'incredibledocs' );?></h4>
                            <hr>
                            <table class="table">
                                <thead>
                                    <tr class="table-primary">
                                        <th scope="col" class="table-secondary"><?php echo esc_html__( 'Knowledge-Base', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-primary text-center"><?php echo esc_html__( 'Access Type (Pro)', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-info text-center"><?php echo esc_html__( 'URL Type', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-warning text-center"><?php echo esc_html__( 'Global Color Scheme', 'incredibledocs' );?></th>
                                        <th scope="col" class="table-success text-center"><?php echo esc_html__( 'Scheduled Report (Pro)', 'incredibledocs' );?></th>
                                    </tr>
                                </thead>
                                <!---------------------------------------------->
                                <tbody>
                                <?php 
                                foreach ( $kb_terms as $term) {
                                    ?>
                                    <tr scope="row">
                                        <!-- KB -->
                                        <!---------------------------------------------->
                                        <td style="font-weight: bold;"> 
                                            <a href="<?php echo esc_url(get_term_link($term->term_id, 'idocs-kb-taxo'));?>"><?php echo esc_html($term->name); ?></a>
                                        </td>
                                        <!-- Access Type -->
                                        <!---------------------------------------------->
                                        <td class="text-center"> 
                                            <?php    
                                            //$access_type = get_term_meta( $term->term_id, 'idocs-kb-taxo-access-type', true );
                                            $access_type = IDOCS_Taxanomies::get_term_meta_caching( $term->term_id, 'idocs-kb-taxo-access-type', false);

                                            //echo esc_html($access_type); 
                                            echo esc_html($access_type);
                                            ?>
                                        </td>
                                        <!-- URL Type -->
                                        <!---------------------------------------------->   
                                        <?php
                                            $red = 0;
                                            //$custom_kb_flag =  get_term_meta( $term->term_id, 'idocs-kb-taxo-custom-kb-page-flag', true );
                                            $custom_kb_flag = IDOCS_Taxanomies::get_term_meta_caching( $term->term_id, 'idocs-kb-taxo-custom-kb-page-flag', false);

                                            if ($custom_kb_flag == 1) {

                                                //$page_id =  get_term_meta( $term->term_id, 'idocs-kb-taxo-custom-kb-page-id', true );
                                                $page_id = IDOCS_Taxanomies::get_term_meta_caching( $term->term_id, 'idocs-kb-taxo-custom-kb-page-id', false);

                                            }
                                            if  ($custom_kb_flag == 1 and $page_id != 0 ) {

                                                $post = get_post($page_id); 
                                                if ($post) {

                                                    $status = 'Custom KB Page';
                                                }
                                                else {

                                                    $status = 'Missing KB Page!';
                                                    $red = 1;
                                                }
                                            }
                                            else {

                                                $status = 'Automatic';
                                               
                                            }
                                            ?>
                                        <td class="text-center" <?php if ( $red ) echo esc_attr("style=color:red");?>>
                                            <?php
                                                echo esc_html($status);
                                            ?>
                                        </td> 
                                        <!-- KB Theme -->
                                        <!---------------------------------------------->
                                        <td class="text-center"> <?php 
                                                echo esc_html(IDOCS_Themes::get_kb_theme_name($term->term_id)); 
                                            ?> 
                                        </td>
                                        <!-- Scheduled Report (Email) -->
                                        <!---------------------------------------------->
                                        <td class="text-center">
                                        <?php 
                                            
                                            //$summary_report_flag = get_term_meta( $term->term_id, 'idocs-kb-taxo-summary-report-flag', true );
                                            //$stored_frequency = get_term_meta( $term->term_id, 'idocs-kb-taxo-email-frequency', true );
                                            //$stored_email = get_term_meta( $term->term_id, 'idocs-kb-taxo-email-address', true );
                                            $summary_report_flag = IDOCS_Taxanomies::get_term_meta_caching( $term->term_id, 'idocs-kb-taxo-summary-report-flag', false);
                                            $stored_frequency = IDOCS_Taxanomies::get_term_meta_caching( $term->term_id, 'idocs-kb-taxo-email-frequency', false);
                                            $stored_email = IDOCS_Taxanomies::get_term_meta_caching( $term->term_id, 'idocs-kb-taxo-email-address', false);
                                            /*--------------------------------------------*/
                                            if ( empty($summary_report_flag) ) 
                                                $summary_report_flag = 0;

                                            if ( $summary_report_flag == 1 and ! empty($stored_frequency) and ! empty($stored_email) )
                                                $scheduled = true;
                                            else
                                                $scheduled = false;

                                            if ($scheduled) {
                                                echo esc_html__("Yes", 'incredibledocs');
                                            }
                                            else {
                                                echo esc_html__("No", 'incredibledocs');
                                            }
                                            ?>    
                                        </td>
                                    </tr>
                                    <?php
                                }
                                
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
            <!---------------------------------------------->
        </div>
        <!-- Column 2: "How to" Tutorials -->
        <div class="col-md-4">
            <!-- Row 3 -->
            <div class="row" style="height: 100%;">
                <div class="col-md-12" style="height: 100%;">
                    <div class="card idocs-dashboard-card">
                        <div class="card-body" >
                            <h4><?php echo esc_html__( 'Getting Started', 'incredibledocs' );?></h4>
                            <hr>
                            <ul style="list-style-type: square;">
                                <li><a href="https://incrediblewp.io/idocs-categories/incredibledocs/" target="_blank"><?php echo esc_html('Check Our Documentation');?></a></li>
                                <li><a href="mailto:support@incrediblewp.io?subject=Product%20Feedback" target="_blank"><?php echo esc_html('**90% Discount** - share your product feedback with us and get an amazing coupon code for the Pro version.');?></a></li>
                            </ul>
                        </div>
                    </div>
                    <!---------------------------------------------->
                    <?php

                    /*
                    <ul>
                                <li><a href="https://www.youtube.com/watch?v=54mLiVKwpR0&list=PL_194WUBJbCHb2TdYz91AGkQnXWKSYrro" target="_blank"><?php echo esc_html('L01 - Introduction');?></a></li>
                                <li><a href="https://youtu.be/69iOjn2A4RE" target="_blank"><?php echo esc_html('L02 - Market Use Cases');?></a></li>
                                <li><a href="https://youtu.be/eFr1tP_EaBU" target="_blank"><?php echo esc_html('L03 - A Knowledge-Base Building Blocks');?></a></li>
                                <li><a href="https://youtu.be/G5aBxR4mr0c" target="_blank"><?php echo esc_html('L04 - Plugin Overview - Frontend - KB View');?></a></li>
                                <li><a href="https://youtu.be/2MfrEXv9s7c" target="_blank"><?php echo esc_html('L05 - Plugin Overview - Frontend - Document View');?></a></li>
                                <li><a href="https://youtu.be/PgDuHXUfslY" target="_blank"><?php echo esc_html('L06 - Plugin Overview - Admin Backend');?></a></li>
                                <li><a href="https://youtu.be/ZaVk-el8bKg" target="_blank"><?php echo esc_html('L07 - Our Dashboard');?></a></li>
                                <li><a href="https://youtu.be/H7jHw6YBNQQ" target="_blank"><?php echo esc_html('L08 - Knowledge-Bases');?></a></li>
                                <li><a href="https://youtu.be/r6KS_tyJVy4" target="_blank"><?php echo esc_html('L09 - Categories');?></a></li>
                                <li><a href="https://youtu.be/vyfJC_pxuTM" target="_blank"><?php echo esc_html('L10 - FAQ Groups');?></a></li>
                                <li><a href="https://youtu.be/KCrLoWTqCxA" target="_blank"><?php echo esc_html('L11 - Tags');?></a></li>
                                <li><a href="https://youtu.be/SNM7xUBAs8A" target="_blank"><?php echo esc_html('L12 - Managing Content Items');?></a></li>
                                <li><a href="https://youtu.be/N_FuxCWEKbE" target="_blank"><?php echo esc_html('L13 - Creating a new Content Item');?></a></li>
                                <li><a href="https://youtu.be/rDdqPIWUyz4" target="_blank"><?php echo esc_html('L14 - URLs Settings');?></a></li>
                                <li><a href="https://youtu.be/Jnzdoa8bT9s" target="_blank"><?php echo esc_html('L15 - Design Settings - KB View');?></a></li>
                                <li><a href="https://youtu.be/LGBHN__XnT4" target="_blank"><?php echo esc_html('L16 - Design Settings - Document View');?></a></li>
                                <li><a href="https://youtu.be/QsS9r2frSaE" target="_blank"><?php echo esc_html('L17 - Pro - Themes Management');?></a></li>
                                <li><a href="https://youtu.be/J8L6RyetXyw" target="_blank"><?php echo esc_html('L18 - Pro - Design Tools');?></a></li>
                                <li><a href="https://youtu.be/hDbyUjp_3jU" target="_blank"><?php echo esc_html('L19 - Pro - Analytics View');?></a></li>
                                <li><a href="https://youtu.be/6UdD2xHchGc" target="_blank"><?php echo esc_html('L20 - Pro - Performance Reports via Email');?></a></li>
                                <li><a href="https://youtu.be/jf-YM8kVcDw" target="_blank"><?php echo esc_html('L21 - Pro - Access Control');?></a></li>
                            </ul>

                    */
                    
                    ?>
                    <!---------------------------------------------->
                    <div class="card idocs-dashboard-card">
                        <!--<div class="card-body"> -->
                            <h4><?php echo esc_html__( 'Pro Features', 'incredibledocs' );?></h4>
                            <hr>
                            <ul style="list-style-type: square;">
                                <?php
                                /*
                                <li>
                                    <span style="font-weight:bold"><?php echo esc_html('Celebration coupon:');?></span>
                                    <span style="color:red; font-weight:bold"><?php echo esc_html('AMAZING-50');?></span>
                                </li>
                                */
                                ?>

                                <li><a href="https://youtu.be/WuXMUiTZyOY" target="_blank"><?php echo esc_html('Enable super-search (content filters, tags, dynamic video links)');?></a></li>
                                <li><a href="https://youtu.be/gbUnUZ7K9VE" target="_blank"><?php echo esc_html('Rating modules (like/dislike, 5-stars, feedback)');?></a></li>
                                <!-- roadmap -->
                                <li><a href="https://youtu.be/5PKJoa-e58A" target="_blank"><?php echo esc_html('Additional content types (internal/YouTube videos)');?></a></li>
                                <!--<li><?php //echo esc_html('Advanced shortcodes (popular content/searches/tags...)');?></li>-->
                                <li><a href="https://youtu.be/-9hmsMCH1W0" target="_blank"><?php echo esc_html('Color schema (full package, custom theme builder)');?></a></li>
                                <li><a href="https://youtu.be/nXYA13dGobk" target="_blank"><?php echo esc_html('Design tools (import, export, reset)');?></a></li>
                                <li><a href="https://youtu.be/frax6ai3tA4" target="_blank"><?php echo esc_html('Analytics view (visits, searches, ratings, feedback)');?></a></li>
                                <li><a href="https://youtu.be/ud6jN7juse4" target="_blank"><?php echo esc_html('Content access control (public/internal/hybrid, groups, users)');?></a></li>
                                <li><a href="https://youtu.be/9CQZy9_B8ME" target="_blank"><?php echo esc_html('Admin access control (role mapping)');?></a></li>
                                <li><a href="https://youtu.be/aia9cHn9WF0" target="_blank"><?php echo esc_html('Geo-location enrichments (country - visits, ratings, searches)');?></a></li>
                                <!-- roadmap -->
                                <!--<li><? //php echo esc_html('Extended Icons pack (for knowledgebases and categories)'); //?></li> -->
                                <!--<li><?php //echo esc_html('Performance email reports');?></li>-->
                                <!--<li><?php //echo esc_html('Analytics data collection settings');?></li>-->
                            </ul>
                        <!--/div>-->
                    </div>
                    <!---------------------------------------------->
                </div>
            </div>
        </div>
    </div>
</div>
<!------------------------------------------------------------------------------------------->
<?php







