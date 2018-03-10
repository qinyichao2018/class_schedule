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
        $this->assign('mon',$mon);
        $this->assign('tue',$tue);
        $this->assign('wed',$wed);
        $this->assign('thu',$thu);
        $this->assign('fri',$fri);
        $this->assign('sat',$sat);
        $this->assign('sun',$sun);
//        var_dump($wed);exit();
        return view();
    }
}
