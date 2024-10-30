<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
get_header();
/*--------------------------------------------*/
// get the current knowledge-base id 
$kb_id = get_queried_object()->term_id;
$category_id = 0; 
require_once IDOCS_DIR_PATH . 'public/templates/idocs_kb_view.php';
/*--------------------------------------------*/
get_footer();