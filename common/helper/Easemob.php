<?php

/**
  --------------------------------------------------
  环信PHP 基础类
  --------------------------------------------------
 */

namespace common\helper;

class Easemob {

    private $client_id;
    private $client_secret;
    private $org_name;
    private $app_name;
    private $url;

//------------------------------------------------------用户体系	
    /**
     * 初始化参数
     *
     * @param array $options   
     * @param $options['client_id']    	
     * @param $options['client_secret'] 
     * @param $options['org_name']    	
     * @param $options['app_name']   		
     */
    public function __construct($options) {
        $this->client_id = isset($options ['client_id']) ? $options ['client_id'] : '';
        $this->client_secret = isset($options ['client_secret']) ? $options ['client_secret'] : '';
        $this->org_name = isset($options ['org_name']) ? $options ['org_name'] : '';
        $this->app_name = isset($options ['app_name']) ? $options ['app_name'] : '';
        if (!empty($this->org_name) && !empty($this->app_name)) {
            $this->url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/';
        }
    }

    /**
     * 获取token 
     */
    function getToken() {
        $options = array(
            "grant_type" => "client_credentials",
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret
        );
        //json_encode()函数，可将PHP数组或对象转成json字符串，使用json_decode()函数，可以将json字符串转换为PHP数组或对象
        $body = json_encode($options);
        //使用 $GLOBALS 替代 global
        $url = $this->url . 'token';
        //$url=$base_url.'token';
        $tokenResult = $this->postCurl($url, $body, $header = array());
        //var_dump($tokenResult['expires_in']);
        //return $tokenResult;
        //return "Authorization:Bearer " . 'YWMtmliy8HAuEeeCgUXLMdfdcwAAAAAAAAAAAAAAAAAAAAHK9ObQAxoR55zLyRc_ZjyeAgMAAAFdcwI5IABPGgBdOzSLzUG6_s4beKuk6GM-vjLvaqvVCZqtm2B-O4OqXw';
        return "Authorization:Bearer " . $tokenResult['access_token'];
    }

    /**
      授权注册
     */
    function createUser($username, $password) {
        $url = $this->url . 'users';
        $options = array(
            "username" => $username,
            "password" => $password
        );
        $body = json_encode($options);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $body, $header);
        return $result;
    }

    /*
      批量注册用户
     */

    function createUsers($options) {
        $url = $this->url . 'users';

        $body = json_encode($options);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $body, $header);
        return $result;
    }

    /*
      重置用户密码
     */

    function resetPassword($username, $newpassword) {
        $url = $this->url . 'users/' . $username . '/password';
        $options = array(
            "newpassword" => $newpassword
        );
        $body = json_encode($options);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $body, $header, "PUT");
        return $result;
    }

    /*
      获取单个用户
     */

    function getUser($username) {
        $url = $this->url . 'users/' . $username;
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, "GET");
        return $result;
    }

    /*
      获取批量用户----不分页
     */

    function getUsers($limit = 0) {
        if (!empty($limit)) {
            $url = $this->url . 'users?limit=' . $limit;
        } else {
            $url = $this->url . 'users';
        }
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, "GET");
        return $result;
    }

    /*
      获取批量用户---分页
     */

    function getUsersForPage($limit = 0, $cursor = '') {
        $url = $this->url . 'users?limit=' . $limit . '&cursor=' . $cursor;

        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, "GET");
        if (!empty($result["cursor"])) {
            $cursor = $result["cursor"];
            $this->writeCursor("userfile.txt", $cursor);
        }
        //var_dump($GLOBALS['cursor'].'00000000000000');
        return $result;
    }

    //创建文件夹
    function mkdirs($dir, $mode = 0777) {
        if (is_dir($dir) || @mkdir($dir, $mode))
            return TRUE;
        if (!mkdirs(dirname($dir), $mode))
            return FALSE;
        return @mkdir($dir, $mode);
    }

    //写入cursor
    function writeCursor($filename, $content) {
        //判断文件夹是否存在，不存在的话创建
        if (!file_exists("resource/txtfile")) {
            mkdirs("resource/txtfile");
        }
        $myfile = @fopen("resource/txtfile/" . $filename, "w+") or die("Unable to open file!");
        @fwrite($myfile, $content);
        fclose($myfile);
    }

    //读取cursor
    function readCursor($filename) {
        //判断文件夹是否存在，不存在的话创建
        if (!file_exists("resource/txtfile")) {
            mkdirs("resource/txtfile");
        }
        $file = "resource/txtfile/" . $filename;
        $fp = fopen($file, "a+"); //这里这设置成a+
        if ($fp) {
            while (!feof($fp)) {
                //第二个参数为读取的长度
                $data = fread($fp, 1000);
            }
            fclose($fp);
        }
        return $data;
    }

    /*
      删除单个用户
     */

    function deleteUser($username) {
        $url = $this->url . 'users/' . $username;
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'DELETE');
        return $result;
    }

    /*
      删除批量用户
      limit:建议在100-500之间，、
      注：具体删除哪些并没有指定, 可以在返回值中查看。
     */

    function deleteUsers($limit) {
        $url = $this->url . 'users?limit=' . $limit;
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'DELETE');
        return $result;
    }

    /*
      修改用户昵称
     */

    function editNickname($username, $nickname) {
        $url = $this->url . 'users/' . $username;
        $options = array(
            "nickname" => $nickname
        );
        $body = json_encode($options);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $body, $header, 'PUT');
        return $result;
    }

    /*
      查看用户是否在线
     */

    function isOnline($username) {
        $url = $this->url . 'users/' . $username . '/status';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        return $result;
    }

    /*
      查看用户离线消息数
     */

    function getOfflineMessages($username) {
        $url = $this->url . 'users/' . $username . '/offline_msg_count';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        return $result;
    }

    /*
      查看某条消息的离线状态
      ----deliverd 表示此用户的该条离线消息已经收到
     */

    function getOfflineMessageStatus($username, $msg_id) {
        $url = $this->url . 'users/' . $username . '/offline_msg_status/' . $msg_id;
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        return $result;
    }

    /*
      禁用用户账号
     */

    function deactiveUser($username) {
        $url = $this->url . 'users/' . $username . '/deactivate';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header);
        return $result;
    }

    /*
      解禁用户账号
     */

    function activeUser($username) {
        $url = $this->url . 'users/' . $username . '/activate';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header);
        return $result;
    }

    /*
      强制用户下线
     */

    function disconnectUser($username) {
        $url = $this->url . 'users/' . $username . '/disconnect';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        return $result;
    }

    //--------------------------------------------------------上传下载
    /*
      上传图片或文件
     */
    function uploadFile($filePath) {
        $url = $this->url . 'chatfiles';
        $file = file_get_contents($filePath);
        $body['file'] = $file;
        $header = array('enctype:multipart/form-data', $this->getToken(), "restrict-access:true");
        $result = $this->postCurl($url, $body, $header, 'XXX');
        return $result;
    }

    /*
      下载文件或图片
     */

    function downloadFile($uuid, $shareSecret) {
        $url = $this->url . 'chatfiles/' . $uuid;
        $header = array("share-secret:" . $shareSecret, "Accept:application/octet-stream", $this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        $filename = md5(time() . mt_rand(10, 99)) . ".png"; //新图片名称
        if (!file_exists("resource/down")) {
            //mkdir("../image/down");
            mkdirs("resource/down/");
        }

        $file = @fopen("resource/down/" . $filename, "w+"); //打开文件准备写入
        @fwrite($file, $result); //写入
        fclose($file); //关闭
        return $filename;
    }

    /*
      下载图片缩略图
     */

    function downloadThumbnail($uuid, $shareSecret) {
        $url = $this->url . 'chatfiles/' . $uuid;
        $header = array("share-secret:" . $shareSecret, "Accept:application/octet-stream", $this->getToken(), "thumbnail:true");
        $result = $this->postCurl($url, '', $header, 'GET');
        $filename = md5(time() . mt_rand(10, 99)) . "th.png"; //新图片名称
        if (!file_exists("resource/down")) {
            //mkdir("../image/down");
            mkdirs("resource/down/");
        }

        $file = @fopen("resource/down/" . $filename, "w+"); //打开文件准备写入
        @fwrite($file, $result); //写入
        fclose($file); //关闭
        return $filename;
    }

    //--------------------------------------------------------发送消息
    /*
      发送文本消息
     */
    function sendText($from = "admin", $target_type, $target, $content, $ext) {
        $url = $this->url . 'messages';
        $body['target_type'] = $target_type;
        $body['target'] = $target;
        $options['type'] = "txt";
        $options['msg'] = $content;
        $body['msg'] = $options;
        $body['from'] = $from;
        $body['ext'] = $ext;
        $b = json_encode($body);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $b, $header);
        return $result;
    }

    /*
      发图片消息
     */

    function sendImage($filePath, $from = "admin", $target_type, $target, $filename, $ext) {
        $result = $this->uploadFile($filePath);
        $uri = $result['uri'];
        $uuid = $result['entities'][0]['uuid'];
        $shareSecret = $result['entities'][0]['share-secret'];
        $url = $this->url . 'messages';
        $body['target_type'] = $target_type;
        $body['target'] = $target;
        $options['type'] = "img";
        $options['url'] = $uri . '/' . $uuid;
        $options['filename'] = $filename;
        $options['secret'] = $shareSecret;
        $options['size'] = array(
            "width" => 480,
            "height" => 720
        );
        $body['msg'] = $options;
        $body['from'] = $from;
        $body['ext'] = $ext;
        $b = json_encode($body);
        $header = array($this->getToken());
        //$b=json_encode($body,true);
        $result = $this->postCurl($url, $b, $header);
        return $result;
    }

    //-------------------------------------------------------------聊天记录

    /*
      导出聊天记录----不分页
     */
    function getChatRecord($ql) {
        if (!empty($ql)) {
            $url = $this->url . 'chatmessages?ql=' . $ql;
        } else {
            $url = $this->url . 'chatmessages';
        }
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, "GET");
        return $result;
    }

    /*
      导出聊天记录---分页
     */

    function getChatRecordForPage($ql, $limit = 0, $cursor) {
        if (!empty($ql)) {
            $url = $this->url . 'chatmessages?ql=' . $ql . '&limit=' . $limit . '&cursor=' . $cursor;
        }
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, "GET");
        $cursor = $result["cursor"];
        $this->writeCursor("chatfile.txt", $cursor);
        //var_dump($GLOBALS['cursor'].'00000000000000');
        return $result;
    }

    /**
     * $this->postCurl方法
     */
    function postCurl($url, $body, $header, $type = "POST") {
        //1.创建一个curl资源
        $ch = curl_init();
        //2.设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url); //设置url
        //1)设置请求头
        //array_push($header, 'Accept:application/json');
        //array_push($header,'Content-Type:application/json');
        //array_push($header, 'http:multipart/form-data');
        //设置为false,只会获得响应的正文(true的话会连响应头一并获取到)
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 设置超时限制防止死循环
        //设置发起连接前的等待时间，如果设置为0，则无限等待。
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //2)设备请求体
        if (count($body) > 0) {
            //$b=json_encode($body,true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body); //全部数据使用HTTP协议中的"POST"操作来发送。
        }
        //设置请求头
        if (count($header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        //上传文件相关设置
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算
        //3)设置提交方式
        switch ($type) {
            case "GET":
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case "PUT"://使用一个自定义的请求信息来代替"GET"或"HEAD"作为HTTP请									                     求。这对于执行"DELETE" 或者其他更隐蔽的HTT
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }


        //4)在HTTP请求中包含一个"User-Agent: "头的字符串。-----必设

        curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)'); // 模拟用户使用的浏览器
        //5)
        //3.抓取URL并把它传递给浏览器
        $res = curl_exec($ch);

        $result = json_decode($res, true);
        //4.关闭curl资源，并且释放系统资源
        curl_close($ch);
        if (empty($result))
            return $res;
        else
            return $result;
    }

}

?>