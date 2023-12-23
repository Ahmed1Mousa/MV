<?php
// Start output buffering and set timezone
ob_start();
ini_set('date.timezone','Africa/Cairo');
date_default_timezone_set('Africa/Cairo');
session_start();

// Include necessary files
require_once('initialize.php');
require_once('classes/DBConnection.php'); // Include the DBConnection class file here
require_once('classes/SystemSettings.php');

// Initialize the DBConnection class to create a database connection
$db = new DBConnection;
$conn = $db->conn;

// Your existing functions

function redirect($url = '') {
    if(!empty($url)) {
        echo '<script>location.href="'.base_url .$url.'"</script>';
    }
}

function validate_image($file) {
    $file = explode("?", $file)[0];
    if(!empty($file)) {
        if(is_file(base_app.$file)) {
            return base_url.$file;
        } else {
            return base_url.'dist/img/no-image-available.png';
        }
    } else {
        return base_url.'dist/img/no-image-available.png';
    }
}

function isMobileDevice() {
    $aMobileUA = array(
        '/iphone/i' => 'iPhone', 
        '/ipod/i' => 'iPod', 
        '/ipad/i' => 'iPad', 
        '/android/i' => 'Android', 
        '/blackberry/i' => 'BlackBerry', 
        '/webos/i' => 'Mobile'
    );

    foreach($aMobileUA as $sMobileKey => $sMobileOS) {
        if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
    }
    
    return false;
}

function format_num($number = 0, $max_decimal = '') {
    if(is_numeric($number)) {
        $ex = explode('.',$number);
        $dec = isset($ex[1]) ? strlen($ex[1]) : 0;
        if(empty($max_decimal) || (is_numeric($max_decimal) && $max_decimal >= $dec)) {
            return number_format($number, $dec);
        } else {
            if(is_numeric($max_decimal)) {
                return number_format($number, $max_decimal);
            } else {
                return "Invalid Maximum Decimal";
            }
        }
    } else {
        return "Invalid Number";
    }
}

// End output buffering
ob_end_flush();
?>
