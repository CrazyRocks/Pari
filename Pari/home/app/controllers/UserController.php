<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 5/23/2015
 * Time: 1:06 PM
 */
namespace Pari\Home\Controllers;

class UserController extends ControllerBase{

    public function indexAction(){
        $this->request->get('callback');
        $this->view->pick('user/index');
        return;
    }

    public function signInAction(){

        $isLogin = false;
        $TOKEN = 111; // token value

        $uName = $isLogin ? "pari" : "unknown_pari_"; // 是否登录
        $reslut = $uName.$TOKEN;

        $this->view->setVar("TOKEN", $reslut);
        $this->view->pick('user/signin');
        return;
    }

    public function resetPwdAction(){
        $this->view->pick('user/resetpassword');
        return;
    }

    public function safeSettingAction(){
        $this->view->pick('user/safesetting');
        return;
    }

    public function findPwdAction(){
        $this->view->pick('user/findpassword');
        return;
    }
}