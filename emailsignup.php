<?php

if (!function_exists('add_action'))
{
    require_once("../../../wp-config.php");
}

if ((isset($_POST['email'])) && (strlen(trim($_POST['email'])) > 0)) {
	$email = stripslashes(strip_tags($_POST['email']));
} else {$email = 'No email entered';}
ob_start();

$body = 'subscribe';

$toaddy = $_POST['toaddy'] . "@aweber.com";

if (!wp_mail( $toaddy, 'Subscribe', 'Subscribe', 'From: '.$email))
{
    die('Mail not sent');
}