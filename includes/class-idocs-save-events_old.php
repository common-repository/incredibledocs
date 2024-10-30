<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* Save user-events for analytics view (search queries, document visits, rating, feedback)
/*---------------------------------------------------------------------------------------*/
class IDOCS_Save_Events {

    /*---------------------------------------------------------------------------------------*/
    // add a row to the search_logs table with the new search event
    public static function save_search_query_event( $search_query, $found_flag, $current_ip, $kb_id ) {

        global $wpdb;
        $table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_search_logs';
        $country_name = self::convert_ip_to_country($current_ip);
        /*---------------------------------------*/
        $wpdb->insert(
            $table_name,
            array(
                // The result will respect the time zone defined in the Settings → General → Time Zone select field
                'search_time'   => current_datetime()->format('Y-m-d H:i:s'),
                'search_query'  => sanitize_text_field($search_query),
                'found_flag'    => intval($found_flag),
                'kb_id'         => intval($kb_id),
                'country'       => sanitize_text_field($country_name),
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public static function selectRandomCountry() {

        $countries = [
            
            'India' => 20,
            'United States' => 15,
            'United Kingdom' => 10,
            'Australia' => 3,
            'Germany' => 5,
            'France' => 5,
            'Spain' => 5,
            'Indonesia' => 3.5,
            'Brazil' => 6,
            'Sweden' => 2,
            'South Africa' => 5,
            'Bangladesh' => 3,
            'Russia' => 2,
            'Mexico' => 2,
            'Japan' => 2,
            'Philippines' => 2,
            'Vietnam' => 2
        ];
    
        $totalPercentage = array_sum($countries);

        $rand = mt_rand(1, $totalPercentage * 100);
        $cumulativePercentage = 0;

        foreach ($countries as $country => $percentage) {
            $cumulativePercentage += $percentage;
            if ($rand <= $cumulativePercentage * 100) {
                return $country;
            }
        }
    
        // Default to the last country (shouldn't happen)
        return end($countries);
    }
    
    /*---------------------------------------------------------------------------------------*/
    public static function convert_ip_to_country($current_ip) {

        //error_log($current_ip);
        //delete_transient('idocs_transient_ip_to_country');
        $cached_data =  get_transient( 'idocs_transient_ip_to_country');
        
        if ( false === $cached_data || !(isset($cached_data[$current_ip])) ) {

            // in case the pro version is not enabled, the next filter will not work (setting empty value to name)
            $country_name = ''; 
            // use a pro filer to translate ip to country 
            $country_name = apply_filters('idocspro_ip_to_country', $current_ip);
            // handle a local host environment 
            if ($current_ip == '127.0.0.1') {

                $country_name = "Local Host";
                //$country_name = IDOCS_Save_Events::selectRandomCountry();
                //error_log( $country_name);
            };
        
            /*--------------------------------------------*/
            // scenario #1 - no cache data 
            if ( false === $cached_data) {
                // create empty array
                $cached_data = [];
                $cached_data[$current_ip] = $country_name;
                set_transient( 'idocs_transient_ip_to_country', $cached_data, 86400); 
            }
            else { // scenario #2 - cache data avialable but not on that term object - !(isset($cached_data[$current_ip]))
                $cached_data[$current_ip] = $country_name;
                // setting the transient without expiration time so it will not reset the existing expiration time
                // otherwise, one single visit per day and the transient will never expire. 
                set_transient( 'idocs_transient_ip_to_country', $cached_data ); 

            }
            /*--------------------------------------------*/
            //error_log('setting cache for that ip');
            $cached_data[$current_ip] = $country_name;
            return $country_name;
        }
        else {

            //error_log('getting cache for that ip');
            return $cached_data[$current_ip];

        }
    } 
    /*---------------------------------------------------------------------------------------*/
    // save a content visit event (5e5827093d)
    public static function save_content_visit_event( $content_id, $content_type, $current_ip, $kb_id ) {

        global $wpdb;
        $table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_visits_content';
        
        /*
        $ip_array = [ '6.6.6.6', '2.2.2.2', '3.3.3.3', '4.4.4.4', '5.5.5.5'];
        $random_index = array_rand($ip_array);
        $random_ip = $ip_array[$random_index];
        $current_ip = $random_ip;
        */

        $country_name = self::convert_ip_to_country($current_ip);
        /*---------------------------------------*/
        $wpdb->insert(
            $table_name,
            array(
                'visit_time' => current_datetime()->format('Y-m-d H:i:s'),
                'content_id'  => intval($content_id),
                'content_type'  => sanitize_text_field($content_type),
                'kb_id'        => intval($kb_id),
                'country'      => sanitize_text_field($country_name),
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    // save taxonomy visit event (5e5827093d)
    public static function save_taxonomy_visit_event( $term_id, $taxonomy, $current_ip, $kb_id ) {

        global $wpdb;
        $table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_visits_taxonomy';

        /*
        $ip_array = [ '6.6.6.6', '2.2.2.2', '3.3.3.3', '4.4.4.4', '5.5.5.5'];
        $random_index = array_rand($ip_array);
        $random_ip = $ip_array[$random_index];
        $current_ip = $random_ip;
        */

        $country_name = self::convert_ip_to_country($current_ip);
        /*---------------------------------------*/
        $wpdb->insert(
            $table_name,
            array(
                'visit_time' => current_datetime()->format('Y-m-d H:i:s'),
                'term_id'      => intval($term_id),
                'taxonomy'     => sanitize_text_field($taxonomy),
                'kb_id'        => intval($kb_id),
                'country'      => sanitize_text_field($country_name),
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public static function save_content_rating_event( $content_id, $content_type, $rating_score, $current_ip, $kb_id ) {

        global $wpdb;
        $table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_ratings_content';

        /*
        $ip_array = [ '6.6.6.6', '2.2.2.2', '3.3.3.3', '4.4.4.4', '5.5.5.5'];
        $random_index = array_rand($ip_array);
        $random_ip = $ip_array[$random_index];
        $current_ip = $random_ip;
        */

        $country_name = self::convert_ip_to_country($current_ip);

        /*---------------------------------------*/
        $wpdb->insert(
            $table_name,
            array(
                // The result will respect the time zone defined in the Settings → General → Time Zone select field
                'rating_time' => current_datetime()->format('Y-m-d H:i:s'),
                'content_id' => intval($content_id),
                'content_type' => sanitize_text_field($content_type),
                'kb_id'       => intval($kb_id),
                'rating_score'   => intval($rating_score),
                'country'      => sanitize_text_field($country_name),
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public static function save_taxonomy_rating_event( $term_id, $taxonomy, $rating_score, $current_ip, $kb_id ) {

        global $wpdb;
        $table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_ratings_taxonomy';
        //error_log($current_ip);
        $country_name = self::convert_ip_to_country($current_ip);
        /*---------------------------------------*/
        $wpdb->insert(
            $table_name,
            array(
                // The result will respect the time zone defined in the Settings → General → Time Zone select field
                'rating_time' => current_datetime()->format('Y-m-d H:i:s'),
                'term_id' => intval($term_id),
                'taxonomy' => sanitize_text_field($taxonomy),
                'kb_id'       => intval($kb_id),
                'rating_score'   => intval($rating_score),
                'country'      => sanitize_text_field($country_name),

            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
    public static function save_document_feedback_event( $document_id, $kb_id, $feedback_option, $feedback_comment, $full_name, $email ) {

        global $wpdb;
        $table_name = $wpdb->prefix . IDOCS_SHORT_PLUGIN_NAME . '_feedbacks';
        /*---------------------------------------*/
        $wpdb->insert(
            $table_name,
            array(
                // The result will respect the time zone defined in the Settings → General → Time Zone select field
                'feedback_time'    => current_datetime()->format('Y-m-d H:i:s'),
                'document_id'      => intval($document_id),
                'kb_id'            => intval($kb_id),
                'feedback_option'  =>  intval($feedback_option),
                'feedback_comment' => sanitize_text_field($feedback_comment),
                'feedback_status'  => 0, // new feedback
                'full_name'        => sanitize_text_field($full_name),
                'email'            => sanitize_email($email),
            )
        );
    }
    /*---------------------------------------------------------------------------------------*/
}


