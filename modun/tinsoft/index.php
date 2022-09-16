<?php
require_once '../../config.php';
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập để tiếp tục');
}
else if($user_info['level'] != 'admin'){
    echo JsonStringFyError('Chức năng này chỉ dành cho admin');
}
else if(empty($_POST['type'])){
    echo JsonStringFyError('Vui lòng lựa chọn chức năng để thực hiện');
}
else {
    $Tinsoft = new Tinsoft;
    switch ($_POST['type']) {
        case 'update':
            if(empty($_POST['site_tinsoft_partner_key'])){
                echo JsonStringFyError('Vui lòng nhập mã key mới để thực hiện');

            }
            $Api_key = Request::Clean_POST('site_tinsoft_partner_key');
            if($Api_key == $site['site_tinsoft_partner_key']){
                echo JsonStringFyError('Mã api key trùng với key hiện tại');
                die();
            }
            $result = $Tinsoft->LoadKeyUser($Api_key)->GetInfo();
            if(!empty($result['success'])){
                $update = $conn->query("UPDATE `table_site` SET `site_tinsoft_partner_key` = '$Api_key' WHERE `site_id` = '".$site['site_id']."' ");
                if($update) {
                    echo JsonStringFySuccess('Cập nhật thông tin thành công');
                }
                else {
                    echo JsonStringFyError('Lỗi máy chủ vui lòng thử lại');
                }

            }
            else {
                echo JsonStringFyError('Mã api key không chính xác vui lòng kiểm tra lại');
            }
            # code...
            break;
        case 'order':
            if(empty($_POST['days'])){
                echo JsonStringFyError('Vui lòng nhập số ngày muốn mua');
            }
            else if(empty($_POST['quantity'])){
                echo JsonStringFyError('Vui lòng nhập số lượng key muốn mua');
            }
            else if(!isset($_POST['vip'])) {
                echo JsonStringFyError('Vui lòng chọn loại key muốn mua');
            }
            else {
                $days     = Request::Clean_POST('days');
                $quantity = Request::Clean_POST('quantity');
                $vip      = Request::Clean_POST('vip');
                $result   = $Tinsoft->LoadKeyUser($site['site_tinsoft_partner_key'])->BuyNewProxy($vip,$days,$quantity);
                if(!empty($result['success'])){

                    foreach($result['data'] as $item) {
                        if($item['success']) {
                            $creat = $conn->query("INSERT INTO `table_tinsoft` SET `user_key` = '".$site['site_tinsoft_partner_key']."', 
                                                                                   `proxy_key` = '".$item['key']."',
                                                                                   `date_expired` = '".$item['date_expired']."',
                                                                                   `vip` = '".$vip."' ");
                        }
                    }
                    echo JsonStringFySuccess('Mua key proxy thành công');
                }
                else {
                    echo JsonStringFyError('Mua api key proxy thất bại');
                }
            }
           
            break;
        case 'update-proxy': 
            $result = $Tinsoft->LoadKeyUser($site['site_tinsoft_partner_key'])->GetUserKeys();
            if($result['success']) {
                foreach ($result['data'] as $item) {
                    if($item['success']){

                        $num_rows = $conn->query("SELECT COUNT(*) FROM `table_tinsoft` WHERE `user_key` = '".$site['site_tinsoft_partner_key']."' AND `proxy_key` = '".$item['key']."' ")->fetch_assoc();
                        if(empty($num_rows['COUNT(*)'])){
                            $creat = $conn->query("INSERT INTO `table_tinsoft` SET `user_key` = '".$site['site_tinsoft_partner_key']."', 
                                                                                    `proxy_key` = '".$item['key']."',
                                                                                    `date_expired` = '".$item['date_expired']."',
                                                                                    `vip`          = '".$item['isVip']."' ");
                        }
                        else {
                            $conn->query("UPDATE `table_tinsoft` SET `date_expired` = '".$item['date_expired']."' WHERE `proxy_key` = '".$item['key']."' ");
                        }
                        
                    }
                }
                echo JsonStringFySuccess('Cập nhật thông tin thành công');
            }
            else {
                echo JsonStringFyError('Cập nhật thông tin thất bại');
            }
            break;
        case 'extend':
            if(empty($_POST['key'])) {
                echo JsonStringFyError('Đã xảy ra lỗi vui lòng gửi mã key');

            }
            else {
                $result = $Tinsoft->LoadKeyUser($site['site_tinsoft_partner_key'])->LoadKeyProxy($_POST['key'])->ExtendKey();
                if($result['success']) {
                    echo JsonStringFySuccess('Gia hạn proxy key thành công vui lòng cập nhật lại');
                }
                else {
                    echo JsonStringFyError('Gia hạn proxy key thất bại');
                }
            }
            break;
        default:
            echo JsonStringFyError('Lựa chọn không hợp lệ');
            break;
    }
}

?>