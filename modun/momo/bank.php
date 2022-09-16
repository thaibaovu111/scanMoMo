<?php
require_once '../../config.php';
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập lại để tiếp tục');
}
else if(empty($_POST['phone'])){
    echo JsonStringFyError('Vui lòng chọn số điện thoại gửi');
}
else {
    switch ($_POST['type']) {
        case 'check-name':
            if(empty($_POST['accid'])){
                echo JsonStringFyError('Vui lòng nhập số tài khoản cần kiểm tra');
            }
            else if(empty($_POST['bankName'])){
                echo JsonStringFyError('Vui lòng chọn ngân hàng phù hợp');
            }
            else {
                $result = $momo->LoadData(Request::Clean_POST('phone'))->LoginTimeSetup();
                if($result['status'] == 'error'){
                    echo JsonStringFyError('Đăng nhập tài khoản thất bại vui lòng thử lại');
                }
                else {
                    $result = $momo->LoadData(Request::Clean_POST('phone'))->CheckNameBank(Request::Clean_POST('accid'), Request::Clean_POST('bankName'));
                    if($result['status'] == 'error'){
                        echo JsonStringFyError($result['message']);
                    }
                    else {
                        echo JsonStringFy($result);
                    }
                }
            }
            break;
        case 'send-bank':
            if(empty($_POST['accid'])){
                echo JsonStringFyError('Vui lòng nhập số tài khoản cần kiểm tra');
            }
            else if(empty($_POST['bankName'])){
                echo JsonStringFyError('Vui lòng chọn ngân hàng phù hợp');
            }
            else {
                $result = $momo->LoadData(Request::Clean_POST('phone'))->LoginTimeSetup();
                if($result['status'] == 'error'){
                    echo JsonStringFyError('Đăng nhập tài khoản thất bại vui lòng thử lại');
                }
                else {
                    $result = $momo->LoadData(Request::Clean_POST('phone'))->SendMoneyBank(Request::Clean_POST('bankName'), Request::Clean_POST('accid'), Request::Clean_POST('amount'), Request::Clean_POST('comment'));
                    if($result['status'] == 'error'){
                        echo JsonStringFyError($result['message']);
                    }
                    else {
                        echo JsonStringFy($result);
                    }
                }
            }
            break;
        case 'napas':
            if(empty($_POST['cardNumber'])) {
                echo JsonStringFyError('Vui lòng nhập số thẻ để liên kết');
            }
            else if(empty($_POST['name'])){
                echo JsonStringFyError('Vui lòng nhập tên chủ thẻ để liên kết');
            }
            else if(empty($_POST['date'])){
                echo JsonStringFyError('Vui lòng nhập ngày hết hạn của thẻ');
            }
            else {
                $result = $momo->LoadData(Request::Clean_POST('phone'))->LoginTimeSetup();
                $result = $momo->LoadData($_POST['phone'])->CheckCard();
                if($result['status'] == 'success'){
                    echo JsonStringFy(array(
                        'status' => 'error',
                        'message'=> 'Tài khoản này đã liên kết với thẻ rồi'
                    ));
                    $creatCard = $conn->query("UPDATE `table_momo` SET `BankVerify` = '2' WHERE `phone` = '".$_POST['phone']."'");
                    die();
                }
                if($result['status'] == 'success') {
                    $result = $momo->LoadData(Request::Clean_POST('phone'))->VeriFyNapas(Request::Clean_POST('cardNumber'), Request::Clean_POST('name'), $_POST['date']);
                    if($result['status'] == 'success'){
                        $creatCard = $conn->query("UPDATE `table_momo` SET `BankVerify` = '2' WHERE `phone` = '".$_POST['phone']."'");
                        echo JsonStringFySuccess($result['message']);
                    }
                    else {
                        echo JsonStringFyError($result['message']);
                    }
                }
                else {
                    echo JsonStringFyError('Đăng nhập tài khoản thất bại vui lòng thử lại');
                }
            }
            break;
        default:
            echo JsonStringFyError('Lựa chọn không phù hợp chưa có chức năng này');
            break;
    }
}
?>