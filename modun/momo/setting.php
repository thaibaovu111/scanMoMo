<?php
require_once '../../config.php';
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập để tiếp tục');
}
else if(empty($_POST['phone'])){
    echo JsonStringFyError('Vui lòng gửi số điện thoại để làm');
}
else if(empty($_POST['type'])){
    echo JsonStringFyError('Vui lòng chọn đúng chức năng để thực hiện');
}
else {
    $phone = Request::Clean_POST('phone');
    switch ($_POST['type']) {
        case 'delete':
            $creat = $conn->query("DELETE FROM `table_momo` WHERE `phone` = '$phone' ");
            if($creat) {
                echo JsonStringFySuccess('Đã xóa thành công số điện thoại này');
            }
            else {
                echo JsonStringFyError('Xóa thất bại vui lòng kiểm tra lại');
            }
            break;
        case 'login':
            $select = $conn->query("SELECT * FROM `table_momo` WHERE `phone` = '$phone' ");
            if(empty($select->num_rows)){
                echo JsonStringFyError('Đănh nhập thất bại số điện thoại '. $phone);
                die();
            }
            else if(($select->fetch_assoc()['TimeLogin'] + 600) > time()) {
                echo JsonStringFyError('Thất bại tài khoản vừa mới đăng nhập');
                die();
            }
            $result = $momo->LoadData($phone)->LoginUser();
            if($result['status'] == 'success') {
                echo JsonStringFySuccess('Đăng nhập thành công số điện thoại '. $phone);
            }
            else {
                echo JsonStringFyError('Đănh nhập thất bại số điện thoại '. $phone);
            }
            break;
        default:
            echo JsonStringFyError('Chức năng không khả dụng vui lòng kiểm tra lại');
            break;
    }
}
?>