<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
$design_settings = IDOCS_Database::get_plugin_design_settings($kb_id, null);
$breadcrumbs_home_url = $design_settings['breadcrumbs_box_home_url'];
$breadcrumbs_home_text = $design_settings['breadcrumbs_box_home_text'];
/*--------------------------------------------*/
$kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($kb_id);
$kb_link = get_term_link($kb_term->slug, 'idocs-kb-taxo');
$category_term = get_term_by('id', $category_id, 'idocs-category-taxo');
//do_action( 'qm/debug', $category_term );
//do_action( 'qm/debug', $category_term2 );
$breadcrumb = IDOCS_Shortcodes::build_breadcrumb ($category_term);
/*--------------------------------------------*/
?>
<nav class="row idocs-right-breadcrumbs-box" aria-label="Breadcrumb"> 
    <div class="col-12">
        <!-- Display the Home Button -->
        <a href="<?php echo esc_url($breadcrumbs_home_url);?>" rel="nofollow" title = "Breadcrumbs Home Button Link">
            <span class="idocs-right-breadcrumbs-box-item"  >
                <?php echo esc_html($breadcrumbs_home_text) ?>	
            </span>
        </a>
        <span class="idocs-right-breadcrumbs-box-separator">
                    <?php echo esc_attr("&nbsp;&#187;&nbsp;"); ?>		
        </span>
        <!-- Display the KB Button -->
        <a href="<?php echo esc_url($kb_link);?>" rel="nofollow" title = "Breadcrumbs Knowledge Base Button Link">
            <span class="idocs-right-breadcrumbs-box-item"  >
                <?php echo esc_html($kb_term->name); ?>	
            </span>
        </a>
        <?php
        /*--------------------------------------------*/
        //do_action( 'qm/debug', $post );
        foreach ($breadcrumb as $br) {
            //do_action( 'qm/debug', $br);
            ?>
                <span class="idocs-right-breadcrumbs-box-separator">
                    <?php echo esc_attr("&nbsp;&#187;&nbsp;");?>		
                </span>
                <!------------------------------------------>
                <a href="<?php echo esc_url($br[1]);?>" rel="nofollow">
                    <span class="idocs-right-breadcrumbs-box-item"  title = "Breadcrumbs Category or Document Link">
                        <?php echo esc_html(ucfirst($br[0])); ?>	
                    </span>
                </a>
                <!------------------------------------------>
            <?php
        }
        ?> 
    </div>
</nav>
<?php
/*--------------------------------------------*/
