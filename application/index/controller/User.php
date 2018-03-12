<?php
/**
 * Created by PhpStorm.
 * User: bc
 * Date: 2018/3/12
 * Time: 19:05
 */

namespace app\index\controller;


use think\Controller;
use think\Db;

class User extends Controller
{
    public function login()
    {
        return $this->fetch('login');
    }

}