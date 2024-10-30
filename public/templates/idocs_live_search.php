<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
$search_title = $design_settings['live_search_title_text'];
$search_sub_title = $design_settings['live_search_sub_title_text'];
$show_search_sub_title = $design_settings['live_search_sub_title_show'];
$search_placeholder = $design_settings['live_search_input_search_placeholder'];
$show_kb_icon = $design_settings['live_search_kb_icon_show'];
$kb_icon_opacity = $design_settings['live_search_kb_icon_opacity'];
$kb_icon_width = $design_settings['live_search_kb_icon_width'];
$kb_icon_color = $design_settings['live_search_kb_icon_color'];
$analytics_data_video_view_visit = $design_settings['analytics_data_video_view_visit'];
/*--------------------------------------------*/
// get all categories related to specific knowledge-base
$category_list = get_terms( array(

    'taxonomy'   => 'idocs-category-taxo',
    'hide_empty' => false,
    'meta_key'   => 'idocs-category-taxo-kb',
    'meta_value' => $kb_id,
    'parent' => 0, // get only the top-level 
    'orderby' => 'name',
    'order' => 'ASC', 

) );
/*--------------------------------------------*/
$kb_icon_url = IDOCS_Taxanomies::get_term_meta_caching( $kb_id, 'idocs-kb-taxo-icon-url', false);
/*--------------------------------------------*/
if (isset($_SERVER['REMOTE_ADDR']) && array_key_exists('REMOTE_ADDR', $_SERVER)) 
        $current_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    else
        $current_ip = '';

/*--------------------------------------------*/
/*
    <div class="col-2  d-flex justify-content-center align-items-center">

*/
?>
<!-- Overall Search Box: Title Container + Subtitle Container + Input Container -->
<section class="row idocs-live-search-box">
    <!---------------------------------------------->  
    <div class="col-2 d-flex justify-content-center align-items-center">
        <div class="d-none d-md-block">
            <?php
            if ( $show_kb_icon ) {

                if ( empty ( $kb_icon_url ) ) {
                    // use the icon picker 
                    //$kb_icon_key =  get_term_meta( $kb_id, 'idocs-kb-taxo-icon-picker', true );
                    $kb_icon_key = IDOCS_Taxanomies::get_term_meta_caching( $kb_id, 'idocs-kb-taxo-icon-picker', false);
                    $kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($kb_id);
                    $kb_link = get_term_link($kb_term->slug, 'idocs-kb-taxo');
                    /*--------------------------------------------*/
                    if ( $kb_icon_key != null ) {
                        
                        ?>
                            <a href="<?php echo esc_url($kb_link);?>" title = "Knowledge Base View Link">
                                <span class="idocs-live-search-kb-icon">
                                    <?php /*IDOCS_ICONS::echo_icon_svg_tag($kb_icon_key, $kb_icon_width, $kb_icon_width, $kb_icon_opacity, $kb_icon_color);*/?>
                                    <?php IDOCS_ICONS::echo_icon_svg_tag($kb_icon_key, 0, 0); ?>
                                </span>
                            </a>
                        <?php
                        
                    }
                    else {
                        ?>
                        <p><?php echo esc_html__( 'Missing KB icon', 'incredibledocs' );?></p>
                        <?php
                    }
                } 
                /*--------------------------------------------*/
                else { // use the custom icon
                    
                    ?>
                        <span class="idocs-live-search-kb-icon">
                                <img src="<?php echo esc_url($kb_icon_url); ?>" alt="knowledge-base icon">
                        </span>
                    <?php
                }
            }	
            ?>
        </div>
    </div>
    <!---------------------------------------------->
    <div class="col-10 idocs-live-search-right-sub-box">
        <!-- Search Title Container -->
        <div class ="idocs-live-search-title-container">
            <h2><?php echo esc_html($search_title); ?></h2>
        </div>
        <!---------------------------------------------->
        <!-- Search Sub-Title Container -->
        <?php if ($show_search_sub_title) {
            ?>
            <div class ="idocs-live-search-sub-title-container">
                <h3><?php echo esc_html($search_sub_title); ?></h3>
            </div>
            <?php
        } ?>
        <!---------------------------------------------->
        <div class="idocs-live-search-input-container d-flex justify-content-center">
            <!---------------------------------------------->
            <!-- Input and Output DIVs in column structure -->
            <div class="idocs-live-search-input-and-output d-flex flex-column">
                <!---------------------------------------------->
                <!-- Input Box: Input Bar + Category Selection -->
                <div class="idocs-live-search-input-box d-flex flex-row"> 
                        <!-- Input Bar: Search Term + Close Icon -->
                        <div class="idocs-live-search-input-bar d-flex flex-row">
                            <input class="idocs-live-search-input-field" type="text" class="form-control" id="idocs-search-term" placeholder="<?php echo esc_attr($search_placeholder); ?>"  autocomplete="off" data-kb_id="<?php echo esc_attr($kb_id);?>" data-current_ip="<?php echo esc_attr($current_ip);?>" ></input>
                            <input type="hidden" id="idocs-internal-user" value="<?php echo esc_attr( is_user_logged_in() ); ?>" ></input>
                            <input type="hidden" id="idocs-user-id" value="<?php echo esc_attr( get_current_user_id() ); ?>" ></input>
                            <input type="hidden" id="idocs-current-ip" value="<?php echo esc_attr( $current_ip ); ?>" ></input>
                            <input type="hidden" id="idocs-analytics-video-visit-event" value="<?php echo esc_attr( $analytics_data_video_view_visit ); ?>" ></input>
                            <!---------------------------------------------->
                            <div class="idocs-live-search-close-search-button d-none">
                                
                                    <?php IDOCS_ICONS::echo_icon_svg_tag('circle-xmark', 32, 32, 1);?>

                            </div>
                            <!---------------------------------------------->
                        </div>
                        <!---------------------------------------------->
                        <!-- Category Selection -->
                        <div class="idocs-live-search-input-category">
                            <select id="idocs-selected-category" name="idocs-selected-category" title="Select Category Filter" style="max-width: 100%">
                                <option value=0><?php echo esc_html__( 'All Main Categories', 'incredibledocs' );?></option>
                                <?php
                                foreach ($category_list as $category) {
                                    ?>
                                    <option value="<?php echo esc_attr($category->term_id);?>">
                                        <?php echo esc_attr($category->name);?>
                                    </option>	
                                    <?php
                                    /* echo '<option value="'. esc_attr($category->term_id) .'"'.'>'. esc_attr($category->name) .'</option>'; */
                                }
                                ?>
                            </select>
                        </div>
                </div> <!-- Input Box -->
                <!---------------------------------------------->
                <!-- Placeholder to display the search result -->
                <div class="idocs-live-search-result d-none"> 
                </div>
            </div> <!-- Input and Output -->
        </div> <!-- Input Container -->
        <!---------------------------------------------->
    </div>
    <?php
    ?>
</section> <!-- Overall Search Box -->
<?php
