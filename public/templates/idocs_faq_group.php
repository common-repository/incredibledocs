<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
$faqs_box_title_text = $design_settings["faqs_box_title_text"];
$lock_root_faqs = $design_settings["faqs_box_lock_root_faqs"];
/*--------------------------------------------*/
if ($lock_root_faqs) {

    $category_id = 0;

};
/*--------------------------------------------*/
$faq_groups = array($faq_group_term);

if ($faq_groups == null) {
    return;
}
/*--------------------------------------------*/
?>
<section class="row idocs-faq-box">
    <div class="col">        
        <span class="idocs-faq-box-title"><?php echo esc_html($faqs_box_title_text); ?></span>
          <!------------------------------------------>
        <?php
            $accordion_index = 0;
            /*--------------------------------------------*/ 
            foreach ($faq_groups as $faq_group) {
                ?>
                <br>
                <span class="idocs-faq-group-title"><?php echo esc_html($faq_group->name);?></span>
                <!------------------------------------------>
                <!-- BOOTSTRAP ACCORDION -->
                <!------------------------------------------>
                <div class="accordion" id="idocs-accordion-<?php echo esc_attr($accordion_index);?>">	
                    <?php

                    $the_query = IDOCS_Shortcodes::faqs_per_group_caching ( $faq_group->term_id );
                    //do_action( 'qm/debug', $faqs );
                    $faq_index = 0;
                    /*--------------------------------------------*/
                    while ( $the_query->have_posts() ) {

                        $item_prefix = (string) $accordion_index . (string) $faq_index;
                        $the_query->the_post();

                        ?>
                        <!------------------------------------------>
                        <!-- ACCORDION ITEM -->
                        <div class="accordion-item">
                            <!-- ACCORDION HEADER -->
                            <span class="accordion-header" id="heading-<?php echo esc_attr($item_prefix);?>">
                                <!-- data-bs-target is the id of the accordion collapse div.  -->
                                <button class="accordion-button idocs-faq-item-title" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse-<?php echo esc_attr($item_prefix);?>" 
                                        aria-expanded="false" aria-controls="collapse-<?php echo esc_attr($item_prefix);?>">
                                    
                                    <?php echo esc_html(ucfirst(get_the_title())); ?>
                                </button>
                            </span>
                            <!-- accordion-collapse -->
                            <div class="accordion-collapse collapse" id="collapse-<?php echo esc_attr($item_prefix);?>" 
                                 aria-labelledby="heading-<?php echo esc_attr($item_prefix);?>" 
                                 data-bs-parent="#idocs-accordion-<?php echo esc_attr($accordion_index);?>">
                                <!--  -->
                                <div class="accordion-body idocs-faq-item-content">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $faq_index++;
                    }
                    /*--------------------------------------------*/
                    wp_reset_postdata();
                    /*--------------------------------------------*/
                    ?>
                </div>
                <?php
                $accordion_index++;
            }
            ?>
    </div> 
</section> 
<?php
/*--------------------------------------------*/
