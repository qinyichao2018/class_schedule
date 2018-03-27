<?php
/**
 * Created by PhpStorm.
 * User: bc
 * Date: 2018/3/12
 * Time: 19:05
 */

namespace app\index\controller;


use app\index\search\Search;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use app\index\convert\Convert;
use app\index\often\Often;

class User extends Controller
{
    public function student_login()
    {
//        dump($this->request->action());
//        dump($this->request->controller());
//        dump($this->request->module());
        return $this->fetch('student_login');
    }

    public function teacher_login()
    {
        return $this->fetch('teacher_login');
    }

    public function check_student_register(Request $request)
    {
//        初始返回参数
        $status = 0;       //默认返回失败
        $result = '';     //返回错误的信息
        $data = $request->param();
        $rult = [
            'school_name|学校名' => 'require',   //用'|'符号给school_name起名
            'class_name' => 'require',
            'student_number' => 'require|number',
            'student_name' => 'require',
            'password' => 'require',
            'repassword' => 'require',
//            'verification_code' => 'require|captcha',
        ];
        //自定义验证失败时的提示信息
        $msg = [
            'school_name' => ['require' => '学校名不能为空，请检查！'],
            'class_name' => ['require' => '班级名不能为空，请检查！'],
            'student_number' => [
                'require' => '学号不能为空，请检查！',
                'number' => '学号请输入数字'
            ],
            'student_name' => ['require' => '姓名不能为空，请检查！'],
            'password' => ['require' => '密码不能为空，请检查！'],
            'repassword' => ['require' => '确认密码不能为空，请检查！'],
//            'verification_code' => [
//                'require' => '验证码不能为空，请检查！',
//                'captcha' => '验证码错误，请重输！',
//            ]
        ];

        //进行验证
        $result = $this->validate($data, $rult, $msg);           //$result这里返回两种值，1：字符串'true'，2：自定义字符串的错误信息
        //如果验证成功
        if ($result == 'true') {
            //这里没有''的时候会出现只要$result的值为true就满足if语句的条件,比如字符串的错误信息,所以返回的错误信息不能为字符串'true'
            //构造查询条件
            $map = [
                'school_name' => $data['school_name'],
                'class_name' => $data['class_name'],
                'student_number' => $data['student_number'],
                //验证吗不在数据库查询
            ];
            $user = Db::name('student')->where($map)->find();
            $sc = $user['school_name'];
            $cl = $user['class_name'];
            $st = $user['student_number'];
            if ($user != null) {
                $result = "该用户信息已被注册，学校:$sc 班级:$cl 学号:$st 的学生注册信息已存在";
            } else {
                $stu_info = array(
                    'student_name' => $data['student_name'],
                    'student_number' => $data['student_number'],
                    'school_name' => $data['school_name'],
                    'class_name' => $data['class_name'],
                    'password' => $data['password'],
                    'repassword' => $data['repassword'],
                    'created_at' => date("Y-m-d G:i:s "),
                );
                if ($data['password'] == $data['repassword']) {
                    Db::name('student')->insert($stu_info);
                    $student = Db::name('student')->where($stu_info)->find();
                    $status = 1;

                    session('user_info', $student);
                    $result = '注册成功，点击【确定】进入';
                } else {
                    $result = '两次密码输入不一致，请重新输入';
                }

            }
        }

        return ['status' => $status, 'message' => $result, 'data' => $data];    //数据返回客户端
    }

    public function check_teacher_register(Request $request)
    {
//        初始返回参数
        $status = 0;       //默认返回失败
        $result = '';     //返回错误的信息
        $data = $request->param();
        if (!array_key_exists('checkbox', $data)) {
            $data['head_teacher'] = '否';
        }
        $rult = [
            'school_name|学校名' => 'require',   //用'|'符号给school_name起名
            'Subject' => 'require',
            'teacher_number' => 'require|number',
            'teacher_name' => 'require',
            'head_teacher' => 'require',
            'password' => 'require',
            'repassword' => 'require',
//            'verification_code' => 'require|captcha',
        ];
        //自定义验证失败时的提示信息
        $msg = [
            'school_name' => ['require' => '学校名不能为空，请检查！'],
            'Subject' => ['require' => '学科不能为空，请检查！'],
            'teacher_number' => [
                'require' => '教师证号不能为空，请检查！',
                'number' => '教师号请填写纯数字'
            ],
            'teacher_name' => ['require' => '姓名不能为空，请检查！'],
            'head_teacher' => ['require' => '管理班级名不能为空，请检查！'],
            'password' => ['require' => '密码不能为空，请检查！'],
            'repassword' => ['require' => '确认密码不能为空，请检查！'],
//            'verification_code' => [
//                'require' => '验证码不能为空，请检查！',
//                'captcha' => '验证码错误，请重输！',
//            ]
        ];
        //进行验证
        $result = $this->validate($data, $rult, $msg);           //$result这里返回两种值，1：字符串'true'，2：自定义字符串的错误信息
        //如果验证成功
        if ($result == 'true') {
            //这里没有''的时候会出现只要$result的值为true就满足if语句的条件,比如字符串的错误信息,所以返回的错误信息不能为字符串'true'
            //构造查询条件
            $map = [
                'school_name' => $data['school_name'],
                'Subject' => $data['Subject'],
                'teacher_name' => $data['teacher_name'],
                'teacher_number' => $data['teacher_number'],
                //验证吗不在数据库查询
            ];
            $user = Db::name('teacher')->where($map)->find();
            $sc = $user['school_name'];
            $cl = $user['Subject'];
            $st = $user['teacher_number'];
            if ($user != null) {
                $result = "该用户信息已被注册，学校:$sc 学科:$cl 教师证号:$st 的教师注册信息已存在";
            } else {
                $teacher_info = array(
                    'teacher_number' => $data['teacher_number'],
                    'school_name' => $data['school_name'],
                    'Subject' => $data['Subject'],
                    'head_teacher' => $data['head_teacher'],
                    'teacher_name' => $data['teacher_name'],
                    'password' => $data['password'],
                    'repassword' => $data['repassword'],
                    'created_at' => date("Y-m-d G:i:s "),
                );
                if ($data['password'] == $data['repassword']) {
                    Db::name('teacher')->insert($teacher_info);
                    $status = 1;
                    session('user_info', $data);
                    $result = '注册成功，点击【确定】进入';
                } else {
                    $result = '两次密码输入不一致，请重新输入';
                }
            }
        }
        return ['status' => $status, 'message' => $result, 'data' => $data];    //数据返回客户端
    }

    public function check_student_login(Request $request)
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
//            'verification_code' => 'require|captcha',
        ];
        //自定义验证失败时的提示信息
        $msg = [
            'school_name' => ['require' => '学校名不能为空，请检查！'],
            'class_name' => ['require' => '班级名不能为空，请检查！'],
            'student_number' => ['require' => '学号不能为空，请检查！'],
            'student_name' => ['require' => '姓名不能为空，请检查！'],
            'password' => ['require' => '密码不能为空，请检查！'],
//            'verification_code' => [
//                'require' => '验证码不能为空，请检查！',
//                'captcha' => '验证码错误，请重输！',
//            ]
        ];

        //进行验证
        $result = $this->validate($data, $rult, $msg);           //$result这里返回两种值，1：字符串'true'，2：自定义字符串的错误信息
        //如果验证成功
        if ($result == 'true') {
            //这里没有''的时候会出现只要$result的值为true就满足if语句的条件,比如字符串的错误信息,所以返回的错误信息不能为字符串'true'
            //构造查询条件
            $map = [
                'school_name' => $data['school_name'],
                'class_name' => $data['class_name'],
                'student_number' => $data['student_number'],
                'student_name' => $data['student_name'],
                //验证吗不在数据库查询
            ];
            $user = Db::name('student')->where($map)->find();
            if ($user == null) {
                $result = '没有找到该用户,请检查登录信息';
            } elseif ($user['password'] == $data['password']) {
                $status = 1;
                session('user_info', $user);
                $result = '登录成功，点击【确定】进入';
            } else {
                $result = '密码错误';
            }
        }

        return ['status' => $status, 'message' => $result, 'data' => $data];    //数据返回客户端
    }

    public function check_teacher_login(Request $request)
    {
//        初始返回参数
        $status = 0;       //默认返回失败
        $result = '';     //返回错误的信息
        $data = $request->param();
        if (!array_key_exists('checkbox', $data)) {
            $data['head_teacher'] = '否';
        }
        $rult = [
            'school_name|学校名' => 'require',   //用'|'符号给school_name起名
            'Subject' => 'require',
            'teacher_number' => 'require',
            'teacher_name' => 'require',
            'head_teacher' => 'require',
            'password' => 'require',
//            'verification_code' => 'require|captcha',
        ];
        //自定义验证失败时的提示信息
        $msg = [
            'school_name' => ['require' => '学校名不能为空，请检查！'],
            'Subject' => ['require' => '学科不能为空，请检查！'],
            'teacher_number' => ['require' => '教师证号不能为空，请检查！'],
            'teacher_name' => ['require' => '姓名不能为空，请检查！'],
            'head_teacher' => ['require' => '管理班级名不能为空，请检查！'],
            'password' => ['require' => '密码不能为空，请检查！'],
//            'verification_code' => [
//                'require' => '验证码不能为空，请检查！',
//                'captcha' => '验证码错误，请重输！',
//            ]
        ];
        //进行验证
        $result = $this->validate($data, $rult, $msg);           //$result这里返回两种值，1：字符串'true'，2：自定义字符串的错误信息
        //如果验证成功
        if ($result == 'true') {
            //这里没有''的时候会出现只要$result的值为true就满足if语句的条件,比如字符串的错误信息,所以返回的错误信息不能为字符串'true'
            //构造查询条件
            $map = [
                'school_name' => $data['school_name'],
                'Subject' => $data['Subject'],
                'teacher_name' => $data['teacher_name'],
                'teacher_number' => $data['teacher_number'],
                //验证吗不在数据库查询
            ];
            $user = Db::name('teacher')->where($map)->find();
            if ($user == null) {
                $result = '没有找到该用户,请检查登录信息';
            } elseif ($user['password'] == $data['password']) {
                if ($user['head_teacher'] == "否") {
                    //用户不是班主任时
                    if ($user['head_teacher'] == $data['head_teacher']) {
//                        符合数据库用户信息
                        $status = 1;
                        session('user_info', $user);
                        $sub = $user['Subject'];
                        $result = "登录成功，您是<$sub>老师，点击【确定】进入";
                    } else {
                        //不符合数据库用户数据
                        $result = '您不是班主任，请勿勾选班主任项';
                    }
                } else {
                    //用户是班主任时
                    if ($user['head_teacher'] == $data['head_teacher']) {
                        $status = 1;
                        session('user_info', $user);
                        $sub = $user['Subject'];
                        $ht = $user['head_teacher'];
                        $result = "登录成功，您是<$sub>老师和<$ht>班的班主任，点击【确定】进入";
                    } else {
                        if (!array_key_exists('checkbox', $data)) {
                            $result = '您是班主任，请勾选班主任项以管理班级';
                        } elseif ($user['head_teacher'] != $data['head_teacher']) {
                            $result = '班主任项管理班级名有误请检查';
                        }
                    }
                }

            } else {
                $result = '密码错误';
            }
        }

        return ['status' => $status, 'message' => $result, 'data' => $data];    //数据返回客户端
    }

    public function student_index()
    {
        $post_data = session('user_info');
        $schedule_check = 'yes';
        $map2 = Often::mapWeek();
        //分割线
//        var_dump($post_data);exit();
        if ($post_data['default_schedule'] == null) {
            $map_schedule = [
                'class_name' => $post_data['class_name'],
                'school_name' => $post_data['school_name'],
                'founder' => $post_data['student_name'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            Often::insert_week($map_schedule, $map2);
            $monday = Db::name('monday')->where($map_schedule)->find();
            $tuesday = Db::name('tuesday')->where($map_schedule)->find();
            $wednesday = Db::name('wednesday')->where($map_schedule)->find();
            $thursday = Db::name('thursday')->where($map_schedule)->find();
            $friday = Db::name('friday')->where($map_schedule)->find();
            $saturday = Db::name('saturday')->where($map_schedule)->find();
            $sunday = Db::name('sunday')->where($map_schedule)->find();
            $map_schedule2 = [
                'class_name' => $post_data['class_name'],
                'school_name' => $post_data['school_name'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'monday_id' => $monday['monday_id'],
                'tuesday_id' => $tuesday['tuesday_id'],
                'wednesday_id' => $wednesday['wednesday_id'],
                'thursday_id' => $thursday['thursday_id'],
                'friday_id' => $friday['friday_id'],
                'saturday_id' => $saturday['saturday_id'],
                'sunday_id' => $sunday['sunday_id'],
                'if_public' => 'yes',
                'founder' => $post_data['student_name'],
                'schedule_name' => '初始课表',
            ];
            Db::name('schedule_info')->insert($map_schedule2);
            $new_schedule = Db::name('schedule_info')->where($map_schedule2)->find();
            Db::name('student')->where('student_id', $post_data['student_id'])->update(['default_schedule' => $new_schedule['schedule_id']]);
            $user_info1 = Db::name('student')->where('student_id', $post_data['student_id'])->find();
            session('user_info', $user_info1);
        } else {
            $new_schedule = Db::name('schedule_info')->where('schedule_id', $post_data['default_schedule'])->find();
            $user_info1 = Db::name('student')->where('student_id', $post_data['student_id'])->find();
            session('user_info', $user_info1);
        }
        //分割线

        $get_schedule = Search::get_scheduleAll($map2, $new_schedule);
        $ago = Convert::time_ago($new_schedule['updated_at']);
        $map_total = [
            'school_name' => $post_data['student_name'],
            'class_name' => $post_data['class_name'],
            'founder' => $post_data['student_name'],
        ];
        $schedule_total = Db::name('schedule_info')->where($map_total)->select();

        if (count($schedule_total) > 9) {
            $schedule_check = 'no';
        }

        $this->assign('mon', $get_schedule[0]);
        $this->assign('tue', $get_schedule[1]);
        $this->assign('wed', $get_schedule[2]);
        $this->assign('thu', $get_schedule[3]);
        $this->assign('fri', $get_schedule[4]);
        $this->assign('sat', $get_schedule[5]);
        $this->assign('sun', $get_schedule[6]);
        $this->assign('schedule_id', $new_schedule['schedule_id']);
        $this->assign('sch_info', $new_schedule);
        $this->assign('updated', $ago);
        $this->assign('schedule_check', $schedule_check);
        if ($post_data == null) {
            $post_data = array('');
        }
        if (!array_key_exists('student_name', $post_data)) {
            $this->error('未学生登录,正在返回主页面', 'index/index');
        } else {
            return $this->fetch('user/student_index');
        }
    }

    public function teacher_index()
    {
        $post_data = session('user_info');
        $map = [
            'school_name' => $post_data['school_name'],
            'teacher_number' => $post_data['teacher_number'],
        ];
        $schedule_info = Db::name('worktable')->where($map)->find();
        $map2 = Often::mapWeek();
        $get_schedule = Search::get_scheduleAll($map2, $schedule_info);

        if ($post_data == null) {
            $post_data = array('');
        }
        //老师有工作表，实现教师个人主页为自定义工作表，每个课程可以从已有课程表
        //导入，智能导入，选择班级，选择课程，则自动导入相应课程到对应位置或者
//        输入教师名，学校名，教师编号，自动寻找课表有填写自己名字的课程导入
//        Search::get_scheduleAll($schedule);

        $this->assign('mon', $get_schedule[0]);
        $this->assign('tue', $get_schedule[1]);
        $this->assign('wed', $get_schedule[2]);
        $this->assign('thu', $get_schedule[3]);
        $this->assign('fri', $get_schedule[4]);
        $this->assign('sat', $get_schedule[5]);
        $this->assign('sun', $get_schedule[6]);
        $this->assign('schedule_id', $schedule_info['schedule_id']);

        if (!array_key_exists('teacher_name', $post_data)) {
            $this->error('未登录教师，正在返回主页面', 'index/index');
        } else {
            return $this->fetch('user/teacher_index');
        }
    }

    public function student_register()
    {
        return $this->fetch('user/student_register');
    }

    public function teacher_register()
    {
        return $this->fetch('user/teacher_register');
    }

    public function login_out()
    {
        //删除用户信息
        session::delete('user_info');   //删除当前用户的信息，destory删除所有用户的用户信息
        $this->success('注销成功，正在返回主页', 'index/index');
    }

    public function edit_sche()
    {
        $post_data = session('user_info');
        $if_default = input('if_default');
        $post_id = input('id');
        if ($if_default == 'yes') {
            $schedule_info = Db::name('schedule_info')->where('schedule_id', $post_data['default_schedule'])->find();
        } elseif ($if_default == 'no') {
            $schedule_info = Db::name('schedule_info')->where('schedule_id', $post_id)->find();
        }
        $map2 = Often::mapWeek();

        $get_schedule = Search::get_scheduleAll($map2, $schedule_info);
        $this->assign('mon', $get_schedule[0]);
        $this->assign('tue', $get_schedule[1]);
        $this->assign('wed', $get_schedule[2]);
        $this->assign('thu', $get_schedule[3]);
        $this->assign('fri', $get_schedule[4]);
        $this->assign('sat', $get_schedule[5]);
        $this->assign('sun', $get_schedule[6]);
        $this->assign('sun', $get_schedule[6]);
        $this->assign('schedule_id', $schedule_info['schedule_id']);
        $this->assign('schedule_name', $schedule_info['schedule_name']);
        return $this->fetch('edit_sche');
    }

    public function edit_save(Request $request)
    {
        $data = $request->param();
        $schedule = Db::name('schedule_info')->where('schedule_id', $data['schedule_id'])->find();
        $weekMapOne = [
            'monday', 'tuesday', 'wednesday',
            'thursday', 'friday', 'saturday',
            'sunday',
        ];
        $sectionMapOne = [
            'section_0', 'section_1', 'section_2', 'section_3', 'section_4', 'section_5',
            'section_6', 'section_7', 'section_8', 'section_9', 'section_10',
        ];
        $i = Often::edit_save($weekMapOne, $sectionMapOne, $schedule, $data);
        if ($data['schedule_name'] == $schedule['schedule_name']) {
            $flag2 = 1;
        } else {
            $flag2 = 0;
        }
        $cou = count($i);
        $cou1 = $cou / 2;
        $message = "共修改 $cou1 项，修改的数据项为：";
        $num = 0;
        if ($cou == 0) {
            if ($flag2 == 1) {
                $message = '没有修改任何数据';
            } elseif ($flag2 == 0) {
                Db::name('schedule_info')->where('schedule_id', $data['schedule_id'])->update(['updated_at' => date("Y-m-d G:i:s"), 'schedule_name' => $data['schedule_name']]);

                $message = '仅修改表名:' . $schedule['schedule_name'] . '--->' . $data['schedule_name'];
            }

        } else {
            for ($num, $num1 = 1; $num < $cou; $num++, $num1++) {
                $weekNumber = Often::englishCovertChinese($i[$num]);
                $sectionNumber = Often::englishCovertChinese($i[++$num]);
                Db::name('schedule_info')->where('schedule_id', $data['schedule_id'])->update(['updated_at' => date("Y-m-d G:i:s"), 'schedule_name' => $data['schedule_name']]);
                $message = '表名:' . $schedule['schedule_name'] . '--->' . $data['schedule_name'] . "\n" . $message;
                $message = $message . "\n" . $num1 . ':' . $weekNumber . '  ' . $sectionNumber . "\n";
            }
        }

        return ['data' => $data, 'message' => $message];
    }

    public function student_personal_info()
    {

        $post_data = session('user_info');
        $map = [
            'school_name' => $post_data['school_name'],
            'class_name' => $post_data['class_name'],
            'student_name' => $post_data['student_name'],
            'student_number' => $post_data['student_number']
        ];
        $post_data = Db::name('student')->where($map)->find();
        $this->assign('post_data', $post_data);
        return $this->fetch('student_personal_info');
    }

    public function teacher_personal_info()
    {

        $post_data = session('user_info');
        $map = [
            'school_name' => $post_data['school_name'],
            'head_teacher' => $post_data['head_teacher'],
            'teacher_name' => $post_data['teacher_name'],
            'teacher_number' => $post_data['teacher_number']
        ];
        $post_data = Db::name('teacher')->where($map)->find();
        $this->assign('post_data', $post_data);
        return $this->fetch('teacher_personal_info');
    }

    public function management_work()
    {
        return $this->fetch('management_work');
    }

    public function management_class()
    {
        return $this->fetch('management_class');
    }


    public function test()
    {
        $student = Db::name('student')->where('student_name', '王杰')->find();
        $char = $student['schedule_list'];
        $char = explode('_', $char);
        var_dump($student['schedule_list']);
        var_dump('--------------------->');
        var_dump($char);
        exit();
        return $this->fetch('test');
    }

    public function test_check(Request $request)
    {
        $status = 1;       //默认返回失败
        $result = '';     //返回错误的信息
        $data = $request->param();
        $result = $data['text1'];
        Db::name('notes')->where('student_name', '王杰')->update(['note_text' => $result]);
        return ['status' => $status, 'message' => $result, 'data' => $data];

    }

    public function new_edit_save(Request $request)
    {
        $data = $request->param();
        $u = session('user_info');
        $status = 0;
        $message = '';
        $name_info = '';
        $class_teacher = '';
        if (array_key_exists('student_name', $u)) {
            $class_teacher = 'class_name';
        } elseif (array_key_exists('teacher_name', $u)) {
            $class_teacher = 'teacher_number';
        } else {
            $this->error('未登录，现在是游客身份！', 'index/index');
        }

        if ($data['schedule_name'] == null) {
            $status = 0;
            $message = '课表名不能为空';
        } else {
            $status = 1;

            $map1 = [
                'schedule_name' => null,
                'founder' => $u['student_name'],
                'school_name' => $u['school_name'],
                'class_name' => $u['class_name'],
            ];
            $schedule = Db::name('schedule_info')->where($map1)->find();

            $weekMapOne = [
                'monday', 'tuesday', 'wednesday',
                'thursday', 'friday', 'saturday',
                'sunday',
            ];
            $sectionMapOne = [
                'section_0', 'section_1', 'section_2', 'section_3', 'section_4', 'section_5',
                'section_6', 'section_7', 'section_8', 'section_9', 'section_10',
            ];
            $i = Often::edit_save($weekMapOne, $sectionMapOne, $schedule, $data);
            Db::name('schedule_info')->where($map1)->update(['updated_at' => date("Y-m-d H:i:s"), 'if_public' => 'yes', 'schedule_name' => $data['schedule_name']]);
            $cou = count($i);
            $cou1 = $cou / 2;
            $message = "创建课程表：" . $data['schedule_name'] . "；\n共添加 $cou1 个课程，添加的课程时间为：";
            $num = 0;
            if ($cou == 0) {
                $message = "创建空课程表：" . $data['schedule_name'];
            } else {
                for ($num, $num1 = 1; $num < $cou; $num++, $num1++) {
//

                    $weekNumber = Often::englishCovertChinese($i[$num]);
                    $sectionNumber = Often::englishCovertChinese($i[++$num]);
                    $mapUp = ['school_name' => $u['school_name'], 'class_name' => $u['class_name']];
                    Db::name('schedule_info')->where($mapUp)->update(['updated_at' => date("Y-m-d H:i:s")]);
                    $message = $message . "\n" . $num1 . ':' . $weekNumber . '  ' . $sectionNumber . "\n";
                }
            }
//
//            $message = 'kdkds';
        }
        return ['data' => $data, 'message' => $message, 'status' => $status];
    }

    public function new_schedule()
    {
        $post_data = session('user_info');

        $name_info = '';
        $class_teacher = '';
        $flag1 = 0;
        if (array_key_exists('student_name', $post_data)) {
            $name_info = 'student_name';
            $class_teacher = 'class_name';
        } elseif (array_key_exists('teacher_name', $post_data)) {
            $name_info = 'teacher_name';
            $class_teacher = 'teacher_number';
        } else {
            $this->error('未登录，现在是游客身份！', 'index/index');
        }
        $map_1 = [
            'founder' => $post_data[$name_info],
            $class_teacher => $post_data[$class_teacher],
            'school_name' => $post_data['school_name'],
        ];
        $allSchedule = Db::name('schedule_info')->where($map_1)->select();
        foreach ($allSchedule as $k => $v) {
            if ($v['schedule_name'] == null) {
                $flag1 = 1;
            }
        }
        if ($flag1 == 0) {
            $map2 = Often::mapWeek();
            $map_schedule = [
                'founder' => $post_data[$name_info],
                $class_teacher => $post_data[$class_teacher],
                'school_name' => $post_data['school_name'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            Often::insert_week($map_schedule, $map2);
            $monday = Db::name('monday')->where($map_schedule)->find();
            $tuesday = Db::name('tuesday')->where($map_schedule)->find();
            $wednesday = Db::name('wednesday')->where($map_schedule)->find();
            $thursday = Db::name('thursday')->where($map_schedule)->find();
            $friday = Db::name('friday')->where($map_schedule)->find();
            $saturday = Db::name('saturday')->where($map_schedule)->find();
            $sunday = Db::name('sunday')->where($map_schedule)->find();
            $map_schedule2 = [
                'founder' => $post_data[$name_info],
                'class_name' => $post_data['class_name'],
                'school_name' => $post_data['school_name'],
                'created_at' => date('Y-m-d H:i:s'),
                'monday_id' => $monday['monday_id'],
                'tuesday_id' => $tuesday['tuesday_id'],
                'wednesday_id' => $wednesday['wednesday_id'],
                'thursday_id' => $thursday['thursday_id'],
                'friday_id' => $friday['friday_id'],
                'saturday_id' => $saturday['saturday_id'],
                'sunday_id' => $sunday['sunday_id']
            ];
            Db::name('schedule_info')->insert($map_schedule2);
            $schedule_info = Db::name('schedule_info')->where($map_schedule2)->find();


            $get_schedule = Search::get_scheduleAll($map2, $schedule_info);
            $this->assign('mon', $get_schedule[0]);
            $this->assign('tue', $get_schedule[1]);
            $this->assign('wed', $get_schedule[2]);
            $this->assign('thu', $get_schedule[3]);
            $this->assign('fri', $get_schedule[4]);
            $this->assign('sat', $get_schedule[5]);
            $this->assign('sun', $get_schedule[6]);
            $this->assign('schedule_id', $schedule_info['schedule_id']);
        } else {
            $map_1 = [
                'schedule_name' => null,
                'founder' => $post_data[$name_info],
                $class_teacher => $post_data[$class_teacher],
                'school_name' => $post_data['school_name'],
            ];
            $Schedule = Db::name('schedule_info')->where($map_1)->find();

            $monday = Db::name('monday')->where('monday_id', $Schedule['monday_id'])->find();
            $tuesday = Db::name('tuesday')->where('tuesday_id', $Schedule['tuesday_id'])->find();
            $wednesday = Db::name('wednesday')->where('wednesday_id', $Schedule['wednesday_id'])->find();
            $thursday = Db::name('thursday')->where('thursday_id', $Schedule['thursday_id'])->find();
            $friday = Db::name('friday')->where('friday_id', $Schedule['friday_id'])->find();
            $saturday = Db::name('saturday')->where('saturday_id', $Schedule['saturday_id'])->find();
            $sunday = Db::name('sunday')->where('sunday_id', $Schedule['sunday_id'])->find();

            $this->assign('mon', $monday);
            $this->assign('tue', $tuesday);
            $this->assign('wed', $wednesday);
            $this->assign('thu', $thursday);
            $this->assign('fri', $friday);
            $this->assign('sat', $saturday);
            $this->assign('sun', $sunday);
            $this->assign('schedule_id', $Schedule['schedule_id']);
        }

        return $this->fetch('new_schedule');
    }

    public function schedule_list()
    {

        $post_data = session('user_info');

        $map1 = [
            'founder' => $post_data['student_name'],
            'class_name' => $post_data['class_name'],
            'school_name' => $post_data['school_name'],

        ];
        $map2 = [
            'student_number' => $post_data['student_number'],
            'class_name' => $post_data['class_name'],
            'school_name' => $post_data['school_name']
        ];


        $student_info = Db::name('student')->where($map2)->find();

        $schedule_info_page = Db::name('schedule_info')->where($map1)->paginate(5);
        $page = $schedule_info_page->render();
        $list = $schedule_info_page->each(function ($item, $key) {
            $item['updated_at'] = Convert::time_ago($item['updated_at']);

            return $item;
        });
        $this->assign('default_schedule', $student_info['default_schedule']);
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch('schedule_list');
    }


    public function en_default(Request $request)
    {
        $data_id = $request->param('id');
        $post_data = session('user_info');
        $map1 = [
            'school_name' => $post_data['school_name'],
            'class_name' => $post_data['class_name'],
            'founder' => $post_data['student_name'],
            'default_schedule' => 'true',
        ];
        Db::name('student')->where('student_id', $post_data['student_id'])->update(['default_schedule' => $data_id, 'updated_at' => date("Y-m-d H:i:s")]);
        $user_info1 = Db::name('student')->where('student_id', $post_data['student_id'])->find();
        session('user_info', $user_info1);
    }

    public function delete_list(Request $request)
    {
        $data_id = $request->param('id');
        Db::name('schedule_info')->where('schedule_id', $data_id)->delete();

    }


}