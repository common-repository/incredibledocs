<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
// Panel (container for Sections)--> Section/s (group of controls) --> 
// Controls (UI Elements) --> Setting/s (associate Controls with the settings that are saved in the database.)
/*---------------------------------------------------------------------------------------*/
class IDOCS_Customizer {

    private $defaults_design_options;

    /*---------------------------------------------------------------------------------------*/
    public function __construct() {

	}
    /*---------------------------------------------------------------------------------------*/
    public static function hex2rgb($hex) {
		// Remove the hash if it's there
		$hex = str_replace('#', '', $hex);
		
		// Convert shorthand hex color (e.g. #03F) to full hex (e.g. #0033FF)
		if (strlen($hex) == 3) {
			$hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
		}
	
		// Get RGB values
		$red = hexdec(substr($hex, 0, 2));
		$green = hexdec(substr($hex, 2, 2));
		$blue = hexdec(substr($hex, 4, 2));
	
		// Return the RGB values as a string
		return "$red, $green, $blue";
	}
    /*---------------------------------------------------------------------------------------*/
    public function sanitize_color_with_opacity( $color ) {

        if ( false === strpos( $color, 'rgba' ) ) {
            return sanitize_hex_color( $color );
        }
    
        // If the color has an alpha value, sanitize it as RGBA.
        if ( preg_match( '|rgba\((\d+),\s*(\d+),\s*(\d+),\s*([0-9\.]+)\)|i', $color, $matches ) ) {
            $red   = intval( $matches[1] );
            $green = intval( $matches[2] );
            $blue  = intval( $matches[3] );
            $alpha = floatval( $matches[4] );
    
            return 'rgba(' . $red . ', ' . $green . ', ' . $blue . ', ' . $alpha . ')';
        }
    
        // If the color is not in a valid format, return empty string (or default value).
        return '';
    }
    /*---------------------------------------------------------------------------------------*/
    public static function add_opacity_to_hex($hex, $opacity) {
        // Remove the hash if it's there
        $hex = str_replace('#', '', $hex);
        
        // Convert shorthand hex color (e.g. #03F) to full hex (e.g. #0033FF)
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }
    
        // Get RGB values
        $red = hexdec(substr($hex, 0, 2));
        $green = hexdec(substr($hex, 2, 2));
        $blue = hexdec(substr($hex, 4, 2));
    
        // Calculate alpha value from opacity (between 0 and 1)
        $alpha = $opacity < 0 ? 0 : ($opacity > 1 ? 1 : $opacity);
    
        // Return RGBA value as a string
        return "rgba($red, $green, $blue, $alpha)";
    }
    /*---------------------------------------------------------------------------------------*/
    public static function design_color_options_per_kb_theme ( $kb_id ) {

        // get the saved theme id of the specific kb
        //$theme_id = get_term_meta( $kb_id, 'idocs-kb-taxo-theme-id', true );
        $theme_id = IDOCS_Taxanomies::get_term_meta_caching(  $kb_id, 'idocs-kb-taxo-theme-id', false);
        //error_log('kb_id:' . $kb_id);
        //error_log('theme_id:' . $theme_id);
        $theme_colors = IDOCS_Themes::get_theme_colors($theme_id);        
        /*--------------------------------------------*/
        return array (

            'tag_view_background_color' => $theme_colors['background'],
            /*--------------------------------------------*/
            'kb_view_background_color' => $theme_colors['background'],
            /*--------------------------------------------*/
            // Live Search
            'live_search_kb_icon_color' => $theme_colors['secondary'],
            'live_search_box_border_color' => $theme_colors['secondary'],
            'live_search_box_background_color'  => $theme_colors['primary'],
            'live_search_title_color' => $theme_colors['background'],
            'live_search_sub_title_color' => $theme_colors['secondary'],
            'live_search_input_box_text_color' => $theme_colors['text'],
            'live_search_input_box_text_placeholder_color' => $theme_colors['text'],
            'live_search_input_box_border_color' => $theme_colors['secondary'],
            'live_search_input_box_background_color' => $theme_colors['background'],
            'live_search_button_close_icon_color' => $theme_colors['primary'],
            'live_search_button_close_icon_hover_color' => $theme_colors['accent'], 
            
            'live_search_result_content_filter_text_color' => $theme_colors['text'],
            'live_search_result_content_filter_background_color' => $theme_colors['background'],
            'live_search_result_content_filter_background_hover_color' => self::add_opacity_to_hex($theme_colors['primary'], 0.3),
            'live_search_result_content_filter_icon_color' => $theme_colors['text'],
    
            'live_search_result_border_color' => $theme_colors['secondary'],
            'live_search_result_item_border_color' => $theme_colors['secondary'],
            'live_search_result_background_color'  => $theme_colors['background'],
            'live_search_result_item_text_color' => $theme_colors['text'],
            'live_search_result_item_icon_color' => $theme_colors['primary'],
            'live_search_result_item_text_hover_color' => $theme_colors['text'],
            'live_search_result_item_background_hover_color' => $theme_colors['accent'],
            /*--------------------------------------------*/
            'categories_box_background_color'  => $theme_colors['background'],
            'category_title_text_color' => $theme_colors['text'],
            'category_title_icon_color' => $theme_colors['accent'],
            'category_title_counter_text_color' => $theme_colors['secondary'],

            'category_title_counter_background_color' => self::add_opacity_to_hex($theme_colors['primary'], 0.7),

            'category_description_text_color' => $theme_colors['text'],
            'category_content_item_text_color' => $theme_colors['text'],
            'category_content_item_icon_color' => $theme_colors['primary'],
            
            'category_content_item_text_hover_color' => $theme_colors['text'],
            'category_content_item_background_hover_color' => $theme_colors['accent'],

            'category_card_background_color'  => $theme_colors['background'],
            'category_card_hover_background_color' => self::add_opacity_to_hex($theme_colors['secondary'], 0.2),
            'category_card_shadow_color' => $theme_colors['primary'],
            'category_card_hover_shadow_color' => $theme_colors['accent'],
            'category_card_border_color' => $theme_colors['primary'],

            'sub_categories_box_background_color' => $theme_colors['background'],
            'sub_categories_box_border_color' => $theme_colors['secondary'],
            'sub_categories_box_shadow_color' => $theme_colors['secondary'],
            /*--------------------------------------------*/
            'faqs_box_title_color' => $theme_colors['background'],
            'faqs_box_background_color' => $theme_colors['primary'],
            'faqs_group_title_color' => $theme_colors['secondary'],
            'faqs_item_title_color' => $theme_colors['text'],
            'faqs_item_title_background_color' => $theme_colors['background'],
            'faqs_item_title_hover_background_color' => $theme_colors['accent'],
            'faqs_item_content_background_color' => $theme_colors['background'],
            /*--------------------------------------------*/
            'document_view_background_color'  => $theme_colors['background'],
            /*--------------------------------------------*/ 
            'breadcrumbs_box_separator_color' => $theme_colors['primary'],
            'breadcrumbs_box_background_color' => $theme_colors['background'],
            'breadcrumbs_box_border_color' => $theme_colors['secondary'],
            'breadcrumbs_box_item_text_color' => $theme_colors['text'],
            'breadcrumbs_box_item_text_hover_color' => $theme_colors['secondary'],
            'breadcrumbs_box_item_background_color' => $theme_colors['background'],
            'breadcrumbs_box_item_hover_background_color' => $theme_colors['primary'],
            /*--------------------------------------------*/
            'content_and_sidebars_box_background_color'  => $theme_colors['background'],
            'content_and_sidebars_box_border_color' => $theme_colors['secondary'],
            /*--------------------------------------------*/
            'left_sidebar_box_background_color' => $theme_colors['background'],
            /*--------------------------------------------*/
            'navigation_box_background_color' => $theme_colors['background'],
            'navigation_box_border_color' => $theme_colors['primary'],
            'navigation_box_shadow_color' => $theme_colors['primary'],

            'navigation_box_category_title_text_color' => $theme_colors['text'],
            'navigation_box_category_title_background_color' => $theme_colors['background'],
            'navigation_box_category_icon_color' => $theme_colors['primary'],
            'navigation_box_category_accordion_color' => $theme_colors['background'],
            'navigation_box_category_counter_text_color' => $theme_colors['text'],
            'navigation_box_category_counter_background_color' => self::add_opacity_to_hex($theme_colors['primary'], 0.2),
            'navigation_box_category_counter_border_color' => self::add_opacity_to_hex($theme_colors['primary'], 0.8),

            'navigation_box_sub_category_title_text_color' => $theme_colors['text'],
            'navigation_box_sub_category_title_background_color' => $theme_colors['background'],

            'navigation_box_document_item_text_color' => $theme_colors['text'],
            'navigation_box_document_item_background_color' => $theme_colors['background'],
            'navigation_box_document_item_icon_color' => $theme_colors['primary'],
            'navigation_box_document_item_hover_text_color' => $theme_colors['text'],
            'navigation_box_document_item_hover_text_background_color' => $theme_colors['accent'],
            'navigation_box_document_item_active_text_color' => $theme_colors['primary'],
            /*--------------------------------------------*/
            //'document_content_box_text_color'  => $theme_colors['text'],

            'document_content_box_background_color'  => $theme_colors['background'],
            'document_metadata_text_color' => $theme_colors['text'],
            'document_metadata_print_color' => $theme_colors['text'],
            'document_navigation_link_icon_color' => $theme_colors['primary'],

            /*--------------------------------------------*/
            'document_tags_item_text_color' => $theme_colors['text'],
            'document_tags_item_border_color'  => $theme_colors['text'],
            /*--------------------------------------------*/
            'right_sidebar_box_background_color' => $theme_colors['background'],
            /*--------------------------------------------*/
            'toc_box_background_color' => $theme_colors['background'],
            'toc_box_title_text_color' => $theme_colors['text'],
            'toc_box_title_border_color' => $theme_colors['primary'],
            'toc_box_border_color' => $theme_colors['secondary'],
            'toc_box_title_background_color'  => $theme_colors['background'],

            'toc_items_text_color' => $theme_colors['text'],
            'toc_items_text_hover_color' => $theme_colors['text'],
            'toc_items_hover_background_color' => $theme_colors['accent'],
            /*--------------------------------------------*/
            'likes_rating_box_text_color' => $theme_colors['text'],
            'likes_rating_box_background_color' => $theme_colors['background'],
            'likes_rating_box_border_color' => $theme_colors['text'],
            'likes_rating_box_yes_button_icon_color' => $theme_colors['text'],
            'likes_rating_box_yes_button_background_color' => $theme_colors['background'],
            'likes_rating_box_yes_button_border_color' => $theme_colors['text'],
            'likes_rating_box_yes_button_hover_background_color' => '#1abc9c',
            'likes_rating_box_no_button_icon_color' => $theme_colors['text'],
            'likes_rating_box_no_button_background_color' => $theme_colors['background'],
            'likes_rating_box_no_button_border_color' => $theme_colors['text'],
            'likes_rating_box_no_button_hover_background_color' => '#b72e19',
            /*--------------------------------------------*/
            'feedback_box_background_color' => $theme_colors['background'],
            'feedback_box_border_color' => $theme_colors['text'],
            'feedback_box_title_text_color' => $theme_colors['text'],
            'feedback_box_item_text_color' => $theme_colors['text'],
            'feedback_box_item_background_color' => $theme_colors['background'], 
            'feedback_box_submit_button_background_color' => $theme_colors['background'],
            'feedback_box_submit_button_border_color' => $theme_colors['text'],
            'feedback_box_submit_button_hover_background_color' => $theme_colors['accent'],
            'feedback_box_submit_button_text_color' => $theme_colors['text'],
            /*--------------------------------------------*/
            'related_document_tags_text_color' => $theme_colors['text'],
            /*--------------------------------------------*/
            'tag_view_background_color'  => $theme_colors['background'],
            'tag_view_title_text_color' => $theme_colors['text'],
            'tag_view_title_background_color' => $theme_colors['background'],
             /*--------------------------------------------*/
            'tag_content_box_background_color' => $theme_colors['background'],
            'tag_content_box_border_color'  => $theme_colors['text'],
            
            'tag_content_card_hover_background_color' => $theme_colors['background'],
            'tag_content_card_background_color' => $theme_colors['background'],
            'tag_content_card_shadow_color' => $theme_colors['primary'],
            'tag_content_card_border_color' => $theme_colors['text'],
             /*--------------------------------------------*/
             'faqgroup_view_background_color'  => $theme_colors['background'],
             'faqgroup_view_title_text_color' => $theme_colors['text'],
             'faqgroup_view_title_background_color' => $theme_colors['background'],
            /*--------------------------------------------*/

        );
    }
    /*---------------------------------------------------------------------------------------*/
    // get all the kb design options
    public static function default_design_options ( $kb_id ) {
        
        //error_log($kb_id);
        $theme_colors = self::design_color_options_per_kb_theme( $kb_id );
        
        /*--------------------------------------------*/
        $all_settings = array (

            // KB View
            'kb_view_show_live_search' => 1,
            'kb_view_show_breadcrumb' => 1,
            'kb_view_show_faqs' => 1,
            'kb_view_show_page_title' => 1,
            'kb_view_background_image' => '',
            'kb_view_width' => 100,
            'kb_view_margin_padding' => '0, 0, 0, 0, 0, 0, 0, 0',
            /*--------------------------------------------*/
            // Live Search Box
            'live_search_box_background_image' => '',
            'live_search_box_border_radius' => 0,
            'live_search_box_border_width' => 0,
            'live_search_box_margin_padding' => '0, 0, 0, 0, 10, 0, 30, 0',
            
            // Live Search - KB Icon
            'live_search_kb_icon_show' => 1,
            'live_search_kb_icon_opacity' => 1,
            'live_search_kb_icon_width' => 60,

            // Live Search Title
            'live_search_title_text' => "Welcome! How can we help you?",
            'live_search_title_font_size' => 2.5,
            'live_search_title_font_weight' => 700,
            'live_search_title_padding_top' => 10,
            'live_search_title_padding_bottom' => 10,
            'live_search_title_margin_bottom' => 10,
            
            // Live Search Sub-Title
            'live_search_sub_title_show' => 1,
            'live_search_sub_title_text' => "Find setup guides, tutorials, troubleshooting, videos and more.",
            'live_search_sub_title_font_size' => 1.4,
            'live_search_sub_title_padding_top' => 10,
            'live_search_sub_title_padding_bottom' => 25,
            'live_search_input_output_width' => 70,

            // Live Search Input Box
            'live_search_input_search_placeholder' => "Search here...",
            'live_search_input_box_font_size' => 1.1,
            'live_search_input_box_padding' => '10, 10, 10, 10',
            'live_search_input_box_border_width' => 2,
            'live_search_input_box_border_radius' => 10,
            'live_search_input_bar_width' => 65,
    
            // Live Search Result
            'live_search_no_result_feedback' => 'Sorry, no result for that search.',
            'live_search_result_order_alphabetically' => 1,

            //'live_search_result_width' => 70, // %
            'live_search_result_height' => 350,
            'live_search_result_item_font_size' => 0.9,
            'live_search_result_item_icon_size' => 1,
            'live_search_result_item_padding' => '10, 10, 10, 10',

            'live_search_result_content_filter_padding' => '5, 5, 5, 5',


            'live_search_min_amount_characters_for_search' => 3,
            'live_search_keystroke_delay_before_search' => 650,
            /*--------------------------------------------*/
            // Categories Box
            'categories_box_num_columns' => 'value3',
            'categories_box_hide_empty_categories' => 0,
            'categories_box_animated_categories' => 1,
            'categories_box_cards_order_by' => 'category_order',
            //'categories_box_layout' => 'value1',
            'categories_box_background_image' => '',
            'categories_box_width' => 90, // %
            'categories_box_spacing_between_cards' => 3,
            'categories_box_minimum_height' => 400,
            'categories_box_margin_padding' => '20, 0, 20, 0, 10, 10, 10, 10',

            // Category Card
            'category_card_detailed_layout' => 0, // ON/OFF
            'category_card_documents_order_by' => 'custom_display_order',
            'category_card_hover_transition_effect' => 1,
            'category_card_show_shadow' => 1, // ON/OFF
            'category_card_border_radius' => 5,
            'category_card_border_width' => 2,
            'category_card_height' => 150,
            'category_card_padding' => '20, 0, 20, 0',

            'sub_categories_box_show_shadow' => 0, // ON/OFF
            'sub_categories_box_border_width' => 0,
            'sub_categories_box_border_radius' => 5,
            'sub_categories_box_padding' => '10, 10, 10, 10',

            // Category Title & Icon
            'category_title_icon_show' => 1, 
            'category_title_icon_size' => 35,

            'category_title_font_size' => 1.3,
            'category_title_text_alignment' => 'value2', // Left, Centered, Right
        
            'category_title_show_counter' => 1, // ON/OFF
            'category_title_counter_text_alignment' => 'value2', // Left, Centered, Right
            'category_title_counter_font_size' => 0.8,

            // Category Description
            'category_description_font_size' => 0.9,
        
            // Category Content Item
            'category_content_item_font_size' => 0.9,
            'category_content_item_icon_size' => 20,
            'category_content_item_padding' => '10, 10, 10, 10',

            // Category Card - Sub-Category
          
            
            // Category Card - Document Item
            /*--------------------------------------------*/
            // FAQs BOX
            'faqs_box_lock_root_faqs' => 0,
            'faqs_box_title_text' => 'Frequently Asked Questions',
            'faqs_box_title_font_size' => 2,
            'faqs_box_width' => 60, // %
            'faqs_box_margin_padding' => '0, 0, 100, 0, 10, 10, 20, 10',

            // FAQs Group
            'faqs_group_title_font_size' => 1.5,
            'faqs_group_title_padding' => '0, 0, 10, 10',

            // FAQs Item
            'faqs_item_title_font_size' => 0.9,
            'faqs_item_title_padding' => '10, 10, 10, 10',
            'faqs_item_content_font_size' => 0.9,
            /*--------------------------------------------*/
            // Document View
            'document_view_show_live_search' => 1,
            'document_view_show_breadcrumb' => 1,
            'document_view_background_image' => '',
            'document_view_width' => 100,
            'document_view_margin_padding' => '0, 0, 0, 0, 0, 0, 0, 0',
            /*--------------------------------------------*/
            // Content and Sidebars Box
            'content_and_sidebars_box_show_left_sidebar' => 1,
            'content_and_sidebars_box_show_right_sidebar' => 1,
            'content_and_sidebars_box_border_radius' => 0,
            'content_and_sidebars_box_border_width' => 0,
            'content_and_sidebars_box_margin_padding' => '10, 10, 10, 10, 0, 0, 0, 0',
            /*--------------------------------------------*/
            // Left Sidebar Box
            'left_sidebar_box_width' => 3,
            'left_sidebar_box_padding' => '0, 10, 10, 0',
            /*--------------------------------------------*/
            // Right Sidebar
            'right_sidebar_box_show_toc' => 1,
            'right_sidebar_box_show_document_tags' => 1,
            'right_sidebar_box_width' => 2 ,
            'right_sidebar_box_padding' => '0, 0, 0, 0',
            /*--------------------------------------------*/
            'document_tags_box_margin_padding' => '0, 0, 0, 0, 0, 0, 0, 0',
            'document_tags_item_font_size' => 0.8,
            'document_tags_item_show_background_color' => 1,
            'document_tags_item_border_radius' => 5,
            'document_tags_item_border_width' => 0,
            'document_tags_item_padding' => '3, 5, 3, 5',
            /*--------------------------------------------*/
            // Document Content Box
            'document_content_box_show_tags' => 0,
            'document_content_box_show_related_documents' => 1,
            'document_content_box_show_document_like_rating' => 1,
            'document_content_box_show_document_star_rating' => 1,

            'document_content_box_show_document_feedback' => 1,
            'document_content_box_padding' => '10, 10, 10, 15',
            /*--------------------------------------------*/
            // Document Metadata
            'document_metadata_show_tags' => 1,
            'document_metadata_show_document_title' => 1,
			'document_metadata_show_last_updated_date' => 1,
			'document_metadata_show_estimated_time_to_read'=> 1,
			'document_metadata_show_author' => 1,
            'document_metadata_show_visits_counter' => 1,
            'document_metadata_show_rating_score' => 1,
            
            'document_metadata_show_print_icon' => 1,
            /*--------------------------------------------*/
            // ROADMPAP
            //'document_metadata_show_share_icon' => 1,

            'document_metadata_font_size' => 1,
            /*--------------------------------------------*/
            // Navigation Box
            'navigation_box_hide_empty_categories' => 1,
            'navigation_box_only_current_category' => 0,
            'navigation_box_border_radius' => 5,
            'navigation_box_border_width' => 1,
            'navigation_box_show_shadow' => 1,
            'navigation_box_padding' => '0, 0, 0, 0',

             // Navigation Box - Category 
            'navigation_box_show_category_icon' => 1,
            'navigation_box_category_show_counter' => 1,
            'navigation_box_category_icon_size' => 1.2,
            'navigation_box_category_title_font_size' => 1,
            'navigation_box_category_title_bold' => 1,            
            
            'navigation_box_category_counter_circle_height' => 1.7,
            'navigation_box_category_counter_circle_width' => 2.4,
            'navigation_box_category_counter_font_size' => 0.9,
            'navigation_box_category_counter_border_radius' => 10,
            'navigation_box_category_counter_border_width' => 3,

            // Navigation Box - Sub-Category Title
            'navigation_box_sub_category_title_font_size' => 0.8,
            'navigation_box_sub_category_title_bold' => 1,
            
            // Navigation Box - Document Item
            'navigation_box_document_item_icon_width' => 0.9,
            'navigation_box_document_item_font_size' => 0.9,
            /*--------------------------------------------*/
            // Breadcrumbs Box
            'breadcrumbs_box_home_url' => get_home_url(),
            'breadcrumbs_box_home_text' => 'Home',
            'breadcrumbs_box_border_radius' => 0,
            'breadcrumbs_box_border_width' => 0,
            'breadcrumbs_box_margin_padding' => '0, 0, 0, 0, 10, 0, 0, 10',
            'breadcrumbs_box_separator_font_size' => 1,

            // Breadcrumbs Item
            'breadcrumbs_box_item_font_size' => 1,
            'breadcrumbs_box_item_padding' => '5, 5, 5, 5',
            /*--------------------------------------------*/
            // TOC Box 
            'toc_box_border_radius' => 5,
            'toc_box_border_width' => 3,
            'toc_box_sticky_z_index' => 10,
            'toc_box_sticky_margin_top' => 50,
            'toc_box_padding' => '5, 10, 10, 10',
            
            // TOC Title
            'toc_box_title_text' => 'Table of Contents',
            'toc_box_title_font_size' => 0.9,
            'toc_box_title_alignment' => 'value1',
            'toc_box_title_border_width' => 1,
            'toc_box_title_padding' => '0, 0, 0, 0',

            // TOC Items
            'toc_items_header_start' => 'value2',
            'toc_items_header_end' => 'value6',
            'toc_items_font_size' => 0.8,
            'toc_items_padding' => '5, 5, 0, 5',
            /*--------------------------------------------*/
            // Likes Rating Box
            'likes_rating_box_title_font_size' => 1.4,
            'likes_rating_box_title_border_width' => 1,
            /*--------------------------------------------*/
            // Feedback Box
            'feedback_collection_probability' => 40,
            'feedback_box_improve_feedback_title' => 'How can we improve this content?',
            'feedback_box_title_font_size' => 1.4,
            'feedback_box_border_width' => 1,
            'feedback_box_padding' => '5, 15, 5, 15',
            // Feedback Box Item
            'feedback_box_item_font_size' => 1,
            // Feedback Box - Submit Button
            'feedback_box_submit_button_font_size' => 1,
            /*--------------------------------------------*/
            // Tag View
            'tag_view_show_live_search' => 1,
            'tag_view_background_image' => '',
            'tag_view_width' => 100,
            'tag_view_margin_padding' => '0, 0, 50, 0, 0, 0, 0, 0',
            'tag_view_title_font_size' => 2,
            'tag_view_title_margin_padding' => '0, 0, 0, 0, 0, 0, 0, 0',
            /*--------------------------------------------*/
            'tag_content_box_show_documents' => 1,
            'tag_content_box_show_links' => 1,
            'tag_content_box_show_videos' => 1,
            'tag_content_box_show_faqs' => 1,
            'tag_content_box_border_radius' => 0,
            'tag_content_box_border_width' => 0,
            'tag_content_box_margin_padding' => '30, 0, 30, 0, 0, 100, 0, 100',
            /*--------------------------------------------*/
            // Tag Content Card
            'tag_content_card_items_order_by' => 'custom_display_order',
            'tag_content_card_show_shadow' => 1, // ON/OFF
            'tag_content_card_border_radius' => 5,
            'tag_content_card_border_width' => 2,
            'tag_content_card_height' => 300,
            'tag_content_card_padding' => '0, 0, 0, 0',
            /*--------------------------------------------*/
            'faqgroup_view_show_live_search' => 1,
            'faqgroup_view_width' => 100,
            'faqgroup_view_background_image' => '',
            'faqgroup_view_margin_padding' => '0, 0, 0, 0, 0, 0, 0, 0',
            /*--------------------------------------------*/
            'analytics_data_kb_view_visit' => 1,
            'analytics_data_category_view_visit' => 1,
            'analytics_data_tag_view_visit' => 1,
            'analytics_data_faq_group_view_visit' => 1,
            'analytics_data_document_view_visit' => 1,
            'analytics_data_video_view_visit' => 1,
            /*--------------------------------------------*/
            
        );
        
        return array_merge($all_settings, $theme_colors);
    }
    /*---------------------------------------------------------------------------------------*/
    // register panels, sections, settings and controls - hooked to the "customize_register" action hook 
    public function custom_customize_register( $wp_customize ) {
        
        // get the list of knowledge-bases 
        $kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
        /*--------------------------------------------*/    
        foreach ( $kb_terms as $term) {

           // error_log($term->term_id);
            $kb_id = $term->term_id;
            //error_log('registering design for kb:' . $term->term_id);
            $this->register_panel( $wp_customize, $kb_id );
            $this->register_sections( $wp_customize, $kb_id  );
            $this->register_controls_and_settings( $wp_customize, $kb_id );
            IDOCS_Database::suspend_design_settings_caching($kb_id);
            
        }
    }
    /*---------------------------------------------------------------------------------------*/
    // create a custom panel for a specific knowledge-base
    private function register_panel( $wp_customize, $kb_id ) {

        $kb_term = IDOCS_Taxanomies::get_specific_kb_term_caching($kb_id);
        //$kb_term = get_term( $kb_id, 'idocs-kb-taxo');
        $kb_name = $kb_term->name;   
        /*--------------------------------------------*/
        $wp_customize->add_panel( 'idocs_customizer_panel_'. $kb_id, 
                array(
                    'title' => esc_html('IncredibleDocs: ' . $kb_name), 
                    'description' => esc_html__('Controls the design settings for the a knowledgebase.', 'incredibledocs'),
                    'priority' => 1,
                    'capability' => '', 
                    'theme_supports' => '',
                    'active_callback' => ''
                ) 
            ); 
    }
    /*---------------------------------------------------------------------------------------*/
    // create sections for a specific knowledge-base
    private function register_sections( $wp_customize, $kb_id ) {

        /*--------------------------------------------*/
        // Section #1 - KnowledgeBase View
        $wp_customize->add_section( 'idocs_customizer_kb_view_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Knowledge Base View','incredibledocs'),
            'description' => esc_html__('Controls the design settings for the knowledge-base view.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 1,
        ));
        /*--------------------------------------------*/
        // Section #2 - Live Search Module
        $wp_customize->add_section( 'idocs_customizer_live_search_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Live Search', 'incredibledocs'),
            'description' => esc_html__('Controls the design settings for the live search module.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 2,
        ));
        /*--------------------------------------------*/ 
        // Section #7 - Breadcrumbs Module
        $wp_customize->add_section( 'idocs_customizer_breadcrumbs_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Breadcrumbs', 'incredibledocs'),
            'description' => esc_html__('Controls the design settings for the breadcrumbs module.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 3,
        ));
        /*--------------------------------------------*/    
        // Section #3 - Categories Cards Module
        $wp_customize->add_section( 'idocs_customizer_categories_cards_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Categories Cards', 'incredibledocs'),
            'description' => esc_html__('Controls the design settings for the categories cards module.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 4,
        ));        
        /*--------------------------------------------*/
        // Section #4 - FAQs Module
        $wp_customize->add_section( 'idocs_customizer_faqs_section_'. $kb_id , 
        array(
            'title'      => esc_html__('FAQs', 'incredibledocs'),
            'description' => esc_html__('Controls the design settings for the FAQs module.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 5,
        )); 
        /*--------------------------------------------*/
        // Section #5 - Document View
        $wp_customize->add_section( 'idocs_customizer_document_view_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Document View', 'incredibledocs'),
            'description' => esc_html__('Controls the design settings for the document view.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 6,
        ));
        /*--------------------------------------------*/
        // Section #6 - Document Content
        $wp_customize->add_section( 'idocs_customizer_document_content_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Document Content', 'incredibledocs'),
            'description' => esc_html__('Controls the design settings for the document content.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 7,
        ));
        /*--------------------------------------------*/
        // Section #8- Sidebar Navigator Module
        $wp_customize->add_section( 'idocs_customizer_sidebar_navigator_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Sidebar Navigator','incredibledocs'),
            'description' => esc_html__('Controls the design settings for the sidebar navigator module.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 8,
        ));
        /*--------------------------------------------*/
        // Section #9 - TOC
        $wp_customize->add_section( 'idocs_customizer_toc_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Table of Content (TOC)','incredibledocs'),
            'description' => esc_html__('Controls the design settings for the Table of Content(TOC) module.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 9,
        ));
        /*--------------------------------------------*/
        // Section #10 - Likes Rating
        $wp_customize->add_section( 'idocs_customizer_likes_rating_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Likes Rating','incredibledocs'),
            'description' => esc_html__('Controls the design settings for the likes rating module.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 10,
        ));
        /*--------------------------------------------*/
        // Section #11 - Feedback Form
        $wp_customize->add_section( 'idocs_customizer_feedback_form_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Feedback Form','incredibledocs'),
            'description' => esc_html__('Controls the design settings for the feedback form module.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 11,
        ));
        /*--------------------------------------------*/
        // Section #12 - Document Tags
        $wp_customize->add_section( 'idocs_customizer_document_tags_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Document Tags','incredibledocs'),
            'description' => esc_html__('Controls the design settings for the document tags module.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 12,
        ));
        /*--------------------------------------------*/
        // Section #14 - Tag View
        $wp_customize->add_section( 'idocs_customizer_tag_view_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Tag View','incredibledocs'),
            'description' => esc_html__('Controls the design settings for the tag view.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 13,
        ));
        /*--------------------------------------------*/
        // Section #15 - Tag Content Cards
        $wp_customize->add_section( 'idocs_customizer_tag_content_cards_section_'. $kb_id , 
        array(
            'title'      => esc_html__('Tag Content Cards','incredibledocs'),
            'description' => esc_html__('Controls the design settings for the tag content cards.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 14,
        ));
        /*--------------------------------------------*/
        // Section #14 - FAQ Group View
        $wp_customize->add_section( 'idocs_customizer_faq_group_view_section_'. $kb_id , 
        array(
            'title'      => esc_html__('FAQ Group View','incredibledocs'),
            'description' => esc_html__('Controls the design settings for the faq group view.', 'incredibledocs'),
            'panel'      => 'idocs_customizer_panel_'. $kb_id,
            'capability' => '', 
            'priority'   => 15,
        ));
        

    }
    /*---------------------------------------------------------------------------------------*/
    // create controls and settings for a specific knowledge-base
    private function register_controls_and_settings( $wp_customize, $kb_id ) {
        
        // custom customizer controls - are loaded only in the callback hook function 
        require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer-multi-dimensions.php';
        require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer-padding-dimensions.php';
        require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer-notice.php';
        require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer-warning-notice.php';
        //require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer-text-radio-button.php';
        require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer-slider.php';
        require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer-toggle-switch.php';
        require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer-color.php';
        require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer-dimension.php';
        require_once IDOCS_DIR_PATH . 'admin/customizer/class-idocs-customizer-url.php';
        /*--------------------------------------------*/
        $this->defaults_design_options = self::default_design_options( $kb_id );
        /*--------------------------------------------*/
        $this->kb_view_section($wp_customize, $kb_id );
        $this->live_search_section($wp_customize, $kb_id); 
        $this->breadcrumbs_section($wp_customize, $kb_id);
        $this->categories_cards_section($wp_customize, $kb_id);
        $this->faqs_section($wp_customize, $kb_id);
        $this->document_view_section($wp_customize, $kb_id);
        $this->document_content_section($wp_customize, $kb_id);
        $this->sidebar_navigator_section($wp_customize, $kb_id);
        $this->toc_section($wp_customize, $kb_id);
        $this->likes_rating_section($wp_customize, $kb_id);
        $this->feedback_form_section($wp_customize, $kb_id);
        $this->tag_view_section($wp_customize, $kb_id);
        $this->tag_content_cards_section($wp_customize, $kb_id);
        $this->faqgroup_view_section($wp_customize, $kb_id);
        $this->document_tags_section($wp_customize, $kb_id);
        
    }
    /*---------------------------------------------------------------------------------------*/
    public function sanitize_image($file, $setting ) {

        $mimes = array(

            'jpg|jpeg|jpe' => 'image/jpeg',
            'webp'         => 'image/webp', 
            'gif'          => 'image/gif',
            'png'          => 'image/png',
            'bmp'          => 'image/bmp',
            'tif|tiff'     => 'image/tiff',
            'ico'          => 'image/x-icon'
        );
        /*--------------------------------------------*/
        //check file type from file name
        $file_ext = wp_check_filetype( $file, $mimes );
        //if file has a valid mime type return it, otherwise return default
        return ( $file_ext['ext'] ? $file : $setting->default );
        
    }
    /*---------------------------------------------------------------------------------------*/
    // KB View 
    private function kb_view_section( $wp_customize,  $kb_id ) {

        // KB View Box
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[kb_view_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'kb_view_notice_'. $kb_id,
            array(
                'label' => __( 'KB View Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[kb_view_notice]',
            )
        ) );
        /*---------------------------------------*/
        //  KB View Box - Show Live Search (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[kb_view_show_live_search]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['kb_view_show_live_search'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'kb_view_show_live_search_'. $kb_id,
            array(
                'label' => esc_html__( 'Live Search','incredibledocs' ),
                'section' => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[kb_view_show_live_search]',
            )
        ) );
        /*---------------------------------------*/	
        //  KB View Box - Show Breadcrumb (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[kb_view_show_breadcrumb]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['kb_view_show_breadcrumb'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'kb_view_show_breadcrumb_'. $kb_id,
            array(
                'label' => esc_html__( 'Breadcrumbs-Bar','incredibledocs' ),
                'section' => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[kb_view_show_breadcrumb]',
            )
        ) );
        /*---------------------------------------*/	
        //  KB View Box - Show FAQs (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[kb_view_show_faqs]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['kb_view_show_faqs'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'kb_view_show_faqs_'. $kb_id,
            array(
                'label' => esc_html__( 'FAQs', 'incredibledocs' ),
                'section' => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[kb_view_show_faqs]',
            )
        ) );	
        /*---------------------------------------*/
        //  KB View Box - Page Title (Custom KB Page) (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[kb_view_show_page_title]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['kb_view_show_page_title'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'kb_view_show_page_title_'. $kb_id,
            array(
                'label' => esc_html__( 'Page Title (Custom KB Page)', 'incredibledocs' ),
                'section' => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[kb_view_show_page_title]',
            )
        ) );	
        /*---------------------------------------*/		
        // Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[kb_view_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['kb_view_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
            
        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'kb_view_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the background color for the knowledge-base container. Remove any selected image to use the background color.', 'incredibledocs' ),
                'section' => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[kb_view_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        /*---------------------------------------*/	
        // Background Image
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[kb_view_background_image]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['kb_view_background_image'],
                'transport' => 'refresh',    
                'sanitize_callback' => array($this, 'sanitize_image'),
            )
        );

        $wp_customize->add_control( new WP_Customize_Image_Control(
            $wp_customize, 
            'kb_view_background_image_'. $kb_id, 
            array(
                'label'    => esc_html__('Background Image', 'incredibledocs'),
                'description' => esc_html__( 'Set the background image for the knowledge-base container. It will overtake the background color.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[kb_view_background_image]',
            )
        ));

        /*---------------------------------------*/	
        // Width Slider
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[kb_view_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['kb_view_width'],
                'transport' => 'refresh',
            )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'kb_view_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (%)', 'incredibledocs'),
                'description' => esc_html__( 'Set the width of the knowledge-base container.', 'incredibledocs' ),

                'section'  => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[kb_view_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/
        // Margin and Padding
        $wp_customize->add_setting( 'idocs_design_options_' . $kb_id . '[kb_view_margin_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['kb_view_margin_padding'],
            'transport' => 'refresh',
        ) );
    
        
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'kb_view_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the margin and padding for the knowledge-base container. Left & right margins are auto calculated based on the box width.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_kb_view_section_'. $kb_id,
            'settings' => 'idocs_design_options_' . $kb_id . '[kb_view_margin_padding]',

        ) ) );
        
        /*---------------------------------------*/
        // KB Icon Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_kb_icon_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'live_search_kb_icon_notice_'. $kb_id,
            array(
                'label' => __( 'Knowledge-Base Icon' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[live_search_kb_icon_notice]',
            )
        ) );
        /*---------------------------------------*/
        // Live Search KB Icon - Show KB Icon (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_kb_icon_show]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['live_search_kb_icon_show'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'live_search_kb_icon_show_'. $kb_id,
            array(
                'label' => esc_html__( 'Knowledge-Base Icon' ),
                'section' => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_kb_icon_show]',
            )
        ) );	
        /*---------------------------------------*/
        // Live Search - KB Icon Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_kb_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_kb_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_kb_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the color for the knowledge-base icon (only for an icon selected using the plugin icon-picker).', 'incredibledocs' ),
                'section' => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_kb_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Live Search - KB Icon Opacity
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_kb_icon_opacity]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_kb_icon_opacity'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_kb_icon_opacity_'. $kb_id, 
            array(
                'label'    => esc_html__('Opacity', 'incredibledocs'),
                'description' => esc_html__( 'Set the opacity for the knowledge-base icon.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_kb_icon_opacity]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 1, // Required. Maximum value for the slider
                    'step' => 0.01, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    

        /*---------------------------------------*/
        // Live Search - KB Icon Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_kb_icon_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_kb_icon_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_kb_icon_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (%)', 'incredibledocs'),
                'description' => esc_html__( 'Set the width for the knowledge-base icon.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_kb_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_kb_icon_width]',
                
                'input_attrs' => array(
                    'min' => 1, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    

    }
    /*---------------------------------------------------------------------------------------*/
    private function document_view_section( $wp_customize, $kb_id ) {

        // Document View Box Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_view_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'document_view_box_title_'. $kb_id,
            array(
                'label' => __( 'Document View Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_view_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	
        // Document View - Show Live Search (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_view_show_live_search]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_view_show_live_search'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_view_show_live_search_'. $kb_id,
            array(
                'label' => esc_html__( 'Live Search', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_view_show_live_search]',
            )
        ) );
        /*---------------------------------------*/	
        // Document View - Show Breadcrumb (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_view_show_breadcrumb]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_view_show_breadcrumb'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_view_show_breadcrumb_'. $kb_id,
            array(
                'label' => esc_html__( 'Breadcrumbs-Bar', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_view_show_breadcrumb]',
            )
        ) );
        /*---------------------------------------*/	

        // Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_view_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['document_view_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'document_view_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_view_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/	
        // Background Image
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_view_background_image]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['document_view_background_image'],
                'transport' => 'refresh',
                'sanitize_callback' => array($this, 'sanitize_image'),
            )
         );

        $wp_customize->add_control( new WP_Customize_Image_Control(
            $wp_customize, 
            'document_view_background_image_'. $kb_id, 
            array(
                'label'    => esc_html__('Background Image', 'incredibledocs'),
                'section'  => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_view_background_image]',
            )
        ));
        /*---------------------------------------*/	
        // Width Slider
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_view_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['document_view_width'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'document_view_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (%)', 'incredibledocs'),
                'section'  => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_view_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_view_margin_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['document_view_margin_padding'],
            'transport' => 'refresh',
    
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'document_view_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default margin and padding for the quick button container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_document_view_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[document_view_margin_padding]',
            
        ) ) );
        /*---------------------------------------*/	
        // Content and Sidebars Box Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'content_and_sidebars_box_title_'. $kb_id,
            array(
                'label' => __( 'Content and Sidebars Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	        
        // Content and Sidebars Box - Show Left-Sidebar (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[content_and_sidebars_box_show_left_sidebar]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['content_and_sidebars_box_show_left_sidebar'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'content_and_sidebars_box_show_left_sidebar_'. $kb_id,
            array(
                'label' => esc_html__( 'Left Sidebar', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[content_and_sidebars_box_show_left_sidebar]',
            )
        ) );
        /*---------------------------------------*/	 
        // Content and Sidebars Box - Show Right-Sidebar (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[content_and_sidebars_box_show_right_sidebar]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['content_and_sidebars_box_show_right_sidebar'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'content_and_sidebars_box_show_right_sidebar_'. $kb_id,
            array(
                'label' => esc_html__( 'Right Sidebar' , 'incredibledocs'),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[content_and_sidebars_box_show_right_sidebar]',
            )
        ) );
        /*---------------------------------------*/	     
        // Content and Sidebars Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['content_and_sidebars_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'content_and_sidebars_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/	 
		// Content and Sidebars Box - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['content_and_sidebars_box_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'content_and_sidebars_box_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/	 
        // Content and Sidebars Box - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['content_and_sidebars_box_border_radius'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'content_and_sidebars_box_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/	        
        // Content and Sidebars Box - Border Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['content_and_sidebars_box_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'content_and_sidebars_box_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_margin_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['content_and_sidebars_box_margin_padding'],
            'transport' => 'refresh',
    
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'content_and_sidebars_box_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default margin and padding for the content and sidebars container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_document_view_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_margin_padding]',
            
        ) ) );

        /*
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['content_and_sidebars_box_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'content_and_sidebars_box_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the live search result item container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_document_view_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[content_and_sidebars_box_padding]',
        ) ) );
        */
        /*---------------------------------------*/	
        // Left Sidebar Box Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[left_sidebar_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'left_sidebar_box_title_'. $kb_id,
            array(
                'label' => __( 'Left Sidebar Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[left_sidebar_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	
        // Left Sidebar Box Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[left_sidebar_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'left_sidebar_box_title_'. $kb_id,
            array(
                'label' => __( 'Left Sidebar Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[left_sidebar_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	
        // Left Sidebar Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[left_sidebar_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['left_sidebar_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'left_sidebar_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[left_sidebar_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );       
        /*---------------------------------------*/	
        // Left Sidebar Box - Width 
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[left_sidebar_box_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['left_sidebar_box_width'],
                'transport' => 'refresh',

            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'left_sidebar_box_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (1/12 %)', 'incredibledocs'),
                'section'  => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[left_sidebar_box_width]',
                
                'input_attrs' => array(
                    'min' => 1, // Required. Minimum value for the slider
                    'max' => 5, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[left_sidebar_box_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['left_sidebar_box_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'left_sidebar_box_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the left sidebar container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_document_view_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[left_sidebar_box_padding]',
        ) ) );
        /*---------------------------------------*/	
        // Right Sidebar Box Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[right_sidebar_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'right_sidebar_box_title_'. $kb_id,
            array(
                'label' => __( 'Right Sidebar Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[right_sidebar_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	
        // Right Sidebar Box - Show TOC (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[right_sidebar_box_show_toc]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['right_sidebar_box_show_toc'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'right_sidebar_box_show_toc_'. $kb_id,
            array(
                'label' => esc_html__( 'Table of Content (TOC)', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[right_sidebar_box_show_toc]',
            )
        ) );	
        /*---------------------------------------*/	
        // Right Sidebar Box - Show Document Tags (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[right_sidebar_box_show_document_tags]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['right_sidebar_box_show_document_tags'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'right_sidebar_box_show_document_tags_'. $kb_id,
            array(
                'label' => esc_html__( 'Document Tags', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[right_sidebar_box_show_document_tags]',
            )
        ) );	
        /*---------------------------------------*/	
        /*---------------------------------------*/	
        // Right Sidebar Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[right_sidebar_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['right_sidebar_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'right_sidebar_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[right_sidebar_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );       
        /*---------------------------------------*/	
        // Right Sidebar Box - Width 
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[right_sidebar_box_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['right_sidebar_box_width'],
                'transport' => 'refresh',
             
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'right_sidebar_box_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (1/12 %)', 'incredibledocs'),
                'section'  => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[right_sidebar_box_width]',
                
                'input_attrs' => array(
                    'min' => 1, // Required. Minimum value for the slider
                    'max' => 5, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[right_sidebar_box_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['right_sidebar_box_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'right_sidebar_box_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the right side container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_document_view_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[right_sidebar_box_padding]',
        ) ) );
        /*---------------------------------------*/	
        
        /*---------------------------------------*/	
        // Document Navigation Links Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_navigation_links_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'document_navigation_links_notice_'. $kb_id,
            array(
                'label' => __( 'Document Navigation Links' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_navigation_links_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	
        // Document Navigation Link - Icon Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_navigation_link_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['document_navigation_link_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'document_navigation_link_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'section' => 'idocs_customizer_document_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_navigation_link_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/	

    
    }
    /*---------------------------------------------------------------------------------------*/
    private function document_content_section( $wp_customize, $kb_id ) {

        /*---------------------------------------*/	
        // Document Content Box Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_content_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'document_content_box_title_'. $kb_id,
            array(
                'label' => __( 'Document Content Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_content_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/
        // Document Content Box - Show Tags (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_content_box_show_tags]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_content_box_show_tags'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_content_box_show_tags_'. $kb_id,
            array(
                'label' => esc_html__( 'Document Tags (End of Doc)', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_content_box_show_tags]',
            )
        ) );
        /*---------------------------------------*/
        // Document Content Box - Show Document Like Rating (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_content_box_show_document_like_rating]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_content_box_show_document_like_rating'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_content_box_show_document_like_rating_'. $kb_id,
            array(
                'label' => esc_html__( 'Collect Likes Rating', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_content_box_show_document_like_rating]',
            )
        ) );
        /*---------------------------------------*/
        // Document Content Box - Show Document Star Rating (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_content_box_show_document_star_rating]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_content_box_show_document_star_rating'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_content_box_show_document_star_rating_'. $kb_id,
            array(
                'label' => esc_html__( 'Collect Stars Rating', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_content_box_show_document_star_rating]',
            )
        ) );
        /*---------------------------------------*/	
        // Document Content Box - Show Document Feedback (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_content_box_show_document_feedback]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_content_box_show_document_feedback'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_content_box_show_document_feedback_'. $kb_id,
            array(
                'label' => esc_html__( 'Collect Form Feedback', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_content_box_show_document_feedback]',
            )
        ) );
        
        
        /*---------------------------------------*/        
        // Document Content Box - Show Related Documents (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_content_box_show_related_documents]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_content_box_show_related_documents'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_content_box_show_related_documents_'. $kb_id,
            array(
                'label' => esc_html__( 'Related Documents' , 'incredibledocs'),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_content_box_show_related_documents]',
            )
        ) );

        /*
        // Document Content Box - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_content_box_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['document_content_box_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

            )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'document_content_box_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_content_box_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );  
        */

        // Document Content Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_content_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['document_content_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

            )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'document_content_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_content_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );    

        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_content_box_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['document_content_box_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'document_content_box_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the document content container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_document_content_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[document_content_box_padding]',
        
        ) ) );
        /*---------------------------------------*/	
        // Document Metadata Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_metadata_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'document_metadata_title_'. $kb_id,
            array(
                'label' => __( 'Document Metadata' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_metadata_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	
        /*---------------------------------------*/
        
        // Document Metadata - Show Document Tags (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_metadata_show_tags]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_metadata_show_tags'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_metadata_show_tags_'. $kb_id,
            array(
                'label' => esc_html__( 'Document Tags (Top Doc)', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_metadata_show_tags]',
            )
        ) );
         /*---------------------------------------*/
        // Document Metadata - Show Document Title (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_metadata_show_document_title]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_metadata_show_document_title'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_metadata_show_document_title_'. $kb_id,
            array(
                'label' => esc_html__( 'Document Title', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_metadata_show_document_title]',
            )
        ) );
        /*---------------------------------------*/
        // Document Metadata - Show Last Updated Date (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_metadata_show_last_updated_date]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_metadata_show_last_updated_date'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_metadata_show_last_updated_date_'. $kb_id,
            array(
                'label' => esc_html__( 'Last Updated Date', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_metadata_show_last_updated_date]',
            )
        ) );
        /*---------------------------------------*/	
        // Document Metadata - Show Estimated Time to Read (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_metadata_show_estimated_time_to_read]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_metadata_show_estimated_time_to_read'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_metadata_show_estimated_time_to_read_'. $kb_id,
            array(
                'label' => esc_html__( 'Estimated Time to Read', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_metadata_show_estimated_time_to_read]',
            )
        ) );
        /*---------------------------------------*/
        // Document Metadata - Show Author (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_metadata_show_author]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_metadata_show_author'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_metadata_show_author_'. $kb_id,
            array(
                'label' => esc_html__( 'Author', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_metadata_show_author]',
            )
        ) );	
        /*---------------------------------------*/
        // Document Metadata - Show visits_counter (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_metadata_show_visits_counter]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_metadata_show_visits_counter'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_metadata_show_visits_counter_'. $kb_id,
            array(
                'label' => esc_html__( 'Visits Counter (Pro)', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_metadata_show_visits_counter]',
            )
        ) );	
        /*---------------------------------------*/
        // Document Metadata - Show Rating Score (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_metadata_show_rating_score]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_metadata_show_rating_score'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_metadata_show_rating_score_'. $kb_id,
            array(
                'label' => esc_html__( 'Rating Score (Pro)', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_metadata_show_rating_score]',
            )
        ) );	

        /*---------------------------------------*/
        // Document Metadata - Show Print Document Icon (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_metadata_show_print_icon]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_metadata_show_print_icon'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_metadata_show_print_icon_'. $kb_id,
            array(
                'label' => esc_html__( 'Print Document', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_metadata_show_print_icon]',
            )
        ) );	
        /*---------------------------------------*/		
        // Document Metadata - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_metadata_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['document_metadata_font_size'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'document_metadata_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_metadata_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/	
        // Document Metadata - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_metadata_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['document_metadata_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'document_metadata_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_document_content_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_metadata_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/	
        
        /*---------------------------------------*/


    }
    /*---------------------------------------------------------------------------------------*/
    private function live_search_section( $wp_customize, $kb_id ) {

        // Live Search Box Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'live_search_box_title_'. $kb_id,
            array(
                'label' => __( 'Live Search Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	
        // Live Search Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the background color for the live-search container. Remove any selected image to use the background color.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Live Search Background Image
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_box_background_image]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_box_background_image'],
                'transport' => 'refresh',
                'sanitize_callback' => array($this, 'sanitize_image'),

            )
         );

        $wp_customize->add_control( new WP_Customize_Image_Control(
            $wp_customize, 
            'live_search_box_background_image_'. $kb_id, 
            array(
                'label'    => esc_html__('Background Image', 'incredibledocs'),
                'description' => esc_html__( 'Set the background image for the live-search container. It will overtake the background color.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_box_background_image]',
            )
        ));
        /*---------------------------------------*/
        // Live Search Box - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_box_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_box_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_box_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the border color for the live-search container.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[live_search_box_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/	 
        // Live Search Box - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_box_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_box_border_radius'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_box_border_radius', 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'description' => esc_html__( 'Set the border radius for the live-search container.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_box_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/	        
        // Live Search Box - Border Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_box_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_box_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_box_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'description' => esc_html__( 'Set the border width for the live-search container.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_box_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_box_margin_padding]', 
            array(
                'type' => 'option',
                'default'           => $this->defaults_design_options['live_search_box_margin_padding'],
                'transport' => 'refresh',
            ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'live_search_box_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the margin and padding for the live-search container.', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default margin and padding for the live search container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_live_search_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[live_search_box_margin_padding]',

        ) ) );

        /*---------------------------------------*/
        
        /*---------------------------------------*/
        // Live Search Title Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_title_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'live_search_title_notice_'. $kb_id,
            array(
                'label' => __( 'Search Main Title' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[live_search_title_notice]',
            )
        ) );
        /*---------------------------------------*/
        // Live Search Title Text
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_title_text]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['live_search_title_text'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            )
            );
 
        $wp_customize->add_control( new WP_Customize_Control( 
            $wp_customize, 
            'live_search_title_text_'. $kb_id,
            array(
                'label' => esc_html__( 'Title Text' , 'incredibledocs'),
                'description' => esc_html__( 'Set the text content for the live-search main title.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_title_text]',
                'type' => 'text',
            )
        ) );
        /*---------------------------------------*/
        // Live Search - Title Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'description' => esc_html__( 'Set the text font size for the live-search main title.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    
        /*---------------------------------------*/
        // Live Search - Font Weight
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_title_font_weight]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_title_font_weight'],
                'transport' => 'refresh',
            )
         );
         $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_title_font_weight_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Weight', 'incredibledocs'),
                'description' => esc_html__( 'Set the text font weight for the live-search main title.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_title_font_weight]',
                
                'input_attrs' => array(
                    'min' => 100, // Required. Minimum value for the slider
                    'max' => 900, // Required. Maximum value for the slider
                    'step' => 100, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 
        /*---------------------------------------*/
        // Live Search - Title Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_title_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_title_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_title_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the text color for the live-search main title.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_title_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        $wp_customize->add_setting('idocs_design_options_'. $kb_id. '[live_search_title_padding_top]', 
            
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' 			=> $this->defaults_design_options['live_search_title_padding_top'],
                'transport' => 'refresh',
            ));
    
        $wp_customize->add_control(new IDOCS_Customizer_Dimension(
                $wp_customize,
                'live_search_title_padding_top_'. $kb_id,
                
                array(

                    'label' => __('Top Padding (px)', 'incredibledocs'),
                    'description' => esc_html__( 'Set the top-padding for the live-search main title.', 'incredibledocs' ),
                    'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                    'settings' => 'idocs_design_options_'. $kb_id .'[live_search_title_padding_top]',
               
                ))
        );
        /*---------------------------------------*/
        $wp_customize->add_setting('idocs_design_options_'. $kb_id. '[live_search_title_padding_bottom]', 
            
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' 			=> $this->defaults_design_options['live_search_title_padding_bottom'],
                'transport' => 'refresh',
            ));
    
        $wp_customize->add_control(new IDOCS_Customizer_Dimension(
                $wp_customize,
                'live_search_title_padding_bottom_'. $kb_id,
                
                array(

                    'label' => __('Bottom Padding (px)', 'incredibledocs'),
                    'description' => esc_html__( 'Set the bottom-padding for the live-search main title.', 'incredibledocs' ),
                    'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                    'settings' => 'idocs_design_options_'. $kb_id. '[live_search_title_padding_bottom]',
               
                ))
        );
        /*---------------------------------------*/
        // Live Search Sub-Title
        /*---------------------------------------*/        
        // Live Search Sub-Title Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_sub_title_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'live_search_sub_title_heading_'. $kb_id,
            array(
                'label' => __( 'Search Sub-Title' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[live_search_sub_title_notice]',
            )
        ) );
        /*---------------------------------------*/
        //  KB View Box - Show Sub-Title (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_sub_title_show]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['live_search_sub_title_show'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'live_search_sub_title_show_'. $kb_id,
            array(
                'label' => esc_html__( 'Sub-Title' , 'incredibledocs'),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_sub_title_show]',
            )
        ) );
        /*---------------------------------------*/
        // Live Search Sub-Title Text
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_sub_title_text]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['live_search_sub_title_text'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            )
            );
 
        $wp_customize->add_control( new WP_Customize_Control( 
            $wp_customize, 
            'live_search_sub_title_text_'. $kb_id,
            array(
                'label' => esc_html__( 'Sub-Title Text', 'incredibledocs' ),
                'description' => esc_html__( 'Set the text content for the live-search sub-title.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_sub_title_text]',
                'type' => 'text',
            )
        ) );
        /*---------------------------------------*/
        // Live Search - Title Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[live_search_sub_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_sub_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_sub_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'description' => esc_html__( 'Set the text font size for the live-search sub-title.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_sub_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    
        /*---------------------------------------*/
        // Live Search - Title Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[live_search_sub_title_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_sub_title_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_sub_title_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the text color for the live-search sub-title.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_sub_title_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Live Search - Title Top Padding
        $wp_customize->add_setting('idocs_design_options_'. $kb_id . '[live_search_sub_title_padding_top]', 
            
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' 			=> $this->defaults_design_options['live_search_sub_title_padding_top'],
                'transport' => 'refresh',
            ));
    
        $wp_customize->add_control(new IDOCS_Customizer_Dimension(
                $wp_customize,
                'live_search_sub_title_padding_top_'. $kb_id,
                
                array(

                    'label' => __('Top Padding (px)', 'incredibledocs'),
                    'description' => esc_html__( 'Set the top-padding for the live-search sub-title.', 'incredibledocs' ),
                    'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                    'settings' => 'idocs_design_options_'. $kb_id. '[live_search_sub_title_padding_top]',
               
                ))
        );
        
        /*---------------------------------------*/
        // Live Search - Title Bottom Padding
        $wp_customize->add_setting('idocs_design_options_'. $kb_id .'[live_search_sub_title_padding_bottom]', 
            
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' 			=> $this->defaults_design_options['live_search_sub_title_padding_bottom'],
                'transport' => 'refresh',
            ));
    
        $wp_customize->add_control(new IDOCS_Customizer_Dimension(
                $wp_customize,
                'live_search_sub_title_padding_bottom_'. $kb_id,
                
                array(

                    'label' => __('Bottom Padding (px)', 'incredibledocs'),
                    'description' => esc_html__( 'Set the bottom-padding for the live-search sub-title.', 'incredibledocs' ),
                    'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                    'settings' => 'idocs_design_options_'. $kb_id. '[live_search_sub_title_padding_bottom]',
               
                ))
        );
        /*---------------------------------------*/
        // Live Search Input-Output Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_input_output_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
  
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'live_search_input_output_notice_'. $kb_id,
            array(
                'label' => __( 'Search Input-Output Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_input_output_notice]',
                //'priority' => 1,
            )
        ) );
         /*---------------------------------------*/
        //  Live Search Input Output Box - Width Slider
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_input_output_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_input_output_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_input_output_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (%)', 'incredibledocs'),
                'description' => esc_html__( 'Set the width for the live-search input-output box.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_input_output_width]',
                
                'input_attrs' => array(
                    'min' => 20, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));

        
        // Live Search Input Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_input_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_input_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

            )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_input_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the background color for the live-search input-output box.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[live_search_input_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
    
        
        /*---------------------------------------*/
        // Live Search Input Box - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[live_search_input_box_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_input_box_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_input_box_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the text color for the live-search input-output box.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_input_box_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Live Search Input Field - Text Placeholder Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_input_box_text_placeholder_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_input_box_text_placeholder_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_input_box_text_placeholder_color_'. $kb_id,
            array(
                'label' => esc_html__('Placeholder Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the color for the live search input-text placeholder .', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_input_box_text_placeholder_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        
        // Live Search Input - Search Placeholder
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_input_search_placeholder]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['live_search_input_search_placeholder'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            )
            );
 
        $wp_customize->add_control( new WP_Customize_Control( 
            $wp_customize, 
            'live_search_input_search_placeholder_'. $kb_id,
            array(
                'label' => esc_html__( 'Search Placeholder Text', 'incredibledocs' ),
                'description' => esc_html__( 'Set the text for the live search placeholder.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_input_search_placeholder]',
                'type' => 'text',
            )
        ) );
        // Live Search Input Box Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_input_box_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_input_box_font_size'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_input_box_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'description' => esc_html__( 'Set the font size for the live search input.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_input_box_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*********************************/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_input_box_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['live_search_input_box_padding'],
            'transport' => 'refresh',
          
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'live_search_input_box_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the padding for the live search input container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_live_search_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[live_search_input_box_padding]',

        ) ) );
        /*********************************/
        // Live Search Input Box - Border Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_input_box_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_input_box_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_input_box_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'description' => esc_html__( 'Set the border width for the live search input container.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_input_box_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));     

        // Live Search Input Box Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_input_box_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_input_box_border_radius'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_input_box_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'description' => esc_html__( 'Set the border radius for the live search input container.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[live_search_input_box_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    

        // Live Search Input Box - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_input_box_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_input_box_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_input_box_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the border color for the live search input container.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_input_box_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Live Search Input Bar  - Width Slider
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_input_bar_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_input_bar_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_input_bar_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Input Bar Width (%)', 'incredibledocs'),
                'description' => esc_html__( 'Set the width for the live search input container.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_input_bar_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        
        
        // Live Search Result Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[live_search_result_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'live_search_result_title_'. $kb_id,
            array(
                'label' => __( 'Search Result Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_result_notice]',
                //'priority' => 1,
            )
        ) );
       
        // Live Search No Result Feedback
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_no_result_feedback]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['live_search_no_result_feedback'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            )
            );
 
        $wp_customize->add_control( new WP_Customize_Control( 
            $wp_customize, 
            'live_search_no_result_feedback_'. $kb_id,
            array(
                'label' => esc_html__( 'No Result Feedback Text' , 'incredibledocs'),
                'description' => esc_html__( 'Set the text content for the no-result scenario.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_no_result_feedback]',
                'type' => 'text',
            )
        ) );

        //  Live Search Result Order Alphabetically
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_result_order_alphabetically]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['live_search_result_order_alphabetically'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'live_search_result_order_alphabetically_'. $kb_id,
            array(
                'label' => esc_html__( 'Order Results Alphabetically', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_result_order_alphabetically]',
            )
        ) );

        /*
        // Live Search Result - Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_result_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_result_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_result_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (%)', 'incredibledocs'),
                'description' => esc_html__( 'Set width for the search result container.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_result_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    
        */
        /*---------------------------------------*/
        // Live Search Result - Height
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_result_height]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_result_height'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_result_height_'. $kb_id, 
            array(
                'label'    => esc_html__('Height (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_result_height]',

                'input_attrs' => array(
                    'min' => 100, // Required. Minimum value for the slider
                    'max' => 300, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  
        /*---------------------------------------*/

        // Live Search Result - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_result_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_result_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_result_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'description' => esc_html__( 'Set background color for the search result container.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_result_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Live Search Result - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_result_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_result_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_result_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'description' => esc_html__( 'Set border color for the search result container.', 'incredibledocs' ),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_result_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Live Search Result Item Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[live_search_result_item_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'live_search_result_item_notice_'. $kb_id,
            array(
                'label' => __( 'Search Result Item' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_item_notice]',
                //'priority' => 1,
            )
        ) );

        // Live Search Result Item Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_result_item_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_result_item_font_size'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_result_item_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'description' => esc_html__( 'Set font size for the search result items.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_result_item_font_size]',

                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 

        // Live Search Result Item Icon Size
        
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_result_item_icon_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_result_item_icon_size'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_result_item_icon_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Icon Size (rem)', 'incredibledocs'),
                'description' => esc_html__( 'Set icon size for the search result items.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_result_item_icon_size]',

                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 

        // Live Search Result Item - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_result_item_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_result_item_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_result_item_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'description' => esc_html__( 'Set text color for the search result items.', 'incredibledocs' ),

                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_item_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Live Search Result Item - Icon Color
       
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_result_item_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_result_item_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_result_item_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'description' => esc_html__( 'Set text color for the search result items.', 'incredibledocs' ),

                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_item_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

         // Live Search Result Item - Text Hover Color
         $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_result_item_text_hover_color]',
         array(
             'type' => 'option',
             'capability'    => 'manage_options',
             'default' => $this->defaults_design_options['live_search_result_item_text_hover_color'],
             'transport' => 'refresh',
             'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

         )
         );
 
         $wp_customize->add_control( new IDOCS_Customizer_Color( 
             $wp_customize, 
             'live_search_result_item_text_hover_color_'. $kb_id,
             array(
                 'label' => esc_html__('Text Hover Color', 'incredibledocs'),
                 'description' => esc_html__( 'Set text hover color for the search result items.', 'incredibledocs' ),

                 'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                 'settings' => 'idocs_design_options_'. $kb_id .'[live_search_result_item_text_hover_color]',
                 'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                 )
             )
         );

         // Live Search Result Item - Background Hover Color
         $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_result_item_background_hover_color]',
         array(
             'type' => 'option',
             'capability'    => 'manage_options',
             'default' => $this->defaults_design_options['live_search_result_item_background_hover_color'],
             'transport' => 'refresh',
             'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

         )
         );
 
         $wp_customize->add_control( new IDOCS_Customizer_Color( 
             $wp_customize, 
             'live_search_result_item_background_hover_color_'. $kb_id,
             array(
                 'label' => esc_html__('Background Hover Color', 'incredibledocs'),
                 'description' => esc_html__( 'Set background color for the search result items.', 'incredibledocs' ),
                 'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                 'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_item_background_hover_color]',
                 'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                 )
             )
         );
        

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_result_item_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['live_search_result_item_padding'],
            'transport' => 'refresh',
          
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'live_search_result_item_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set padding for the search result items.', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the live search result item container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_live_search_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_item_padding]',
        ) ) );

        // Live Search Result Content Filter Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[live_search_result_content_filter_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'live_search_result_content_filter_notice_'. $kb_id,
            array(
                'label' => __( 'Result Content Filter (Pro)' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_content_filter_notice]',
                //'priority' => 1,
            )
        ) );
    
        // Live Search Content Filter - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_result_content_filter_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_result_content_filter_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_result_content_filter_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'description' => esc_html__( 'Set text color for the search result content filters.', 'incredibledocs' ),

                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_content_filter_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Live Search Content Filter - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_result_content_filter_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_result_content_filter_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_result_content_filter_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'description' => esc_html__( 'Set background color for the search result content filters.', 'incredibledocs' ),

                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_content_filter_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        
        // Live Search Content Filter - Background Hover Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_result_content_filter_background_hover_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_result_content_filter_background_hover_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_result_content_filter_background_hover_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Hover Color', 'incredibledocs'),
                'description' => esc_html__( 'Set background hover color for the search result content filters.', 'incredibledocs' ),

                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_content_filter_background_hover_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        
        // Live Search Content Filter - Icon Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_result_content_filter_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['live_search_result_content_filter_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'live_search_result_content_filter_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'description' => esc_html__( 'Set icon color for the search result content filters.', 'incredibledocs' ),

                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_content_filter_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

                 

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[live_search_result_content_filter_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['live_search_result_content_filter_padding'],
            'transport' => 'refresh',
          
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'live_search_result_content_filter_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set padding for the search result items.', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the live search result content filters.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_live_search_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[live_search_result_content_filter_padding]',
        ) ) );

        /**************************************/
        // Live Search Sensitivity Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[live_search_sensitivity_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'live_search_sensitivity_notice_'. $kb_id,
            array(
                'label' => __( 'Search Sensitivity' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_sensitivity_notice]',
                //'priority' => 1,
            )
        ) );

        // Live Search Sensitivity Warning Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[live_search_sensitivity_warning_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_WarningNotice( 
            $wp_customize, 
            'live_search_sensitivity_warning_notice_'. $kb_id,
            array(
                'label' => __( 'Please note! page must be reloaded outside the customizer after making changes to the following parameters.' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[live_search_sensitivity_warning_notice]',
                //'priority' => 1,
            )
        ) );

       
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_min_amount_characters_for_search]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['live_search_min_amount_characters_for_search'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'live_search_min_amount_characters_for_search_'. $kb_id, 
            array(
                'label'    => esc_html__('Minimum Input Size for Search', 'incredibledocs'),
                'description' => esc_html__( 'Set the minimum amount of input characters for triggering search.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[live_search_min_amount_characters_for_search]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 6, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 
        
         // Live Search Result - Width
         $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[live_search_keystroke_delay_before_search]' , 
         array(
             'type' => 'option',
             'capability'    => 'manage_options',
             'default'     => $this->defaults_design_options['live_search_keystroke_delay_before_search'],
             'transport' => 'refresh',
         )
      );

     $wp_customize->add_control( new IDOCS_Customizer_Slider(
         $wp_customize, 
         'live_search_keystroke_delay_before_search_'. $kb_id, 
         array(
             'label'    => esc_html__('Keystroke Delay before Search (ms)', 'incredibledocs'),
             'description' => esc_html__( 'Set the keystock delay time in milliseconds before triggering search.', 'incredibledocs' ),
             'section'  => 'idocs_customizer_live_search_section_'. $kb_id,
             'settings' => 'idocs_design_options_'. $kb_id .'[live_search_keystroke_delay_before_search]',
             
             'input_attrs' => array(
                 'min' => 100, // Required. Minimum value for the slider
                 'max' => 1500, // Required. Maximum value for the slider
                 'step' => 100, // Required. The size of each interval or step the slider takes between the minimum and maximum values
             ),
         )
     ));
    }
    /*---------------------------------------------------------------------------------------*/
    private function categories_cards_section( $wp_customize, $kb_id ) {

        /*---------------------------------------*/	
        // Categories Box - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[categories_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'categories_box_notice_'. $kb_id,
            array(
                'label' => __( 'Categories Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[categories_box_notice]',
            )
        ) );

        /*---------------------------------------*/
        
        // Categories Box - Number of Columns
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[categories_box_num_columns]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['categories_box_num_columns'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',

            )
         );

        $wp_customize->add_control( 'categories_box_num_columns_'. $kb_id, 
            array(
                'label'    => esc_html__('Number of Columns', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[categories_box_num_columns]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(
                    'value1' => 1,
                    'value2' => 2,
                    'value3' => 3,
                    'value4' => 4,
                    'value5' => 5,
                ),
            )
        );
        /*---------------------------------------*/
        //Categories Box - Hide Empty Categories (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[categories_box_hide_empty_categories]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['categories_box_hide_empty_categories'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'categories_box_hide_empty_categories_'. $kb_id,
            array(
                'label' => esc_html__( 'Hide Empty Categories' , 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[categories_box_hide_empty_categories]',
            )
        ) );
        /*---------------------------------------*/
        //Categories Box - Animated Categories (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[categories_box_animated_categories]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['categories_box_animated_categories'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'categories_box_animated_categories_'. $kb_id,
            array(
                'label' => esc_html__( 'Animated Categories', 'incredibledocs' ),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[categories_box_animated_categories]',
            )
        ) );
        /*---------------------------------------*/
        // Categories Box - Cards Order By
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[categories_box_cards_order_by]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['categories_box_cards_order_by'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',

            )
         );

        $wp_customize->add_control( 'categories_box_cards_order_by_'. $kb_id, 
            array(
                'label'    => esc_html__('Cards Order By', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[categories_box_cards_order_by]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(

                    'name'   		   => 'Alphabetical by Name',
					'category_order'   => 'Configured Category Order',

                ),
            )
        );
        /*---------------------------------------*/	
        // Categories Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[categories_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['categories_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'categories_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[categories_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        	
        // Categories Box Background Image
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[categories_box_background_image]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['categories_box_background_image'],
                'transport' => 'refresh',
                'sanitize_callback' => array($this, 'sanitize_image'),

            )
         );


        $wp_customize->add_control( new WP_Customize_Image_Control(
            $wp_customize, 
            'categories_box_background_image_'. $kb_id, 
            array(
                'label'    => esc_html__('Background Image', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[categories_box_background_image]',
            )
        ));
        /*---------------------------------------*/	
        
        // Categories Box - Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[categories_box_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['categories_box_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'categories_box_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (%)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[categories_box_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/	
        
        // Categories Box - Spacing between Cards
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[categories_box_spacing_between_cards]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['categories_box_spacing_between_cards'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'categories_box_spacing_between_cards_'. $kb_id, 
            array(
                'label'    => esc_html__('Space between Cards', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[categories_box_spacing_between_cards]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 5, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/	
        // Categories Box - Minimum Height
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[categories_box_minimum_height]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['categories_box_minimum_height'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'categories_box_minimum_height_'. $kb_id, 
            array(
                'label'    => esc_html__('Minimum Height (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[categories_box_minimum_height]',
                
                'input_attrs' => array(
                    'min' => 100, // Required. Minimum value for the slider
                    'max' => 1000, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[categories_box_margin_padding]', 
            array(
                'type' => 'option',
                'default'           => $this->defaults_design_options['categories_box_margin_padding'],
                'transport' => 'refresh',
            ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'categories_box_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the margin and padding for the for the categories container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_categories_cards_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[categories_box_margin_padding]',

        ) ) );

        /*
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[categories_box_padding]', 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'           => $this->defaults_design_options['categories_box_padding'],
                'transport' => 'refresh',
            ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'categories_box_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the categories container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_categories_cards_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[categories_box_padding]',

        ) ) );
        */
        /*---------------------------------------*/	
        // Categories Card - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[category_card_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_card_notice_'. $kb_id,
            array(
                'label' => __( 'Category Card' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_notice]',
            )
        ) );
        /*---------------------------------------*/
        
        

        /*---------------------------------------*/
        //  Category Card - Document Order By
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_documents_order_by]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_card_documents_order_by'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',

            )
         );

        $wp_customize->add_control( 'category_card_documents_order_by_'. $kb_id, 
            array(
                'label'    => esc_html__('Content Order By', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_documents_order_by]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(

                    'title'   		     => 'Alphabetical by Title',
					'created_date'       => 'Created Date',
					'last_modified_date' => 'Last Modified Date',
                    'custom_display_order' => 'Custom Display Order',

                ),
            )
        );
        
        /*---------------------------------------*/	
        // Categories Card - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_card_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_card_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Categories Card - Hover Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_hover_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_card_hover_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        ));

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_card_hover_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_hover_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        /*---------------------------------------*/
        // Category Card - Hover Transition Effect (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_hover_transition_effect]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['category_card_hover_transition_effect'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'category_card_hover_transition_effect_'. $kb_id,
            array(
                'label' => esc_html__( 'Hover Transition Effect', 'incredibledocs' ),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_hover_transition_effect]',
            )
        ) );
        /*---------------------------------------*/
        // Category Card - Show Shadow (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_show_shadow]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['category_card_show_shadow'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'category_card_show_shadow_'. $kb_id,
            array(
                'label' => esc_html__( 'Card Shadow', 'incredibledocs' ),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_show_shadow]',
            )
        ) );
        // Categories Card - Shadow Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_shadow_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_card_shadow_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        ));

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_card_shadow_color_'. $kb_id,
            array(
                'label' => esc_html__('Shadow Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_shadow_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Categories Card - Hover Shadow Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_hover_shadow_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_card_hover_shadow_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        ));

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_card_hover_shadow_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Shadow Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_hover_shadow_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Categories Card - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_card_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_card_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Categories Card - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_card_border_radius'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_card_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        
        /*---------------------------------------*/ 
        // Categories Card - Border Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_card_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_card_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    
        /*---------------------------------------*/	
        // Categories Card - Height
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_height]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_card_height'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_card_height_'. $kb_id, 
            array(
                'label'    => esc_html__('Height (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_height]',
                
                'input_attrs' => array(
                    'min' => 100, // Required. Minimum value for the slider
                    'max' => 300, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_padding]', array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default'       => $this->defaults_design_options['category_card_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'category_card_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the category card container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_categories_cards_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[category_card_padding]',
        ) ) );
        
        // Categories Icon - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_icon_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_icon_notice_'. $kb_id,
            array(
                'label' => __( 'Category Card Icon' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_icon_notice]',
            )
        ) );

        // Category Title Icon - Show (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_title_icon_show]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['category_title_icon_show'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'category_title_icon_show_'. $kb_id,
            array(
                'label' => esc_html__( 'Icon' , 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_icon_show]',
            )
        ) );
       
        // Categories Title - Icon Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_icon_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_title_icon_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_title_icon_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Icon Size (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_icon_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 80, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   

        // Categories Title - Icon Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_title_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_title_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );


        // Categories Title - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_title_notice_'. $kb_id,
            array(
                'label' => __( 'Category Card Title' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_notice]',
            )
        ) );

        // Categories Title - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        
        // Categories Title - Text Alignment
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_title_text_alignment]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_title_text_alignment'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',

                )
         );

        $wp_customize->add_control( 'category_title_text_alignment_'. $kb_id, 
            array(
                'label'    => esc_html__('Text Alignment', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_text_alignment]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(
                    'value1' => 'Left',
                    'value2' => 'Centered',
                    'value3' => 'Right',
                ),
            )
        );

        // Categories Title - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_title_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_title_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
      
        // Categories Title Counter - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_counter_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_title_counter_notice_'. $kb_id,
            array(
                'label' => __( 'Category Card Counters' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_counter_notice]',
            )
        ) );
        

        // Category Title - Show Counter (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_show_counter]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['category_title_show_counter'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'category_title_show_counter_'. $kb_id,
            array(
                'label' => esc_html__( 'Content Counters', 'incredibledocs' ),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_show_counter]',
            )
        ) );

        // Category Title - Counter Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_title_counter_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_title_counter_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_title_counter_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_counter_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Category Title - Counter Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_title_counter_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_title_counter_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_title_counter_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_counter_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Categories Title Counter - Text Alignment
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_counter_text_alignment]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_title_counter_text_alignment'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',

            )
         );

        $wp_customize->add_control( 'category_title_counter_text_alignment_'. $kb_id, 
            array(
                'label'    => esc_html__('Text Alignment', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[category_title_counter_text_alignment]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(
                    'value1' => 'Left',
                    'value2' => 'Centered',
                    'value3' => 'Right',
                ),
            )
        );

        // Category Title Counter - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_counter_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_title_counter_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_title_counter_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_counter_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/
        // Category Description - Notice 
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_description_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_description_notice_'. $kb_id,
            array(
                'label' => __( 'Category Description' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_description_notice]',
            )
        ) );
        /*---------------------------------------*/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_detailed_layout]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['category_card_detailed_layout'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'category_card_detailed_layout_'. $kb_id,
            array(
                'label' => esc_html__( 'Category Description (Main Cat.)' , 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_detailed_layout]',
            )
        ) );

        // Category Description - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_description_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_description_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_description_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_description_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 5, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/
        // Category Description Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_description_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_description_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_description_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_description_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Categories Content Item - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_content_item_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_content_item_notice_'. $kb_id,
            array(
                'label' => __( 'Category Content Item' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_notice]',
            )
        ) );
        /*---------------------------------------*/
        

        /*---------------------------------------*/

        // Category Content Item - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_content_item_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_content_item_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_content_item_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/
        // Categories Title - Icon Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_content_item_icon_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_content_item_icon_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_content_item_icon_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Icon Size (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_icon_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 80, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/        
        // Category Content Item - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_content_item_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_content_item_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_content_item_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Category Content Item - Icon Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_content_item_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_content_item_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_content_item_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Category Content Item - Text Hover Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_content_item_text_hover_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_content_item_text_hover_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_content_item_text_hover_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Hover Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_text_hover_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Category Content Item - Background Hover Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_content_item_background_hover_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_content_item_background_hover_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_content_item_background_hover_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Hover Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_background_hover_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_content_item_padding]', array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default'       => $this->defaults_design_options['category_content_item_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'category_content_item_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the category card container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_categories_cards_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[category_content_item_padding]',
        ) ) );
        /*---------------------------------------*/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_icon_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_icon_notice_'. $kb_id,
            array(
                'label' => __( 'Category Card Icon' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_icon_notice]',
            )
        ) );

        // Category Title Icon - Show (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_title_icon_show]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['category_title_icon_show'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'category_title_icon_show_'. $kb_id,
            array(
                'label' => esc_html__( 'Icon' , 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_icon_show]',
            )
        ) );
       
        // Categories Title - Icon Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_icon_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_title_icon_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_title_icon_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Icon Size (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_icon_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 80, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   

        // Categories Title - Icon Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_title_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_title_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );


        // Categories Title - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_title_notice_'. $kb_id,
            array(
                'label' => __( 'Category Card Title' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_notice]',
            )
        ) );

        // Categories Title - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        
        // Categories Title - Text Alignment
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_title_text_alignment]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_title_text_alignment'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',

                )
         );

        $wp_customize->add_control( 'category_title_text_alignment_'. $kb_id, 
            array(
                'label'    => esc_html__('Text Alignment', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_text_alignment]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(
                    'value1' => 'Left',
                    'value2' => 'Centered',
                    'value3' => 'Right',
                ),
            )
        );

        // Categories Title - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_title_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_title_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
      
        // Categories Title Counter - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_counter_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_title_counter_notice_'. $kb_id,
            array(
                'label' => __( 'Category Card Counters' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_counter_notice]',
            )
        ) );
        

        // Category Title - Show Counter (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_show_counter]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['category_title_show_counter'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'category_title_show_counter_'. $kb_id,
            array(
                'label' => esc_html__( 'Content Counters', 'incredibledocs' ),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_show_counter]',
            )
        ) );

        // Category Title - Counter Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_title_counter_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_title_counter_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_title_counter_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_counter_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Category Title - Counter Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_title_counter_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_title_counter_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_title_counter_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_counter_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Categories Title Counter - Text Alignment
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_counter_text_alignment]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_title_counter_text_alignment'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',

            )
         );

        $wp_customize->add_control( 'category_title_counter_text_alignment_'. $kb_id, 
            array(
                'label'    => esc_html__('Text Alignment', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[category_title_counter_text_alignment]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(
                    'value1' => 'Left',
                    'value2' => 'Centered',
                    'value3' => 'Right',
                ),
            )
        );

        // Category Title Counter - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_title_counter_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_title_counter_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_title_counter_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_title_counter_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/
        // Category Description - Notice 
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_description_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_description_notice_'. $kb_id,
            array(
                'label' => __( 'Category Description' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_description_notice]',
            )
        ) );
        /*---------------------------------------*/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_card_detailed_layout]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['category_card_detailed_layout'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'category_card_detailed_layout_'. $kb_id,
            array(
                'label' => esc_html__( 'Category Description (Main Cat.)' , 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_card_detailed_layout]',
            )
        ) );

        // Category Description - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_description_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_description_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_description_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_description_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 5, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/
        // Category Description Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_description_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_description_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_description_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_description_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Categories Content Item - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_content_item_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'category_content_item_notice_'. $kb_id,
            array(
                'label' => __( 'Category Content Item' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_notice]',
            )
        ) );
        /*---------------------------------------*/
        

        /*---------------------------------------*/

        // Category Content Item - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_content_item_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_content_item_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_content_item_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/
        // Categories Title - Icon Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_content_item_icon_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['category_content_item_icon_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'category_content_item_icon_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Icon Size (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_icon_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 80, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/        
        // Category Content Item - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_content_item_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_content_item_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_content_item_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Category Content Item - Icon Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_content_item_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_content_item_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_content_item_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Category Content Item - Text Hover Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_content_item_text_hover_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_content_item_text_hover_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_content_item_text_hover_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Hover Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_text_hover_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Category Content Item - Background Hover Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[category_content_item_background_hover_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['category_content_item_background_hover_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'category_content_item_background_hover_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Hover Color', 'incredibledocs'),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[category_content_item_background_hover_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[category_content_item_padding]', array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default'       => $this->defaults_design_options['category_content_item_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'category_content_item_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the category card container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_categories_cards_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[category_content_item_padding]',
        ) ) );

        /*---------------------------------------*/
        // Sub Categories Box - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[sub_categories_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'sub_categories_box_notice_'. $kb_id,
            array(
                'label' => __( 'Sub Categories Box' ),
                //'description' => __(''),
                'section'     => 'idocs_customizer_categories_cards_section_'. $kb_id,
                
                'settings' => 'idocs_design_options_'. $kb_id .'[sub_categories_box_notice]',
            )
        ) );
        /*---------------------------------------*/
        // Sub Categories Box Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[sub_categories_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['sub_categories_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'sub_categories_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section'     => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[sub_categories_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Sub Categories Box - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[sub_categories_box_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['sub_categories_box_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'sub_categories_box_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section'     => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[sub_categories_box_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Sub Categories Box - Show Shadow (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[sub_categories_box_show_shadow]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['sub_categories_box_show_shadow'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'sub_categories_box_show_shadow_'. $kb_id,
            array(
                'label' => esc_html__( 'Box Shadow', 'incredibledocs' ),
                'section' => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[sub_categories_box_show_shadow]',
            )
        ) );
        /*---------------------------------------*/
        // Sub Categories Box - Shadow Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[sub_categories_box_shadow_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['sub_categories_box_shadow_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'sub_categories_box_shadow_color_'. $kb_id,
            array(
                'label' => esc_html__('Shadow Color', 'incredibledocs'),
                'section'     => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[sub_categories_box_shadow_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Sub Categories Box - Border Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[sub_categories_box_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['sub_categories_box_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'sub_categories_box_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[sub_categories_box_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  
        /*---------------------------------------*/
       
        // Sub Categories Box - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[sub_categories_box_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['sub_categories_box_border_radius'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'sub_categories_box_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_categories_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[sub_categories_box_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[sub_categories_box_padding]', array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default'       => $this->defaults_design_options['sub_categories_box_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'sub_categories_box_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the category card container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_categories_cards_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[sub_categories_box_padding]',
        ) ) ); 
        /*---------------------------------------*/

    }
    /*---------------------------------------------------------------------------------------*/
    private function faqs_section( $wp_customize, $kb_id ) {

        /*---------------------------------------*/	
        // FAQs Box - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'faqs_box_notice_'. $kb_id,
            array(
                'label' => __( 'FAQs Box' ),
                //'description' => __(''),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_box_notice]',
            )
        ) );
        /*---------------------------------------*/
        // FAQs Box - Lock Root FAQs (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_box_lock_root_faqs]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['faqs_box_lock_root_faqs'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'faqs_box_lock_root_faqs_'. $kb_id,
            array(
                'label' => esc_html__( 'Lock Root FAQs', 'incredibledocs' ),
                'section' => 'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_box_lock_root_faqs]',
            )
        ) );
        /*---------------------------------------*/	
        // FAQs BOX - Title Text
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_box_title_text]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['faqs_box_title_text'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            )
            );
 
        $wp_customize->add_control( new WP_Customize_Control( 
            $wp_customize, 
            'faqs_box_title_text_'. $kb_id,
            array(
                'label' => esc_html__( 'Title Text', 'incredibledocs' ),
                'section' => 'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_box_title_text]',
                'type' => 'text',
            )
        ) );
        /*---------------------------------------*/
        // FAQs BOX - Title Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqs_box_title_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['faqs_box_title_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'faqs_box_title_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_box_title_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // FAQs BOX - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqs_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['faqs_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'faqs_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // FAQs Box - Title Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_box_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['faqs_box_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'faqs_box_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_box_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  
        /*---------------------------------------*/
        // FAQs Box - Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_box_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['faqs_box_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'faqs_box_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (%)', 'incredibledocs'),
                'section'  => 'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_box_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   
        /*---------------------------------------*/
        // FAQs Box - Margin and Padding
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqs_box_margin_padding]', 
            array(
                'type' => 'option',
                'default'           => $this->defaults_design_options['faqs_box_margin_padding'],
                'transport' => 'refresh',
            ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'faqs_box_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default margin and padding for the FAQs Box container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     =>  'idocs_customizer_faqs_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[faqs_box_margin_padding]',

        ) ) );
        /*---------------------------------------*/
        // FAQs Group Title - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_group_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'faqs_group_notice_'. $kb_id,
            array(
                'label' => __( 'FAQs Group Title' ),
                //'description' => __(''),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_group_notice]',
            )
        ) );
        /*---------------------------------------*/
        // FAQs Group - Title Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqs_group_title_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['faqs_group_title_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'faqs_group_title_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_group_title_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // FAQs Group - Title Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_group_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['faqs_group_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'faqs_group_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_group_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  
        /*---------------------------------------*/
        // FAQs Group - Title Padding
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_group_title_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['faqs_group_title_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'faqs_group_title_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the FAQs Group container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     =>  'idocs_customizer_faqs_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[faqs_group_title_padding]',

        ) ) );
        /*---------------------------------------*/
       
        // FAQs Item Title - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_item_title_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'faqs_item_title_notice_'. $kb_id,
            array(
                'label' => __( 'FAQs Item Title' ),
                //'description' => __(''),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_item_title_notice]',
            )
        ) );
        /*---------------------------------------*/
        // FAQs Item - Title Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqs_item_title_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['faqs_item_title_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'faqs_item_title_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_item_title_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // FAQs Item - Title Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_item_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['faqs_item_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'faqs_item_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_item_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  
        /*---------------------------------------*/
        // FAQs Item - Title Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqs_item_title_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['faqs_item_title_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'faqs_item_title_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_item_title_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // FAQs Item - Title Hover Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqs_item_title_hover_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['faqs_item_title_hover_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'faqs_item_title_hover_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Background Color', 'incredibledocs'),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_item_title_hover_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        /*---------------------------------------*/
        // FAQs Item - Title Padding
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_item_title_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['faqs_item_title_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'faqs_item_title_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the FAQs Item container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     =>  'idocs_customizer_faqs_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[faqs_item_title_padding]',
        ) ) );
        /*---------------------------------------*/
       
        // FAQs Item Content - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_item_content_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'faqs_item_content_notice_'. $kb_id,
            array(
                'label' => __( 'FAQs Item Content' ),
                //'description' => __(''),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_item_content_notice]',
            )
        ) );

        // FAQs Item - Content Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqs_item_content_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['faqs_item_content_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'faqs_item_content_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_item_content_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // FAQs Item - Content Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqs_item_content_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['faqs_item_content_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'faqs_item_content_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  =>  'idocs_customizer_faqs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqs_item_content_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 
        /*---------------------------------------*/

    }
    /*---------------------------------------------------------------------------------------*/
    private function breadcrumbs_section( $wp_customize, $kb_id ) {

        // Breadcrumbs Box - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'breadcrumbs_box_notice_'. $kb_id,
            array(
                'label' => __( 'Breadcrumbs Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_notice]',
            )
        ) );
        /*---------------------------------------*/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_home_url]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['breadcrumbs_box_home_url'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_url',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_URL( 
            $wp_customize, 
            'breadcrumbs_box_home_url_'. $kb_id,
            array(
                'label' => esc_html__( 'Home URL', 'incredibledocs' ),
                'description' => esc_html__( 'Set the breadcrumbs home url (default is the current website).', 'incredibledocs' ),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_home_url]',
                'type' => 'text',
            )
        ) );        
        /*---------------------------------------*/
        // Breadcrumbs - Home Text
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_home_text]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['breadcrumbs_box_home_text'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            )
            );
 
        $wp_customize->add_control( new WP_Customize_Control( 
            $wp_customize, 
            'breadcrumbs_box_home_text_'. $kb_id,
            array(
                'label' => esc_html__( 'Home Text' , 'incredibledocs'),
                'description' => esc_html__( 'Set the text content for the breadcrumbs home button.', 'incredibledocs' ),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_home_text]',
                'type' => 'text',
            )
        ) );
        /*---------------------------------------*/        
        // Breadcrumbs Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[breadcrumbs_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['breadcrumbs_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'breadcrumbs_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the background color for the breadcrumbs constainer.', 'incredibledocs' ),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Breadcrumbs Box - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['breadcrumbs_box_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'breadcrumbs_box_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the border color for the breadcrumbs constainer.', 'incredibledocs' ),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
      

        // Categories Card - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['breadcrumbs_box_border_radius'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'breadcrumbs_box_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'description' => esc_html__( 'Set the border radius for the breadcrumbs constainer.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['breadcrumbs_box_border_width'],
                'transport' => 'refresh',
              //  'sanitize_callback' => 'sanitize_hex_image',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'breadcrumbs_box_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'description' => esc_html__( 'Set the border width for the breadcrumbs constainer.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[breadcrumbs_box_margin_padding]', 
            array(
                'type' => 'option',
                'default'           => $this->defaults_design_options['breadcrumbs_box_margin_padding'],
                'transport' => 'refresh',
            ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'breadcrumbs_box_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the margin and padding for the breadcrumbs container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[breadcrumbs_box_margin_padding]',

        ) ) );


        // Breadcrumbs Box - Separator Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_separator_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['breadcrumbs_box_separator_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

            )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'breadcrumbs_box_separator_color_'. $kb_id,
            array(
                'label' => esc_html__('Separator Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the color for the separator character between items.', 'incredibledocs' ),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_separator_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Breadcrumbs Box Separator - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_separator_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['breadcrumbs_box_separator_font_size'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'breadcrumbs_box_separator_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Separator Font Size (rem)', 'incredibledocs'),
                'description' => esc_html__( 'Set the font size for the separator character between items.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_separator_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 


        // Breadcrumbs Box Item - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'breadcrumbs_box_item_notice_'. $kb_id,
            array(
                'label' => __( 'Breadcrumbs Item' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_notice]',
            )
        ) );

        
        // Breadcrumbs Box Item - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['breadcrumbs_box_item_font_size'],
                'transport' => 'refresh',
             
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'breadcrumbs_box_item_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'description' => esc_html__( 'Set the font size for the breadcrumbs items.', 'incredibledocs' ),
                'section'  => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 

        // Breadcrumbs Box - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[breadcrumbs_box_item_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['breadcrumbs_box_item_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'breadcrumbs_box_item_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the text color for the breadcrumbs items.', 'incredibledocs' ),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Breadcrumbs Box - Text Hover Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_text_hover_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['breadcrumbs_box_item_text_hover_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'breadcrumbs_box_item_text_hover_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Hover Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the text hover color for the breadcrumbs items.', 'incredibledocs' ),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_text_hover_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        
        // Breadcrumbs Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['breadcrumbs_box_item_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'breadcrumbs_box_item_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the background color for the breadcrumbs items.', 'incredibledocs' ),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Breadcrumbs Box - Hover Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_hover_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['breadcrumbs_box_item_hover_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'breadcrumbs_box_item_hover_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Background Color', 'incredibledocs'),
                'description' => esc_html__( 'Set the hover background color for the breadcrumbs items.', 'incredibledocs' ),
                'section' => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[breadcrumbs_box_item_hover_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );


        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[breadcrumbs_box_item_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['breadcrumbs_box_item_padding'],
            'transport' => 'refresh',
          
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'breadcrumbs_box_item_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the padding for the breadcrumbs item container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_breadcrumbs_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[breadcrumbs_box_item_padding]',

        ) ) );
    }
    /*---------------------------------------------------------------------------------------*/
    private function sidebar_navigator_section( $wp_customize, $kb_id ) {

        // Navigation Box - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[navigation_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'navigation_box_notice_'. $kb_id,
            array(
                'label' => __( 'Sidebar Navigation Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_notice]',
            )
        ) );
        
        
        // Navigatio Box - Show Empty Categories (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_hide_empty_categories]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['navigation_box_hide_empty_categories'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'navigation_box_hide_empty_categories_'. $kb_id,
            array(
                'label' => esc_html__( 'Hide Empty Categories', 'incredibledocs' ),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_hide_empty_categories]',
            )
        ) );	

        // Navigatio Box - Only Current Category (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_only_current_category]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['navigation_box_only_current_category'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'navigation_box_only_current_category_'. $kb_id,
            array(
                'label' => esc_html__( 'Only Current Category', 'incredibledocs' ),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_only_current_category]',
            )
        ) );	



        //  Navigation Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Navigation Box - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        
        
        // Navigation Box - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_border_radius'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    

        // Navigation Box - Show Shadow (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_show_shadow]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['navigation_box_show_shadow'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'navigation_box_show_shadow_'. $kb_id,
            array(
                'label' => esc_html__( 'Box Shadow', 'incredibledocs' ),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_show_shadow]',
            )
        ) );

        // Categories Card - Shadow Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_shadow_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_shadow_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        ));

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_shadow_color_'. $kb_id,
            array(
                'label' => esc_html__('Shadow Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_shadow_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );




        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['navigation_box_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'navigation_box_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the navigation container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[navigation_box_padding]',
        ) ) );
        
        // Navigation Box Category - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'navigation_box_category_notice_'. $kb_id,
            array(
                'label' => __( 'Navigation Category' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_notice]',
            )
        ) );
        /*---------------------------------------*/	        
        //  Navigation Box Category - Show Category Icon (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_show_category_icon]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['navigation_box_show_category_icon'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'navigation_box_show_category_icon_'. $kb_id,
            array(
                'label' => esc_html__( 'Show Category Icon' , 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_show_category_icon]',
            )
        ) );
        /*---------------------------------------*/	
        //  Navigation Box Category - Show Counter (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_show_counter]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['navigation_box_category_show_counter'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'navigation_box_category_show_counter_'. $kb_id,
            array(
                'label' => esc_html__( 'Show Category Counter' , 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_show_counter]',
            )
        ) );
        /*---------------------------------------*/
    
        // Navigation Box Category - Icon Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_icon_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_category_icon_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_category_icon_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Icon Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_icon_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));          
        /*---------------------------------------*/	
        //  Navigation Box Category - Icon Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_category_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_category_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        /*---------------------------------------*/
        // Navigation Box Category Title - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_category_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_category_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Title Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));     

        // Navigation Box Category Title - Bold (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_title_bold]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['navigation_box_category_title_bold'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'navigation_box_category_title_bold_'. $kb_id,
            array(
                'label' => esc_html__( 'Title Bold', 'incredibledocs' ),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_title_bold]',
            )
        ) );

        //  Navigation Box Category Title - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_title_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_category_title_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_category_title_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Title Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_title_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Navigation Box Category Title - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_title_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_category_title_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
            )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_category_title_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Title Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_title_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Navigation Category Counter - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[ navigation_category_counter_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            ' navigation_category_counter_notice_'. $kb_id,
            array(
                'label' => __( 'Category Counter' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[ navigation_category_counter_notice]',
            )
        ) );
        /*---------------------------------------*/
        // Navigation Box Category - Counter Height
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_circle_height]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_category_counter_circle_height'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_category_counter_circle_height_'. $kb_id, 
            array(
                'label'    => esc_html__('Height (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_circle_height]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/
        // Navigation Box Category - Counter Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_circle_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_category_counter_circle_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_category_counter_circle_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_circle_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));          
        /*---------------------------------------*/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_category_counter_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_category_counter_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 
        /*---------------------------------------*/
        //  Navigation Box Category - Counter Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_category_counter_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
            )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_category_counter_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        
        //  Navigation Box Category - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_category_counter_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
            
            )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_category_counter_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_category_counter_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_category_counter_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  
        /*---------------------------------------*/    
        // Category Counter - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_category_counter_border_radius'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_category_counter_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_category_counter_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/    
        // Navigation Box Sub-Category - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_sub_category_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'navigation_box_sub_category_notice_'. $kb_id,
            array(
                'label' => __( 'Navigation Sub-Category' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_sub_category_notice]',
            )
        ) );
          
        // Navigation Box Sub-Category Title - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_sub_category_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_sub_category_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_sub_category_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_sub_category_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    

        // Navigation Box Category Title - Bold (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_sub_category_title_bold]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['navigation_box_sub_category_title_bold'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'navigation_box_sub_category_title_bold_'. $kb_id,
            array(
                'label' => esc_html__( 'Text Bold', 'incredibledocs' ),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_sub_category_title_bold]',
            )
        ) );

        //  Navigation Box Sub-Category Title - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_sub_category_title_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_sub_category_title_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_sub_category_title_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_sub_category_title_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
  
        //  Navigation Box Sub-Category Title - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_sub_category_title_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_sub_category_title_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_sub_category_title_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_sub_category_title_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        
        // Navigation Box Document Item - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'navigation_box_document_item_notice_'. $kb_id,
            array(
                'label' => __( 'Navigation Document Item' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_notice]',
            )
        ) );
 

        // Navigation Box Document Item - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_document_item_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_document_item_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 
                
        //  Navigation Box Document Item - Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[navigation_box_document_item_icon_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['navigation_box_document_item_icon_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'navigation_box_document_item_icon_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Icon Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_icon_width]',
                
                'input_attrs' => array(
                    'min' => 0.1, // Required. Minimum value for the slider
                    'max' => 3, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   

        //  Navigation Box Document Item - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[navigation_box_document_item_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_document_item_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_document_item_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
      

        //  Navigation Box Document Item - Icon Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[navigation_box_document_item_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_document_item_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_document_item_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Navigation Box Document Item - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_document_item_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_document_item_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Navigation Box Document Item - Hover Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_hover_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_document_item_hover_text_color'],
            'transport' => 'refresh',
           'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_document_item_hover_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_hover_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Navigation Box Document Item - Hover Text Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_hover_text_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_document_item_hover_text_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_document_item_hover_text_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Text Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_hover_text_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        
        //  Navigation Box Document Item - Active Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_active_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['navigation_box_document_item_active_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'navigation_box_document_item_active_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Active Item Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_sidebar_navigator_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[navigation_box_document_item_active_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

    }
    /*---------------------------------------------------------------------------------------*/
    private function toc_section( $wp_customize, $kb_id ) {

        // TOC Box - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'toc_box_notice_'. $kb_id,
            array(
                'label' => __( 'TOC Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_box_notice]',
            )
        ) );
        

        //  TOC Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['toc_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'toc_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  TOC Box - Left Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['toc_box_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'toc_box_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Left-Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_box_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

       
        /*
        // TOC Box - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['toc_box_border_radius'],
                'transport' => 'refresh',
            )
         );

        
        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'toc_box_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[toc_box_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        */

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['toc_box_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'toc_box_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Left-Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[toc_box_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_sticky_z_index]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['toc_box_sticky_z_index'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'toc_box_sticky_z_index_'. $kb_id, 
            array(
                'label'    => esc_html__('Sticky TOC Z-Index', 'incredibledocs'),
                'section'  => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[toc_box_sticky_z_index]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_sticky_margin_top]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['toc_box_sticky_margin_top'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'toc_box_sticky_margin_top_'. $kb_id, 
            array(
                'label'    => esc_html__('Sticky TOC Top Margin (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[toc_box_sticky_margin_top]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 200, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['toc_box_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'toc_box_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the TOC box container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_toc_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[toc_box_padding]',

        ) ) );

        // TOC Title - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_title_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'toc_title_notice_'. $kb_id,
            array(
                'label' => __( 'TOC Title' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_title_notice]',
            )
        ) );

     
        // TOC Title - Text
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_title_text]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['toc_box_title_text'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            )
            );
 
        $wp_customize->add_control( new WP_Customize_Control( 
            $wp_customize, 
            'toc_box_title_text_'. $kb_id,
            array(
                'label' => esc_html__( 'Title Text', 'incredibledocs' ),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_box_title_text]',
                'type' => 'text',
            )
        ) );


        // TOC Title - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['toc_box_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'toc_box_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_box_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        
        // TOC Title - Text Alignment
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[toc_box_title_alignment]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['toc_box_title_alignment'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',

            )
         );
         
        $wp_customize->add_control( 'toc_box_title_alignment_'. $kb_id, 
            array(
                'label'    => esc_html__('Text Alignment', 'incredibledocs'),
                'section'  => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_box_title_alignment]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(
                    'value1' => 'Left',
                    'value2' => 'Centered',
                    'value3' => 'Right',
                ),
            )
        );
        

        //  TOC Title Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[toc_box_title_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['toc_box_title_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'toc_box_title_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_box_title_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        
        //  TOC Title Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_title_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['toc_box_title_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'toc_box_title_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_box_title_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        
        //  TOC Title Bottom-Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_title_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['toc_box_title_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'toc_box_title_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Bottom-Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_box_title_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
    
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_title_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['toc_box_title_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'toc_box_title_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Bottom-Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[toc_box_title_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_box_title_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['toc_box_title_padding'],
            'transport' => 'refresh',
          
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'toc_box_title_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the TOC title container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_toc_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[toc_box_title_padding]',

        ) ) );
        
        // TOC Items - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_items_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'toc_items_notice_'. $kb_id,
            array(
                'label' => __( 'TOC Items' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_items_notice]',
            )
        ) );

        // TOC Items
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_items_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'toc_items_notice_'. $kb_id,
            array(
                'label' => __( 'TOC Items' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_items_notice]',
            )
        ) );
    
        // TOC Items - Header Start
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[toc_items_header_start]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['toc_items_header_start'],
                'transport' => 'refresh',
                'sanitize_callback' => function( $value ) use ( $kb_id ) {

                    $after_sanitize = $this->sanitize_toc_header_start( $value, $kb_id );
                    //error_log($after_sanitize);
                    return $after_sanitize;
                },

            )
         );
         
        $wp_customize->add_control( 'toc_items_header_start_'. $kb_id, 
            array(
                'label'    => esc_html__('TOC Header Start', 'incredibledocs'),
                'section'  => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_items_header_start]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(

                    'value1' => 'H1',
                    'value2' => 'H2',
                    'value3' => 'H3',
                    'value4' => 'H4',
                    'value5' => 'H5',
                    'value6' => 'H6',
                ),
            )
        );

        // TOC Items - Header End
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[toc_items_header_end]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['toc_items_header_end'],
                'transport' => 'refresh',
                
                'sanitize_callback' => function( $value ) use ( $kb_id ) {

                    $after_sanitize = $this->sanitize_toc_header_end( $value, $kb_id );
                    //error_log($after_sanitize);
                    return $after_sanitize;
                },
                
            )
         );
         
        $wp_customize->add_control( 'toc_items_header_end_'. $kb_id, 
            array(
                'label'    => esc_html__('TOC Header End', 'incredibledocs'),
                'section'  => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_items_header_end]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(

                    'value1' => 'H1',
                    'value2' => 'H2',
                    'value3' => 'H3',
                    'value4' => 'H4',
                    'value5' => 'H5',
                    'value6' => 'H6',
                ),
            )
        );

        // TOC Items - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[toc_items_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['toc_items_font_size'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'toc_items_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_items_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 

        //  TOC Items - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_items_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['toc_items_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

            )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'toc_items_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_items_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  TOC Items - Text Hover Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_items_text_hover_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['toc_items_text_hover_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'toc_items_text_hover_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Hover Color', 'incredibledocs'),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_items_text_hover_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        
        //  TOC Items - Hover Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[toc_items_hover_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['toc_items_hover_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'toc_items_hover_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_toc_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[toc_items_hover_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[toc_items_padding]', 
            array(
                'type' => 'option',
                'default'           => $this->defaults_design_options['toc_items_padding'],
                'transport' => 'refresh',
            ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'toc_items_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the TOC items container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_toc_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[toc_items_padding]',

        ) ) );
        
    }
    /*---------------------------------------------------------------------------------------*/
    private function likes_rating_section( $wp_customize, $kb_id ) {

        // Likes Rating - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'likes_rating_box_notice_'. $kb_id,
            array(
                'label' => __( 'Likes Rating Box' ),
                //'description' => __(''),
                'section'  => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_notice]',
            )
        ) );

        /*
        // Likes Rating - Show Like Feedback (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_show_like_feedback]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['likes_rating_box_show_like_feedback'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'likes_rating_box_show_like_feedback_'. $kb_id,
            array(
                'label' => esc_html__( 'Like/Dislike Feedback' ),
                'section'  => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_show_like_feedback]',
            )
        ) );
        */
        /*---------------------------------------*/

        // Likes Rating - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['likes_rating_box_title_font_size'],
                'transport' => 'refresh',
             
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'likes_rating_box_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Title Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 
       
       
        

        //  Likes Rating - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['likes_rating_box_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Likes Rating - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['likes_rating_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Likes Rating - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['likes_rating_box_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_title_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['likes_rating_box_title_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'likes_rating_box_title_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[likes_rating_box_title_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  

        // Likes Rating Yes Button
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_yes_button_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'likes_rating_yes_button_notice_'. $kb_id,
            array(
                'label' => __( "'Yes' Button" ),
                //'description' => __(''),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_yes_button_notice]',
            )
        ) );

        //  Likes Rating Yes Button - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_yes_button_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['likes_rating_box_yes_button_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_yes_button_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_yes_button_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Likes Rating Yes Button Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_yes_button_background_color]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['likes_rating_box_yes_button_background_color'],
                'transport' => 'refresh',
                'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

            )
            );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_yes_button_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_yes_button_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Likes Rating Yes Button Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_yes_button_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['likes_rating_box_yes_button_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_yes_button_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_yes_button_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Likes Rating Button Yes Hover Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_yes_button_hover_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['likes_rating_box_yes_button_hover_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_yes_button_hover_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_yes_button_hover_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Likes Rating No Button
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_no_button_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'likes_rating_no_button_notice_'. $kb_id,
            array(
                'label' => __( "'No' Button" ),
                //'description' => __(''),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_no_button_notice]',
            )
        ) );

        //  Likes Rating No Button - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_no_button_icon_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['likes_rating_box_no_button_icon_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_no_button_icon_color_'. $kb_id,
            array(
                'label' => esc_html__('Icon Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_no_button_icon_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Likes Rating No Button Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_no_button_background_color]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['likes_rating_box_no_button_background_color'],
                'transport' => 'refresh',
                'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

            )
            );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_no_button_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_no_button_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Likes Rating No Button Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_no_button_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['likes_rating_box_no_button_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_no_button_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_no_button_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Likes Rating Button No Hover Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[likes_rating_box_no_button_hover_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['likes_rating_box_no_button_hover_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'likes_rating_box_no_button_hover_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_likes_rating_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[likes_rating_box_no_button_hover_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

    }
    /*---------------------------------------------------------------------------------------*/
    private function feedback_form_section( $wp_customize, $kb_id ) {

       
        // Feedback Box Notice 
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'feedback_box_notice_'. $kb_id,
            array(
                'label' => __( 'Feedback Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_notice]',
            )
        ) );

        
        /*---------------------------------------*/
        // Feedback Collection Probability
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_collection_probability]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['feedback_collection_probability'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'feedback_collection_probability_'. $kb_id, 
            array(
                'label'    => esc_html__('Collection Probability (%)', 'incredibledocs'),
                'section'  => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_collection_probability]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 
        /*---------------------------------------*/
        // Feedback Form - Feedback Title
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_improve_feedback_title]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['feedback_box_improve_feedback_title'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            )
            );
 
        $wp_customize->add_control( new WP_Customize_Control( 
            $wp_customize, 
            'feedback_box_improve_feedback_title_'. $kb_id,
            array(
                'label' => esc_html__( 'Feedback Title', 'incredibledocs' ),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_improve_feedback_title]',
                'type' => 'text',
            )
        ) );
        /*---------------------------------------*/ 
        // Feedback Form - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['feedback_box_title_font_size'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'feedback_box_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Title Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 
       
        //  Feedback - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_title_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['feedback_box_title_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'feedback_box_title_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Title Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_title_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );


        //  Feedback - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['feedback_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'feedback_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Feedback - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['feedback_box_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'feedback_box_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['feedback_box_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'feedback_box_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id . '[feedback_box_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[feedback_box_padding]', 
            array(
                'type' => 'option',
                'default'           => $this->defaults_design_options['feedback_box_padding'],
                'transport' => 'refresh',
            ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'feedback_box_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the feedback form container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'  => 'idocs_customizer_feedback_form_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id . '[feedback_box_padding]',

        ) ) );

        // Feedback Box Item Notice 
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_item_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'feedback_box_item_notice_'. $kb_id,
            array(
                'label' => __( 'Input Item' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_item_notice]',
            )
        ) );

        
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_item_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['feedback_box_item_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'feedback_box_item_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_item_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 

        //  Feedback Box Item - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_item_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['feedback_box_item_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'feedback_box_item_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_item_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Feedback Box Item - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_item_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['feedback_box_item_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'feedback_box_item_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_item_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

         
        // Feedback Box Submit Button Notice 
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'feedback_box_submit_button_notice_'. $kb_id,
            array(
                'label' => __( 'Submit Button' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_notice]',
            )
        ) );

        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['feedback_box_submit_button_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'feedback_box_submit_button_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section'  => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        )); 

        //  Feedback - Submit Button Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['feedback_box_submit_button_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'feedback_box_submit_button_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Feedback - Submit Button Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['feedback_box_submit_button_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'feedback_box_submit_button_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        //  Feedback - Submit Button Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['feedback_box_submit_button_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'feedback_box_submit_button_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        
        //  Feedback - Submit Button Hover Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_hover_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['feedback_box_submit_button_hover_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'feedback_box_submit_button_hover_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_feedback_form_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[feedback_box_submit_button_hover_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public function sanitize_toc_header_end ( $value, $kb_id ) {

        // Get the value of the first setting from the UI
        $toc_header_start = $GLOBALS['wp_customize']->get_setting('idocs_design_options_'. $kb_id. '[toc_items_header_start]')->value();
        $toc_header_end = $value;
       
        // Compare the values
        if ( $toc_header_end  <  $toc_header_start ) {
            
            // If the second setting is not greater than the first, set a default value
            return 'value6';
        }

        return sanitize_text_field( $value );

    }
    /*---------------------------------------------------------------------------------------*/
    public function sanitize_toc_header_start ( $value, $kb_id ) {

        // Get the value of the first setting from the UI
        $toc_header_end = $GLOBALS['wp_customize']->get_setting('idocs_design_options_'. $kb_id. '[toc_items_header_end]')->value();
        $toc_header_start = $value;
       
        // Compare the values
        if ( $toc_header_start  >  $toc_header_end ) {
            
            // If the second setting is not greater than the first, set a default value
            return 'value1';
        }

        return sanitize_text_field( $value );

    }
    /*---------------------------------------------------------------------------------------*/
    public function tag_view_section ($wp_customize, $kb_id ) {

    
        // Tag View Box Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_view_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'tag_view_box_title_'. $kb_id,
            array(
                'label' => __( 'Tag View Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_tag_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[tag_view_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	
        // Tag View - Show Live Search (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_view_show_live_search]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['tag_view_show_live_search'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'tag_view_show_live_search_'. $kb_id,
            array(
                'label' => esc_html__( 'Live Search', 'incredibledocs' ),
                'section' => 'idocs_customizer_tag_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_view_show_live_search]',
            )
        ) );
        /*---------------------------------------*/	
        // Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_view_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['tag_view_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'tag_view_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_tag_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[tag_view_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/	
        // Background Image
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_view_background_image]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['tag_view_background_image'],
                'transport' => 'refresh',
                'sanitize_callback' => array($this, 'sanitize_image'),
            )
         );

        $wp_customize->add_control( new WP_Customize_Image_Control(
            $wp_customize, 
            'tag_view_background_image_'. $kb_id, 
            array(
                'label'    => esc_html__('Background Image', 'incredibledocs'),
                'section'  => 'idocs_customizer_tag_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[tag_view_background_image]',
            )
        ));
        /*---------------------------------------*/	
        // Width Slider
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_view_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['tag_view_width'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'tag_view_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (%)', 'incredibledocs'),
                'section'  => 'idocs_customizer_tag_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[tag_view_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_view_margin_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['tag_view_margin_padding'],
            'transport' => 'refresh',
    
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'tag_view_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default margin and padding for the quick button container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_tag_view_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[tag_view_margin_padding]',
            
        ) ) );
        /*---------------------------------------*/	 
        // Tag View Title Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_view_title]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'tag_view_box_title_'. $kb_id,
            array(
                'label' => __( 'Tag Title' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_tag_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[tag_view_title]',
                //'priority' => 1,
            )
        ) );

        // Tag View Title - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_view_title_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['tag_view_title_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'tag_view_title_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section' => 'idocs_customizer_tag_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_view_title_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));   

        // Tag View - Title Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_view_title_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['tag_view_title_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'tag_view_title_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_tag_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_view_title_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        // Tag View - Title Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_view_title_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['tag_view_title_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'tag_view_title_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_tag_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_view_title_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        
        
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_view_title_margin_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['tag_view_title_margin_padding'],
            'transport' => 'refresh',
    
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'tag_view_title_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default margin and padding for the tag title container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section' => 'idocs_customizer_tag_view_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[tag_view_title_margin_padding]',
            
        ) ) );

    }
    /*---------------------------------------------------------------------------------------*/
    private function tag_content_cards_section( $wp_customize, $kb_id ) {


        // Tag Content Box Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_content_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'tag_content_box_title_'. $kb_id,
            array(
                'label' => __( 'Tag Content Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[tag_content_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	        
        // Tag Content Box - Show Documents (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_box_show_documents]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['tag_content_box_show_documents'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'tag_content_box_show_documents_'. $kb_id,
            array(
                'label' => esc_html__( 'Show Documents', 'incredibledocs' ),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_box_show_documents]',
            )
        ) );
        /*---------------------------------------*/	 
        // Tag Content Box - Show Links (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_box_show_links]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['tag_content_box_show_links'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'tag_content_box_show_links_'. $kb_id,
            array(
                'label' => esc_html__( 'Show Links', 'incredibledocs' ),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_box_show_links]',
            )
        ) );
        /*---------------------------------------*/
        // Tag Content Box - Show Videos (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_box_show_videos]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['tag_content_box_show_videos'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'tag_content_box_show_videos_'. $kb_id,
            array(
                'label' => esc_html__( 'Show Vidoes', 'incredibledocs' ),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_box_show_videos]',
            )
        ) );
        /*---------------------------------------*/
        // Tag Content Box - Show FAQs (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_box_show_faqs]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['tag_content_box_show_faqs'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'tag_content_box_show_faqs_'. $kb_id,
            array(
                'label' => esc_html__( 'Show FAQs', 'incredibledocs' ),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_box_show_faqs]',
            )
        ) );
        /*---------------------------------------*/
        // Tag Content Box - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_content_box_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['tag_content_box_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'tag_content_box_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[tag_content_box_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/	 
		// Tag Content Box - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_content_box_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['tag_content_box_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'tag_content_box_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[tag_content_box_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/	 
        // Tag Content Box - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_content_box_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['tag_content_box_border_radius'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'tag_content_box_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[tag_content_box_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/	        
        // Tag Content Box - Border Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_content_box_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['tag_content_box_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'tag_content_box_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[tag_content_box_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[tag_content_box_margin_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['tag_content_box_margin_padding'],
            'transport' => 'refresh',
    
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'tag_content_box_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default margin and padding for the content and sidebars container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[tag_content_box_margin_padding]',
            
        ) ) );
        /*---------------------------------------*/
        /*---------------------------------------*/		
        // Content Card - Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id . '[content_card_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'content_card_notice_'. $kb_id,
            array(
                'label' => __( 'Content Card' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[content_card_notice]',
            )
        ) );
        /*---------------------------------------*/	
       // Categories Card - Height
       $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_card_height]' , 
       array(
           'type' => 'option',
           'capability'    => 'manage_options',
           'default'     => $this->defaults_design_options['tag_content_card_height'],
           'transport' => 'refresh',
       )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'tag_content_card_height_'. $kb_id, 
            array(
                'label'    => esc_html__('Height (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_card_height]',
                
                'input_attrs' => array(
                    'min' => 100, // Required. Minimum value for the slider
                    'max' => 300, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));  
        /*---------------------------------------*/        
        //  Content Card - Items Order By
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_card_items_order_by]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['tag_content_card_items_order_by'],
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',

            )
         );

        $wp_customize->add_control( 'tag_content_card_items_order_by_'. $kb_id, 
            array(
                'label'    => esc_html__('Content Order By', 'incredibledocs'),
                'section'  => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_card_items_order_by]',
             //   'priority' => 1,
                'type'     => 'select',
                'choices'    => array(

                    'title'   		     => 'Alphabetical by Title',
					'created_date'       => 'Created Date',
					'last_modified_date' => 'Last Modified Date',
                    'custom_display_order' => 'Custom Display Order',

                ),
            )
        );
        
        /*---------------------------------------*/	
        // Content Card - Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_card_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['tag_content_card_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'tag_content_card_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_card_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Content Card - Hover Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_card_hover_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['tag_content_card_hover_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        ));

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'tag_content_card_hover_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Hover Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_card_hover_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        /*---------------------------------------*/
        // Content Card - Show Shadow (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_card_show_shadow]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['tag_content_card_show_shadow'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'tag_content_card_show_shadow_'. $kb_id,
            array(
                'label' => esc_html__( 'Card Shadow', 'incredibledocs' ),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_card_show_shadow]',
            )
        ) );
        // Categories Card - Shadow Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_card_shadow_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['tag_content_card_shadow_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        ));

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'tag_content_card_shadow_color_'. $kb_id,
            array(
                'label' => esc_html__('Shadow Color', 'incredibledocs'),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_card_shadow_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Categories Card - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_card_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['tag_content_card_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'tag_content_card_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_card_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );

        // Categories Card - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_card_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['tag_content_card_border_radius'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'tag_content_card_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_card_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        
        /*---------------------------------------*/ 
        // Categories Card - Border Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_card_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['tag_content_card_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'tag_content_card_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[tag_content_card_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    
        /*---------------------------------------*/	
        
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[tag_content_card_padding]', array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default'       => $this->defaults_design_options['tag_content_card_padding'],
            'transport' => 'refresh',
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'tag_content_card_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default padding for the content card container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_tag_content_cards_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[tag_content_card_padding]',
        ) ) );
        

        /***************************** &&&&&&*/
        /***************************** */
        /***************************** */
        // Categories Icon - Notice
        
    }
    /*---------------------------------------------------------------------------------------*/
    public function faqgroup_view_section ($wp_customize, $kb_id ) {

    
        // FAQ Group View Box Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqgroup_view_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'faqgroup_view_box_title_'. $kb_id,
            array(
                'label' => __( 'FAQ Group View Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_faq_group_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[faqgroup_view_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	
        // faqgroup View - Show Live Search (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[faqgroup_view_show_live_search]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['faqgroup_view_show_live_search'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'faqgroup_view_show_live_search_'. $kb_id,
            array(
                'label' => esc_html__( 'Live Search', 'incredibledocs' ),
                'section' => 'idocs_customizer_faq_group_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[faqgroup_view_show_live_search]',
            )
        ) );
        /*---------------------------------------*/	
        // Background Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqgroup_view_background_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['faqgroup_view_background_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'faqgroup_view_background_color_'. $kb_id,
            array(
                'label' => esc_html__('Background Color', 'incredibledocs'),
                'section' => 'idocs_customizer_faq_group_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[faqgroup_view_background_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/	
        // Background Image
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqgroup_view_background_image]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['faqgroup_view_background_image'],
                'transport' => 'refresh',
                'sanitize_callback' => array($this, 'sanitize_image'),
            )
         );

        $wp_customize->add_control( new WP_Customize_Image_Control(
            $wp_customize, 
            'faqgroup_view_background_image_'. $kb_id, 
            array(
                'label'    => esc_html__('Background Image', 'incredibledocs'),
                'section'  => 'idocs_customizer_faq_group_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[faqgroup_view_background_image]',
            )
        ));
        /*---------------------------------------*/	
        // Width Slider
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqgroup_view_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['faqgroup_view_width'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'faqgroup_view_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Width (%)', 'incredibledocs'),
                'section'  => 'idocs_customizer_faq_group_view_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[faqgroup_view_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 100, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[faqgroup_view_margin_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['faqgroup_view_margin_padding'],
            'transport' => 'refresh',
    
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'faqgroup_view_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default margin and padding for the quick button container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_faq_group_view_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[faqgroup_view_margin_padding]',
            
        ) ) );
        /*---------------------------------------*/	 
        
    }
    /*---------------------------------------------------------------------------------------*/
    public function document_tags_section ($wp_customize, $kb_id ) {

        /*---------------------------------------*/	
        // Document Tags Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_tags_box_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'document_tags_box_title_'. $kb_id,
            array(
                'label' => __( 'Document Tags Box' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_document_tags_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_tags_box_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/	
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_tags_box_margin_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['document_tags_box_margin_padding'],
            'transport' => 'refresh',
    
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Multi_Dimensions( 
            $wp_customize, 
            'document_tags_box_margin_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Margin and Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the default margin and padding for the quick button container.', 'incredibledocs' ),
            'choices'    => array(
                'margin' => array(
                    'margin-top'     => '',
                    'margin-right'   => '',
                    'margin-bottom'  => '',
                    'margin-left'    => '',
                ),
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_document_tags_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[document_tags_box_margin_padding]',
            
        ) ) );
        /*---------------------------------------*/
     
        // Tag Item Notice
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_tags_item_notice]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => '',
                'transport' => 'refresh',
            )
        );
        
        $wp_customize->add_control( new IDOCS_Customizer_Notice( 
            $wp_customize, 
            'document_tags_item_title_'. $kb_id,
            array(
                'label' => __( 'Tag Item' ),
                //'description' => __(''),
                'section' => 'idocs_customizer_document_tags_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id. '[document_tags_item_notice]',
                //'priority' => 1,
            )
        ) );
        /*---------------------------------------*/
         // Tag Item - Font Size
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_tags_item_font_size]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['document_tags_item_font_size'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'document_tags_item_font_size_'. $kb_id, 
            array(
                'label'    => esc_html__('Font Size (rem)', 'incredibledocs'),
                'section' => 'idocs_customizer_document_tags_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_tags_item_font_size]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 10, // Required. Maximum value for the slider
                    'step' => 0.1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));         
        /*---------------------------------------*/
        // Tag Item - Text Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_tags_item_text_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['document_tags_item_text_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )
        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'document_tags_item_text_color_'. $kb_id,
            array(
                'label' => esc_html__('Text Color', 'incredibledocs'),
                'section' => 'idocs_customizer_document_tags_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_tags_item_text_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        // Tag Item - Show Tag Background (ON/OFF)
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_tags_item_show_background_color]',
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default' => $this->defaults_design_options['document_tags_item_show_background_color'],
                'transport' => 'refresh',
            )
            );
 
        $wp_customize->add_control( new IDOCS_Customizer_Toggle_Switch( 
            $wp_customize, 
            'document_tags_item_show_background_color_'. $kb_id,
            array(
                'label' => esc_html__( 'Tag Background Color', 'incredibledocs' ),
                'section' => 'idocs_customizer_document_tags_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_tags_item_show_background_color]',
            )
        ) );	
        /*---------------------------------------*/
        // Tag Item - Border Width
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_tags_item_border_width]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['document_tags_item_border_width'],
                'transport' => 'refresh',
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'document_tags_item_border_width_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Width (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_document_tags_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_tags_item_border_width]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));    
        /*---------------------------------------*/
        // Tag Item - Border Radius
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_tags_item_border_radius]' , 
            array(
                'type' => 'option',
                'capability'    => 'manage_options',
                'default'     => $this->defaults_design_options['document_tags_item_border_radius'],
                'transport' => 'refresh',
              
            )
         );

        $wp_customize->add_control( new IDOCS_Customizer_Slider(
            $wp_customize, 
            'document_tags_item_border_radius_'. $kb_id, 
            array(
                'label'    => esc_html__('Border Radius (px)', 'incredibledocs'),
                'section'  => 'idocs_customizer_document_tags_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_tags_item_border_radius]',
                
                'input_attrs' => array(
                    'min' => 0, // Required. Minimum value for the slider
                    'max' => 50, // Required. Maximum value for the slider
                    'step' => 1, // Required. The size of each interval or step the slider takes between the minimum and maximum values
                ),
            )
        ));
        /*---------------------------------------*/
        // Tag Item - Border Color
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id .'[document_tags_item_border_color]',
        array(
            'type' => 'option',
            'capability'    => 'manage_options',
            'default' => $this->defaults_design_options['document_tags_item_border_color'],
            'transport' => 'refresh',
            'sanitize_callback' => array( $this, 'sanitize_color_with_opacity' )

        )
        );

        $wp_customize->add_control( new IDOCS_Customizer_Color( 
            $wp_customize, 
            'document_tags_item_border_color_'. $kb_id,
            array(
                'label' => esc_html__('Border Color', 'incredibledocs'),
                'section' =>  'idocs_customizer_document_tags_section_'. $kb_id,
                'settings' => 'idocs_design_options_'. $kb_id .'[document_tags_item_border_color]',
                'show_opacity' => true, // Optional. Show or hide the opacity value on the opacity slider handle. Default: true
                )
            )
        );
        /*---------------------------------------*/
        $wp_customize->add_setting( 'idocs_design_options_'. $kb_id. '[document_tags_item_padding]', array(
            'type' => 'option',
            'default'           => $this->defaults_design_options['document_tags_item_padding'],
            'transport' => 'refresh',
          
        ) );
    
        $wp_customize->add_control( new IDOCS_Customizer_Padding_Dimensions( 
            $wp_customize, 
            'document_tags_item_padding_'. $kb_id, 
            array(
            'label'       => esc_html__( 'Padding', 'incredibledocs' ),
            'description' => esc_html__( 'Set the padding for the live search input container.', 'incredibledocs' ),
            'choices'    => array(
                
                'padding' => array(
                    'padding-top'    => '',
                    'padding-right'  => '',
                    'padding-bottom' => '',
                    'padding-left'   => '',
                ),
            ),
            'section'     => 'idocs_customizer_document_tags_section_'. $kb_id,
            'settings' => 'idocs_design_options_'. $kb_id. '[document_tags_item_padding]',

        ) ) );
        /*---------------------------------------*/
        
		
        
    }
    /*---------------------------------------------------------------------------------------*/
}
/*---------------------------------------------------------------------------------------*/
// https://github.com/maddisondesigns/customizer-custom-controls
// https://maddisondesigns.com/2017/05/the-wordpress-customizer-a-developers-guide-part-1/
// https://maddisondesigns.com/2017/05/the-wordpress-customizer-a-developers-guide-part-2
// https://www.usablewp.com/wordpress-customizer/
// https://developer.wordpress.org/themes/customize-api/customizer-objects/
