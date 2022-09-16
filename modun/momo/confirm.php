<?php
require_once '../../config.php';
header("content-type: charset=utf-8");
if(empty($_GET['phone'])){
      header("content-type: application/json;charset=utf-8");
      echo json_encode(array(
            'status' => 'error',
            'message'=> 'Thất bại thiếu số điện thoại'
      ),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
      die();
}
else if(empty($_GET['code'])){
      header("content-type: application/json;charset=utf-8");
      echo json_encode(array(
            'status' => 'error',
            'message'=> 'Thất bại thiếu mã otp'
      ),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
      die();
}

echo $momo->LoadData(trim($_GET['phone']))->ConfirmNaPas($_GET['code']);
?>