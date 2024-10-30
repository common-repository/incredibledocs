<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
$breadcrumbs_home_url = $design_settings['breadcrumbs_box_home_url'];
$breadcrumbs_home_text = $design_settings['breadcrumbs_box_home_text'];
/*--------------------------------------------*/
$kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($kb_id);
$kb_link = get_term_link($kb_term->slug, 'idocs-kb-taxo');
$categories = IDOCS_Taxanomies::get_the_terms_caching( $doc_id, 'idocs-category-taxo' );
$breadcrumb = IDOCS_Shortcodes::build_breadcrumb ($categories[0]);
//do_action( 'qm/debug', $breadcrumb );
$document = get_post ($doc_id);
/*--------------------------------------------*/
?>
<nav class="row idocs-right-breadcrumbs-box" aria-label="Breadcrumb"> 
    <div class="col-12">
        <!-- Display the Home Button -->
        <!----------------------------->
        <a href="<?php echo esc_url($breadcrumbs_home_url);?>" rel="nofollow" title = "Breadcrumbs Home Button Link">
            <span class="idocs-right-breadcrumbs-box-item" >
                <?php echo esc_html($breadcrumbs_home_text) ?>	
            </span>
        </a>
        <span class="idocs-right-breadcrumbs-box-separator">
                    <?php echo esc_attr("&nbsp;&#187;&nbsp;"); ?>		
        </span>
        <!-- Display the KB Button -->
        <!----------------------------->
        <a href="<?php echo esc_url($kb_link);?>" rel="nofollow" title = "Breadcrumbs Knowledge Base Button Link">
            <span class="idocs-right-breadcrumbs-box-item" >
                <?php echo esc_html($kb_term->name); ?>	
            </span>
        </a>
        <?php
        /*--------------------------------------------*/         
        foreach ($breadcrumb as $br) {
            //do_action( 'qm/debug', $br);
            ?>
                <span class="idocs-right-breadcrumbs-box-separator">
                    <?php echo esc_attr("&nbsp;&#187;&nbsp;");?>		
                </span>
                <a href="<?php echo esc_url($br[1]); ?>" rel="nofollow" title = "Breadcrumbs Category or Document Link">
                    <span class="idocs-right-breadcrumbs-box-item" >
                        <?php echo esc_html(ucfirst($br[0])); ?>	
                    </span>
                </a>
            <?php
        }
        /*--------------------------------------------*/
        ?>
        <span class="idocs-right-breadcrumbs-box-separator">
            <?php echo esc_html("&nbsp;&#187;&nbsp;");?>		
        </span>
        <!----------------------------->
        <a>
            <span class="idocs-right-breadcrumbs-box-item" >
                <?php echo esc_html(ucfirst($document->post_title)) ?>	
            </span>
        </a>
    </div>
</nav>
<?php
/*--------------------------------------------*/
