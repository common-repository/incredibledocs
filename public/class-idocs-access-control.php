<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
// Providing utility functions to check access for content (knowlege-base, categories, document)
/*---------------------------------------------------------------------------------------*/
class IDOCS_Access_Check {
 
    /*--------------------------------------------*/
    // filter out categories based on the kb and type of user. 
    public static function filter_out_categories($kb_id, $interal_user, $category_list) {

        // get the access type of the kb. 
        //$kb_access_type = get_term_meta( $kb_id, 'idocs-kb-taxo-access-type', true);
        $kb_access_type = IDOCS_Taxanomies::get_term_meta_caching( $kb_id, 'idocs-kb-taxo-access-type', false);
        /*----------------------------------------*/
        /*
            next scenarios to check:
            1. public kb with a public-visitor or an internal-user --> no-filter for all categories 
            2. internal or hybrid kb (check each category)
                2.1 hybrid kb, non-public category, public visitor --> filter out  
                2.2 internal or hybrid kb, groups category 
                    2.2.1 user is not part of the category groups --> filter out   
        */
        /*----------------------------------------*/
        // scenario #1 - a public kb --> no need to filter any category
        // if it is a public kb, no filtering is needed. 
        /*----------------------------------------*/ 
        if ( $kb_access_type == 'Public' ) {
            return $category_list;
        }
        /*----------------------------------------*/
        // scenario #2
        // kb is now "Internal" or Hybrid" --> need to check each category
        /*----------------------------------------*/ 
        foreach ($category_list as $key=>$category_term) {

            //$cat_access_type = get_term_meta( $category_term->term_id, 'idocs-category-taxo-access-type', true );
            $cat_access_type = IDOCS_Taxanomies::get_term_meta_caching( $category_term->term_id, 'idocs-category-taxo-access-type', false);
            $remove_category = false;
            /*----------------------------------------*/ 
            // public-visitor in an internal kb already filtered out at the kb level
            // hybrid kb, non-public category, public visitor --> filter out 
            if( ( $kb_access_type == 'Hybrid') and ($cat_access_type!= 0) and (! $interal_user) ) {
                
                    $remove_category = true; 
                    //do_action( 'qm/debug', $remove_category );
            }  
            /*----------------------------------------*/ 
            // internal or hybrid kb, groups category --> need to check if user is inside 
            if ($cat_access_type == 2) { // accces type is "Groups"
                
                //do_action( 'qm/debug', $cat_access_type );
                //$cat_groups =  get_term_meta( $category_term->term_id, 'idocs-category-taxo-groups', true );
                $cat_groups = IDOCS_Taxanomies::get_term_meta_caching( $category_term->term_id, 'idocs-category-taxo-groups', true);
                //do_action( 'qm/debug', $cat_groups );
                $user_groups = get_user_meta(get_current_user_id(), 'idocs_groups', true);
                $found_group = false;
                if ( (! empty($cat_groups) ) and (! empty($user_groups) ) ) {

                    foreach ($user_groups as $user_group) {

                        if ( in_array($user_group, $cat_groups) ) {
                            $found_group = true;
                            break;
                        }
                    };
                };
                /*----------------------------------------*/
                if (! $found_group ) { // user is not part of any group assigned to this category 
                    $remove_category = true;   
                };
            }; 
            /*----------------------------------------*/ 
            if ( $remove_category ) { // user is not part of any group assigned to this category 
                unset($category_list[$key]);
            }
            /*----------------------------------------*/ 
        };
        /*----------------------------------------*/ 
        return $category_list;
    }
    /*---------------------------------------------------------------------------------------*/
    // utility function to check access for a specific document (5e5827093d)
    public static function check_access_to_content_item( $kb_id, $post_id, $internal_user, $user_id ) {

        // kb access type 
        $kb_access_type = IDOCS_Taxanomies::get_term_meta_caching( $kb_id, 'idocs-kb-taxo-access-type', false);
        //error_log('kb access type: ' . $kb_access_type);
        //error_log('user id: ' . $user_id);
        /*----------------------------------------*/
        // if it is a public kb, access allowed.
        if ( $kb_access_type == 'Public' ) {

            return true;

        };
        /*----------------------------------------*/
        // root category id of that document 
        //$cat_id = get_post_meta($post_id, 'idocs-parent-category-meta', true);
        //error_log('parent cat id: ' .  $cat_id);
        $cat_id = IDOCS_CPT::get_post_meta_caching($post_id, 'idocs-parent-category-meta');
        error_log('parent cat id: ' .  $cat_id);

        // root category access type 
        //$cat_access_type =  get_term_meta( $cat_id, 'idocs-category-taxo-access-type', true );
        $cat_access_type = IDOCS_Taxanomies::get_term_meta_caching( $cat_id, 'idocs-category-taxo-access-type', false);
        //error_log('cat access type: ' . $cat_access_type);
        //do_action( 'qm/debug', $kb_access_type );
        //do_action( 'qm/debug', $cat_access_type );
        /*----------------------------------------*/
       
        /*----------------------------------------*/
        // hybrid kb and public category, access allowed. 
        if ( $kb_access_type == 'Hybrid'  and  $cat_access_type == 0) {

            return true;

        };
        /*----------------------------------------*/
        // (internal kb) internal user and ""?
        if ( $internal_user and $cat_access_type == 0 ) {

            return true;

        };
        /*----------------------------------------*/
        //error_log('internal user: ' . $internal_user);
        // (hybrid or internal kb) internal user and all logged-in users category 
        if ( $internal_user and $cat_access_type == 1 ) {

            return true;

        };
        /*----------------------------------------*/
        // (hybrid or internal kb) internal user and groups category 
        if ( $internal_user and $cat_access_type == 2) {

            //$cat_groups =  get_term_meta( $cat_id, 'idocs-category-taxo-groups', true );
            $cat_groups = IDOCS_Taxanomies::get_term_meta_caching( $cat_id, 'idocs-category-taxo-groups', true);
            $user_groups = get_user_meta( $user_id , 'idocs_groups', true);
            /*----------------------------------------*/
            if ( (! empty($cat_groups) ) and (! empty($user_groups) ) ) {

                foreach ($user_groups as $user_group) {

                    if ( in_array($user_group, $cat_groups) ) {

                        return true;

                    }
                };
            };
        };
        /*----------------------------------------*/
        return false;
    }
}
/*---------------------------------------------------------------------------------------*/
