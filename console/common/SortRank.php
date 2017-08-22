<?php
/**
 * 根据楼盘区域等级、楼盘重要性等级、楼盘的精准开盘时间计算排序值
 * @Author: <lixiaobin>
 * @Date: 17-3-29
 */

namespace console\common;

class SortRank{

    /**
     * $areaWeight 区域权重
     * $estateWeight 楼盘权重
     * $openingDate 开盘日期
    */
    public static function getSortRank($areaWeight,$estateWeight,$openingDate){
        $areaRank = self::loupanAreaScore($areaWeight);
        $estateRank = self::loupanWeightScore($estateWeight);
        $openingDateRank = self::loupanOpeningDataScore($openingDate);
        return $areaRank * 0.2 + $estateRank * 0.4 + $openingDateRank * 0.4;
    }

    /**
     * 楼盘区域权重得分值计算
     * @Author: <lixiaobin>
    */
    private static function loupanAreaScore($areaWeight){
        $areaWeightMax = 2;
        if($areaWeight == 0) return 0;
        $areaWeight = $areaWeight > $areaWeightMax ? $areaWeightMax : $areaWeight;
        return (2- $areaWeight + 1) * 50;
    }

    /**
     * 楼盘重要性排序得分
    */
    private static function loupanWeightScore($estateWeight){
        $estateWeightMax = 5;
        if($estateWeight == 0) return 0;
        $estateWeight = $estateWeight > $estateWeightMax ? $estateWeightMax : $estateWeight;
        return (5- $estateWeight + 1) * 20;
    }


    /**
     * 楼盘准确开盘时间排序得分 (开盘时间与当前时间的距离分)
     *
    */
    private static function loupanOpeningDataScore($openingDate){
        $millisecondsInADay = 24 * 60 * 60 * 1000;
        $diff = ceil(abs(time() - strtotime($openingDate)) / $millisecondsInADay);
        if($diff <= 7){
           $openingDateRank = 100;
        }elseif($diff > 7 && $diff <= 15 ){
           $openingDateRank = 80;
        }elseif($diff > 15 && $diff <= 30){
           $openingDateRank = 60;
        }elseif($diff > 30 && $diff <= 60){
           $openingDateRank = 40;
        }else{
           $openingDateRank = 20;
        }
        return $openingDateRank;
    }

}