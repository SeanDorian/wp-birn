<?php
global $wpsc_error_reporting;
if($wpsc_error_reporting==false) {
    error_reporting(0);
}
if (!function_exists('add_action'))
{
    require_once("../../../../wp-config.php");
}

global $current_user, $wpdb, $wpscSupportTickets;

if(is_user_logged_in()) {
    if ( function_exists('current_user_can') && !current_user_can('manage_wpsc_support_tickets')) {
            die(__('Cheatin&#8217; uh?', 'wpsc-support-tickets'));
    }

    if(@isset($_GET['ticketid']) && @is_numeric($_GET['ticketid']) && @!isset($_GET['replyid'])) {
        $primkey = intval($_GET['ticketid']);

        $wpdb->query("DELETE FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$primkey}';");
        $wpdb->query("DELETE FROM `{$wpdb->prefix}wpscst_replies` WHERE `ticket_id`='{$primkey}';");
        header("HTTP/1.1 301 Moved Permanently");
        header ('Location: '.get_admin_url().'admin.php?page=wpscSupportTickets-admin');
        exit();
    }
     if(@isset($_GET['replyid']) && @is_numeric($_GET['replyid']) && @isset($_GET['ticketid']) && @is_numeric($_GET['ticketid'])) {
        $primkey = intval($_GET['replyid']);
        $ticketprimkey = intval($_GET['ticketid']);

        $wpdb->query("DELETE FROM `{$wpdb->prefix}wpscst_replies` WHERE `primkey`='{$primkey}';");
        header("HTTP/1.1 301 Moved Permanently");
        header ('Location: '.get_admin_url().'admin.php?page=wpscSupportTickets-edit&primkey='.$ticketprimkey);
        exit();
    }

}


?>