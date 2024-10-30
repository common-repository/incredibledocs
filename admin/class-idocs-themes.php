<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
class IDOCS_Themes {

    public static function get_theme_colors( $theme_id ) {

        // check the type of theme: default or custom (custom will be larger than 1000)
        $theme_colors = array ();
        //do_action( 'qm/debug', $theme_id );
        /*------------------------------------------------*/
        if ( $theme_id > 1000 ) { // custom theme 
 
            // removing the constant delta added to the theme_id for custom themes   
            //$colors = get_term_meta( $theme_id - 1000, 'idocs-theme-taxo-colors', true );
            $colors = IDOCS_Taxanomies::get_term_meta_caching( $theme_id - 1000, 'idocs-theme-taxo-colors', true);
            //do_action( 'qm/debug', $colors );
            /*--------------------------------------------*/
            // checking of the custom theme is still available 
            if ( ! empty($colors) ) {

                $theme_colors['primary'] = $colors[0];
                $theme_colors['secondary'] = $colors[1];
                $theme_colors['background'] = $colors[2];
                $theme_colors['accent'] = $colors[3];
                $theme_colors['text'] = $colors[4];

            }
            /*--------------------------------------------*/
            else { // fallback to default theme - if pro version is not enabled or the theme colors are not available

                $theme_colors =  self::get_all_themes()[ 1 ]['colors'];

            }
        }
        /*--------------------------------------------*/
        else { // default theme 
            
            //error_log('required theme id:' . $theme_id);
            $list_themes = self::get_all_themes();
            /*--------------------------------------------*/
            if ( isset($list_themes[ $theme_id ]) ) {

                $theme_colors =  $list_themes[ $theme_id ]['colors'];
               // do_action( 'qm/debug', $theme );

            }
            else { //pro version not activated so pro-theme will not be available 

                $theme_colors =  self::get_all_themes()[ 1 ]['colors'];

            }
        }
        /*--------------------------------------------*/
        //do_action( 'qm/debug', $theme_colors );
        return $theme_colors;
    }
    /*---------------------------------------------------------------------------------------*/
    public static function get_all_themes() {
      
        $themes = self::get_default_themes();
        // apply_filter - let the pro version add additional theme options (5e5827093d)
        //$temp = apply_filters('idocspro_more_themes', $themes);
        //do_action( 'qm/debug', apply_filters('idocspro_more_themes', $themes));
		return apply_filters('idocspro_more_themes', $themes);
    }
    /*---------------------------------------------------------------------------------------*/    
    public static function get_default_themes() {

        $themes = array(

            1 => array(
                'name' => 'Ocean Breeze',
                'custom_theme' => 0,
                'colors' => array(
                    'primary'   => '#3498db',
                    'secondary' => '#2c3e50',
                    'background' => '#ecf0f1',
                    'accent'    => '#1abc9c',
                    'text'      => '#000000',
                ),
            ),
        
        2 => array(
                
            'name' => 'Green Forest',
            'custom_theme' => 0,
            'colors' => array(
                'primary'   => '#001233',
                'secondary' => '#5cbf00',
                'background' => '#ecf0f1',
                'accent'    => '#cccccc',
                'text'      => '#000000',
            ),
            )
        );
        /*--------------------------------------------*/
		return $themes;
    }
    /*---------------------------------------------------------------------------------------*/
    public static function get_kb_theme_name( $kb_id ) {

        // get the theme id stored in the kb term
        //$theme_id = get_term_meta( $kb_id, 'idocs-kb-taxo-theme-id', true );
        $theme_id = IDOCS_Taxanomies::get_term_meta_caching(  $kb_id, 'idocs-kb-taxo-theme-id', false);
        /*--------------------------------------------*/
        if ( $theme_id < 1000 ) { // using a theme from the default plugin themes

            $list_themes = self::get_all_themes();
            if ( isset($list_themes[ $theme_id ]) ) {
                $theme =  self::get_all_themes()[ $theme_id ];
            }
            else { // pro version is not active anymore so pro-theme not available - use free theme. 
                $theme =  self::get_all_themes()[ 1 ];
            }
            return $theme['name'];
        }
        else { // custom theme 

            $theme_term = get_term_by('id', $theme_id - 1000, 'idocs-theme-taxo');
            if ( $theme_term != null ) {
                return $theme_term->name;
            }
            else
            {
                return "Custom theme not found";
            }
        }
    }
    /*---------------------------------------------------------------------------------------*/
    public static function get_design_tabs () {

        $design_tabs = array (

            /*--------------------------------------------*/
            'customizer' => array (
                'tab_active' => true,
                'tab_title' => __( 'Customizer', 'incredibledocs' ),
                'tab_page'  => 'design-customizer-page.php',
                'page_path' => IDOCS_ADMIN_DIR_PATH,
            ),
            /*--------------------------------------------*/
            'kb_theme' => array (
                'tab_active' => true,
                'tab_title' => __( 'KB Theme', 'incredibledocs' ),
                'tab_page'  => 'design-kb-theme-page.php',
                'page_path' => IDOCS_ADMIN_DIR_PATH,
            ),
            /*--------------------------------------------*/
            'custom_theme' => array (
                'tab_active' => false,
                'tab_title' => __( 'Custom Theme', 'incredibledocs' ),
                'tab_page'  => 'design-custom-theme-page.php',
                'page_path' => '',
            ),
            /*--------------------------------------------*/
            'tools' => array (
                'tab_active' => false,
                'tab_title' => __( 'Tools', 'incredibledocs' ),
                'tab_page'  => 'design-tools-page.php',
                'page_path' => '',
            ),
        
        );
        /*--------------------------------------------*/
        // apply_filter - let the pro-version turn on design tabs
		return apply_filters('idocs_design_tabs',  $design_tabs );
    }
    /*---------------------------------------------------------------------------------------*/
}