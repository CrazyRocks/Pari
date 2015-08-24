<?php
/**
 * 首页
 * User: Admin
 * Date: 2015/1/15
 * Time: 12:23
 */
namespace Pari\Banker\Controllers;

class HomeController extends ControllerBase{


    public function IndexAction(){
            $this->view->pick('home');
    }

    public function newGameAction(){
            $this->view->pick('newGame');
    }

    public function resetPwdAction(){
            $this->view->pick('resetPassword');
    }

    public function safeAction(){
            $this->view->pick('safeSetting');
    }


}