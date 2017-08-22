<?php
/**
 * 环信聊天用户聊天记录逻辑层.
 * User: luohua
 * Date: 17-4-17
 * Time: 下午5:13
 */
namespace common\logic;
use common\models\im\MessageConversation;
use common\models\im\MessageInfo;
use common\models\estate\agency\AgencyInfo;
use common\components\HuanXinUsers;

class ImMessageLogic
{
    //插入客户会话记录并返回插入id
    public static function insertConversationRecord($data = [])
    {
        if (empty($data))
            return false;
        $result = MessageConversation::insertRecord($data);
        return $result;
    }

    //插入客户IM聊天消息并据返回插入id
    public static function insertInfoRecord($data = [])
    {
        if (empty($data))
            return false;
        $result = MessageInfo::insertRecord($data);
        return $result;
    }

    /*
     * 删除客户IM聊天消息
     */
    public static function deleteInfoRecord($data=[]){
        if (empty($data))
            return false;
        $result = MessageInfo::deleteRecord($data);
        return $result;
    }

    /*
     * 根据用户id和魔售经纪人id获取两人的聊天记录
     */
    public static function getMessageInfo($data = [])
    {
        if (empty($data))
            return false;
        $condition = [
            'userID' => intval($data['userID']),
            'serviceUserID' => intval($data['serviceUserID']),
            'pageSize' => intval($data['pageSize']),
            'pageNo' => intval($data['pageNo'])
        ];
        $result = MessageConversation::getMessageInfoByPage($condition);
        return $result;
    }

    /*
     * 根据用户id获取用户聊天魔售经纪人信息
     */
    public static function getUserBrokerList($userID)
    {
        $condition = [
            'userID' => intval($userID),
        ];
        $result = MessageConversation::getUserBrokerList($condition);
        
        $agencyIDs = [];
        if (!empty($result)){
             foreach ($result as $v){
                 $agencyIDs[] = $v["serviceUserID"];
                 $messageInfo[] = MessageConversation::getUserMessageID($v);
             }
        }
        //查询与用户聊天所有经纪人信息
        $agencyInfo = AgencyInfo::selectAll("agencyID,agencyName,avatarsImageName,chatUserID,",["agencyID" => $agencyIDs, "isActive" =>1]);

        //$huanXin = new HuanXinUsers();
        foreach ($agencyInfo as $key => $value){
            if (!empty($messageInfo)){
                foreach ($messageInfo as $messValue){
                    if ($value["agencyID"] == $messValue['serviceUserID']){
                        $agencyInfo[$key]['messageTextContent'] = $messValue['messageinfos']['messageTextContent'];
                        $agencyInfo[$key]['inDate'] = self::_dateTonvertFromat(strtotime($messValue['inDate']));
                        $agencyInfo[$key]['inDates'] = $messValue['inDate'];
                        //$agencyInfo[$key]['status'] = $huanXin->actionIsonline($value['chatUserID']);
                    }
                }
            }
        }
        return $agencyInfo;
    }

    /*
     * 根据用户id获取用户聊天魔售经纪人信息
     */
    public static function getUserBrokerListOld($userID)
    {
        $condition = [
            'userID' => intval($userID),
        ];
        $result = MessageConversation::getUserBrokerList($condition);
        $agencyIDs = [];
        if (!empty($result)){
            foreach ($result as $v){
                $agencyIDs[] = $v["serviceUserID"];
            }
        }
        //查询与用户聊天所有经纪人信息
        $agencyInfo = AgencyInfo::selectAll("agencyID,agencyName,avatarsImageName,chatUserID,",["agencyID" => $agencyIDs, "isActive" =>1]);
        return $agencyInfo;
    }

    //查询用户和经纪人聊天未读的记录数量
    public static function getUserMessageNum($params){
        return $userMessageNum = MessageConversation::getUserMessageNum($params);
    }

    //查询用户和经纪人聊天未读的记录总数量
    public static function getUserMessageUnReadNum($params){
        return $userMessageNum = MessageConversation::getUserMessageUnReadNum($params);
    }

    /*
     * 根据用户id和魔售经纪人id获取两人的聊天记录
     */
    public static function setMessageViewed($condition)
    {
        /*$condition = [
            'userID' => intval($userID),
            'serviceUserID' => intval($serviceUserID)
        ];*/
        $condition = ['in','conversationID',$condition];
        $result = MessageConversation::setMessageViewed($condition);
        return $result;
    }

    /**
     * 计算时间 如：今日 8:08 昨日 19:20 大于48小时的 2017-06-29
     * @Date: 2017-06-29
     * @Params: $sorce_date 时间戳
     * @Params: $type 表示返回事件格式 1:表示带有时分格式如：2017-06-29 10:11 2：只是日期格式
     * @Return: String
     */
    private static function _dateTonvertFromat($time,$type = 2){
        //获取今天时间戳
        $nowTime = time();
        $timeHtml = '';
        //今日0时至现在的秒数
        $todaySeconed = $nowTime - strtotime(date('Y-m-d'));
        switch($time){
            //今天
            case ($time+$todaySeconed)>=$nowTime:
                $timeHtml = date('H:i', $time);
                break;
            //昨天
            case ($time+3600*24*2)>=$nowTime:
                $temp_time = date('H:i',$time);
                //$timeHtml = '昨天 '.$temp_time ;
                $timeHtml = '昨天 ';
                break;
            //大于48小时的日期
            default:
                if($type == 1){
                    $timeHtml = date('Y-m-d H:i',$time);
                }else{
                    $timeHtml = date('Y-m-d',$time);
                }
                break;
        }
        return $timeHtml;
    }

}