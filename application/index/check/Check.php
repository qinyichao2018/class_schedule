<?php
/**
 * Created by PhpStorm.
 * User: bc
 * Date: 2018/3/16
 * Time: 13:29
 */

namespace app\index\check;


use think\Controller;

class Check extends Controller
{
    public static function check_updated()
    {

    }

    public static function comparing($x, $y)  //两个数值比较大小
    {
        if ($x > $y) {
            return $x;
        } else {
            return $y;
        }
    }

    public static function comparingAll($x)  //一组数据里返回最大值
    {
        $num = count($x);
        $num = $num - 1;
        $z = 0;
        for ($num;$num > 0;$num--){
            if ($x[$num] > $x[$num-1]) {
                $x[$num-1] = $x[$num];
            }
            $z =  $x[$num-1];
        }
        return $z;
    }

}