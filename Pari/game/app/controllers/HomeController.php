<?php
/**
 * 首页
 * User: Admin
 * Date: 2015/1/15
 * Time: 12:23
 */
namespace Pari\Game\Controllers;

class HomeController extends ControllerBase{



    public function initialize(){

    }


    public function IndexAction(){
        //默认跳转到登录页面
        $this->response->redirect('user/signIn');
    }

    public function listAction(){


    }


}