<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-5-17
 * Time: 下午2:24
 */

namespace common\logic;


use common\models\user\WishList;

class FollowLogic
{
    /**
     * 获取用户是否关注楼盘
     * <liangshimao>
     */
    public static function getStatus($userID,$estateID,$propertyTypeID)
    {
        return WishList::getWishStatus($userID, $estateID, $propertyTypeID);
    }

    /**
     * 添加楼盘关注
     *<liangshimao>
     */
    public static function addWish($userID,$estateID,$propertyTypeID)
    {
        return WishList::addWish($userID, $estateID, $propertyTypeID);
    }

    /**
     * 取消关注
     * <liangshimao>
     */
    public static function cancelWish($userID,$estateID,$propertyTypeID)
    {
        return WishList::editRecord($userID, $estateID, $propertyTypeID,['isActive'=>0]);
    }

}