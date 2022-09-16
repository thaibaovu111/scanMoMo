<?php

class Mocha {

    




    private function Curl($url, $header, $data = '', $port = '6768'){
        $curl = curl_init();
        $opt = array(
            CURLOPT_URL => trim($url),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST => empty($data) ? FALSE : TRUE,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_CUSTOMREQUEST => empty($data) ? 'GET' : 'POST',
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_ENCODING => "",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_PORT    => $port
        );
        if(!empty($this->proxy)) {
            $opt[CURLOPT_PROXY] = $this->proxy;
        }
        curl_setopt_array($curl,$opt);

        $data = curl_exec($curl);

        if(is_object(json_decode($data))) return json_decode($data, true);
        return $data;
    }
}

?>