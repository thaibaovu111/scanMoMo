<?php
require_once '../../config.php';
use Firebase\JWT\JWT;
if(!empty($user_info['username'])){
   echo JsonStringFyError('Bạn đã đăng nhập vui lòng đăng xuất để đăng nhập lại');
}
else if(empty($_POST['email'])){
   echo JsonStringFyError('Vui lòng điền email hoặc tên người dùng để đăng nhập');

}
else if(empty($_POST['password'])){
   echo JsonStringFyError('Vui lòng điền mật khẩu để đăng nhập');
}
else {
    $email = Request::Clean_POST('email');
    $password = md5($_POST['password']);
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        $select = $conn->query("SELECT * FROM `table_user` WHERE `email` = '$email' AND `password` = '$password' ")->fetch_assoc();
        if(empty($select['email'])){
            echo JsonStringFyError('Email hoặc mật khẩu không chính xác vui lòng kiểm tra lại');
        }
        else if($select['email']  == $email){
            echo JsonStringFy(array(
                'error' => false,
                'success' => true,
                'message' => 'Đăng nhập vào tài khoản thành công thành công'
            ));
        }
    }
    else {
        $select = $conn->query("SELECT * FROM `table_user` WHERE `username` = '$email' AND `password` = '$password' ")->fetch_assoc();
        if(empty($select['username'])){
            echo JsonStringFyError('Tài khoản hoặc mật khẩu không chính xác vui lòng kiểm tra lại');
        }
        else if($select['username'] == $email){
            echo JsonStringFy(array(
                'error' => false,
                'success' => true,
                'message' => 'Đăng nhập vào tài khoản thành công thành công'
            ));
        }
    }
    $AUTH_TOKEN = JWT::encode($select,$_SERVER_PRIVATE_KEY);
    setcookie('AUTH_TOKEN', $AUTH_TOKEN, (!empty($_POST['remember'])) ? time() + (86400 * 30) : time() + 86400 , '/', $site_domain);
}
?>