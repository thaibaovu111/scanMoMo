<?php

class SMS

{
    private $api_keys = '';

    public function Loadkey($keys)
    {
        $this->api_keys = trim($keys);
        return $this;
    }

    public function Getinfo()
    {
        $url = 'https://chothuesimcode.com/api?act=account&apik='. $this->api_keys;
        return $this->Curl($url);
    }

    public function GetPhoneMoMo()
    {
        $url = 'https://chothuesimcode.com/api?act=number&apik='.$this->api_keys.'&appId=1034';
        return $this->Curl($url);
    }

    public function GetCode($id)
    {
        $url = 'https://chothuesimcode.com/api?act=code&apik='.$this->api_keys.'&id='. $id;
        return $this->Curl($url);
    }

    private function Curl($url)
    {
        $curl = curl_init();
        $OTP =  array(
            CURLOPT_URL => trim($url),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 3
        );
        if(!empty($_COOKIE['PROXY'])){
            $OTP[CURLOPT_PROXY] = base64_decode($_COOKIE['PROXY']);
        }
        curl_setopt_array($curl,$OTP);

        return json_decode(curl_exec($curl), true);
    }
}

?>