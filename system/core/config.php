<?php
define('SITE_URL', 'http://smsvip.me');
define("DATABASE", "admincheck");
define("USERNAME", "root");
define("PASSWORD", "");
define("LOCALHOST", "localhost");
require_once ('database.php');
$db = new Database(DATABASE, USERNAME, PASSWORD, LOCALHOST);
$sqlConnect = $conn = mysqli_connect(LOCALHOST, USERNAME, PASSWORD, DATABASE);
require_once ('requests.php');
require_once ('class.momo.php');
require_once ('class.viettel.php');
require_once ('class.tinsoft.php');

$ServerErrors = array();
if (mysqli_connect_errno()) {
	$ServerErrors[] = "Failed to connect to MySQL: " . mysqli_connect_error();
}
if (!function_exists('curl_init')) {
	$ServerErrors[] = "PHP CURL is NOT installed on your web server !";
}
if (!extension_loaded('gd') && !function_exists('gd_info')) {
	$ServerErrors[] = "PHP GD library is NOT installed on your web server !";
}
if (!extension_loaded('zip')) {
	$ServerErrors[] = "ZipArchive extension is NOT installed on your web server !";
}
$query = mysqli_query($sqlConnect, "SET NAMES utf8mb4");
if (isset($ServerErrors) && !empty($ServerErrors)) {
	foreach ($ServerErrors as $Error) {
		echo "<h3>" . $Error . "</h3>";
	}
	die();
}


$site = $sqlConnect->query("SELECT * FROM `table_site` LIMIT 1 ")->fetch_assoc();



?>