<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      s
/*---------------------------------------------------------------------------------------*/
/*  Add:
    #1 - a cron event to clean-up historical analytics data from the database.
    #2 - a cron event per kb for email reporting 

    This class is adding one-side which is the cron events/hooks. Callback funtions are added by external classes.
    It means that every interval, wordpress will trigger an custom action event/hook.
    The hook event will be processed by a callback function added using add_action (different classes).

    WP-Cron:
    * runs at specified intervals (not at specific times)
    * default intervals - hourly, twice daily, daily 
    * we can define custom intervals 
/*---------------------------------------------------------------------------------------*/
class IDOCS_Cron {

    // define the name of the custom event (must be static as it used during de-activation)
    public static $db_cleanup_hook = 'idocs_db_cleanup_event';
    /*---------------------------------------------------------------------------------------*/
    // add custom intervals using the cron_schedules hook filter (hooked in the class-idocs) 
    public function custom_wpcron_intervals( $schedules ) {

        // The default supported recurrences are ‘hourly’, ‘twicedaily’, ‘daily’, and ‘weekly’.
        // https://developer.wordpress.org/reference/functions/wp_get_schedules/

        $schedules['monthly'] = array(
            'interval' => MONTH_IN_SECONDS,
            'display' => __('Once a month')
        );
        
        return $schedules;
    } 
    /*---------------------------------------------------------------------------------------*/
    // schedule wpcron events (5e5827093d)
    public function schedule_custom_wpcron_events() {

       // the hook to register plugin custom schedules before using them. 
        add_filter( 'cron_schedules', array($this, 'custom_wpcron_intervals') );

        /*----------------------------------------------*/
        // schedule event for the db clean-up
        /*----------------------------------------------*/
        // before scheduling an event, check that it is not already exist 
		if ( ! wp_next_scheduled( self::$db_cleanup_hook ) ) {

            /*
			Schedules a recurring event. Schedules an event which will be triggered by WordPress at the specified interval.
			The action will trigger when someone visits your WordPress site if the scheduled time has passed.

                $timestamp (int) - Unix timestamp (UTC) for when to next run the event.
                $recurrence (string) - How often the event should subsequently recur.
                $hook (string) - Action hook to execute when the event is run.

            */
            wp_schedule_event( time(), 'monthly', self::$db_cleanup_hook );
           
		}
        /*----------------------------------------------*/
        // schedule events for the email reporting
        /*----------------------------------------------*/ 
        // get all kbs 
        /*
        $kb_terms = get_terms( array(
            'taxonomy'   => 'idocs-kb-taxo',
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        ) );
        */
        $kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
        //do_action( 'qm/debug', $kb_terms );
        /*----------------------------------------------*/
        foreach ( $kb_terms as $term) {

            // get te summary report flag
            $summary_report_flag = get_term_meta( $term->term_id, 'idocs-kb-taxo-summary-report-flag', true );
            $stored_frequency = get_term_meta( $term->term_id, 'idocs-kb-taxo-email-frequency', true );
		    $stored_email = get_term_meta( $term->term_id, 'idocs-kb-taxo-email-address', true );

            if ( empty($summary_report_flag) ) 
                $summary_report_flag = 0;

            if ( $summary_report_flag == 1 and ! empty($stored_frequency) and ! empty($stored_email) )
                $ready_to_schedule = true;
            else
                $ready_to_schedule = false;

            // if the flag is ON, frequency and email are not empty for that kb then move forward with scheduling a cron event 
            if ( $ready_to_schedule == 1 ) {

                // generate dynamic event name 
                $event_name = 'idocs_email_reporting_' . $term->slug; 
                // get the stored frequency and day 
                if ( empty($stored_frequency) ) $stored_frequency = 0;
                $stored_day = get_term_meta( $term->term_id, 'idocs-kb-taxo-email-day', true );
                /*----------------------------------------------*/
                // translate arrays 
                $email_frequencies = array (

                    'f1' => 'daily',
                    'f2' => 'weekly',
                    'f3' => 'monthly',
                
                );
                $email_days = array (

                    'd1' => 'Sunday',
                    'd2' => 'Monday',
                    'd3' => 'Tuesday',
                    'd4' => 'Wednesday',
                    'd5' => 'Thursday',
                    'd6' => 'Friday',
                    'd7' => 'Saturday',
                    'nr' => 'Not Relevant',
                );
                /*----------------------------------------------*/
                // before scheduling an event:
                //  check that it is not already exist and the stored frequency is not empty
                if ( ! wp_next_scheduled( $event_name ) and ($stored_frequency != 0) ) {

                    //do_action( 'qm/debug', $stored_frequency );
                    switch ( $stored_frequency ) {

                        case 'f1': // daily
                            
                            $timestamp = strtotime('+1 day 10:00:00', time());
                            //do_action( 'qm/debug', time() );
                            //do_action( 'qm/debug', current_datetime()->getTimestamp() );
                            break;
                        case 'f2': // weekly
                            $timestamp = strtotime('next ' . $email_days[$stored_day] . ' 10:00');
                            break;    
                        case 'f3': // monthly
                            $timestamp = strtotime('first day of next month');
                            break;    
                        
                    }

                    // schedule the cron event 
                    wp_schedule_event( $timestamp, $email_frequencies[$stored_frequency], $event_name );
                      
                }    
            }
        }
    } 
    /*---------------------------------------------------------------------------------------*/
    // remove a schedule wpcron event when a kb is deleted 
    public function remove_wp_cron_event_when_kb_is_deleted($term, $tt_id, $deleted_term, $object_ids) {


        $event_name = 'idocs_email_reporting_' . $deleted_term->slug; 

        if ( wp_next_scheduled( $event_name ) ) {

            wp_clear_scheduled_hook(  $event_name );
            
        }
    }
    /*---------------------------------------------------------------------------------------*/
    // remove cron events during plugin deactivation 
    public static function wpcron_deactivation() {

        // unschedules db cleanup cron event 
        wp_clear_scheduled_hook( self::$db_cleanup_hook );
        /*----------------------------------------------*/
        // unschedule events for email reporting per kb
        $kb_terms = IDOCS_Taxanomies::get_kb_terms_caching();
        /*----------------------------------------------*/
        foreach ( $kb_terms as $term) {

            $summary_report_flag = get_term_meta( $term->term_id, 'idocs-kb-taxo-summary-report-flag', true );
            
            if ( $summary_report_flag == 1 ) {

                $event_name = 'idocs_email_reporting_' . $term->slug; 
                wp_clear_scheduled_hook(  $event_name );

            }
        }
    }    
    /*---------------------------------------------------------------------------------------*/
}