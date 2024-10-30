<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
$doc_id = $current_doc_id;
// Retrieves the terms of the custom taxonomy ('idocs-tag-taxo') that are attached to the post ($doc-id)
// terms are items within the custom taxanomy (5e5827093d)
$terms = IDOCS_Taxanomies::get_the_terms_caching( $doc_id, 'idocs-tag-taxo' );
//do_action( 'qm/debug', $terms );
$tags = [];
/*--------------------------------------------*/
if ($terms) {
    foreach ($terms as $term)
        array_push($tags, $term->name);
};
/*--------------------------------------------*/
if ( is_array($tags) ) {

    $params = array(
        'post_type'         => 'idocs_content',
        'post_status'       => 'publish',
        'tax_query' => array(
            array(
            'taxonomy' => 'idocs-tag-taxo',
            'field' => 'name',
            'terms' => $tags,
            ),
        ),
    );
    $query = new WP_Query($params);
}
/*--------------------------------------------*/
?>
<section class="idocs-related-documents-box">
    <h5><?php echo esc_html__( 'Related Documents', 'incredibledocs' );?></h5>
    <ul>
        <?php
        while ($query->have_posts()) {
            
            $query->the_post();
            $newtab = false;
            $post_id = get_the_ID();
            $newtab = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-content-newtab-meta');
            /*--------------------------------------------*/
            if ( empty ($newtab) ) {
                $newtab = 0;
            }	
            if ($post_id != $doc_id) {
                ?>
                <li>
                    <a href="<?php echo esc_url(get_the_permalink()); ?>" <?php if ($newtab) echo esc_attr(" target=_blank");?> title = "Related Document Link">
                        <?php echo esc_html(get_the_title()); ?>
                    </a>
                </li>
                <?php
            }
        }
        wp_reset_postdata(); 
        ?>
    </ul>
</section>
<div class="sperator">
    <hr>
</div>
<?php
/*--------------------------------------------*/