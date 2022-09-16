<?php
require_once '../../config.php';
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập để tiếp tục');
}
else if(empty($_POST['type'])){
    echo JsonStringFyError('Vui lòng chọn chức năng thực hiện');
}
else {
    switch ($_POST['type']) {
        case 'get-name':
            if(empty($_POST['receiver'])){
                echo JsonStringFyError('Vui lòng nhập số điện thoại cần kiểm tra');
            }
            else {
                $receiver = Request::Clean_POST('receiver');
                if(empty($_POST['phone'])){
                    $phone = $conn->query("SELECT `phone` FROM `table_momo` WHERE `success` = 'true' ORDER BY RAND()")->fetch_assoc()['phone'];
                    $result = $momo->LoadData($phone)->LoginTimeSetup();
                }
                else 
                {
                    $phone = Request::Clean_POST('phone');
                }
                $results = $momo->LoadData($phone)->GetNamePrivate($receiver);
                echo JsonStringFy($results);
            }
            break;
        case 'send':
            if(empty($_POST['phone'])){
                echo JsonStringFyError('Vui lòng chọn số điện thoại gửi đi');
            }
            else if(empty($_POST['receiver'])){
                echo JsonStringFyError('Vui lòng nhập số điện thoại nhận tiền');
            }
            else if(empty($_POST['amount'])){
                echo JsonStringFyError('Vui lòng nhập số tiền chuyển đi');
            }
            else if($_POST['amount'] < 100){
                echo JsonStringFyError('Số tiền chuyển phải lớn hơn 100đ');
            }
            else {
                $sender   = Request::Clean_POST('phone');
                $receiver = Request::Clean_POST('receiver');
                $amount   = Request::Clean_POST('amount');
                $comment  = Request::Clean_POST('comment') ?? '';

                $result = $momo->LoadData($sender)->LoginTimeSetup();
                if($result['status'] != 'success'){
                    echo JsonStringFyError('Đăng nhập tài khoản momo '. $sender. ' thất bại');
                    die();
                }
                $result = $momo->LoadData($sender)->SendMoney($receiver, $amount , $comment);
                if($result['status'] == 'success'){
                    echo JsonStringFySuccess('Chuyển tiền thành công đến số điện thoại '.$receiver);
                }
                else {
                    echo JsonStringFyError($result['message']);
                }
            }

            break;
        case 'request':
            if(empty($_POST['phone'])){
                echo JsonStringFyError('Vui lòng chọn số điện thoại gửi đi');
            }
            else if(empty($_POST['receiver'])){
                echo JsonStringFyError('Vui lòng nhập số điện thoại nhận tiền');
            }
            else if(empty($_POST['amount'])){
                echo JsonStringFyError('Vui lòng nhập số tiền chuyển đi');
            }
            else if($_POST['amount'] < 100){
                echo JsonStringFyError('Số tiền chuyển phải lớn hơn 100đ');
            }
            else {
                $sender   = Request::Clean_POST('phone');
                $receiver = Request::Clean_POST('receiver');
                $amount   = Request::Clean_POST('amount');
                $comment  = Request::Clean_POST('comment') ?? '';

                $result = $momo->LoadData($sender)->LoginTimeSetup();
                if($result['status'] != 'success'){
                    echo JsonStringFyError('Đăng nhập tài khoản momo '. $sender. ' thất bại');
                    die();
                }
                $result = $momo->LoadData($sender)->RequestMoney($receiver, $amount , $comment);
                if($result['status'] == 'success'){
                    echo JsonStringFySuccess('Yêu cầu chuyển tiền thành công đến số điện thoại '.$receiver);
                }
                else {
                    echo JsonStringFyError($result['message']);
                }
            }
            break;
        case 'send-mess': 
            if(empty($_POST['receiver'])){
                echo JsonStringFyError('Vui lòng nhập số điện thoại nhận');
            }
            else {
                $sender   = Request::Clean_POST('phone');
                $receiver = Request::Clean_POST('receiver');
                $comment  = $_POST['message'];
                $result = $momo->LoadData($sender)->LoginTimeSetup();
                if($result['status'] != 'success'){
                    echo JsonStringFyError('Đăng nhập tài khoản momo '. $sender. ' thất bại');
                    die();
                }
                $result = $momo->LoadData($sender)->SendMess($receiver, $comment);
                if(check_img('image') == true){
                    $uploads_dir = 'file';
                    $number_random = random('1234567890qwertyuiopasdfghjklzxcvbnm', 10);
                    $tmp_name_front = $_FILES['image']['tmp_name'];
                    $file_excel = "$uploads_dir/$number_random.png";
                    $create = move_uploaded_file($tmp_name_front, $file_excel);

                    if(!$create) {
                        echo JsonStringFyError('Đã xảy ra lỗi khi up load file');
                        die();
                    }
                    else {

                        $image = file_get_contents("$uploads_dir/$number_random.png");
                        $result_image = $momo->LoadData($sender)->SendImage($receiver, $image);
                    }
                }
                if($result['status'] == 'success'){
                    echo JsonStringFySuccess('Gửi tin nhắn thành công đến số điện thoại '.$receiver);
                    $conn->query("UPDATE `table_momo` SET `messages_sent` = `messages_sent` + 1 WHERE `phone` = '$sender' ");
                    $addfriend = $momo->LoadData($sender)->AddFriend($receiver);
                    $table_chat = $conn->query("INSERT INTO `table_message` SET `sender` = '$sender',
                                                                                `receiver` = '$receiver',
                                                                                `message`  = '".StringDataBase($comment)."',
                                                                                `status` = 'success',
                                                                                `time` = now() ");
                }
                else {
                    echo JsonStringFyError($result['message']);
                    $table_chat = $conn->query("INSERT INTO `table_message` SET `sender` = '$sender',
                                                                                `receiver` = '$receiver',
                                                                                `message`  = '".StringDataBase($comment)."',
                                                                                `status` = 'error',
                                                                                `time` = now() ");
                }

            }
            break;
        default:
            echo JsonStringFyError('Lựa chọn không hợp lệ');
            break;
    }
}
?>