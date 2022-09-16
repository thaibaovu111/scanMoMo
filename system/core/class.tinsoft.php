<?php

class Tinsoft

{
    private $keys = '';

    private $key_proxy = '';

    public function LoadKeyUser($keys)
    {
        $this->keys = trim($keys);
        return $this;
    }

    public function LoadKeyProxy($keys)
    {
        $this->key_proxy = trim($keys);
        return $this;
    }

    public function BuyNewProxy($type = 0, $days = 1, $number = 1)
    {
        $url = 'http://proxy.tinsoftsv.com/api/orderKeys.php?key='.$this->keys.'&quantity='.$number.'&days='.$days.'&vip='.$type;
        return $this->CURL($url);
    }

    public function GetInfo()
    {
        $url = 'http://proxy.tinsoftsv.com/api/getUserInfo.php?key='. $this->keys;
        return $this->CURL($url);
    }

    public function GetUserKeys()
    {
        $url = 'http://proxy.tinsoftsv.com/api/getUserKeys.php?key='. $this->keys;
        return $this->CURL($url);
    }

    public function GetNewProxy()
    {
        $url = 'http://proxy.tinsoftsv.com/api/changeProxy.php?key='.$this->key_proxy;
        return $this->CURL($url);
    }

    public function GetMyProxy()
    {
        $url = 'http://proxy.tinsoftsv.com/api/getProxy.php?key='. $this->key_proxy;
        return $this->CURL($url);
    }

    public function ExtendKey($days = 1)
    {
        $url = 'http://proxy.tinsoftsv.com/api/extendKey.php?key='.$this->keys.'&days='.$days.'&proxy_key='. $this->key_proxy;
        return $this->CURL($url);
    }

    public function GetRowProxy()
    {
        $Tinsoft = $this->GetUserKeys();
        if(empty($Tinsoft['success'])){
            return false;
        }
        $arrayPost   = array();
        $return      = array();
        foreach ($Tinsoft['data'] as $item){
            if(empty($item['success'])) continue;
            $arrayPost[$item['key']] = array(
                CURLOPT_URL => 'http://proxy.tinsoftsv.com/api/changeProxy.php?key='.$item['key'],
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'] ?? '',
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT   => 10
            );
        }
        if(empty($arrayPost)){
            return array(
                'success'  => false,
                'errorDesc'=> 'Khong ton tai key proxy nao'
            );
        }
        $result = $this->multi_thread_curl($arrayPost);
        foreach ($result as $keys => $items){
            if(empty($items['success'])){
                $arrayPost[$keys] = array(
                    CURLOPT_URL => 'http://proxy.tinsoftsv.com/api/getProxy.php?key='.$keys,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT   => 10
                );
            }
            else {
                $return[] = $items;
            }
            
        }

        $results = $this->multi_thread_curl($arrayPost);
        foreach ($results as $items){
            if(!empty($items['success'])){
                $return[] = $items;
            }
        }
        return $return;

    }

    public function multi_thread_curl(array $optionArray){

        $ch = 'ch_';
        $results = array();
            //Create your cURL resources.
    
        foreach($optionArray as $thread => $item) {
            ${$ch.$thread} = curl_init();
    
            curl_setopt_array(${$ch.$thread}, $item); //Set your main curl options.
    
        }
    
        //Create the multiple cURL handler.
        $mh = curl_multi_init();
    
        //Add the handles.
        foreach($optionArray as $keys => $thread) {
    
            curl_multi_add_handle($mh, ${$ch . $keys});
    
        }
    
        $active = null;
    
        //execute the handles.
        do {
    
            $mrc = curl_multi_exec($mh, $active);
    
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    
        while ($active && $mrc == CURLM_OK) {
    
            if (curl_multi_select($mh) != -1) {
                do {
    
                    $mrc = curl_multi_exec($mh, $active);
    
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
    
        }
    
        //Get your data and close the handles.
        foreach($optionArray as $thread => $item) {
    
            $result  = curl_multi_getcontent(${$ch . $thread});
    
            if(is_object(json_decode($result))){
    
                $results[$thread] = json_decode($result, true);
    
            }
    
            curl_multi_remove_handle($mh, ${$ch . $thread});
    
        }
    
        //Close the multi handle exec.
        curl_multi_close($mh);
        return $results;
    }

    private function CURL($Action) 
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $Action,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'] ?? '',
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT   => 3
        ));
        $response = curl_exec($curl);
        return (is_object(json_decode($response))) ? json_decode($response, true) : $response;
    }
}

?>