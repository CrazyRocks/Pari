<?php
/**
 * 首页
 * User: Admin
 * Date: 2015/1/15
 * Time: 12:23
 */
namespace Pari\Admin\Controllers;
use Phalcon\Mvc\Model;
class IndexController extends ControllerBase
{
    /**
     * @TODO 管理面板Dashboard
     */
    public function IndexAction(){



        $this->view->pick('');

    }

    public function SignAction(){

    }

    /**
     * @TODO 登出
     */
    public function SignOutAction()
    {
        $this->session->remove('authAdmin');
        $this->flash->success('Goodbye!');
        return $this->response->redirect('index/index');
    }
}