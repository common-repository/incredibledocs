<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
/*---------------------------------------------------------------------------------------*/
?>
<nav class="row idocs-categories-box">
<?php
    // display only top-level (root) categories in a kb
    if ( $category_id == 0 ) {
        
        require_once IDOCS_DIR_PATH . 'public/templates/idocs_cards_root.php';

    }
    /*--------------------------------------------*/ 
    // display a cards view of a specific category 
    else {

        // check if the category has any direct content
        $any_direct_docs = IDOCS_Shortcodes::check_for_any_direct_content( $category_id );
        if ( $any_direct_docs ) {

            require_once IDOCS_DIR_PATH . 'public/templates/idocs_cards_category_with_docs.php';
            
        } else {

            require_once IDOCS_DIR_PATH . 'public/templates/idocs_cards_category_no_docs.php';

        }
    }
?>
</nav> <!-- "idocs-category-view" --> 
<?php
/*--------------------------------------------*/
