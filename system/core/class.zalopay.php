<?php


class ZaloPay

{
    private $config = array();

    private $connect;

    private $rsa;

    public function __construct($conn)
    {
        $this->connect = $conn;
        return $this;
    }

    private function CheckUser($phone)
    {
        $select = $this->connect->query("SELECT * FROM `table_zalopay` WHERE `phone` = '".$phone."' ");
        if($select->num_rows == 0){
            $this->connect->query("INSERT INTO `table_zalopay` SET `phone` = '".$phone."',
                                                             `deviceid` = '".$this->get_device_id()."' ");
                                                               
        }
        $this->config = $this->connect->query("SELECT * FROM `table_zalopay` WHERE `phone` = '".$phone."' LIMIT 1 ")->fetch_assoc();
        return $this;
    }

    public function LoadData($phone)
    {
        $select = $this->connect->query("SELECT * FROM `table_zalopay` WHERE `phone` = '".$phone."' LIMIT 1 ");
        if($select->num_rows == 0){
            $this->CheckUser($phone);
            return $this;
        }
        $this->config = $select->fetch_assoc();
        return $this;
    }

    public function SendOTP()
    {
        $result = $this->CheckBeUser();
        if($result['status'] == 'error'){
            return $result;
        }
        $result = $this->GetDataSendOTP();
        if(empty($result['data'])){
            return array(
                'status' => 'success',
                'message'=> 'Gửi Mã OTP Thành Công'
            );
        }
        return $result;
    }

    public function ImportOTP($code){
        if(empty($code)){
            return array(
                'status' => 'error',
                'message'=> 'Vui lòng nhập mã OTP của bạn'
            );
        }
        $result = $this->GetDataImportOTP(trim($code));
        if(empty($result['data']['phone_verified_token'])){
            return array(
                'status' => 'error',
                'message'=> 'Mã xác nhận không chính xác vui lòng thử lại'
            );
        }
        else if(!empty($result['data']['phone_verified_token'])){
        $this->connect->query("UPDATE `table_zalopay` SET `phone_verified_token` = '".$result['data']['phone_verified_token']."' WHERE `phone` = '".$this->config['phone']."' ");
            return array(
                'status' => 'success',
                'message'=> 'Nhập mã OTP thành công'
            );
        }
        return $result;

    }

    public function LoginUser($password = ''){
        if(!empty($password)){
            $this->config['password'];
        }
        $result = $this->GetPublicKey();
        if($result['status'] == 'error') {
            return $result;
        }
        $encrypt = $this->RSA_Encrypt($this->config['public_key'], $this->config['password']);
        $pin = $this->CreatPin($encrypt);
        $result = $this->GetDataLogin($pin);
        if(!empty($result['data'])){
            $this->connect->query("UPDATE `table_zalopay` SET `authorization` = '".$result['data']['session_id']."',
                                                        `access_token` = '".$result['data']['access_token']."',
                                                        `zalo_id`      = '".$result['data']['zalo_id']."'
                                                        `user_id`      = '".$result['data']['user_id']."',
                                                        `password`     = '".$this->config['password']."' WHERE `phone` = '".$this->config['phone']."' ");
        }
        return $result;
    }



    public function CheckBeUser()
    {
        $result = $this->GetDataUser();
        if(empty($result)){
            return array(
                'status' => 'error',
                'message'=> 'Đã xảy ra lỗi với máy chủ'
            );
        }
        else if(empty($result['data'])){
            return array(
                'status' => 'error',
                'message'=> 'Không tìm thấy dữ liệu trả về'
            );
        } 
        $data_ = $result['data'];
        if($data_['is_exist'] === false){
            return array(
                'status' => 'error',
                'message'=> 'Số điện thoại này chưa đăng ký tài khoản nào'
            );
        }
        $send_otp_token = $data_['send_otp_token'];
        $this->connect->query("UPDATE `table_zalopay` SET `send_otp_token` = '$send_otp_token' WHERE `phone` = '".$this->config['phone']."' ");
        $this->LoadData($this->config['phone']);
        return array(
            'status' => 'success',
            'display_name' => $data_['display_name'],
            'message'=> 'Thành công'
        );
    }

    public function CreatQrCode()
    {
        $header = array(
            'Host: qr.zalopay.vn',
            'user-agent: ZaloPayClient/6.8',
            'content-length: 0',
        );

        return $this->CURL('', $header);
    }

    public function GetPublicKey()
    {
        $header = array(
            'Host; api.zalopay.vn',
            'x-platform: NATIVE',
            'x-device-os: ANDROID',
            'x-device-id: '.$this->config['deviceid'],
            'x-device-model: Samsung SM_G532G',
            'x-app-version: 6.8.0',
            'user-agent: '.$_SERVER['HTTP_USER_AGENT'].' ZaloPay Android / 9464',
            'x-density: hdpi',
            'authorization: Bearer'
        );
        $result = $this->CURL('https://api.zalopay.vn/v2/user/public-key', $header);
        if(!empty($result['data']['public_key'])){
            $this->connect->query("UPDATE `table_zalopay` SET `public_key` = '".$result['data']['public_key']."' WHERE `phone` = '".$this->config['phone']."' ");
            return array(
                'status' => 'success',
                'message'=> 'Lấy public key thành công'
            );
        }
        return array(
            'status' => 'error',
            'message'=> 'Get public key thất bại'
        );
    }


    private function GetDataLogin($pin)
    {
        $header = array(
            'Host; api.zalopay.vn',
            'x-platform: NATIVE',
            'x-device-os: ANDROID',
            'x-device-id: '.$this->config['deviceid'],
            'x-device-model: Samsung SM_G532G',
            'x-app-version: 6.8.0',
            'user-agent: '.$_SERVER['HTTP_USER_AGENT'].' ZaloPay Android / 9464',
            'x-density: hdpi',
            'authorization: Bearer'
        );
        $Data = array(
            'phone_number' => $this->config['phone'],
            'pin' => $pin,
            'phone_verified_token' => $this->config['phone_verified_token']
        );
        return $this->CURL('https://api.zalopay.vn/v2/account/phone/session',$header ,$Data);
    }

    public function GetDataUser()
    {
        $header = array(
            'Host; api.zalopay.vn',
            'x-platform: NATIVE',
            'x-device-os: ANDROID',
            'x-device-id: '.$this->config['deviceid'],
            'x-device-model: Samsung SM_G532G',
            'x-app-version: 6.8.0',
            'user-agent: '.$_SERVER['HTTP_USER_AGENT'].' ZaloPay Android / 9464',
            'x-density: hdpi',
            'authorization: Bearer'
        );

        $Action = 'https://api.zalopay.vn/v2/account/phone/status?phone_number='.$this->config['phone'];
        return $this->CURL($Action, $header, '');
    }

    public function GetDataSendOTP()
    {
        $header = array(
            'Host; api.zalopay.vn',
            'x-platform: NATIVE',
            'x-device-os: ANDROID',
            'x-device-id: '.$this->config['deviceid'],
            'x-device-model: Samsung SM_G532G',
            'x-app-version: 6.8.0',
            'user-agent: '.$_SERVER['HTTP_USER_AGENT'].' ZaloPay Android / 9464',
            'x-density: hdpi',
            'authorization: Bearer'
        );
        $Data = array(
            'phone_number' => $this->config['phone'],
            'send_otp_token'=> $this->config['send_otp_token']
        );
        $Action = 'https://api.zalopay.vn/v2/account/otp';
        return $this->CURL($Action, $header, $Data);

    }

    public function GetDataImportOTP($code)
    {
        $header = array(
            'Host; api.zalopay.vn',
            'x-platform: NATIVE',
            'x-device-os: ANDROID',
            'x-device-id: '.$this->config['deviceid'],
            'x-device-model: Samsung SM_G532G',
            'x-app-version: 6.8.0',
            'user-agent: '.$_SERVER['HTTP_USER_AGENT'].' ZaloPay Android / 9464',
            'x-density: hdpi',
            'authorization: Bearer'
        );

        $Action = 'https://api.zalopay.vn/v2/account/otp-verification';
        $Data = array(
            'phone_number' => $this->config['phone'],
            'otp'          => $code
        );
        return $this->CURL($Action, $header, $Data);

    }

    public function RSA_Encrypt($key,$content)
    {
        if(empty($this->rsa)){
            $this->INCLUDE_RSA($key);
        }
        return base64_encode($this->rsa->encrypt($content));
    }

    private function CreatPin($encrypt = '')
    {
        $length = strlen($encrypt);
        $chia = floor($length / 76);
        $string = ''; $so = 0;
        for ($i = 0; $i <= $chia; $i++){
            $string .= substr($encrypt, $so, 76)."\n";
            $so += 76;
        }
        return $string;
    }

    private function INCLUDE_RSA($key)
    {
        require(dirname(__FILE__).'/lib/RSA/Crypt/RSA.php');
        $this->rsa = new Crypt_RSA();
        $this->rsa->loadKey($key);
        $this->rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        return $this;
    }

    private function CURL($Action,$header,$data = '')
    {
        $Data = is_array($data) ? json_encode($data) : $data;
        $curl = curl_init();
        $opt = array(
            CURLOPT_URL => $Action,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST => empty($data) ? FALSE : TRUE,
            CURLOPT_POSTFIELDS => $Data,
            CURLOPT_CUSTOMREQUEST => empty($data) ? 'GET' : 'POST',
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_ENCODING => "",
            CURLOPT_HEADER => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2,
            CURLOPT_TIMEOUT => 20,
        );
        curl_setopt_array($curl,$opt);
        $body = curl_exec($curl);
        // echo strlen($body); die;
        if(is_object(json_decode($body))){
            return json_decode($body,true);
        }
        return $body;
    }

    private function get_device_id($length = 16)
    {
        $characters = '0123456789abcdef';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

?>