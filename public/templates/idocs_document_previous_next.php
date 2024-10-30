<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
$links_array = IDOCS_CPT::document_navigation_links($current_document_id, $current_category_id);
//do_action( 'qm/debug', $current_document_id );
//do_action( 'qm/debug', $current_category_id );
//do_action( 'qm/debug', $links_array );
/*---------------------------------------------------------------------------------------*/
if ($links_array[0] || $links_array[1]) {

    ?>
    <section class="idocs-document-navigation-links">
        <?php if ($links_array[0]) : ?>
            <a rel="prev" href="<?php echo esc_url($links_array[0]); ?>" class="idoc-document-nav-link">
                <?php IDOCS_ICONS::echo_icon_svg_tag("angle-left", 30, 30);?>
                <?php echo esc_html__( 'Previous Document', 'incredibledocs' );?>
            </a>
        <?php endif; ?>
        <?php if ($links_array[1]) : ?>
            <a rel="next" href="<?php echo esc_url($links_array[1]); ?>" class="idoc-document-nav-link">
                <?php echo esc_html__( 'Next Document', 'incredibledocs' );?>
                <?php IDOCS_ICONS::echo_icon_svg_tag("angle-right", 30, 30);?>
            </a>
        <?php endif; ?>
    </section>
    <div class="sperator">
            <hr>
        </div>
    <?php
}
/*--------------------------------------------*/
