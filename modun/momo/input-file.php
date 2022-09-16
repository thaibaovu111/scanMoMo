<?php
require_once '../../config.php';
error_reporting(0);
if(empty($username)){
    echo JsonStringFy(array(
        'status' => 'error',
        'message'=> 'Vui lòng đăng nhập lại'
    )); 
    die();
}
else if($_POST['type'] == 'save'){
    if(empty($_POST['title'])){
        echo JsonStringFy(array(
            'status' => 'error',
            'message'=> 'Vui lòng nhập tiêu đề của file'
        ));
        die();
    }
    else if(check_xlsx('file') == false){
        echo JsonStringFyError('Vui lòng nhập file có đuôi xlsx');
    }
    else if(empty($_POST['type'])){
        echo JsonStringFyError('Vui lòng chọn chức năng để thực hiện');
    }
    else {
        $uploads_dir = 'file';
        $number_random = random('1234567890qwertyuiopasdfghjklzxcvbnm', 10);
        $tmp_name_front = $_FILES['file']['tmp_name'];
        $file_excel = "$uploads_dir/$number_random.xlsx";
        $create = move_uploaded_file($tmp_name_front, $file_excel);
        if(!$create) {
            echo JsonStringFyError('Đã xảy ra lỗi khi up load file');
            die();
        }
        
        try {
            $FileExcel = PHPExcel_IOFactory::identify($file_excel);
            $ObjReader = PHPExcel_IOFactory::createReader($FileExcel);
            $ObjPHPExcel = $ObjReader->load($file_excel);
        }
        catch (\Throwable $e) {
            die('Lỗi không thể đọc file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        if(file_exists($file_excel)) unlink($file_excel);
        
        $Sheet = $ObjPHPExcel->getSheet(0);
        
        $Column = $Sheet->getHighestRow();
        
        $highestColumn = $Sheet->getHighestColumn();
                
        for ($i = 1; $i < $Column; $i++) {
            $data_excel[] = $Sheet->rangeToArray('A' . $i . ':' . 'C' . $i, NULL, TRUE,FALSE)[0];
        
        }
        if(is_array($data_excel)){

            $creat_file = fwrite(fopen('file/'.$number_random. '.json', 'w+'),JsonStringFy($data_excel));
            if(!$creat_file) {
                echo JsonStringFyError('Đã xảy ra lỗi khi xử lý file excel');
                die();
            }
        }
        
        $file_save = '/modun/momo/file/'. $number_random. '.json';
        
        $title = Request::Clean_POST('title');
        $create_sql = $conn->query("INSERT INTO `table_excel` SET `title` = '$title', `file` = '$file_save', `num_rows` = '1', `total` = '".count($data_excel)."'  ");
        if($create_sql) {
            echo JsonStringFySuccess('Upload file thành công ');
        }
        else {
            echo JsonStringFyError('Đã xảy ra lỗi vui lòng thử lại sau');
        }
    }
}
else if($_POST['type'] == 'delete') {
    if(empty($_POST['file'])){
        echo JsonStringFyError('Vui lòng chọn file cần xóa');

    }
    else {
        $creat = $conn->query("DELETE FROM `table_excel` WHERE `id` = '".Request::Clean_POST('file')."' ");
        $delete = $conn->query("DELETE FROM `table_message` WHERE `file_id` = '".Request::Clean_POST('file')."'");
        if($creat) {
            echo JsonStringFySuccess('Xóa file dữ liệu thành công');
        }
        else {
            echo JsonStringFyError('Xóa file dữ liệu thất bại');
        }
    }
}
else if($_POST['type'] == 'save-txt'){
    if(empty($_FILES['file'])) {
        echo JsonStringFyError('Vui lòng nhập file số điện thoại');


    }
    else if(empty($_POST['title'])){
        echo JsonStringFyError('Vui lòng nhập tiêu đề file để dễ phân biệt');
    }
    else if(empty($_POST['text'])){
        echo JsonStringFyError('Vui lòng nhập chữ cố định để lưu vào file');
    }
    else if(empty($_POST['number_rand'])){
        echo JsonStringFyError('Vui lòng nhập số kí tự random');
    }
    else {
        $uploads_dir = 'file';
        $number_random = random('1234567890qwertyuiopasdfghjklzxcvbnm', 10);
        $tmp_name_front = $_FILES['file']['tmp_name'];
        $file_excel = "$uploads_dir/$number_random.txt";
        $create = move_uploaded_file($tmp_name_front, $file_excel);
        if(!$create) {
            echo JsonStringFyError('Đã xảy ra lỗi khi up load file');
            die();
        }

        $data = file_get_contents($file_excel, FILE_USE_INCLUDE_PATH);

        if(file_exists($file_excel)) unlink($file_excel);

        $explode = explode("\n", $data);

        $explode_trim = array_map(function ($item){
            return trim($item);
        }, $explode);

        $text = trim($_POST['text']);

        $save_txt = array();

        if(!strstr($text, '{}')){
            echo JsonStringFyError('Bạn đã điền thiếu dấu {} để thay kí tự vào');
        }
        else {
            foreach ($explode_trim as $value){
                $save_txt[] = array(
                    $value,
                    str_replace('{}', random('AWERTYQUIOPASDFGHJKLZXCVBNM1234567890',(int) $_POST['number_rand']), $text)
                );
            }

            $creat_file = fwrite(fopen('file/'.$number_random. '.json', 'w+'),JsonStringFy($save_txt));
            if(!$creat_file) {
                echo JsonStringFyError('Đã xảy ra lỗi khi xử lý file excel');
                die();
            }
            else {
                $file_save = '/modun/momo/file/'. $number_random. '.json';
        
                $title = Request::Clean_POST('title');
                $create_sql = $conn->query("INSERT INTO `table_excel` SET `title` = '$title', `file` = '$file_save', `num_rows` = '1', `total` = '".count($save_txt)."'  ");
                if($create_sql) {
                    echo JsonStringFySuccess('Upload file thành công ');
                }
                else {
                    echo JsonStringFyError('Đã xảy ra lỗi vui lòng thử lại sau');
                }
            }
        }
    }
}
?>