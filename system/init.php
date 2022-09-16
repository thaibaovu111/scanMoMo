<?php
if (!version_compare(PHP_VERSION, '5.5.0', '>=')) {
	exit("Required PHP_VERSION >= 5.5.0 , Your PHP_VERSION is : " . PHP_VERSION . "\n");
}
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once 'library/vendor/autoload.php';
require_once 'core/XLSXReader.php';
require_once 'core/tables.php';
require_once 'core/config.php';
require_once 'core/function.db.php';
require_once 'core/function.member.php';
require_once 'core/function.php';
require_once 'library/vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
require_once 'core/class.zalopay.php';

?>