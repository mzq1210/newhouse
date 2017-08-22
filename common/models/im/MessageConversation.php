<?php

/**
 * Created by PhpStorm.
 * User: luohua
 * Date: 17-4-11
 * Time: 下午8:05
 */

namespace common\models\im;

use Yii;
use yii\db\ActiveRecord;

class MessageConversation extends ActiveRecord {

    public static function tableName() {
        return 'IM_MessageConversation';
    }

    public static function tableDesc() {
        return '客户会话记录';
    }

    public static function insertRecord($data = []) {
        if (empty($data))
            return false;
        $model = new self();
        $model->setAttributes($data, false);
        if ($model->save())
            return $model->primaryKey;
        return false;
    }

    public static function selectRecord($fields = '*', $condition = []) {
        self::find()->select($fields)->where($condition)->asArray()->all();
    }

    public static function getMessageInfo($condition = []) {
        return self::find()->select("inDate,conversationType,messageID")->with("messageinfos")->where($condition)->asArray()->all();
    }

    public static function getMessageInfoByPage($condition = []) {
        $offset = $condition['pageNo'] > 0 ? ($condition['pageNo']) - 1 : 0;
        $pageSize = $condition['pageSize'];
        unset($condition['pageNo']);
        unset($condition['pageSize']);
        $field = "conversationID,inDate,conversationType,messageID,huanxinMessageID,isViewed";
        $query = self::find()->select($field)->with("messageinfos")->where($condition);
        //查询总数
        $countQuery = clone $query;
        $count = $countQuery->count();
        $pageCount = ceil($count / $pageSize);
        $query->orderBy(['inDate' => SORT_DESC]);
        $list = $query->offset($offset * $pageSize)->limit($pageSize)->asArray()->all();
        return ['totalCount' => intval($count), 'pageCount' => $pageCount, 'pageSize' => $pageSize, 'pageNo' => $offset + 1, 'data' => $list];
    }

    public function getMessageinfos() {
        return $this->hasOne(MessageInfo::className(), ['messageID' => 'messageID'])->select("messageTextContent,messageID,messageType,messageExtendContent");
    }

    public static function getUserBrokerList($condition) {
        return self::find()->select("serviceUserID,userID")->where($condition)->distinct()->asArray()->all();
    }

    public static function setMessageViewed($condition) {
        return self::updateAll(['isViewed' => 1], $condition);
    }

    public static function getUserMessageNum($condition) {
        return self::find()->select("count(1) as num,isViewed,serviceUserID")->where($condition)->groupBy('isViewed,serviceUserID')->asArray()->all();
    }

    public static function getUserMessageUnReadNum($condition) {
        return self::find()->select("count(1) as num")->where($condition)->asArray()->one();
    }

    public static function getUserMessageID($condition) {
        return self::find()->select("messageID,serviceUserID,userID,inDate")->with("messageinfos")->where($condition)->orderBy("conversationID DESC")->asArray()->one();
    }

}
