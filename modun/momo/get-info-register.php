<?php
require_once '../../config.php';
require_once '../../system/core/chothuesim.php';
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập để tiếp tục');
}
else {
    $name = $conn->query("SELECT * FROM `table_name` ORDER BY RAND() LIMIT 1 ")->fetch_assoc();
    if(empty($name['name'])){
        echo JsonStringFyError('Đã xảy ra lỗi khi lấy tên random');
    }
    else {
        $Chosim = new SMS;
        $result = $Chosim->Loadkey($site['site_sms_partner_key'])->GetPhoneMoMo();
        if(empty($result['ResponseCode'])){
            if(empty($result['Result']['Number'])){
                echo JsonStringFyError('Lỗi api chothuesim không trả số điện thoại');
                die();
            }
            echo JsonStringFy(array(
                'success' => true,
                'status' => 'success',
                'error'  => false,
                'message'=> 'Lấy thông tin thành công',
                'name'   => $name['name'],
                'sex'    => $name['sex'],
                'phone'  => strlen($result['Result']['Number']) == 9 ? '0'.$result['Result']['Number'] : $result['Result']['Number'],
                'id'     => $result['Result']['Id']
            ));
        }
        else {
            echo JsonStringFy(array(
                'status' => 'error',
                'code'   => -5,
                'success'=> false,
                'error'  => true,
                'message'=> $result['Msg']
            ));
        }
    }
}

?>