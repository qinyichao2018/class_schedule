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
use think\Request;
//use app\index\model\User as UserModel;

class User extends Controller
{
    public function login()
    {
//        dump($this->request->action());
//        dump($this->request->controller());
//        dump($this->request->module());
        return $this->fetch('login');
    }

    public function check_login(Request $request)
    {
//        初始返回参数
        $status = 0;       //默认返回失败
        $result = '';     //返回错误的信息
        $data = $request->param();
        $rult = [
            'school_name|学校名' => 'require',   //用'|'符号给school_name起名
            'class_name' => 'require',
            'student_number' => 'require',
            'student_name' => 'require',
            'password' => 'require',
            'verification_code' => 'require|captcha',
        ];
        //自定义验证失败时的提示信息
        $msg = [
            'school_name' => ['require' => '学校名不能为空，请检查！'],
            'class_name' => ['require' => '班级名不能为空，请检查！'],
            'student_number' => ['require' => '学号不能为空，请检查！'],
            'student_name' => ['require' => '姓名不能为空，请检查！'],
            'password' => ['require' => '密码不能为空，请检查！'],
            'verification_code' => [
                'require' => '验证码不能为空，请检查！',
                'captcha' => '验证码错误，请重输！',
            ]
        ];

        //进行验证
        $result = $this->validate($data, $rult, $msg);           //$result这里智慧返回两种值，1：true，2：字符串的错误信息
        //如果验证成功
        if ($result == true) {
            //构造查询条件
            $map = [
                'school_name' => $data['school_name'],
                'class_name' => $data['class_name'],
                'student_number' => $data['student_number'],
                'student_name' => $data['student_name'],
                'password' => $data['password'],
                //验证吗不在数据库查询
            ];
        }
        $user = Db::name('student')->where($map)->find();
        if ($user == null) {
            $result = '没有找到该用户信息';
        } else {
            $status = 1;
            $result = '验证通过，点击【确定】进入';
        }

        return ['status' => $status, 'message' => $result, 'data' => $data];    //数据返回客户端
    }
}