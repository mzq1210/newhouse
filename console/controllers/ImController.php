<?php
/**
 * 环信批量注册用户.
 * User: luohua
 * Date: 17-3-31
 * Time: 下午2:09
 */
namespace console\controllers;


use Yii;
use yii\console\Controller;
use common\helper\Easemob;
use common\models\im\HuanXinUser;

class ImController extends Controller
{
    private $_h;
    public function init()
    {
        $options['client_id'] = HUANXIN_CLIENT_ID;
        $options['client_secret'] = HUANXIN_CLIENT_SECRET;
        $options['org_name'] = HUANXIN_ORG_NAME;
        $options['app_name'] = HUANXIN_APP_NAME;
        $this->_h = new Easemob($options);
        parent::init();
    }


    public function actionDelete(){
        $a = $this->_h->deleteUsers(100);
        var_dump($a);
    }

    /**
     * @desc 批量注册环信用户
     * @param int $num 批量注册数量
     * @author wangluohua
     */
    public function actionBatchRegister($num = HUANXIN_REGISTER_NUM){
        
        $num = ceil($num/HUANXIN_USER_IMPORT_NUM);
        $userNum = 0;
        for ($i=0; $i<$num; $i++){
            $usersInfo = $this->makeUserInfo(HUANXIN_USER_IMPORT_NUM);
            $userImportNum = $this->actionImport($usersInfo);
            if ($userImportNum === false){
                continue;
            }else{
                $userNum = $userNum+$userImportNum;
            }
            usleep(200000);
        }

        echo '注册环信用户数量为:'.$userNum;
        

    }

    /**
     * @desc 批量注册环信用户用于检测剩余可用环信数量小于规定数量时批量注册
     * @param int $num 
     * @author wangluohua
     */
    public function actionIncrRegister($num = HUANXIN_REGISTER_NUM){
        $condition = ['isAssigned' =>1];
        $resultNum = HuanXinUser::countNum($condition);
        var_dump($resultNum);
        if ($resultNum < HUANXIN_RETISTER_TRIGGER_NUM){
            $this->actionBatchRegister($num);
        }
    }

    /**
     * @desc 批量生成环信用户信息同时保存到本地
     * @param array $usersInfo 
     * @author wangluohua
     */
    public function actionImport($usersInfo){
        $result = $this->_h->createUsers($usersInfo);
        if (isset($result['error'])){
            echo $result['error'];
            return false;
        }
        if (!isset($result['organization']) && !isset($result['applicationName'])){
            echo "error1";
            return false;
        }
        if ($result['organization'] !== HUANXIN_ORG_NAME && $result['applicationName'] !== HUANXIN_APP_NAME){
            echo "error2";
            return false;
        }
        if (empty($result['entities'])){
            echo 'error3';
            return false;
        }
        $users = [];
        foreach ($result['entities'] as $v){

            if ($v['activated'] && !empty($v['username'])){
                $time = date('Y-m-d H:i:s');
                $user['chatUserID'] = $v['username'];
                $user['isAssigned'] = 0;
                $user['inDate'] = $time;
                $user['lastEditDate'] = $time;
                $users[] = $user;
            }

        }
        $field = ['chatUserID','isAssigned','inDate','lastEditDate'];
        $resultNum = HuanXinUser::insertRecord($field, $users);
        return $resultNum;
    }

    /**
     * @desc 批量生成环信用户信息
     * @param int $num
     * @author wangluohua
     */
    private function makeUserInfo($num = HUANXIN_USER_IMPORT_NUM){
        $usersInfo = [];
        for ($i=0; $i<$num;$i++){
            $userName = HUANXIN_USER_PREFIX . $this->getRandChar(12) . $this->getRandChar(12);
            $user['username'] = $userName;
            $user['password'] = $userName;
            $user['nickname'] = $userName;
            $usersInfo[] = $user;
        }
        return $usersInfo;
    }

    /**
     * @desc 获取随机字符串
     * @param int $length
     * @author wangluohua
     */
    private function getRandChar($length = 6){
        $str = null;
        $strPol = "0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }
    
}