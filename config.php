<?php
error_reporting(E_ALL);
require_once ('system/init.php');
set_error_handler('ShowErrorHander');
use Firebase\JWT\JWT;

$site = $sqlConnect->query("SELECT * FROM `table_site` LIMIT 1 ")->fetch_assoc();

$site_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http") . "://".$_SERVER['HTTP_HOST'] . '/'.'momotool/';

$site_link = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http") . "://".$_SERVER['HTTP_HOST'];

$site_domain = parse_url($site_url)['host'];

$site_public = $site_url. 'public';

$site_modun  = $site_url. 'modun';

$_SERVER_PRIVATE_KEY  = "VANBBSBBSBSB";

if(!empty($_COOKIE['AUTH_TOKEN'])){
	try{
		$user = (array)	JWT::decode(trim($_COOKIE['AUTH_TOKEN']), $_SERVER_PRIVATE_KEY, array('HS256'));
        $username = $user['username'];
	}
	catch(\Throwable $e){
		header("Location: ". $site_url. 'logout');
		die();
	}
    $user_info = mysqli_fetch_assoc(mysqli_query($sqlConnect, "SELECT `username`, `email`, `level` FROM `table_user` WHERE `username` = '$username' "));
}

$CheckProxy = false;

$TimeStamp  = time();
$select = mysqli_query($conn,"SELECT * FROM `table_tinsoft` WHERE `user_key` = '".$site['site_tinsoft_partner_key']."' ORDER BY `id`");
$results = array();
while ($rows = mysqli_fetch_assoc($select)){
	if(strtotime($rows['date_expired']) > time()){
		$CheckProxy = true;
		$results[]  = $rows;
	}

}
$_PROXY = '';
if(empty($_COOKIE['PROXY']) and empty($_COOKIE['TIME_OUT'])){
	if(!empty(count($results))){
		$result = $results[array_rand($results,1)];
		if(strtotime($result['date_expired']) > time()){
			$Tinsoft = new Tinsoft;

			setcookie('TIME_OUT', true, time() + 10, '/', $site_domain);

			$tinresult = $Tinsoft->LoadKeyProxy($result['proxy_key'])->GetNewProxy();
			if($tinresult['success']){
				setcookie('PROXY', base64_encode($tinresult['proxy']), time() + $tinresult['next_change'], '/', $site_domain);
					$_PROXY = $tinresult['proxy'];
			}
			else {
				$tinresult = $Tinsoft->LoadKeyUser($result['proxy_key'])->GetMyProxy();
				if($tinresult['success']){
					setcookie('PROXY', base64_encode($tinresult['proxy']), time() + $tinresult['next_change'], '/', $site_domain);

					$_PROXY = $tinresult['proxy'];
				}
			}
		}
	}
}

if(!empty($_COOKIE['PROXY'])){
	$_PROXY = base64_decode($_COOKIE['PROXY']);
}
@ $momo = new MOMO(DATABASE, USERNAME, PASSWORD, LOCALHOST, $_PROXY);

?>