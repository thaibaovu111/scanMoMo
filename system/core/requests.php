<?php
class Request 

{

    // Clean data attack in post data
    public static function Clean_POST($value){
        return !empty($_POST[$value]) ? str_replace(array('<',"'",'>','?','/',"\\",'--','"','eval(','<php','-'),'',htmlspecialchars(addslashes(strip_tags($_POST[$value])))) : null;
    }
    // Clean data attack in get data
    public static function Clean_GET($value){
        return (!empty($_GET[$value])) ? str_replace(array('<',"'",'>','?','/',"\\",'--','"','eval(','<php','-'),'',htmlspecialchars(addslashes(strip_tags($_GET[$value])))) : null;
    }

    public static function POST($value){
        return $_POST[$value] ?? null;
    }

    public static function GET($value){
        return $_GET[$value] ?? null;
    }
}

?>