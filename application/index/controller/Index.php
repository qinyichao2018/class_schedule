<?php

namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $mon = Db::name("monday")->find();
        $tue = Db::name("tuesday")->find();
        $wed = Db::name("wednesday")->find();
        $thu = Db::name("thursday")->find();
        $fri = Db::name("friday")->find();
        $sat = Db::name("saturday")->find();
        $sun = Db::name("sunday")->find();
        $sch_info = Db::name('schedule_info')->where('school_name', $thu['school_name'])->where('class_name', $thu['class_name'])->find();
        $this->assign('sch_info', $sch_info);
        $this->assign('mon', $mon);
        $this->assign('tue', $tue);
        $this->assign('wed', $wed);
        $this->assign('thu', $thu);
        $this->assign('fri', $fri);
        $this->assign('sat', $sat);
        $this->assign('sun', $sun);
//        var_dump($wed);exit();
        return view();
    }

    public function note_student_list()
    {
        return $this->fetch('note_student_list');
    }

    public function note_wall()
    {
        $post_data = input('post.');
        $student = Db::name('class')->where('school_name', $post_data['school_name'])->where('class_name', $post_data['class_name'])->find();
        var_dump($student);
        exit();
        return $this->fetch('note_wall');
    }

}
