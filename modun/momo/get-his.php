<?php
var_dump($_GET['phone']);
include_once '../../config.php';
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập lại để tiếp tục');
}
else if(empty($_POST['phone']) and empty($_GET['phone'])){
    echo JsonStringFyError('Vui lòng nhập số điện thoại cần lấy lịch sử');
}
else {
    $_POST['phone'] = empty($_POST['phone']) ? $_GET['phone'] : $_POST['phone'];

    $days = empty($_POST['days']) ? 5 : (int) $_POST['days'];

    $first = substr(Request::Clean_POST('phone'), 0, 2);
    if($first == '08' or $first == '09'){
        $select = $conn->query("SELECT `phone` FROM `table_momo` WHERE  `phone` LIKE '087%' AND `success` = 'true' ORDER BY RAND() LIMIT 1 ");
    }
    else {
        $select = $conn->query("SELECT `phone` FROM `table_momo` WHERE `phone` LIKE '05%' OR `phone` LIKE '09%' OR `phone` LIKE '08%' AND `success` = 'true' ORDER BY RAND() LIMIT 1 ");
    }

    if(empty($select->num_rows)){
        echo JsonStringFyError('Không có số điện thoại phù hợp trong hệ thống để tra cứu');
    }
    else {
        $PhoneNumber = $select->fetch_assoc()['phone'];
        $result = $momo->LoadData($PhoneNumber)->LoginTimeSetup();
        if($result['status'] == 'error'){
            if($result['code'] != -5) {
                $conn->query("DELETE * FROM `table_momo` WHERE `phone` = '$PhoneNumber' ");
            }

            echo JsonStringFyError($result['message']);
            
        }
        else {
            echo JsonStringFy($momo->LoadData($PhoneNumber)->CheckHisPhone(Request::Clean_POST('phone'), $days));
        } 
    }
    
}
?>