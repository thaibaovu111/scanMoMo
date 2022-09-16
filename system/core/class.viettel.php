<?php

class Viettel {

    public function GetOTP(){
        $header = array(
            'Host: apivtp.vietteltelecom.vn:6768',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: keep-alive',
            'Accept: */*',
            'User-Agent: My Viettel/4.11.1 (iPhone; iOS 13.6.1; Scale/2.00)',
            'Accept-Language: vi-US;q=1, en;q=0.9',
        );
        $data = 'build_code=2021.1.8.3&device_id=40C95252-8D69-4A79-95C7-C6AC741829D6&device_name=iPhone%20%28iPhone%206s%29&os_type=ios&os_version=13.600000&phone=0334506791&version_app=4.11.1';
        return $this->Curl('https://apivtp.vietteltelecom.vn/myviettel.php/getOTPLogin', $header, $data);
    }

    public function Login(){
        $header = array(
            'Host: apivtp.vietteltelecom.vn:6768',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: keep-alive',
            'Accept: */*',
            'User-Agent: My Viettel/4.11.1 (iPhone; iOS 13.6.1; Scale/2.00)',
            'Accept-Language: vi-US;q=1, en;q=0.9',
        );
        $data = 'account=0334506791&build_code=2021.1.8.3&cmnd=&device_id=40C95252-8D69-4A79-95C7-C6AC741829D6&device_name=iPhone%20%28iPhone%206s%29&keyDeviceAcc=HAX2bX_kcdu5tdop6_RGvJbYpsjarGJelVTmXy55CHcoJIWgZpmMj1hSMG-pG0W6TDbIXKYwjlAWycIjsWoJFg&os_type=ios&os_version=13.600000&password=0608&version_app=4.11.1';

        return $this->Curl('https://apivtp.vietteltelecom.vn/myviettel.php/loginMobileV3', $header, $data);

    }

    public function SearchGiftDataTet(){

        $header = array(
            'Host: apivtp.vietteltelecom.vn:6768',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: keep-alive',
            'Accept: */*',
            'User-Agent: My Viettel/4.11.1 (iPhone; iOS 13.6.1; Scale/2.00)',
            'Accept-Language: vi-US;q=1, en;q=0.9',
            'msisdnGift: 0977371507'
        );
        
        $data = 'build_code=2021.10.17&device_id=40C95252-8D69-4A79-95C7-C6AC741829D6&device_name=iPhone%20%28iPhone%206s%29&msisdnGift=0977371507&os_type=ios&os_version=13.600000&token=D639197E-C71F-84A8-4B1F-79B4BCFCFAF8&version_app=5.8';

        return $this->Curl('https://apivtp.vietteltelecom.vn/myviettel.php/searchGiftDataTet', $header, $data);
    }

    public function GetOTPGiftDataTet(){
        $header = array(
            'Host: apivtp.vietteltelecom.vn:6768',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: keep-alive',
            'Accept: */*',
            'User-Agent: My Viettel/4.11.1 (iPhone; iOS 13.6.1; Scale/2.00)',
            'Accept-Language: vi-US;q=1, en;q=0.9',
            'key: otp_gift_data'
        );

        $data = 'build_code=2021.10.17&device_id=40C95252-8D69-4A79-95C7-C6AC741829D6&device_name=iPhone%20%28iPhone%206s%29&key=otp_gift_data&os_type=ios&os_version=13.600000&token=D639197E-C71F-84A8-4B1F-79B4BCFCFAF8&version_app=5.8';

        return $this->Curl('https://apivtp.vietteltelecom.vn/myviettel.php/getOTPGiftDataTet', $header, $data);
    }

    public function SendGiftDataTet(){
        $header = array(
            'Host: apivtp.vietteltelecom.vn:6768',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: keep-alive',
            'Accept: */*',
            'User-Agent: My Viettel/4.11.1 (iPhone; iOS 13.6.1; Scale/2.00)',
            'Accept-Language: vi-US;q=1, en;q=0.9',
            'key: otp_gift_data'
        );

        $data = 'build_code=2021.10.17&device_id=40C95252-8D69-4A79-95C7-C6AC741829D6&device_name=iPhone%20%28iPhone%206s%29&key=otp_gift_data&os_type=ios&os_version=13.600000&token=D639197E-C71F-84A8-4B1F-79B4BCFCFAF8&version_app=5.8';

        return $this->Curl('https://apivtp.vietteltelecom.vn/myviettel.php/getOTPGiftDataTet', $header, $data);
    }

    public function GetCaptcha(){
        $header = array(
            'Host: apivtp.vietteltelecom.vn:6768',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: keep-alive',
            'Accept: */*',
            'User-Agent: My Viettel/4.11.1 (iPhone; iOS 13.6.1; Scale/2.00)',
            'Accept-Language: vi-US;q=1, en;q=0.9',
            'key: otp_gift_data'
        );
        $build_query = array(
            'build_code' => '2021.10.17',
            'device_id'  => '40C95252-8D69-4A79-95C7-C6AC741829D6',
            'device_name'=> 'iPhone (iPhone 6s)',
            'os_type'    => 'ios',
            'os_version' => '13.600000',
            'version_app'=> '5.8',
        );
        return $this->Curl('https://apivtp.vietteltelecom.vn/myviettel.php/getOTPGiftDataTet', $header, http_build_query($build_query));
    }

    public function PaymentOnlineV3(){

        $header = array(
            'Host: apivtp.vietteltelecom.vn:6768',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: keep-alive',
            'Accept: */*',
            'User-Agent: My Viettel/4.11.1 (iPhone; iOS 13.6.1; Scale/2.00)',
            'Accept-Language: vi-US;q=1, en;q=0.9',
        );

        $data = 'build_code=2021.10.17&captcha=u5tp&cardcode=924394800065250&checksum=efXRuXMd/EBgGV57AXNZFYd4LBprj5KSyZNN/R0/LmKscsRxCcAeY9zDLopqHOd8A%2BjzkUx5%2Bbz4p7V2YEjxnDueWO2uyNDGXHdZWguYIJo7jRSIApLXBqx6SbxPCZDcOAjmWdRP5yWqefPNuTdjBQVQUEn9lcI8k%2BfIJRzdlc4%3D&device_id=40C95252-8D69-4A79-95C7-C6AC741829D6&device_name=iPhone%20%28iPhone%206s%29&os_type=ios&os_version=13.600000&phone=0398733902&sid=mti80vt7m72sbgagaueal6h4d5&token=D639197E-C71F-84A8-4B1F-79B4BCFCFAF8&type=1&version_app=5.8';

        $build_query = array(
            'build_code' => '2021.10.17',
            'captcha'    => 'u5tp',
            'cardcode'   => '924394800065250',
            'checksum'   => 'efXRuXMd/EBgGV57AXNZFYd4LBprj5KSyZNN/R0/LmKscsRxCcAeY9zDLopqHOd8A+jzkUx5+bz4p7V2YEjxnDueWO2uyNDGXHdZWguYIJo7jRSIApLXBqx6SbxPCZDcOAjmWdRP5yWqefPNuTdjBQVQUEn9lcI8k+fIJRzdlc4=',
            'device_id'  => '40C95252-8D69-4A79-95C7-C6AC741829D6',
            'device_name'=> 'iPhone (iPhone 6s)',
            'os_type'    => 'ios',
            'os_version' => '13.600000',
            'phone'      => '',
            'sid'        => 'mti80vt7m72sbgagaueal6h4d5',
            'token'      => 'D639197E-C71F-84A8-4B1F-79B4BCFCFAF8',
            'type'       => 1,
            'version_app'=> '5.8'
        );

        return $this->Curl('https://apivtp.vietteltelecom.vn/myviettel.php/paymentOnlineV3', $header, $data);
    }


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