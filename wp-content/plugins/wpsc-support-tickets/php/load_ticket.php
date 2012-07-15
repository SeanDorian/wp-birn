<?php
global $wpsc_error_reporting;
if($wpsc_error_reporting==false) {
    error_reporting(0);
}
if (!function_exists('add_action'))
{
    require_once("../../../../wp-config.php");
}

global $current_user, $wpdb;

if (session_id() == "") {@session_start();};

if((is_user_logged_in() || @isset($_SESSION['wpsc_email'])) && is_numeric($_POST['primkey'])) {
    // Guest additions here
    if(is_user_logged_in()) {
        $wpscst_userid = $current_user->ID;
        $wpscst_email = $current_user->user_email;
        $wpscst_username = $current_user->display_name;
    } else {
        $wpscst_userid = 0;
        $wpscst_email = $wpdb->escape($_SESSION['wpsc_email']);   
        $wpscst_username = __('Guest', 'wpsc-support-tickets').' ('.$wpscst_email.')';
    }    
    
    $primkey = intval($_POST['primkey']);

    $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}' AND `user_id`='{$wpscst_userid}' AND `email`='{$wpscst_email}' LIMIT 0, 1;";
    $results = $wpdb->get_results( $sql , ARRAY_A );
    if(isset($results[0])) {
        echo '<div id="wpscst_meta"><strong>'.base64_decode($results[0]['title']).'</strong> ('.$results[0]['resolution'].' - '.base64_decode($results[0]['type']).')</div>';
        echo '<table style="width:100%;">';
        echo '<thead><tr><th id="wpscst_results_posted_by">'.__('Posted by', 'wpsc-support-tickets').' '.$wpscst_username.' (<span id="wpscst_results_time_posted">'.date('Y-m-d g:i A',$results[0]['time_posted']).'</span>)</th></tr></thead>';

        $messageData = strip_tags(base64_decode($results[0]['initial_message']),'<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>');
        $messageData = explode ( '\\', $messageData);
        $messageWhole = '';
        foreach ($messageData as $messagePart){
        $messageWhole .= $messagePart;	
        }
        echo '<tbody><tr><td id="wpscst_results_initial_message"><br />'.$messageWhole;        
        
        //echo '<tbody><tr><td id="wpscst_results_initial_message"><br />'.strip_tags(base64_decode($results[0]['initial_message']),'<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>').'</td></tr>';
        echo '</tbody></table>';

        $results = NULL;
        $sql = "SELECT * FROM `{$wpdb->prefix}wpscst_replies` WHERE `ticket_id`='{$primkey}' ORDER BY `timestamp` ASC;";
        $result2 = $wpdb->get_results( $sql , ARRAY_A );
        if(isset($result2)) {
            foreach ($result2 as $results) {
                $classModifier1 = NULL;$classModifier2 = NULL;$classModifier3 = NULL;
                if($results['user_id']!=0) {
                    @$user=get_userdata($results['user_id']);
                    @$userdata = new WP_User($results['user_id']);
                    if ( $userdata->has_cap('manage_wpsc_support_tickets') ) {
                        $classModifier1 = ' class="wpscst_staff_reply_table" ';
                        $classModifier2 = ' class="wpscst_staff_reply_thead" ';
                        $classModifier3 = ' class="wpscst_staff_reply_tbody" ';
                    }
                    $theusersname = $user->user_nicename;
                } else {
                    $user = false; // Guest
                    $theusersname = __('Guest', 'wpsc-support-tickets');
                }

                echo '<br /><table style="width:100%;" '.$classModifier1.'>';
                echo '<thead '.$classModifier2.'><tr><th class="wpscst_results_posted_by">'.__('Posted by', 'wpsc-support-tickets').' '.$theusersname.' (<span class="wpscst_results_timestamp">'.date('Y-m-d g:i A',$results['timestamp']).'</span>)</th></tr></thead>';
                $messageData = strip_tags(base64_decode($results['message']),'<p><br><a><br><strong><b><u><ul><li><strike><sub><sup><img><font>');
                $messageData = explode ( '\\', $messageData);
                $messageWhole = '';
                foreach ($messageData as $messagePart){
                $messageWhole .= $messagePart;	
                }
                echo '<tbody '.$classModifier3.'><tr><td class="wpscst_results_message"><br />'.$messageWhole.'</td></tr>';
                echo '</tbody></table>';
            }
        }
    }
}

exit();

?>