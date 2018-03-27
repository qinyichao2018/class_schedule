<?php
/**
 * Created by PhpStorm.
 * User: bc
 * Date: 2018/3/16
 * Time: 12:26
 */

namespace app\index\convert;


use think\Controller;

class Convert extends Controller
{

    public static function time_ago($updated_at)
    {
        $updated_at = strtotime($updated_at);
        $time_2 = time() - $updated_at;
        if ($time_2 < 60) {
            return $time_2 . '秒前';
        } elseif ($time_2 < (60 * 60)) {
            return floor($time_2 / 60) . '分钟前';
        } elseif ($time_2 < (24 * 60 * 60)) {
            return floor($time_2 / 3600) . '小时前';
        } else {
            return floor($time_2 / 86400) . '天前';
        }
    }

    public static function array2_convert_array1($array2,$key)
    {
        return $array2[$key];
    }

}