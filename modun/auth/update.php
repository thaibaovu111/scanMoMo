<?php
if(empty($_COOKIE['PROXY'])){
    echo json_encode(array(
        'data'=> 'undefined'
    ));
}
else {
    echo json_encode(array(
        'data' => base64_decode($_COOKIE['PROXY'])
    )); 
}
?>