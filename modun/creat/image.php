<?php
require_once '../../config.php';
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập lại để tiếp tục');
}
else if(empty($_POST['title'])){
    echo JsonStringFyError('Vui lòng nhập tiêu để ảnh');
}
else if(check_img('image') === false){
    echo JsonStringFyError('Vui lòng gửi ảnh đúng định dạng');
}
else {
    $select = $conn->query("SELECT COUNT(*) FROM `table_images` WHERE `title` = '".Request::Clean_POST('title')."' ")->fetch_assoc();
    if(!empty($select['COUNT(*)'])){
        echo JsonStringFyError('Tiêu để ảnh đã được sử dụng vui lòng thay đổi');
        die();
    }

    $uploads_dir = 'img';
    $number_random = random('1234567890qwertyuiopasdfghjklzxcvbnm', 10);
    $tmp_name_front = $_FILES['image']['tmp_name'];
    $file_excel = "$uploads_dir/$number_random.png";
    $create = move_uploaded_file($tmp_name_front, $file_excel);
    if(!$create) {
        echo JsonStringFyError('Đã xảy ra lỗi khi up load file');
        die();
    }

    $images = file_get_contents($uploads_dir. '/'. $number_random. '.png');
    if(empty($images)){
        echo JsonStringFyError('Đã xảy ra lỗi khi lấy đường dẫn ảnh');
    }
    else {
        $info_image = $momo->CreatLinkImage($images);
        if($info_image['status'] == 'success'){
            $conn->query("INSERT INTO `table_images` SET `image_url` = '".$info_image['image']."', `title` = '".Request::Clean_POST('title')."', `creat_date` = now() ");
            echo JsonStringFySuccess('Tạo đường dẫn file thành công');
        }
        else {
            echo JsonStringFyError('Tạo đường dẫn file thất bại');
        }
    }
    
}
?>