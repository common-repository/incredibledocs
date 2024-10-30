<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
$randomNumber = mt_rand(1, 100); // Generate a random number between 1 and 100
$collect_feedback = $randomNumber <= $feedback_probability; // Return true if the random number is less than or equal to the probability
/*--------------------------------------------*/
?>              
<div id="idocs-content-for-printing">
    <?php
    /*--------------------------------------------*/    
    $toc = '';
    // generate a toc on the current document, update the document with the generated toc links 
    list($current_doc_content, $toc) = IDOCS_Shortcodes::generate_toc($current_doc_content, $current_kb_id);
    
    if ($show_document_title) {
        ?>
            <h1> <?php echo esc_html($current_doc_title); ?> </h1>
        <?php
    }
    /*--------------------------------------------*/
    ?>
    <header class="idocs-document-metadata">
    
        <?php
        /*--------------------------------------------*/	
        if ($show_last_updated_date){
            $updated_date = get_the_modified_time('F jS, Y');
            ?>
                <span>
                    <?php IDOCS_ICONS::echo_icon_svg_tag("calendar-days", 16, 16);?>
                </span>
                <span> <?php echo esc_html__('Updated on ', 'incredibledocs') . esc_html($updated_date); ?> </span>
                <span> <?php echo esc_html("|"); ?> <span>
            <?php
        }
        /*--------------------------------------------*/
        if ($show_estimated_time_to_read){

            $words_count = str_word_count(wp_strip_all_tags($current_doc_content));
            $words_per_min = 275;
            $estimated_mins = round($words_count/$words_per_min)+1;
            ?>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewbox="0 0 512 512"><path opacity="1" fill="#1E3050" d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/></svg>
                </span>
                <span> <?php echo esc_html($estimated_mins) . esc_html__(' Minutes to Read', 'incredibledocs'); ?> </span>
                <span> <?php echo esc_html("|"); ?> <span>

            <?php
        }	
        /*--------------------------------------------*/
        if ($show_author){
            
            ?>
                <span>
                    <?php IDOCS_ICONS::echo_icon_svg_tag("pen", 16, 16);?>
                </span>
                <span> <?php echo esc_html__('Author: ') . esc_html(ucfirst(get_the_author_meta('display_name'))); ?> </span>                
            <?php
        }
        /*--------------------------------------------*/
        if ($show_print_icon){
        
            ?>
            <span class="exclude-from-print" id="idocs-print-document-button"  data-doc_id="<?php echo esc_attr($current_document_id);?>">
                <?php echo esc_attr("|"); ?>
                <?php IDOCS_ICONS::echo_icon_svg_tag("print", 16, 16);?>
                Print
            </span>
            <br>
            <?php
        }
        /*--------------------------------------------*/
        if ($show_visits_counter){

            $visits = apply_filters('idocspro_visits_per_document', 0, $current_kb_id, $current_doc_id);

            ?>
                <span>
                    <?php IDOCS_ICONS::echo_icon_svg_tag("users", 16, 16);?>
                </span>
                <span> 
                    <?php 
                        echo esc_html__('Visits: ') . esc_html(number_format($visits)); 
                    ?> 
                </span>
            <?php
        }
        /*--------------------------------------------*/
        if ($show_rating_score){
            
            $stars_rating = apply_filters('idocspro_stars_rating_per_document', $current_kb_id, $current_doc_id);
            // if the filter is not available the return value will be first parameter ($current_kb_id)
            if ($stars_rating === $current_kb_id) {

                $stars_rating = [];
                $stars_rating["stars_score"] = 0;
                $stars_rating["number_ratings"] = 0;

            };
             /*--------------------------------------------*/
            //do_action( 'qm/debug', $stars_rating );
            ?>
            <span class="idocs-star-rating">
                <?php echo esc_attr("|"); ?>
                <span id="idocs-content-rating-score"> <?php echo esc_html(number_format($stars_rating["stars_score"],1));?>
                </span>

                    <?php
                        
                        for ($star=1 ; $star <= 5; $star ++) {

                            if ($stars_rating["stars_score"] >= $star) {

                                IDOCS_ICONS::echo_icon_svg_tag("star", 14, 14);

                            }
                            /*--------------------------------------------*/
                            
                            else if ($stars_rating["stars_score"] + 0.5 >= $star) {

                                IDOCS_ICONS::echo_icon_svg_tag("star-half-stroke", 14, 14);

                            }
                            /*--------------------------------------------*/
                            
                            else {
                                IDOCS_ICONS::echo_icon_svg_tag("empty-star", 14, 14);
                            }
                        }
                        
                    ?>   
                <span> <?php echo esc_html( "(" . number_format($stars_rating["number_ratings"])) . esc_html__(' ratings)');?></span>
            </span>
            <?php
        }
        /*--------------------------------------------*/
        if ($show_tags_headline){
        
            $tags = IDOCS_Taxanomies::get_the_terms_caching( $current_doc_id, 'idocs-tag-taxo' );
            echo esc_html("|");
            /*--------------------------------------------*/ 
            ?>
            <span><?php IDOCS_ICONS::echo_icon_svg_tag("tags", 16, 16);?></span>
            <span>
                <?php 
                    echo esc_html__(' Tags: ');

                    if (is_array($tags)) {
                        $tag_names = array(); // Create an empty array to store tag names
                        foreach($tags as $tag) {
                            $tag_link = get_term_link( $tag );
                            $tag_names[] = '<a href="' . esc_url($tag_link) . '" class="next-post" title = "Tag Link">' . esc_html($tag->name) . '</a>';
                        }
                        echo implode(', ', $tag_names); // Display the tag names separated by commas
                    
                    }
                    else {
                        ?>
                            <p><?php echo esc_html__( 'No Tags', 'incredibledocs' );?></p>
                        <?php
                        }		
                    
                ?>
            </span>
            <?php
        }
        /*--------------------------------------------*/
        ?>
        <hr>
    </header>
    <!------------------------------------->
    <article id="document-content">
        <?php
        // display the updated content 
        echo wp_kses_post($current_doc_content); 
        ?>   
    </article>
    <!------------------------------------->
    <div class="sperator">
        <hr>
    </div>
    <!------------------------------------->
    <div class="exclude-from-print">
        <?php
            require_once IDOCS_DIR_PATH . 'public/templates/idocs_document_previous_next.php';
        ?>
        <?php
        /*--------------------------------------------*/
        // display like/improve feedbacks 
        if ($show_like_feedback) {

            $content_type = "Document";
            do_action('idocspro_like_content_rating', $content_type, $current_doc_id, $current_kb_id);

        };
        /*--------------------------------------------*/
        if ($show_star_feedback) {

            $content_type = "Document";
            do_action('idocspro_five_stars_content_rating', $content_type, $current_doc_id, $current_kb_id);
            
        };
        /*--------------------------------------------*/
        if ($show_improve_feedback && $collect_feedback) {

            do_action('idocspro_document_feedback', $current_doc_id, $current_kb_id);

        };
        /*--------------------------------------------*/
        if ($show_document_tags_below_content) {
            
            require IDOCS_DIR_PATH . 'public/templates/idocs_document_tags.php';

        };
        /*--------------------------------------------*/
        if ($show_related_documents) {

            ?>
            <div class="sperator">
                <hr>
            </div>
            <?php
            require_once IDOCS_DIR_PATH . 'public/templates/idocs_related_documents.php';

        };
        /*--------------------------------------------*/
        ?> 
    </div>
</div>
<?php
