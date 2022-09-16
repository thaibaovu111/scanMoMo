<?php
require_once '../config.php';
if(!empty($_POST['data'])){
    $explode = explode("\n", $_POST['data']);
    $data = array();
    foreach ($explode as $item) {
        if(strstr($item, '|')){
            $data[] = array(
                'phone' => (string) explode('|', $item)[0],
                'name'  => explode('|', $item)[1]
            );
        }
    }
    download_csv_results($data);
}
else if(!empty($_GET['type'])){
    if($_GET['type'] == 'momo-account'){
        if(empty($username)){
            header('Location: '. $site_url);
        }
        else if($user['level'] != 'admin'){
            header('Location: '. $site_url);
        }
        else {
            $back_up = mysqli_query($conn,"SELECT * FROM `table_momo` WHERE `authorization` != 'undefined' ");
            $data = array();
            while ($rows = mysqli_fetch_assoc($back_up)){
                $data[] = $rows;
            }
            download_csv_results($data);
        }
    }

}
else if(!empty($_GET['file'])){
    if(empty($username)){
        header('Location: '. $site_url);
    }
    else if($user['level'] != 'admin'){
        header('Location: '. $site_url);
    }
    else {
        $select = $conn->query("SELECT `sender`, `receiver`, `message`, `status`, `reason`, `time` FROM `table_message` WHERE `file_id` = '".Request::Clean_GET('file')."' ");
        $data = array();

        while($rows = mysqli_fetch_assoc($select)){
            $data [] = $rows;
        }
        download_csv_results($data);
    }
}
else if(!empty($_POST['datahis'])){
    $explode = explode("\n", $_POST['datahis']);
    $data = array();
    foreach ($explode as $item){
        $explitem = explode('|', $item);
        $data[] = array(
            'tranId'      => $explitem['0'] ?? '',
            'patnerID'    => $explitem['1'] ?? '',
            'partnerName' => $explitem['2'] ?? '',
            'comment'     => $explitem['3'] ?? '',
            'amount'      => $explitem['4'] ?? '',
            'millisecond' => $explitem['5'] ?? '' 
        );
    }
    download_csv_results($data);
}
else if(!empty($_GET['input'])){

    $input = file_get_contents( '../tool/output/'. $_GET['input'], FILE_USE_INCLUDE_PATH);
    if(!empty($input)){
        $explode = explode("\n", $input);
        $return = array();
        foreach($explode as $rows){
            if(empty($rows)) continue;
            $return[] = explode("|", $rows);
        }
        download_csv_results($return);
    }
}
?>