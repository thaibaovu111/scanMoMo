<?php
require_once '../../../config.php';
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập lại để tiếp tục');
}
else if(empty($_POST['file'])){
    echo JsonStringFyError('Vui lòng chọn file cần chạy lại');
}
else if(empty($_PROXY)) {
    echo JsonStringFyError('Không có proxy hoạt động trên web vui lòng thử lại');
}
else {
    $id = Request::Clean_POST('file');
    $select = $conn->query("SELECT MIN(`count_error`) FROM `table_message` WHERE `file_id` = '$id' 
                                                                                AND `status` = 'error' ")
                                                                                ->fetch_assoc()['MIN(`count_error`)'];


        $info_mess = $conn->query("SELECT * FROM `table_message` WHERE `file_id` = '$id' AND `status` = 'error' AND 
                                                                        `count_error` = '$select' LIMIT 1 ");
        if(empty($info_mess->num_rows)){
            echo JsonStringFyError('Đã chạy lại hết đơn lỗi ');
        }
        else {
            $mess_data = $info_mess->fetch_assoc();
            $result = $momo->LoadData($mess_data['sender'])->LoginTimeSetup();
            
            if($result['status'] == 'success'){
                $result_chat = $momo->LoadData($mess_data['sender'])->SendMess($mess_data['receiver'], $mess_data['message']);
                if(!empty($mess_data['image_link'])){
                    $momo->LoadData($mess_data['sender'])->SendImageLink($mess_data['receiver'], $mess_data['image_link']);
                }
                if($result_chat['status'] == 'success'){
                    $creat = $conn->query("UPDATE `table_message` SET `status` = 'success',
                                                                      `time`   = now(),
                                                                      `reason` = '".$result_chat['message']."',
                                                                      `count_error` = 0  WHERE `id` = '".$mess_data['id']."'");
                    echo JsonStringFySuccess($result_chat['message']);
                }
                else {
                    $creat = $conn->query("UPDATE `table_message` SET `status` = 'error',
                                                                        `time`   = now(),
                                                                        `reason` = '".$result_chat['message']."',
                                                                        `count_error` = `count_error` + 1  WHERE `id` = '".$mess_data['id']."'");
                    echo JsonStringFyError($result_chat['message']);
                }
            }
            else {
                echo JsonStringFyError('Đăng nhập tài khoản gửi tin thất bại');
            }
        }

}
?>