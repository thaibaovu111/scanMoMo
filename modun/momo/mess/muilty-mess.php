<?php

use Phonenumber as GlobalPhonenumber;

require_once '../../../config.php';
error_reporting(0);
if(empty($username)){
    echo JsonStringFyError('Vui lòng đăng nhập lại để tiếp tục');
}
else if(empty($_POST['file_id'])){
    echo JsonStringFyError('Vui lòng chọn file cần chạy lại');
}
else if(empty($_PROXY)) {
    echo JsonStringFyError('Không có proxy hoạt động trên web vui lòng thử lại');

}
else {
    if(empty($_PROXY)){
        echo JsonStringFyError('Không có proxy tin nhắn không thể chạy');
    }
    else {
        $MuiltyMoMo = new MuiltyMoMo($conn, $_PROXY);

        $select = $conn->query("SELECT * FROM `table_excel` WHERE `id` = '".Request::Clean_POST('file_id')."' ");
        if(empty($select->num_rows)) {
            echo JsonStringFyError('File dữ liệu không tồn tại hoặc đã bị xóa');
    
        }
        else {
            $data = $select->fetch_assoc();
            $number = $data['num_rows'];
            if($number > ($data['total'] - 1)){
    
                echo JsonStringFyError('Đơn chat đã hoàn thành bạn không thể chạy lại');
                die();
            }
            try {
                $DataMessage = json_decode(file_get_contents('../file/'.basename($data['file']), FILE_USE_INCLUDE_PATH), true);
    
            }
            catch (\Throwable $e) {
                echo JsonStringFy('Lỗi file vui lòng thử lại ');
                die();
            }

            if(empty($DataMessage)) {
                echo JsonStringFyError('Đã xảy ra lỗi khi lấy dữ liệu file');
            }
            else {
                $select = mysqli_query($conn, "SELECT * FROM `table_momo` WHERE `success` = 'true' ORDER BY `messages_sent` LIMIT 20 ");
                $arrayPhone = array();
                while ($rows = mysqli_fetch_assoc($select)){
                    $arrayPhone[$rows['phone']] = $rows;
                }
        
                $Muilty = $MuiltyMoMo->LoadData($arrayPhone);
        
                $LoginSuccess = $Muilty->LoginMuilty();

                $i = 0;
                $FullData = array();
                foreach ($LoginSuccess as $item){
                    $PhoneNumber         = $item['phone'];
                    $item['messageSend'] = $DataMessage[$number]['1'];
                    if(empty($item['messageSend'])) continue;
                    $PhoneNhan = substr($DataMessage[$number]['0'], 0, 1) == 0 ?
                                                                         $DataMessage[$number]['0'] 
                                                                         : '0'.$DataMessage[$number]['0'];
                    $item['userrecevicer'] = Phonenumber::convert($PhoneNhan);

                    $FullData[$PhoneNumber] = $item;
                    $number++; $i++; 
                }
                $conn->query("UPDATE `table_excel` SET `num_rows` = `num_rows` + $i WHERE `id` = '".Request::Clean_POST('file_id')."' ");
                $CheckUserMuilty = $MuiltyMoMo->LoadData($FullData)->CheckUserMuilty();
                $GetRoomId       = $MuiltyMoMo->LoadData($CheckUserMuilty)->GetGroupMulty();
                $SendMess        = $MuiltyMoMo->LoadData($GetRoomId)->SendMessMuilty()->CheckSendMuilty();
                $Console = array();

                $InsertDataBase = 'INSERT INTO `table_message` (`file_id`, `sender`, `receiver`, `message`, `image_link`, `status`, `reason`, `count_error`, `time`) VALUES ';

                foreach ($SendMess as $keys => $item) {
                    if(!empty($item['statusSend'])){
                        $conn->query("UPDATE `table_momo` SET `messages_sent` = `messages_sent` + 1 WHERE `phone` = '$keys' ");
                        $InsertDataBase .= "('".Request::Clean_POST('file_id')."', '$keys', '".$item['userrecevicer']."', '".addslashes($item['messageSend'])."', '', 'success', 'Gửi tin nhắn thành công', '0', now() ),";

                        $Console[] = array(
                            'sender'   => $keys,
                            'recevicer'=> $item['userrecevicer'],
                            'file'     => Request::Clean_POST('file_id'),
                            'status'   => 'success'
                        );
                    }
                    else {
                        $conn->query("UPDATE `table_momo` SET `messages_sent` = `messages_sent` + 1 WHERE `phone` = '$keys' ");
                        $InsertDataBase .= "('".Request::Clean_POST('file_id')."', '$keys', '".$item['userrecevicer']."', '".addslashes($item['messageSend'])."', '', 'error', 'Gửi tin nhắn thất bại', '0', now() ),";
                        $Console[] = array(
                            'sender'   => $keys,
                            'recevicer'=> $item['userrecevicer'],
                            'file'     => Request::Clean_POST('file_id'),
                            'status'   => 'error'
                        );
                    }

                }

                $InsertDataBase = rtrim($InsertDataBase,',');
                mysqli_query($conn,$InsertDataBase);
                echo JsonStringFy($Console);
            }

        }

    }
}

class MuiltyMoMo {

    private $connect ;

    private $config = array();

    private $phone = '';

    private $requestkeyRaw = '';

    private $proxy = '';

    public function __construct($conn, $proxy)
    {
        $this->connect = $conn;
        $this->proxy   = $proxy;
        return $this;
    }

    private function GET_MESSAGE_ARRAY($phone)
    {
        $this->phone = $phone;
        $header = array(
            'Host: m.mservice.io',
            'accept: application/json',
            'app_version: 30252',
            'app_code: 3.0.25',
            'device_os: ANDROID',
            'agent_id: '.$this->config[$this->phone]['agent_id'],
            'sessionkey: '.$this->config[$this->phone]['sessionkey'],
            'user_phone: '.$this->config[$this->phone]['phone'],
            'lang: vi',
            'authorization: Bearer '.$this->config[$this->phone]['authorization'],
            'content-type: application/json',
            'accept-encoding: gzip',
            'user-agent: okhttp/3.14.7'
        );
        $Data = array (
            'roomId' => $this->config[$this->phone]['roomId'],
            'beforeId' => '',
            'limit' => 10,
            'action' => 1,
        );

        return array(
            'header'     => $header,
            'requestBody'=> json_encode($Data)
        );
    }

    public function GetGroupMulty()
    {
        $arrayPostFile = array();
        $urlArray      = array();
        foreach ($this->config as $keys => $item) {

            $requestInfo = $this->GET_GROUP_ID($keys);
            if(empty($requestInfo)) continue;

            $urlArray[] =  'https://m.mservice.io/helios/chat-api/v1/room/';

            $arrayPostFile[] = array(
                CURLOPT_URL => 'https://m.mservice.io/helios/chat-api/v1/room/',
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_POST => empty($data) ? FALSE : TRUE,
                CURLOPT_POSTFIELDS => $requestInfo['requestBody'],
                CURLOPT_CUSTOMREQUEST => empty($requestInfo['requestBody']) ? 'GET' : 'POST',
                CURLOPT_HTTPHEADER => $requestInfo['header'],
                CURLOPT_ENCODING => "",
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_PROXY      => $this->proxy,

            );

        }

        $results = $this->multi_thread_curl($urlArray,$arrayPostFile, 20);

        $return = $this->config;

        foreach ($results as $item) {
            if(!is_array($item)) continue;
            if(empty($item['success'])) continue;
            $convert = Phonenumber::convert($item['json']['userId']);
                                
            $return[$convert]['roomId'] = $item['json']['room']['id'];

        }

        return $return;
    }

    public function SendMessMuilty()
    {
        $arrayPostFile = array();
        $urlArray      = array();
        foreach ($this->config as $keys => $item) {

            $requestInfo = $this->Mess($keys);
            if(empty($requestInfo)) continue;

            $urlArray[] =  'https://helios.mservice.io/helioschat.ChatService/connect"';

            $arrayPostFile[] = array(
                CURLOPT_URL => "https://helios.mservice.io/helioschat.ChatService/connect",
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_POST => TRUE,
                CURLOPT_HEADER  => TRUE,
                CURLOPT_POSTFIELDS => $requestInfo['requestBody'],
                CURLOPT_FOLLOWLOCATION => FALSE,
                CURLOPT_MAXCONNECTS => 1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => $requestInfo['header'],
                CURLOPT_CONNECTTIMEOUT => 1,
                CURLOPT_TIMEOUT_MS => 500,
                CURLOPT_PROXY      => $this->proxy,
                CURLOPT_HTTP_VERSION => CURL_VERSION_HTTP2,
            );
        }

        $results = $this->multi_thread_curl($urlArray,$arrayPostFile, 20);
        return $this;
    }

    public function CheckSendMuilty()
    {
        $arrayPostFile = array();
        $urlArray      = array();
        foreach ($this->config as $keys => $item) {

            $requestInfo = $this->GET_MESSAGE_ARRAY($keys);
            if(empty($requestInfo)) continue;

            $urlArray[] =  'https://m.mservice.io/helios/chat-api/v1/room/fetch-messages';

            $arrayPostFile[] = array(
                CURLOPT_URL => 'https://m.mservice.io/helios/chat-api/v1/room/fetch-messages',
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_POST => empty($data) ? FALSE : TRUE,
                CURLOPT_POSTFIELDS => $requestInfo['requestBody'],
                CURLOPT_CUSTOMREQUEST => empty($requestInfo['requestBody']) ? 'GET' : 'POST',
                CURLOPT_HTTPHEADER => $requestInfo['header'],
                CURLOPT_ENCODING => "",
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_PROXY      => $this->proxy,

            );

        }

        $results = $this->multi_thread_curl($urlArray,$arrayPostFile, 20);

        $return = $this->config;

        foreach ($results as $item) {
            if(!is_array($item)) continue;
            if(empty($item['success'])) continue;
            if(empty($item['json']['data']['0']['senderId'])) continue;
            $convert = Phonenumber::convert($item['json']['data']['0']['senderId']);
                                
            $dataMess = $item['json']['data'];
            foreach($dataMess as $items){
                if($items['requestId'] == $this->config[$convert]['requestId']) {
                    $return[$convert]['statusSend'] = true;
                    break;
                }

            }

        }

        return $return;
    }

    private function GET_GROUP_ID($phone)
    {
        $this->phone = $phone;
        $header = array(
            'Host: m.mservice.io',
            'accept: application/json',
            'app_version: 30252',
            'app_code: 3.0.25',
            'device_os: ANDROID',
            'agent_id: '.$this->config[$this->phone]['agent_id'],
            'sessionkey: '.$this->config[$this->phone]['sessionkey'],
            'user_phone: '.$this->config[$this->phone]['phone'],
            'lang: vi',
            'authorization: Bearer '.$this->config[$this->phone]['authorization'],
            'content-type: application/json',
            'accept-encoding: gzip',
            'user-agent: okhttp/3.14.7'
      );
      $Data = array (
                    'userId' => $this->config[$this->phone]['phone'],
                    'name' => '',
                    'addUserIds' => 
                    array (
                    0 => $this->config[$this->phone]['userrecevicer'],
                    ),
                    'customData' => 
                    array (
                    'users' => 
                    array (
                        0 => 
                        array (
                        'phone' => $this->config[$this->phone]['phone'],
                        'name' => $this->config[$this->phone]['Name'],
                        'avatar' => 'https://s3-ap-southeast-1.amazonaws.com/avatars.mservice.io/'.$this->config[$this->phone]['phone'].'.png',
                        ),
                        1 => 
                        array (
                        'phone' => $this->config[$this->phone]['userrecevicer'],
                        'name' =>  $this->config[$this->phone]['NameRecevicer'] ?? '',
                        'avatar' => 'https://s3-ap-southeast-1.amazonaws.com/avatars.mservice.io/'.$this->config[$this->phone]['userrecevicer'].'.png',
                        'isStranger' => false,
                        ),
                    ),
                    ),
                );
        return array(
            'header'     => $header,
            'requestBody'=> json_encode($Data)
        );
    }

    public function LoadData($arrayPhone)
    {
        $this->config = $arrayPhone;
        return $this;
    }

    public function LoginMuilty() 
    {
        $arrayPostFile = array();
        $urlArray      = array();
        foreach ($this->config as $keys => $item) {

            $requestInfo = $this->USER_LOGIN_MSG($keys);
            if(empty($requestInfo)) continue;

            $urlArray[] =  'https://owa.momo.vn/public/login';

            $arrayPostFile[] = array(
                CURLOPT_URL => 'https://owa.momo.vn/public/login',
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_POST => empty($data) ? FALSE : TRUE,
                CURLOPT_POSTFIELDS => $requestInfo['requestBody'],
                CURLOPT_CUSTOMREQUEST => empty($requestInfo['requestBody']) ? 'GET' : 'POST',
                CURLOPT_HTTPHEADER => $requestInfo['header'],
                CURLOPT_ENCODING => "",
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_PROXY      => $this->proxy,

            );

        }

        $results = $this->multi_thread_curl($urlArray,$arrayPostFile, 20);

        $PhoneSuccess = array();
        foreach ($results as $result){

            if(empty($result)) continue;

            if(empty($result["user"]) or !empty($result['errorCode'])) continue;
            
            $extra = $result["extra"];

            $BankVerify = empty($result['momoMsg']['bankVerifyPersonalid']) ? '1' : '2';
            $convert = $this->ConvertPhone($result["momoMsg"]['userId']);
            if($this->config[$convert]['TimeLogin'] < time() - 600 ){
                $success = $this->connect->query("UPDATE `table_momo` SET `Name`     = '".$result['momoMsg']['name']."',
                                                                            `authorization` = '".$extra["AUTH_TOKEN"]."',
                                                                            `refreshToken`  = '".$extra['REFRESH_TOKEN']."',
                                                                            `BankVerify`    = '".$BankVerify."',
                                                                            `agent_id` = '".$result["momoMsg"]["agentId"]."',
                                                                            `RSA_PUBLIC_KEY` = '".$extra["REQUEST_ENCRYPT_KEY"]."',
                                                                            `BALANCE` = '".$extra["BALANCE"]."',
                                                                            `bankCode` = '".$result['momoMsg']['bankCode']."',
                                                                            `walletStatus` = '".$result['momoMsg']['walletStatus']."',
                                                                            `sessionkey` = '".$extra["SESSION_KEY"]."',
                                                                            `success`    = 'true',
                                                                            `TimeLogin`  = '".time()."' WHERE `phone` = '".$result["momoMsg"]['userId']."' OR `phone` = '$convert' ");
                $select = $this->connect->query("SELECT * FROM `table_momo` WHERE `phone` = '".$result["momoMsg"]['userId']."' OR 
                                                    `phone` = '".$convert."' LIMIT 1 ");

                $PhoneSuccess[$convert] = $select->fetch_assoc();
            }
            else {
                $PhoneSuccess[$convert] = $this->config[$convert];
            }

        }

        return $PhoneSuccess;

    }

    public function CheckUserMuilty()
    {
        $arrayPostFile = array();
        $urlArray      = array();
        $this->requestkeyRaw = $this->generateRandom(32);
        foreach ($this->config as $keys => $item) {

            $requestInfo = $this->CHECK_USER_PRIVATE($keys);

            if(empty($requestInfo)) continue;

            $urlArray[] =  'https://owa.momo.vn/api/CHECK_USER_PRIVATE';

            $arrayPostFile[] = array(
                CURLOPT_URL => 'https://owa.momo.vn/api/CHECK_USER_PRIVATE',
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_POST => empty($data) ? FALSE : TRUE,
                CURLOPT_POSTFIELDS => $requestInfo['requestBody'],
                CURLOPT_CUSTOMREQUEST => empty($requestInfo['requestBody']) ? 'GET' : 'POST',
                CURLOPT_HTTPHEADER => $requestInfo['header'],
                CURLOPT_ENCODING => "",
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_PROXY      => $this->proxy,

            );

        }

        $results = $this->multi_thread_curl($urlArray,$arrayPostFile, 20);

        $return = $this->config;

        foreach ($results as $item) {
            if(!is_array($item)) continue;
            if(!empty($item['errorCode'])) continue;
            $convert = Phonenumber::convert($item['user']);
                                
            $return[$convert]['NameRecevicer'] = $item['extra']['NAME'];


        }

        return $return;
    }

    public function CHECK_USER_PRIVATE($phone)
    {
        $this->phone = $phone;
        if(empty($this->config[$this->phone])) return false;


        $microtime = $this->get_microtime();
        $requestkey = $this->RSA_Encrypt($this->config[$this->phone]["RSA_PUBLIC_KEY"],$this->requestkeyRaw);
        $header = array(
            "agent_id: ".$this->config[$this->phone]["agent_id"],
            "user_phone: ".$this->config[$this->phone]["phone"],
            "sessionkey: ".$this->config[$this->phone]["sessionkey"],
            "authorization: Bearer ".$this->config[$this->phone]["authorization"],
            "msgtype: CHECK_USER_PRIVATE",
            "userid: ".$this->config[$this->phone]["phone"],
            "requestkey: ".$requestkey,
            "Host: owa.momo.vn",
            "User-Agent: okhttp/3.14.17",
            "app_version: 30252",
            "app_code: 3.0.25",
            "device_os: ANDROID",
            'Content-Type: application/json',
            'accept: application/json'
        );
        
        $Data = array (
            'user' => $this->config[$this->phone]['phone'],
            'msgType' => 'CHECK_USER_PRIVATE',
            'cmdId' => (string) $microtime. '000000',
            'lang' => 'vi',
            'time' => (int) $microtime,
            'channel' => 'APP',
            'appVer' => 30252,
            'appCode' => '3.0.25',
            'deviceOS' => 'ANDROID',
            'buildNumber' => 1916,
            'appId' => 'vn.momo.transfer',
            'result' => true,
            'errorCode' => 0,
            'errorDesc' => '',
            'momoMsg' => 
            array (
              '_class' => 'mservice.backend.entity.msg.LoginMsg',
              'getMutualFriend' => false,
            ),
            'extra' => 
            array (
              'CHECK_INFO_NUMBER' => $this->config[$this->phone]['userrecevicer'],
              'checkSum' => $this->generateCheckSum('CHECK_USER_PRIVATE',$microtime),
            ),
        );
        return array(
            'header'      => $header,
            'requestBody' => $this->Encrypt_data($Data,$this->requestkeyRaw)
        );
    }

    private function ConvertPhone($phonenumber)
    {
            $CELL = array (
                '016966'=>'03966',
                '0169'=>'039',
                '0168'=>'038',
                '0167'=>'037',
                '0166'=>'036',
                '0165'=>'035',
                '0164'=>'034',
                '0163'=>'033',
                '0162'=>'032',
                '0120'=>'070',
                '0121'=>'079',
                '0122'=>'077',
                '0126'=>'076',
                '0128'=>'078',
                '0123'=>'083',
                '0124'=>'084',
                '0125'=>'085',
                '0127'=>'081',
                '0129'=>'082',
                '01992'=>'059',
                '01993'=>'059',
                '01998'=>'059',
                '01999'=>'059',
                '0186'=>'056',
                '0188'=>'058'
            );

            $phonenumber=str_replace(' ','',$phonenumber);
            //2. Xóa các dấu chấm phân cách
            $phonenumber=str_replace('.','',$phonenumber);
			//3. Xóa các dấu gạch nối phân cách
            $phonenumber=str_replace('-','',$phonenumber);
            //4. Xóa dấu mở ngoặc đơn
            $phonenumber=str_replace('(','',$phonenumber);
            //5. Xóa dấu đóng ngoặc đơn
            $phonenumber=str_replace(')','',$phonenumber);
            //6. Xóa dấu +
            $phonenumber=str_replace('+','',$phonenumber);
			//7. Chuyển 84 đầu thành 0
			if(substr($phonenumber,0,2) == '84')
			{
				$phonenumber='0'.substr($phonenumber,2,strlen($phonenumber)-2);
			}
            foreach($CELL as $key=>$value)
			{
				//$prefixlen=strlen($key);
				if(strpos($phonenumber,$key) === 0)
				{
				    $prefix = $key;
                    $prefixlen = strlen($key);
                    $phone = substr($phonenumber,$prefixlen,strlen($phonenumber)-$prefixlen);
                    $prefix = str_replace($key,$value,$prefix);
                    $phonenumber = $prefix.$phone;
					//$phonenumber=str_replace($key,$value,$phonenumber);
					break;
				}
			}
        return $phonenumber;
    }

    private function USER_LOGIN_MSG($phone)
    {
        $this->phone = $phone;
        if(empty($this->config[$this->phone])) return false;

        $microtime = $this->get_microtime();
        $header = array(
            "agent_id: ".$this->config[$this->phone]["agent_id"],
            "user_phone: ".$this->config[$this->phone]["phone"],
            "sessionkey: ".(!empty($this->config[$this->phone]["sessionkey"])) ? $this->config[$this->phone]["sessionkey"] : "",
            "authorization: Bearer ".$this->config[$this->phone]["authorization"],
            "msgtype: USER_LOGIN_MSG",
            "Host: owa.momo.vn",
            "user_id: ".$this->config[$this->phone]["phone"],
            "User-Agent: okhttp/3.14.17",
            "app_version: 30252",
            "app_code: 3.0.25",
            "device_os: ANDROID",
            'Content-Type: application/json',
            'accept: application/json'
        );
        $Data = array (
            'user' => $this->config[$this->phone]['phone'],
            'msgType' => 'USER_LOGIN_MSG',
            'pass' => $this->config[$this->phone]['password'],
            'cmdId' => (string) $microtime.'000000',
            'lang' => 'vi',
            'time' => $microtime,
            'channel' => 'APP',
            'appVer' => 30252,
            'appCode' => '3.0.25',
            'deviceOS' => 'ANDROID',
            'buildNumber' => 0,
            'appId' => 'vn.momo.platform',
            'result' => true,
            'errorCode' => 0,
            'errorDesc' => '',
            'momoMsg' => 
            array (
              '_class' => 'mservice.backend.entity.msg.LoginMsg',
              'isSetup' => false,
            ),
            'extra' => 
            array (
              'pHash' => $this->get_pHash(),
              'AAID' => $this->config[$this->phone]['AAID'],
              'IDFA' => '',
              'TOKEN' => $this->config[$this->phone]['TOKEN'],
              'SIMULATOR' => '',
              'SECUREID' => $this->config[$this->phone]['SECUREID'],
              'MODELID' => $this->config[$this->phone]['MODELID'],
              'checkSum' => $this->generateCheckSum('USER_LOGIN_MSG', $microtime),
            ),
        );

        return array(
            'header'      => $header,
            'requestBody' => json_encode($Data)
        );
    }

    public function Mess($phone)
    {
        $this->phone = $phone;
        $header = array(
            "Host: helios.mservice.io",
            "user-agent: xxx grpc-java-okhttp/1.32.2",
            "content-type: application/grpc",
            "te: trailers",
            "authorization: Bearer ".$this->config[$this->phone]['authorization'],
            "grpc-accept-encoding: gzip"
        );
        $this->config[$this->phone]['requestId'] = $this->generateImei();

        if(!empty($this->config[$this->phone]['READ'])){
            $message = array (
                'status' => 'READ',
                'roomId' => $this->config[$this->phone]['roomId'],
                'messageId' => $this->config[$this->phone]['READ'],
            );
        }
        else if(!empty($this->config[$this->phone]['messageSend'])){
                $message = array (
                    'roomId' => $this->config[$this->phone]['roomId'],
                    'requestId' => $this->config[$this->phone]['requestId'],
                    'createAt' => $this->get_microtime(),
                    'parts' => 
                    array (
                    'partType' => 'INLINE',
                    'payload' => 
                    array (
                        'content' => $this->config[$this->phone]['messageSend'],
                        'customData' => 
                        array (
                        'name' => $this->config[$this->phone]['Name'],
                        'userName' => $this->config[$this->phone]['Name'],
                        '_id' => (string) $this->ConvertPhone($this->config[$this->phone]['phone']),
                        'avatar' => 'https://s3-ap-southeast-1.amazonaws.com/avatars.mservice.io/'.$this->ConvertPhone($this->config[$this->phone]['phone']).'.png',
                        ),
                    ),
                    ),
                );
        }
        else if(!empty($this->config[$this->phone]['imageSend'])){
            $message = array (
                'roomId' => $this->config[$this->phone]['roomId'],
                'parts' => 
                array (
                'partType' => 'attachment',
                'payload' => 
                array (
                    'customData' => 
                    array (
                    'userName' => $this->config[$this->phone]['Name'],
                    ),
                    'content' => '',
                    'type' => 'IMAGE',
                    'url' => $this->config[$this->phone]['imageSend'],
                ),
                ),
                'requestId' => $this->config[$this->phone]['requestId'],
            );
                
        }
        
        return array(
            'header' => $header,
            'requestBody'  => $this->HexDataMess($message)
        );

    }

    public function HexDataMess($message)
    {        
        if(is_array($message)) $message = json_encode($message);

        if(!empty($this->config[$this->phone]['READ'])) {
            $hexbin = '0000000';
            $hexbin.= dechex(intdiv(strlen($message), 128));
            $hexbin.= dechex(strlen($message) + 4).'080512';
            $hexbin.= dechex(strlen($message));
        }
        else {
            $hexbin = '000000';

            $tinhtong = 128 * (intdiv(strlen($message),128) - 1);

            $hexbin .= '0'.dechex(strlen($message) + 5).'080F12';

            $hexbin .= dechex(strlen($message) - $tinhtong).'0'.dechex(strlen($message) / 128);
        }

        return hex2bin($hexbin).$message;
    }

    public function RSA_Encrypt($key,$content)
    {
        if(empty($this->rsa)){
            $this->INCLUDE_RSA($key);
        }
        return base64_encode($this->rsa->encrypt($content));
    }

    private function INCLUDE_RSA($key)
    {
        require_once '../../../system/core/lib/RSA/Crypt/RSA.php';
        $this->rsa = new Crypt_RSA();
        $this->rsa->loadKey($key);
        $this->rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        return $this;
    }

    private function get_TOKEN()
    {
        return  $this->generateRandom(22).':'.$this->generateRandom(9).'-'.$this->generateRandom(20).'-'.$this->generateRandom(12).'-'.$this->generateRandom(7).'-'.$this->generateRandom(7).'-'.$this->generateRandom(53).'-'.$this->generateRandom(9).'_'.$this->generateRandom(11).'-'.$this->generateRandom(4);
    }

    public function multi_thread_curl($urlArray, $optionArray, $nThreads)
    {

        //Group your urls into groups/threads.
        $curlArray = array_chunk($urlArray, $nThreads, $preserve_keys = true);
      
        //Iterate through each batch of urls.
        $ch = 'ch_';
        $results = array();

        foreach($curlArray as $threads) {      
      
            //Create your cURL resources.
            foreach($threads as $thread => $value) {
      
                ${$ch . $thread} = curl_init();
        
                curl_setopt_array(${$ch . $thread}, $optionArray[$thread]); //Set your main curl options.
      
            }
      
            //Create the multiple cURL handler.
            $mh = curl_multi_init();
      
            //Add the handles.
            foreach($threads as $thread=>$value) {
      
                curl_multi_add_handle($mh, ${$ch . $thread});
      
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
            foreach($threads as $thread=>$value) {
      
                $result  = curl_multi_getcontent(${$ch . $thread});

                if(is_object(json_decode($result))) $results[$thread] = json_decode($result, true); else $results[$thread] = json_decode($this->Decrypt_data($result), true);
        
                curl_multi_remove_handle($mh, ${$ch . $thread});
      
            }
      
            //Close the multi handle exec.
            curl_multi_close($mh);
      
        }
      
      
        return $results;
      
    }

    public function Encrypt_data($data,$key)
    {

        $iv = pack('C*', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->requestkeyRaw = $key;
        return base64_encode(openssl_encrypt(is_array($data) ? json_encode($data) : $data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv));

    }

    public function Decrypt_data($data)
    {

        $iv = pack('C*', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        return openssl_decrypt(base64_decode($data), 'AES-256-CBC', $this->requestkeyRaw, OPENSSL_RAW_DATA, $iv);

    }

    public function generateCheckSum($type,$microtime)
    {
        $Encrypt =   $this->config[$this->phone]["phone"].$microtime.'000000'.$type. ($microtime / 1000000000000.0) . 'E12';
        $iv = pack('C*', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        return base64_encode(openssl_encrypt($Encrypt, 'AES-256-CBC',$this->config[$this->phone]["setupKeyDecrypt"], OPENSSL_RAW_DATA, $iv));
    }

    private function get_pHash()
    {
        $data = $this->config[$this->phone]["imei"]."|".$this->config[$this->phone]["password"];
        $iv = pack('C*', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        return base64_encode(openssl_encrypt($data, 'AES-256-CBC',$this->config[$this->phone]["setupKeyDecrypt"], OPENSSL_RAW_DATA, $iv));
    }

    public function get_setupKey($setUpKey)
    {
        $iv = pack('C*', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        return openssl_decrypt(base64_decode($setUpKey), 'AES-256-CBC',$this->config[$this->phone]["ohash"], OPENSSL_RAW_DATA, $iv);
    }

    private function generateRandom($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    private function get_SECUREID($length = 17)
    {
        $characters = '0123456789abcdef';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function generateImei()
    {
          return $this->generateRandomString(8) . '-' . $this->generateRandomString(4) . '-' . $this->generateRandomString(4) . '-' . $this->generateRandomString(4) . '-' . $this->generateRandomString(12);
    }

    private function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdef';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function get_string($data){
        return str_replace(array('<',"'",'>','?','/',"\\",'--','eval(','<php','-'),array('','','','','','','','','',''),htmlspecialchars(addslashes(strip_tags($data))));
    }

    public function get_microtime(){
        return round(microtime(true) * 1000);
    }
}

class Phonenumber
{

    static $arr_Prefix = array('CELL'=>array(
                                            '016966'=>'03966',
                                            '0169'=>'039',
                                            '0168'=>'038',
                                            '0167'=>'037',
                                            '0166'=>'036',
                                            '0165'=>'035',
                                            '0164'=>'034',
                                            '0163'=>'033',
                                            '0162'=>'032',
                                            '0120'=>'070',
                                            '0121'=>'079',
                                            '0122'=>'077',
                                            '0126'=>'076',
                                            '0128'=>'078',
                                            '0123'=>'083',
                                            '0124'=>'084',
                                            '0125'=>'085',
                                            '0127'=>'081',
                                            '0129'=>'082',
                                            '01992'=>'059',
                                            '01993'=>'059',
                                            '01998'=>'059',
                                            '01999'=>'059',
                                            '0186'=>'056',
                                            '0188'=>'058'
                                           )
                             );
                             
    public static function convert($phonenumber)
    {
        if(!empty($phonenumber))
        {
            //1. Xóa ký tự trắng
            $phonenumber=str_replace(' ','',$phonenumber);
            //2. Xóa các dấu chấm phân cách
            $phonenumber=str_replace('.','',$phonenumber);
			//3. Xóa các dấu gạch nối phân cách
            $phonenumber=str_replace('-','',$phonenumber);
            //4. Xóa dấu mở ngoặc đơn
            $phonenumber=str_replace('(','',$phonenumber);
            //5. Xóa dấu đóng ngoặc đơn
            $phonenumber=str_replace(')','',$phonenumber);
            //6. Xóa dấu +
            $phonenumber=str_replace('+','',$phonenumber);
			//7. Chuyển 84 đầu thành 0
			if(substr($phonenumber,0,2)=='84')
			{
				$phonenumber='0'.substr($phonenumber,2,strlen($phonenumber)-2);
			}
            foreach(self::$arr_Prefix['CELL'] as $key=>$value)
			{
				//$prefixlen=strlen($key);
				if(strpos($phonenumber,$key)===0)
				{
				    $prefix=$key;
                    $prefixlen = strlen($key);
                    $phone=substr($phonenumber,$prefixlen,strlen($phonenumber)-$prefixlen);
                    $prefix=str_replace($key,$value,$prefix);
                    $phonenumber = $prefix.$phone;
					//$phonenumber=str_replace($key,$value,$phonenumber);
					break;
				}
			}
			
            return $phonenumber;
        }
        else
        {
            return false;
        }
    }
}

?>