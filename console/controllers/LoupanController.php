<?php
/**
 * Created by PhpStorm.
 * User: leexb
 * Date: 17-3-29
 * Time: 下午2:56
 */
namespace console\controllers;

use console\common\SortRank;
use Yii;
use console\common\House;
use yii\console\Controller;

class LoupanController extends Controller{

    protected $db;
    protected $tagColorArr;

    public function init() {
        
        $this->db = Yii::$app->db;
        parent::init();
        
    }
    
    public function actionIndex(){
        $zstime = microtime(true);
        $countSql = " SELECT residentialBuildingID FROM Estate_ResidentialBuilding r,Estate_BasicInfo b WHERE r.estateID = b.estateID AND b.status = 2";
        $data = $this->db->createCommand($countSql)->queryAll();
        $dataArr = $this->_partition($data,ceil(count($data)/500));

        foreach ($dataArr as $val){
            $residentialBuildingIds = [];
            foreach ($val as $v){
                $residentialBuildingIds[]= $v['residentialBuildingID'];
            }
            $house = House::getInstance();
            $house->addRecordLoupan($residentialBuildingIds);
        }
        $zetime=microtime(true);//获取程序执行结束的时间
        $ztotal=$zetime-$zstime;   //计算差值
        echo "\n :[总运行时长：{$ztotal} ]秒 \n";
    }

    //将一位数分成一个二位数组
    private function _partition($arr,$num){
        //数组的个数
        $listcount=count($arr);
        //分成$num 个数组每个数组是多少个元素
        $parem=floor($listcount/$num);
        //分成$num 个数组还余多少个元素
        $paremm=$listcount%$num;
        $start=0;
        for($i=0;$i<$num;$i++){
            $end=$i<$paremm?$parem+1:$parem;
            $newarray[$i]=!empty(array_slice($arr,$start,$end)) ? array_slice($arr,$start,$end) : '';
            if(empty($newarray[$i])) unset($newarray[$i]);
            $start=$start+$end;
        }
        return $newarray;
    }
    
}