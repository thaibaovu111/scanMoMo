<?php
require_once 'config.php';
if(!empty($user_info['username'])){
    header('Location: '. $site_url. 'home');
    die();
}
else if(empty($user_info['username'])){
    header("Location: ". $site_url. 'auth/login');
    die();
}
?>
