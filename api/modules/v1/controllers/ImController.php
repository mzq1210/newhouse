<?php
/**
 * 聊天接口
 * User: 王萝华
 * Date: 17-5-3
 * Time: 上午9:25
 */

namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\logic\AgencyInfoLogic;
use common\logic\ImMessageLogic;
use common\helper\BaseCache;

class ImController extends ActiveController
{
    /*
       * 根据用户提交的数据持久化用户信息
       * 同时根据客户端提交的信息进行根据redis进行去重
       */
    public function actionInmsg(){
        /*echo json_encode($this->request->isGet);
        if (!$this->request->isPost)
            die;*/

        $data = $this->request->post();
        if (!isset($data['messageType']) || empty($data['userID']) || empty($data['conversationType']))
            return ["参数缺失 messageinfo 插入失败", 201];
        if ($data['conversationType'] == 2 && !empty($data['id'])){
            $result = ["message already exist", 201];
            $setResult = BaseCache::setNx($data['id'],"okok",15);
            if ($setResult==true){
                $result = $this->_inmsg($data);
            }
        }else{
            $result = self::_inmsg($data);
        }
        return $result;
    }

    /*
     * 私有方法插入聊天记录
     */
    private function _inmsg($data){
        $inUser = isset($data['inUser']) ? $data['inUser'] : $data['userID'];
        $lastEditUser = isset($data['lastEditUser']) ? $data['lastEditUser'] : $data['userID'];
        $companyCode = isset($data['companyCode']) ? $data['companyCode'] : '1';
        $messageInfoData = [
            'messageType' => intval($data['messageType']),
            'messageTextContent' => isset($data['data']) ? $data['data'] : '',
            'companyCode' => $companyCode,
            'inUser' => $inUser,
            'inDate' => date("Y-m-d H:i:s"),
            'lastEditUser' => $lastEditUser,
            'lastEditDate' => date("Y-m-d H:i:s")
        ];
        if ($data['messageType'] == 1)
            $messageInfoData['messageExtendContent'] = $data['ext'];
        $messageInfoID = ImMessageLogic::insertInfoRecord($messageInfoData);
        if ($messageInfoID==false){
            return ["messageinfo 插入失败", 202];
        }

        $messageConversationData = [
            'userID' => $data['userID'],
            'messageID' => $messageInfoID,
            'conversationType' => $data['conversationType'],
            'isViewed' => isset($data['isViewed']) ? $data['isViewed'] : 1,
            'companyCode' => $companyCode,
            'inUser' => $inUser,
            'inDate' => date("Y-m-d H:i:s"),
            'lastEditUser' => $lastEditUser,
            'lastEditDate' => date("Y-m-d H:i:s"),
            'huanxinMessageID' => $data['id']
        ];
        if ($data['conversationType']==2 && !empty($data['from'])){
            $agencyInfo = AgencyInfoLogic::getAgencyInfoOne('agencyID',['chatUserID'=>$data['from']]);
            if (empty($agencyInfo)){
                ImMessageLogic::deleteInfoRecord(['messageID' => $messageInfoID]);
                return ['agencyInfo 为空', 203];
            }
            $messageConversationData['serviceUserID'] = $agencyInfo['agencyID'];
            $messageConversationData['conversationScene'] = 0;
        }
        if ($data['conversationType']==1 && !empty($data['to'])){
            $agencyInfo = AgencyInfoLogic::getAgencyInfoOne('agencyID',['chatUserID'=>$data['to']]);
            if (empty($agencyInfo)){
                ImMessageLogic::deleteInfoRecord(['messageID' => $messageInfoID]);
                return ['agencyInfo 为空', 203];
            }
            $messageConversationData['serviceUserID'] = $agencyInfo['agencyID'];
            $messageConversationData['conversationScene'] = 0;
        }

        try{
            $messageConversationID = ImMessageLogic::insertConversationRecord($messageConversationData);
        } catch (\Exception $e){
            ImMessageLogic::deleteInfoRecord(['messageID' => $messageInfoID]);
            return ["messageconversation 插入失败", 204];
        }
        $result['results'] = [$messageInfoID,$messageConversationID];
        return ['成功', 200, $result];
    }

    /*
     * 获得用户聊天历史记录
     *  @param userID 用户id
     *  @param serviceUserID 魔售经纪人的ID
     */
    public function actionMessageinfolist(){
        $userID = $this->request->get('userID','');
        $serviceUserID = $this->request->get('serviceUserID','');
        $pageSize = $this->request->get('pageSize',6);
        $pageNo = $this->request->get('pageNo',1);
        $isViewed = $this->request->get('isViewed','');
        if (empty($userID) || empty($serviceUserID)){
            $results['results'] = '参数缺失';
            return ['参数缺失', 203, $results];
        }
        $data = [
            'userID' => $userID,
            'serviceUserID' => $serviceUserID,
            'pageSize' => $pageSize,
            'pageNo' => $pageNo
        ];
        $messageInfos = ImMessageLogic::getMessageInfo($data);
        $conversationIDs = [];
        if (isset($messageInfos['data']) && !empty($messageInfos['data'])){
            foreach ($messageInfos['data'] as $v){
                if ($v['isViewed'] == 0){
                    $conversationIDs[] = $v['conversationID'];
                }
            }
        }
        $results = [];
        if (!empty($conversationIDs)){
            ImMessageLogic::setMessageViewed($conversationIDs);
        }
        if (!empty($messageInfos)) {
            $results['results'] = $messageInfos;
        }
        return ['成功', 200, $results];
    }

    /*
     * 获得用户聊天的服务人员
     *  @param userID 用户id
     */
    public function actionUserbrokerlist(){
        $userID = $this->request->get('userID','');
        if (empty($userID)){
            $results['results'] = '参数缺失';
            return ['参数缺失', 203, $results];
        }
        $messageInfos = ImMessageLogic::getUserBrokerList($userID);
        $results = [];
        if (!empty($messageInfos)) {
            $results['results'] = $messageInfos;
        }
        return ['成功', 200, $results];
    }

    /*
     * 获得用户与聊天服务人员的未读消息数量
     *  @param userID 用户id
     */
    public function actionUsermessagenum(){
        $userID = $this->request->get('userID','');
        $isViewed = $this->request->get('isViewed','');
        if (empty($userID)){
            $results['results'] = '参数缺失';
            return ['参数缺失', 203, $results];
        }
        $params = [
            'userID' => $userID,
        ];
        if (!empty($isViewed) || $isViewed == '0'){
            $params['isViewed'] = $isViewed;
        }
        $messageInfos = ImMessageLogic::getUserMessageNum($params);
        $results = [];
        if (!empty($messageInfos)) {
            $results['results'] = $messageInfos;
        }
        return ['成功', 200, $results];
    }
    /*
         * 获得用户与所有聊天服务人员的未读消息数量总量
         *  @param userID 用户id
         */
    public function actionMessageunreadnum(){
        $userID = $this->request->get('userID','');
        if (empty($userID)){
            $results['results'] = '参数缺失';
            return ['参数缺失', 203, $results];
        }
        $params = [
            'userID' => $userID,
            'isViewed' => 0
        ];
        $messageInfos = ImMessageLogic::getUserMessageUnReadNum($params);
        $results = [];
        if (!empty($messageInfos)) {
            $results['results'] = $messageInfos;
        }
        return ['成功', 200, $results];
    }

}