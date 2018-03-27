<?php
/**
 * Created by PhpStorm.
 * User: bc
 * Date: 2018/3/16
 * Time: 16:46
 */

namespace app\index\often;


use think\Controller;
use think\Db;

class Often extends Controller
{
    public static function mapWeek()  //制作地图周一到周六
    {
        $map2 = [
            'monday', 'tuesday', 'wednesday',
            'thursday', 'friday', 'saturday',
            'sunday'
        ];
        return $map2;
    }

    public static function mapSection()
    {
        $map = [
            'section_1', 'section_2', 'section_3', 'section_4', 'section_5',
            'section_6', 'section_7', 'section_8', 'section_9', 'section_10',
        ];
        return $map;
    }

    public static function getUpdatedArray($get_schedule)  //获取更新和创建时间地图
    {
        $map = [
            $get_schedule[0]['updated_at'], $get_schedule[0]['created_at'],
            $get_schedule[1]['updated_at'], $get_schedule[1]['created_at'],
            $get_schedule[2]['updated_at'], $get_schedule[2]['created_at'],
            $get_schedule[3]['updated_at'], $get_schedule[3]['created_at'],
            $get_schedule[4]['updated_at'], $get_schedule[4]['created_at'],
            $get_schedule[5]['updated_at'], $get_schedule[5]['created_at'],
            $get_schedule[6]['updated_at'], $get_schedule[6]['created_at'],
        ];
        return $map;
    }

    public static function insert_week($map_schedule_info, $map)
    {
        $i = 0;
        $map_count = count($map);
        for ($i; $i < $map_count; $i++) {
            Db::name($map[$i])->insert($map_schedule_info);
        }
    }

    public static function edit_save($weekMapOne, $sectionMapOne, $schedule, $data)
        //更新保存；$weekMapOne：monday到sunday随意数量；$sectionMapOne：section1到section10随意数量；$scheduleId为用户信息里的weekId
    {
        $map = [
            'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',
        ];
//        $weekMapOne = [
//            'monday', 'tuesday', 'wednesday',
//            'thursday', 'friday', 'saturday',
//            'sunday'
//        ];
//        $sectionMapOne = [
//            'section_0','section_1', 'section_2', 'section_3', 'section_4', 'section_5',
//            'section_6', 'section_7', 'section_8', 'section_9', 'section_10',
//        ];
        $weekTableNumber = count($weekMapOne);
        $sectionNumber = count($sectionMapOne);
        $week = 0;
        $i = 0;
        $ary = array();
        for ($week; $week < $weekTableNumber; $week++) {      //第一次循环monday时的section1到section10，一次类推
            $weekData = Db::name($weekMapOne[$week])->where($weekMapOne[$week] . '_id', $schedule[$weekMapOne[$week] . '_id'])->find();  //查找需要对比的表
            $section = 1;
            for ($section; $section < $sectionNumber; $section++) {
                if ($data[$map[$week] . $section] != $weekData['section_' . $section]) {
                    Db::name($weekMapOne[$week])->where($weekMapOne[$week] . '_id', $schedule[$weekMapOne[$week] . '_id'])->update([$sectionMapOne[$section] => $data[$map[$week] . $section], 'updated_at' => date("Y-m-d G:i:s ")]);
                    array_push($ary, $weekMapOne[$week], $sectionMapOne[$section]);
                    $i++;
                }
            }

        }
        return $ary;
    }

    public static function englishCovertChinese($english)
    {
        switch ($english) {
            case 'monday':
                $english = '星期一';
                break;
            case 'tuesday':
                $english = '星期二';
                break;
            case 'wednesday':
                $english = '星期三';
                break;
            case 'thursday':
                $english = '星期四';
                break;
            case 'friday':
                $english = '星期五';
                break;
            case 'saturday':
                $english = '星期六';
                break;
            case 'sunday':
                $english = '星期日';
                break;
            case 'section_1':
                $english = '第一节';
                break;
            case 'section_2':
                $english = '第二节';
                break;
            case 'section_3':
                $english = '第三节';
                break;
            case 'section_4':
                $english = '第四节';
                break;
            case 'section_5':
                $english = '第五节';
                break;
            case 'section_6':
                $english = '第六节';
                break;
            case 'section_7':
                $english = '第七节';
                break;
            case 'section_8':
                $english = '第八节';
                break;
            case 'section_9':
                $english = '第九节';
                break;
            case 'section_10':
                $english = '第十节';
                break;
        }
        return $english;
    }
}