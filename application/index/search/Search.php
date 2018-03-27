<?php
/**
 * Created by PhpStorm.
 * User: bc
 * Date: 2018/3/16
 * Time: 12:57
 */

namespace app\index\search;


use think\Controller;
use think\Db;

class Search extends Controller
{
    public static function search_schedule($school_name, $class_name)  //获取课表信息schedule_info
    {
        $map = [
            'school_name' => $school_name,
            'class_name' => $class_name,
        ];
        $schedule_info = Db::name('schedule_info')->where($map)->find();
        return $schedule_info;
    }

    public static function get_schedule($week, $schedule)  //获取任意一周的课程信息
    {
        $mon = Db::name($week)->where($week . '_id', $schedule[$week . '_id'])->find();
        return $mon;
    }

    public static function get_scheduleAll($map, $schedule)//$map是要查询的week地图,$schedule是查询到的schedule_info表的信息,返回一个2维数组，用法$x[0]['monday_id']
    {
        $map_count = count($map);
        $week = array('');
        $a = 0;
        for ($a; $map_count > $a; $a++) {
            //$map[$a]就是monday表，monday_id,$schedule['monday_id']
            $week[$a] = Db::name($map[$a])->where($map[$a] . '_id', $schedule[$map[$a] . '_id'])->find();
        }
        return $week;
    }
}