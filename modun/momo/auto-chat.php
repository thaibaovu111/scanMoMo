<?php
require_once '../../config.php';
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập để tiếp tục');
}
else if(empty($_POST['file_id'])){
    echo JsonStringFyError('Vui lòng chọn 1 file dữ liệu để chạy');
}
else {
    $select = $conn->query("SELECT * FROM `table_excel` WHERE `id` = '".Request::Clean_POST('file_id')."' ");
    if(empty($select->num_rows)) {
        echo JsonStringFyError('File dữ liệu không tồn tại hoặc đã bị xóa');

    }
    else {
        if(empty($_PROXY)) {
            echo JsonStringFyError('Không có proxy tin nhắn chưa được gửi đi');
            die();
        }

        $data = $select->fetch_assoc();
        $number = $data['num_rows'];
        if($number > ($data['total'] - 1)){

            echo JsonStringFyError('Đơn chat đã hoàn thành bạn không thể chạy lại');
            die();
        }
        try {

            $data_message = json_decode(file_get_contents('file/'.basename($data['file']), FILE_USE_INCLUDE_PATH), true);

        }
        catch (\Throwable $e) {
            echo JsonStringFy('Lỗi file vui lòng thử lại ');
            die();
        }
        if(empty($data_message)) {
            echo JsonStringFyError('Đã xảy ra lỗi khi lấy dữ liệu file');
            die();
        }
        $message = $data_message[$number]['1'];
        $phone_nhan = substr($data_message[$number]['0'], 0, 1) == 0 ? $data_message[$number]['0'] : '0'.$data_message[$number]['0'];
        $phone_recevice = Phonenumber::convert($phone_nhan);

        if(empty($message)) {

            $update = $conn->query("UPDATE `table_excel` SET `num_rows` = `num_rows` + 1 WHERE `id` = '".Request::Clean_POST('file_id')."' ");
            echo JsonStringFyError('Không tồn tại tin nhắn nào');
            die();
        }
        else if(empty($phone_recevice)) {
            $update = $conn->query("UPDATE `table_excel` SET `num_rows` = `num_rows` + 1 WHERE `id` = '".Request::Clean_POST('file_id')."' ");
            echo JsonStringFyError('Tin nhắn gửi đi bị trống đang chuyển tiếp');
            die();
        }
        $min = $conn->query("SELECT MIN(`messages_sent`) FROM `table_momo` WHERE `success` = 'true' ")
                    ->fetch_assoc()['MIN(`messages_sent`)'];

        $phone_sender = (string) $conn->query("SELECT `phone` FROM `table_momo` WHERE `success` = 'true' AND `messages_sent` = '$min' 
                                            ORDER BY  RAND() LIMIT 1")
                                        ->fetch_assoc()['phone'];
        if(empty($phone_sender)) {
            echo JsonStringFyError('Đã xảy ra lỗi khi lấy số điện thoại gửi');
        }
        else {
            $result = $momo->LoadData($phone_sender)->LoginTimeSetup();
            if($result['status'] == 'success'){
                $url_image = '';
                $chat = $momo->LoadData($phone_sender)->SendMess($phone_recevice, $message);
                if(!empty($data_message[$number]['2'])){
                    $url_image = $data_message[$number]['2'];
                    if(filter_var($url_image, FILTER_VALIDATE_URL)){
                        $momo->LoadData($phone_sender)->SendImageLink($phone_recevice, $url_image);
                    }
                    else {
                        $url_image = '';
                    }
                }
                if($chat['status'] == 'success') {
                    echo JsonStringFy(array(
                        'status' => 'success',
                        'success'=> true,
                        'error'  => false,
                        'message'=> 'Đã gửi tin nhắn thành công đến số điện thoại '. $phone_recevice,
                        'receiver' => $phone_recevice,
                        'sender'   => $phone_sender,
                        'time'     => date('d-m-Y H:i:s', time())
                    ));
                    $update = $conn->query("UPDATE `table_excel` SET `num_rows` = `num_rows` + 1 WHERE `id` = '".Request::Clean_POST('file_id')."' ");
                    $conn->query("UPDATE `table_momo` SET `messages_sent` = `messages_sent` + 1 WHERE `phone` = '$phone_sender' ");
                    $addfriend = $momo->LoadData($phone_sender)->AddFriend($phone_recevice);
                    $table_chat = $conn->query("INSERT INTO `table_message` SET `sender` = '$phone_sender',
                                                                                `file_id` = '".Request::Clean_POST('file_id')."',
                                                                                `receiver` = '$phone_recevice',
                                                                                `message`  = '".StringDataBase($message)."',
                                                                                `image_link` = '$url_image',
                                                                                `status` = 'success',
                                                                                `reason` = '".$chat['message']."',
                                                                                `count_error` = 0,
                                                                                `time` = now() ");
                }
                else {
                    echo JsonStringFy(array(
                        'status' => 'error',
                        'success'=> false,
                        'error'  => true,
                        'message'=> $chat['message'],
                        'receiver' => $phone_recevice,
                        'sender'   => $phone_sender,
                        'time'     => date('d-m-Y H:i:s', time())
                    ));
                    $update = $conn->query("UPDATE `table_excel` SET `num_rows` = `num_rows` + 1 WHERE `id` = '".Request::Clean_POST('file_id')."' ");
                    $conn->query("UPDATE `table_momo` SET `messages_sent` = `messages_sent` + 1 WHERE `phone` = '$phone_sender' ");
                    $table_chat = $conn->query("INSERT INTO `table_message` SET `sender` = '$phone_sender',
                                                                                `file_id` = '".Request::Clean_POST('file_id')."',
                                                                                `receiver` = '$phone_recevice',
                                                                                `message`  = '".StringDataBase($message)."',
                                                                                `image_link` = '$url_image',
                                                                                `status` = 'error',
                                                                                `reason` = '".$chat['message']."',
                                                                                `count_error` = `count_error` + 1,
                                                                                `time` = now() ");
                }
            }
            else {
                $conn->query("UPDATE `table_momo` SET `messages_sent` = `messages_sent` + 1 WHERE `phone` = '$phone_sender' ");
                echo JsonStringFyError('Lỗi đăng nhập với số momo trên hệ thống '. $phone_sender);
            }
        }
    }
}

class Phonenumber
{

    static $arr_Prefix = array('CELL'=>array(
                                            '016966'=>'03966',
                                            '0169'=>'039',
                                            '0168'=>'038',
                                            '0167'=>'037',
                                            '0166'=>'036',
                                            '0165'=>'035',
                                            '0164'=>'034',
                                            '0163'=>'033',
                                            '0162'=>'032',
                                            '0120'=>'070',
                                            '0121'=>'079',
                                            '0122'=>'077',
                                            '0126'=>'076',
                                            '0128'=>'078',
                                            '0123'=>'083',
                                            '0124'=>'084',
                                            '0125'=>'085',
                                            '0127'=>'081',
                                            '0129'=>'082',
                                            '01992'=>'059',
                                            '01993'=>'059',
                                            '01998'=>'059',
                                            '01999'=>'059',
                                            '0186'=>'056',
                                            '0188'=>'058'
                                           )
                             );
                             
    public static function convert($phonenumber)
    {
        if(!empty($phonenumber))
        {
            //1. Xóa ký tự trắng
            $phonenumber=str_replace(' ','',$phonenumber);
            //2. Xóa các dấu chấm phân cách
            $phonenumber=str_replace('.','',$phonenumber);
			//3. Xóa các dấu gạch nối phân cách
            $phonenumber=str_replace('-','',$phonenumber);
            //4. Xóa dấu mở ngoặc đơn
            $phonenumber=str_replace('(','',$phonenumber);
            //5. Xóa dấu đóng ngoặc đơn
            $phonenumber=str_replace(')','',$phonenumber);
            //6. Xóa dấu +
            $phonenumber=str_replace('+','',$phonenumber);
			//7. Chuyển 84 đầu thành 0
			if(substr($phonenumber,0,2)=='84')
			{
				$phonenumber='0'.substr($phonenumber,2,strlen($phonenumber)-2);
			}
            foreach(self::$arr_Prefix['CELL'] as $key=>$value)
			{
				//$prefixlen=strlen($key);
				if(strpos($phonenumber,$key)===0)
				{
				    $prefix=$key;
                    $prefixlen = strlen($key);
                    $phone=substr($phonenumber,$prefixlen,strlen($phonenumber)-$prefixlen);
                    $prefix=str_replace($key,$value,$prefix);
                    $phonenumber = $prefix.$phone;
					//$phonenumber=str_replace($key,$value,$phonenumber);
					break;
				}
			}
			
            return $phonenumber;
        }
        else
        {
            return false;
        }
    }
}
?>