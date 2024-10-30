<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
if ($show_tag_background_color == null) {

    $show_tag_background_color = false;

};
//$tags = get_the_terms( $doc_id, 'idocs-tag-taxo' );
$tags = IDOCS_Taxanomies::get_the_terms_caching( $current_document_id, 'idocs-tag-taxo' );

if ($tags != false) {

    $num_of_tags = count($tags);

};
/*--------------------------------------------*/
?>
<nav class="idocs-related-document-tags-box">
    <h5><?php echo esc_html__( 'Tags', 'incredibledocs' );?></h5>
    <div class="idocs-document-tags-items">
        <?php
        if (is_array($tags)) {
            foreach($tags as $tag) {

                $tag_link = get_term_link( $tag );
                /*--------------------------------------------*/
                if ($show_tag_background_color) {

                    $color = IDOCS_Taxanomies::get_term_meta_caching(  $tag->term_id, "idocs-tag-taxo-color", false);
                    
                    ?>
                    <div class="idocs-document-tags-item" style=";background-color:<?php echo esc_attr($color);?>">
                        <a href="<?php echo esc_url($tag_link); ?>" title = "Tag Link" >
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    </div>
                    <?php
                }
                /*--------------------------------------------*/
                else {
                    ?>
                    <a href="<?php echo esc_url($tag_link); ?>" title = "Tag Link">
                        <?php echo esc_html($tag->name); ?>
                    </a>
                    <?php						
                }
            }
        }
        /*--------------------------------------------*/
        else {
            ?>
                <p><?php echo esc_html__( 'No Tags', 'incredibledocs' );?></p>
            <?php
            }		
        ?>
    </div>	
</nav>
<?php
/*--------------------------------------------*/