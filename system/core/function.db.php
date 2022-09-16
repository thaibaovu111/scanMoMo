<?php

function GetConfig() {
    global $sqlConnect;
    $query = mysqli_query($sqlConnect,"SELECT * FROM `".T_CONFIG."`");
    $return = array();
    while($rows = mysqli_fetch_assoc($query)) {

        $return[$rows['name']] = $rows['value'];
    }
    return $return;
}

function GetDataUserByUsername($username = '') {
    global $sqlConnect;
    $query = mysqli_query($sqlConnect, "SELECT * FROM `".T_USERS."` WHERE `username` = '".$username."' LIMIT 1");
    
    return mysqli_fetch_assoc($query);
}

function GetDataUserByEmail($email = '') {
    global $sqlConnect;
    $query = mysqli_query($sqlConnect, "SELECT * FROM `".T_USERS."` LIMIT 1");
    return mysqli_fetch_assoc($query);
}

?>