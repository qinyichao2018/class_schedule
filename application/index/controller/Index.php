<?php

namespace app\index\controller;

use app\index\check\Check;
use app\index\convert\Convert;
use think\Controller;
use think\Db;
use app\index\search\Search;
use app\index\often\Often;
use think\Request;
use think\Session;

class Index extends Controller
{
    public function index()
    {
//        $request = Request::instance();
//        echo '访问ip地址：' . $request->ip() . '<br/>';
        $check = session('user_info');
        $schedule_info = Search::search_schedule('灵川中学', '172');

        if ($check == null) {

            $map2 = Often::mapWeek();
            $get_schedule = Search::get_scheduleAll($map2, $schedule_info);
//            $ary = Often::getUpdatedArray($get_schedule);  // 自定义函数制作$ary地图
//            $ddd = Check::comparingAll($ary);   //自定义函数获取最后更新时间
            $ago = Convert::time_ago($schedule_info['updated_at']);     //调用自定义函数将时间转换为更新于多久前

            $this->assign('mon', $get_schedule[0]);
            $this->assign('tue', $get_schedule[1]);
            $this->assign('wed', $get_schedule[2]);
            $this->assign('thu', $get_schedule[3]);
            $this->assign('fri', $get_schedule[4]);
            $this->assign('sat', $get_schedule[5]);
            $this->assign('sun', $get_schedule[6]);
            $this->assign('schedule_id', $schedule_info['schedule_id']);
            $this->assign('sch_info', $schedule_info);
            $this->assign('updated', $ago);
            return view();
        } elseif (array_key_exists('student_name', $check)) {
            $this->error('您已学生登录，正在跳转至个人主页', 'user/student_index');
        } elseif (array_key_exists('teacher_name', $check)) {
            $this->error('您已教师登录，正在跳转至个人主页', 'user/teacher_index');
        }

    }

    public function browsing()
    {
        $user_info = session('user_info');
        if ($user_info == null) {
            $user_info = 'index';
        } elseif (array_key_exists('student_name', $user_info)) {
            $user_info = 'user/student_index';
        } elseif (array_key_exists('teacher_name', $user_info)) {
            $user_info = 'user/teacher_index';
        }
        $post_data = input('id');
        $schedule_info = Db::name('schedule_info')->where('schedule_id', $post_data)->find();

        $map2 = Often::mapWeek();
        $get_schedule = Search::get_scheduleAll($map2, $schedule_info);
//            $ary = Often::getUpdatedArray($get_schedule);  // 自定义函数制作$ary地图
//            $ddd = Check::comparingAll($ary);   //自定义函数获取最后更新时间
        $ago = Convert::time_ago($schedule_info['updated_at']);     //调用自定义函数将时间转换为更新于多久前

        $this->assign('mon', $get_schedule[0]);
        $this->assign('tue', $get_schedule[1]);
        $this->assign('wed', $get_schedule[2]);
        $this->assign('thu', $get_schedule[3]);
        $this->assign('fri', $get_schedule[4]);
        $this->assign('sat', $get_schedule[5]);
        $this->assign('sun', $get_schedule[6]);
        $this->assign('schedule_id', $schedule_info['schedule_id']);
        $this->assign('sch_info', $schedule_info);
        $this->assign('updated', $ago);
        $this->assign('user_info', $user_info);
        return view();


    }

    public function note_student_list()
    {
        $section = input('section');
        $week = input('week');
        $scheduleId = input('schedule_id');
        $post_data = session('user_info');
        $user_identity = '0';
        $IdentityKey = '';
        $IdentityValue = '';
        if (!is_null($post_data)) {
            $flag = 1;
        } else {
            $flag = 0;
            $user_identity = '0';
        }
        if ($flag and array_key_exists('student_name', $post_data)) {
            $IdentityKey = 'student_name';
            $IdentityValue = $post_data[$IdentityKey];
            $user_identity = '1';
        }
        if ($flag) {
            $map = [$IdentityKey => $IdentityValue, 'section_name' => $section, 'week_name' => $week, 'schedule_id' => $scheduleId];
            $map1 = [$IdentityKey => $IdentityValue, 'section_name' => $section, 'week_name' => $week, 'schedule_id' => $scheduleId, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
            $notes_mine = Db::name('notes')->where($map)->find();
//            var_dump($notes_mine);exit();
            if ($notes_mine == null) {
                Db::name('notes')->insert($map1);
            }
        }
        $map2 = ['section_name' => $section, 'week_name' => $week, 'schedule_id' => $scheduleId];
        $notes = Db::name('notes')->where($map2)->select();

//        var_dump($notes);exit();
        $num = count($notes);
        $i = 0;
        $notes2 = ['week_name' => '', 'section_name' => '', 'student_name' => '', 'schedule_id' => '1'];
        $c_u_text1 = ['创建于：', '最后更新：'];
        $c_u_text2 = array();
        $num2 = array();
//        var_dump($notes);print_r('-------->');var_dump($post_data);exit();
        $title = '';
        for ($i; $i < $num; $i++) {
            $notes[$i]['updated_at'] = Convert::time_ago($notes[$i]['updated_at']);
            $c_u_text2[$i] = $c_u_text1;
            $num2[$i] = $i + 1;
            if ($flag and $notes[$i]['student_name'] == $post_data['student_name']) {
                $notes2 = $notes[$i];
                $c_u_text2[$i] = $c_u_text1;
                $num2[$i] = $i;
                unset($notes[$i]);
                $notes = array_values($notes);  //重新排序
                $num--;
                $i--;
                $title = '我的笔记';
            }
        }
        $c = $i;
        $num_mod6 = 6 - ($num % 6);
        $a = $num_mod6 + $c;
        for ($c; $c < $a; $c++) {
            $notes[$c] = ['section_name' => '', 'week_name' => '', 'schedule_id' => '', 'created_at' => '', 'updated_at' => '', 'student_name' => '',];
            $c_u_text2[$c] = ['', ''];
            $num2[$c] = '';
        }

        $this->assign('user_identity', $user_identity);
        $this->assign('notes', $notes);
        $this->assign('num', $num);
        $this->assign('num1', $num - 2);
        $this->assign('c_u_text2', $c_u_text2);
        $this->assign('num2', $num2);

        $this->assign('notes2', $notes2);
        $this->assign('title', $title);
        $this->assign('schedule_id', $scheduleId);
        return $this->fetch('note_student_list');
    }

    public function note_wall()
    {
        $user_info = session('user_info');
        $schedule_id = input('schedule_id');
        $week_name = input('week_name');
        $student_name = input('student_name');
        $section_name = input('section_name');
        $flag2 = 0;
        if ($user_info == null) {
            $flag2 = 0;
        } elseif (array_key_exists('student_name', $user_info)) {
            if ($user_info['student_name'] == $student_name) {
                $flag2 = 1;
            } else {
                $flag2 = 0;
            }
        } else {
            $flag2 = 0;
        }


        if ($week_name == null) {
            $this->error(null, null, null, 1);
        }
        $map = [
            'week_name' => $week_name, 'student_name' => $student_name, 'section_name' => $section_name, 'schedule_id' => $schedule_id
        ];
        $student = Db::name('notes')->where($map)->find();
        $this->assign('course', $student['course_name']);
        $this->assign('schedule_id', $student['schedule_id']);
        $this->assign('flag2', $flag2);
        $this->assign('week_name', $week_name);
        $this->assign('student_name', $student_name);
        $this->assign('section_name', $section_name);
        $this->assign('note_text', $student['note_text']);
        $this->assign('notes_id', $student['notes_id']);
        return $this->fetch('note_wall');
    }

    public function square()
    {
        return $this->fetch('square');
    }

    public function download()
    {
        return $this->fetch('download');
    }

    public function questionAnswer()
    {
        return $this->fetch('questionAnswer');
    }

    public function search()
    {
        $post_data = session('dataTemp');
        $text_info = explode('-', $post_data['text']);
        $check_num = count($text_info);
        $schedule_info_page = array();
        $ax = [
            'school_name' => $post_data['text'],
            'if_public' => 'yes',
        ];
        $ax2 = [
            'class_name' => $post_data['text'],
            'if_public' => 'yes',
        ];
        if ($check_num < 2) {
            $schedule_info = Db::name('schedule_info')->where($ax)->select();
            if ($schedule_info == null) {
                $schedule_info_page = Db::name('schedule_info')->where($ax2)->paginate(2);
            } else {
                $schedule_info_page = Db::name('schedule_info')->where($ax)->paginate(2);
            }

        } elseif ($check_num == 2) {
            $map = [
                'school_name' => $text_info[0],
                'class_name' => $text_info[1]
            ];
            $schedule_info_page = Db::name('schedule_info')->where($map)->paginate();

        } elseif ($check_num > 2) {
            $this->error('查询格式有误:学校-班级');
        }

        $list = $schedule_info_page->each(function ($item, $key) {
            $item['updated_at'] = Convert::time_ago($item['updated_at']);
            return $item;
        });
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
//        session::delete('dataTemp');
        return $this->fetch('search');
    }

    public function search_check(Request $request)
    {
        $data = $request->param();
        session('dataTemp', $data);
        $message = '';
        $status = 0;
        if ($data['text'] == '') {
            $message = '请输入关键字';
        } else {
            $status = 1;
        }
        return ['data' => $data, 'message' => $message, 'status' => $status];
    }

    public function edit_note()
    {
        $week_name = input('week_name');
        $student_name = input('student_name');
        $section_name = input('section_name');
        $schedule_id = input('schedule_id');
        $map = [
            'week_name' => $week_name,
            'student_name' => $student_name,
            'section_name' => $section_name,
            'schedule_id' => $schedule_id,
        ];
        session('edit_info', $map);
        $notes = Db::name('notes')->where($map)->find();

        $this->assign('note_text', $notes['note_text']);
        $this->assign('week_name', $week_name);
        $this->assign('student_name', $student_name);
        $this->assign('section_name', $section_name);
        $this->assign('schedule_id', $schedule_id);
        return $this->fetch('edit_note');
    }

    public function edit_note_check(Request $request)
    {
        $map = session('edit_info');
        $status = 0;       //默认返回失败
        $result = '';     //返回错误的信息
        $data = $request->param();
        $result = $data['text1'];
        Db::name('notes')->where($map)->update(['note_text' => $result]);
        return ['status' => $status, 'message' => $result, 'data' => $data];

    }

    public function delete_note(Request $request)
    {
        //返回错误的信息
        $data_id = $request->param('id');

        Db::name('notes')->where('notes_id',$data_id)->update(['note_text'=>'','updated_at'=>date('Y-m-d H:i:s')]);
    }


}
