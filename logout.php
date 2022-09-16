<?php
$site_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http") . "://".$_SERVER['HTTP_HOST'] . '/'.'momotool/';
if(empty($_COOKIE)){
    header('Location: '.$site_url);
    die();
}
foreach ($_COOKIE as $keys => $item){

        setcookie($keys,'',time() - 500,'/', $_SERVER['HTTP_HOST']);
        
}
header('Location: '.$site_url);
die();
?>