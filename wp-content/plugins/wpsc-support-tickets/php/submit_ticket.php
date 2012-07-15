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

$devOptions = NULL;
$devOptions = $wpscSupportTickets->getAdminOptions();
if(!isset($devOptions['mainpage']) || $devOptions['mainpage']=='') {
    $devOptions['mainpage'] = home_url();
}

if (session_id() == "") {@session_start();};
if(is_user_logged_in() || @isset($_SESSION['wpsc_email'])) {
   

    if(trim($_POST['wpscst_initial_message'])=='' || trim($_POST['wpscst_title'])=='') {// No blank messages/titles allowed
            if(!headers_sent()) {
                header("HTTP/1.1 301 Moved Permanently");
                header ('Location: '.get_permalink($devOptions['mainpage']));
                exit();
            } else {
                echo '<script type="text/javascript">
                        <!--
                        window.location = "'.get_permalink($devOptions['mainpage']).'"
                        //-->
                        </script>';
            }
        } 
    

    $wpscst_title = base64_encode(strip_tags($_POST['wpscst_title']));
    $wpscst_initial_message = base64_encode($_POST['wpscst_initial_message']);
    $wpscst_department = base64_encode(strip_tags($_POST['wpscst_department']));
    
    // Guest additions here
    if(is_user_logged_in()) {
        $wpscst_userid = $current_user->ID;
        $wpscst_email = $current_user->user_email;
    } else {
        $wpscst_userid = 0;
        $wpscst_email = $wpdb->escape($_SESSION['wpsc_email']);      
    }

    $sql = "
    INSERT INTO `{$wpdb->prefix}wpscst_tickets` (
        `primkey`, `title`, `initial_message`, `user_id`, `email`, `assigned_to`, `severity`, `resolution`, `time_posted`, `last_updated`, `last_staff_reply`, `target_response_time`, `type`) VALUES (
            NULL,
            '{$wpscst_title}',
            '{$wpscst_initial_message}',
            '{$wpscst_userid}',
            '{$wpscst_email}',
            '0',
            'Normal',
            'Open',
            '".time()."',
            '".time()."',
            '',
            '2 days',
            '{$wpscst_department}'
        );
    ";

    $wpdb->query($sql);
    $lastID = $wpdb->insert_id;

    $to      = $wpscst_email; // Send this to the ticket creator
    $subject = $devOptions['email_new_ticket_subject'];
    $message = $devOptions['email_new_ticket_body'];
    $headers = 'From: ' . $devOptions['email'] . "\r\n" .
        'Reply-To: ' . $devOptions['email'] .  "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    /*wp_mail($to, $subject, $message, $headers); Mail has been disabled*/
    

    $to      = $devOptions['email']; // Send this to the admin
    $subject = $_POST['wpscst_title'];
    $message = __('A New Problem Report has been Created: ','wpsc-support-tickets').get_admin_url().'admin.php?page=wpscSupportTickets-edit&primkey='.$lastID." 

".$_POST['wpscst_title']."

".$_POST['wpscst_initial_message'];
    $headers = 'From: Problem_Reports@thebirn.com';
    mail($to, $subject, $message, $headers);

}

if(!headers_sent()) {
    header("HTTP/1.1 301 Moved Permanently");
    header ('Location: '.get_permalink($devOptions['mainpage']));
    
} else {
    echo '<script type="text/javascript">
            <!--
            window.location = "'.get_permalink($devOptions['mainpage']).'"
            //-->
            </script>';
}

    exit();

?>