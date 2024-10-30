<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/* Providing utility functions to display simple analytics data in the main dashboard page.
/*---------------------------------------------------------------------------------------*/
class IDOCS_Dashboard {

    /*---------------------------------------------------------------------------------------*/
    // get the total amount of visits looking on a specific time interval
    // https://github.com/WordPress/WordPress-Coding-Standards/blob/dc2f21771cb2b5336a7e6bb6616abcdfa691d7de/WordPress/Tests/DB/PreparedSQLPlaceholdersUnitTest.inc#L70-L124 
    public static function get_overall_content_visits( $days_back, $kbs_array ) {

        global $wpdb;
        // For the IN operator the coding standards recommend to use a combination of implode() and array_fill()
        // translate the list of kbs to a string of numberes with comma seperator 
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
        // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );   
        //do_action( 'qm/debug', $kbs_list_placeholders );
        //do_action( 'qm/debug', $prepare_values );
        /*--------------------------------------------*/
        if ( !empty($kbs_array) ) {
            
            $result = $wpdb->get_results($wpdb->prepare(
                "SELECT count(*) as total_visits
                FROM {$wpdb->prefix}idocs_visits_content
                WHERE date_add(visit_time, INTERVAL %d day) >= CURDATE()
                AND kb_id IN ($kbs_list_placeholders)",
                $prepare_values
            ), ARRAY_A);
        }
        /*--------------------------------------------*/
       // do_action( 'qm/debug', $wpdb->prepare($sql_query, $days_back, $kbs_list) );
        //do_action( 'qm/debug', $result );
        if ( empty($result) ) {

            $result[0]['total_visits'] = 0;

        };

        if ( $result[0]['total_visits'] == NULL )
	        $result[0]['total_visits'] = 0; 
        /*--------------------------------------------*/
        return $result[0]['total_visits'];
     }
    /*---------------------------------------------------------------------------------------*/
    // get the overall search success rate (%) looking on a specific time interval (5e5827093d)
    public static function get_search_success_rate( $days_back, $kbs_array ) {
        
        global $wpdb;
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
        // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );  
        /*---------------------------------------*/
        // count the amount of search queires with successful result 
        /*--------------------------------------------*/
        if (!empty($kbs_array)) {

            $result_1 = $wpdb->get_results($wpdb->prepare(
                "SELECT count(*) as success_count
                        FROM {$wpdb->prefix}idocs_search_logs
                        WHERE found_flag = 1
                            AND
                                date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                                kb_id IN ($kbs_list_placeholders)",
                $prepare_values), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        if (empty($result_1))
            $result_1[0]["success_count"] = 0;
        //do_action( 'qm/debug', $result_1);
        /*---------------------------------------*/
         // count the amount of search queires without any successful result 
        /*--------------------------------------------*/
        if (!empty($kbs_array)) {
            $result_2 = $wpdb->get_results($wpdb->prepare(
                "SELECT count(*) as no_success_count
                        FROM {$wpdb->prefix}idocs_search_logs
                        WHERE found_flag = 0
                            AND
                                date_add(search_time, INTERVAL %d day) >= CURDATE()
                            AND
                                kb_id IN ($kbs_list_placeholders)",               
                $prepare_values), 
                ARRAY_A
            );
        }
        if (empty($result_2)) 
            $result_2[0]["no_success_count"] = 0;
        /*---------------------------------------*/
        // convert to integer value 
        $success_count = intval($result_1[0]["success_count"]);
        $overall_count = $success_count + intval($result_2[0]["no_success_count"]);
        /*--------------------------------------------*/
        if ($overall_count != 0 )
             // return the success rate in percent, success count, overall count 
            return [round(100*($success_count/$overall_count)),$success_count, $overall_count];
        else
            return [0, 0, 0];

     }
    /*---------------------------------------------------------------------------------------*/
    // get the overall documents ratings (%) looking on a specific time interval 
    public static function get_overall_ratings( $days_back, $kbs_array) {

        global $wpdb;        
        // Assuming $kbs_array is an array of numbers
        //$kbs_list = implode(', ', array_map('intval', $kbs_array));

        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
        // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );  
        /*--------------------------------------------*/
        if (!empty($kbs_array)) {
            $result = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT sum(rating_score) as total_ratings, count(*) as number_ratings
                        FROM {$wpdb->prefix}idocs_ratings_content
                        WHERE date_add(rating_time, INTERVAL %d day) >= CURDATE() 
                        AND kb_id IN ($kbs_list_placeholders)",
                    $prepare_values), ARRAY_A);
        }
        /*--------------------------------------------*/
        if (empty($result)) {

            $result[0]['number_ratings'] = 0;
            $result[0]['total_ratings'] = 0;
            $result[0]['stars_score'] = 0;
            
        }
        /*--------------------------------------------*/
        // result of ratings is empty 
        if ( $result[0]['number_ratings'] == 0 ) {

            $result[0]['total_ratings'] = 0;
            $result[0]['stars_score'] = 0;

        }
        else {

            $result[0]['stars_score'] = round($result[0]['total_ratings']/($result[0]['number_ratings']),2);

        }
        /*--------------------------------------------*/
        return $result;
     }
    /*---------------------------------------------------------------------------------------*/
    // get the total amount of searches looking on a specific time interval 
    public static function get_overall_searches(  $days_back, $kbs_array) {

        global $wpdb;
        //$kbs_list = implode(',', array_map('intval', $kbs_array));   
        // prepare a string with a list of %d as placeholders
        $kbs_list_placeholders =  implode( ', ', array_fill( 0, count( $kbs_array ), '%d' ) );
        // merage all required prepared values with the $kbs_array values into a single array 
        $prepare_values = array_merge( array( $days_back ), $kbs_array );  
        /*--------------------------------------------*/
        if (!empty($kbs_array)) {
            $result = $wpdb->get_results($wpdb->prepare(
                "SELECT count(*) as total_searches, sum(found_flag) as total_found_result, 
                                (count(*)-sum(found_flag)) as total_no_found_result
                        FROM {$wpdb->prefix}idocs_search_logs
                        WHERE date_add(search_time, INTERVAL %d day) >= CURDATE()
                        AND
                                kb_id IN ($kbs_list_placeholders)",
                $prepare_values), 
                ARRAY_A
            );
        }
        /*--------------------------------------------*/
        if (empty($result)) {

            $result[0]['total_searches'] = 0; 
            $result[0]['total_found_result'] = 0;
            $result[0]['total_no_found_result'] = 0; 

        }
        /*--------------------------------------------*/
        if ( $result[0]['total_searches'] == NULL )
	        $result[0]['total_searches'] = 0; 
        if ( $result[0]['total_found_result'] == NULL )
	        $result[0]['total_found_result'] = 0; 
        if ( $result[0]['total_no_found_result'] == NULL )
	        $result[0]['total_no_found_result'] = 0; 
        /*--------------------------------------------*/
        return $result;
     }
    /*---------------------------------------------------------------------------------------*/
    public static function total_pending_comments( $days_back, $kbs_array) {

        $comments_args = array(

            'post_type' => 'idocs_content',
            'status' => 'hold', // Retrieves comments marked as pending moderation.
            'date_query' => array(
                array(
                    'after' => $days_back . ' days ago', // Comments submitted in the last 30 days
                    'inclusive' => true, // include comments from the exact date X days ago
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => 'document-kb-id',
                    'value' => $kbs_array,
                    'compare' => 'IN',
                ),
            ),
        );
        /*--------------------------------------------*/
        $comments = get_comments($comments_args);
        $output = array ();
        $output["total_comments"] = count($comments);
        /*--------------------------------------------*/
        return $output;
     }
    /*---------------------------------------------------------------------------------------*/
    public static function amount_days_last_content_update ( $kb_id ) {

        $meta_query = array(
            array(
                'key' => 'idocs-content-kb-meta',
                'value' => $kb_id,
                'compare' => '=',
            ),
        );
        /*--------------------------------------------*/
        $raw_query = new WP_Query(array(
            'post_type'  => 'idocs_content', 
             // filter the documents from the relevant knowledge-base
             'meta_query' => $meta_query,
        ));
        /*--------------------------------------------*/
        if  ( ! $raw_query->have_posts() ) {

            return null;
        }
        
        $raw_query->the_post();
        // Get the post last update time
        //$last_update_time = get_the_modified_time('Y-m-d H:i:s');
        $last_update_time = get_the_modified_time('Y-m-d');
        // Convert last update time to a DateTime object
        $last_update_date = new DateTime($last_update_time);
        // Get today's date as a DateTime object
        $current_date_object = current_datetime();
        $today = new DateTime($current_date_object->format('Y-m-d'));
        // Calculate the difference between the two dates
        $interval = $today->diff($last_update_date);
        // Assume the first element is the minimum
        $minValue = $interval->days; 
        //do_action( 'qm/debug', $minValue );
        //*--------------------------------------------*/
        while ( $raw_query->have_posts() ) {

            $raw_query->the_post();
            $last_update_time = get_the_modified_time('Y-m-d');
            $last_update_date = new DateTime($last_update_time);
            $current_date_object = current_datetime();
            $today = new DateTime($current_date_object->format('Y-m-d'));
            $interval = $today->diff($last_update_date);
            if ( $interval->days < $minValue )
                $minValue = $interval->days;
        };
        wp_reset_postdata();  
        /*--------------------------------------------*/
        return $minValue;   
    }
    /*---------------------------------------------------------------------------------------*/
}