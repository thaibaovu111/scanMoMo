<?php
require_once '../../config.php';
require_once '../../system/core/chothuesim.php';
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập lại để tiếp tục');
}
else if(empty($_POST['id'])){
    echo JsonStringFyError('Đã xảy ra lỗi vui lòng chạy lại');
}
else {
    $Chosim = new SMS;
    $result = $Chosim->Loadkey($site['site_sms_partner_key'])->GetCode($_POST['id']);
    if(empty($result['ResponseCode'])){
        echo JsonStringFy(array(
            'status' => 'success',
            'message'=> 'Lấy mã OTP Thành công',
            'code'   => $result['Result']['Code'],
            'success'=> true,
            'error'  => false
        ));
    }
    else {
        echo JsonStringFy(array(
            'status' => 'error',
            'message'=> 'Chưa có mã OTP vui lòng thử lại',
            'success'=> false,
            'error'  => true
        ));
    }
}
?>