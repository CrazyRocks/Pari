<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 5/23/2015
 * Time: 1:06 PM
 */
namespace Pari\Game\Controllers;

class UserController extends ControllerBase{

    public function initialize(){
        //如果已经登录, 返回上一页

        //如果上一页不存在, 跳转回首页
    }

    public function indexAction(){

    }

    public function loginAction(){
        $this->view->pick('user/login');
        return;
    }
}