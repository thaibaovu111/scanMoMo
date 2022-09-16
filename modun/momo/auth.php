<?php
require_once '../../config.php';
if(empty($username)){
    echo JsonStringFy(array(
        'status' => 'error',
        'message'=> 'Vui lòng đăng nhập lại'
    )); 
    die();
}
else if(empty($_POST['phone'])){
    echo JsonStringFy(array(
        'status' => 'error',
        'message'=> 'Vui lòng không để trống số điện thoại'
    ));
    die();
}
else if(empty($_POST['pass'])){
    echo JsonStringFy(array(
        'status' => 'error',
        'message'=> 'Vui lòng không để trống mật khẩu'
    ));
    die();
}

if($_POST['type'] == 'register') {
    if(empty($_POST['name'])){
        echo JsonStringFy(array(
            'status' => 'error',
            'message'=> 'Vui lòng không để trống số họ và tên'
        ));
        die();
    }
    if(empty($_POST['otp'])){
        var_dump($momo);
        $result = $momo->LoadData($_POST['phone'])->CheckBeUser();
        if(!empty($result['NAME'])){
            echo JsonStringFy(array(
                'status'  => 'error',
                'message' => 'Thất bại số điện thoại này đã được đăng ký!!'
            ));
            $conn->query("DELETE FROM `table_momo` WHERE `phone` = '".$_POST['phone']."' ");
            die();
        }
        else if($result['status'] != 'error'){
            echo JsonStringFy(array(
                'status' => 'error',
                'message'=> 'Thất bại đã xảy ra lỗi máy chủ'
            )); 
            die();
        }
        $result = $momo->LoadData($_POST['phone'])->SendOTP();
        if($result['status'] == 'success'){
            echo JsonStringFy(array(
                'status' => 'success',
                'message'=> 'Đã gửi mã OTP thành công cho số điện thoại '.$_POST['phone']
            ));
            die();
        }
        else {
            echo JsonStringFy(array(
                'status' => 'error',
                'message'=> 'Thất bại không thể gửi mã OTP vui lòng báo lại với admin!!'
            ));
            $conn->query("DELETE FROM `table_momo` WHERE `phone` = '".$_POST['phone']."' "); 
            die();
        }
    }
    else if(!empty($_POST['otp'])){
        $result = $momo->LoadData($_POST['phone'])->ImportOTP(trim($_POST['otp']));
        if($result['status'] == 'error'){
            echo JsonStringFy($result);
            die();
        }
        $result = $momo->LoadData($_POST['phone'])->Register($_POST['pass'], $_POST['name'], $_POST['sex'] ?? 1);
        if($result['status'] == 'error'){
            echo JsonStringFy($result);
            die();
        }
        else if($result['status'] == 'success'){
            $result = $momo->LoadData($_POST['phone'])->UpDateProFile();
            echo JsonStringFy(array(
                'status' => 'success',
                'success'=> true,
                'message'=> "Đăng ký mới tài khoản momo Thành Công"
            ));
        }
    }
}
else if($_POST['type'] == 'login') {
    if(empty($_POST['otp'])){
        $result = $momo->LoadData($_POST['phone'])->CheckBeUser();
        if($result['status'] == 'error') {
            echo JsonStringFyError('Số điện thoại này chưa được đăng ký tài khoản momo');
            $conn->query("DELETE FROM `table_momo` WHERE `phone` = '".$_POST['phone']."' ");
        }
        else if($result['status'] == 'success'){
            $result = $momo->LoadData($_POST['phone'])->SendOTP();
            if($result['status'] == 'success') {
                echo JsonStringFySuccess('Đã gửi thành công mã OTP vui lòng kiểm tra và nhập');
            }
            else {
                echo JsonStringFyError('Gửi mã OTP thất bại vui lòng kiểm tra lại');
            }
        }
    }
    else if(!empty($_POST['otp'])){
        $result = $momo->LoadData($_POST['phone'])->ImportOTP(trim($_POST['otp']));
        if($result['status'] == 'success') {
            $result = $momo->LoadData($_POST['phone'])->LoginUser($_POST['pass']);
            if($result['status'] == 'success'){
                echo JsonStringFySuccess('Đăng nhập tài khoản thành công');
            }
            else {
                echo JsonStringFyError('Mật khẩu không chính xác vui lòng thử lại');
            }
        }
        else {
            echo JsonStringFyError('Bạn đã nhập sai mã OTP vui lòng thử lại');
        }
    }
}

?>