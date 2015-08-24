<?php
/**
 * 首页
 * User: Admin
 * Date: 2015/1/15
 * Time: 12:23
 */
namespace Pari\Sso\Controllers;

use Zend\Filter\Null;

class IndexController extends ControllerBase
{

    /**
     * @TODO 用户登录注册页面
     */
    public function IndexAction()
    {
        //判断是否已经登录, 如果已经登录获取来源页面,并跳转回去
        if ($this->request->hasQuery('ref')) {
            //引用页
            $referer = $this->request->getQuery('ref', 'trim');
        }
        //判断是否已经登录
        if ($this->service->user()->SignedInfo() != false) {

            //如果没有, 检测设备并返回设备
            $device = $this->cookies->get('device') ?  $this->service->device() : Null;
            $url = $device == 'pc'?'':'';
            $this->response->redirect($url);

            //判断上一页是否在本网站, 如果是跳转回本网站, 不是跳到本网站的首页
            $this->request->getHTTPReferer();

        }
        //从未登录过, 直接渲染模版
        $this->view->pick('');
    }

    /**
     * @TODO 登录
     */
    public function SignInAction()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            //查询是否在白名单

            //在白名单中, 跳过查询是否在黑名单,否则查看黑名单

            //如果频繁登录, 则需要
        }

    }

    /**
     * @TODO 注册
     */
    public function SignUpAction()
    {

    }

    /**
     * @TODO 注销
     */
    public function SignOutAction()
    {

    }

    /**
     * @TODO 找回登录密码
     */
    public function FindPwdAction()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {

        }
        //直接渲染模版
        $this->view->pick('');
    }

    /**
     * @TODO 是否需要验证码
     */
    public function getCaptchaAction($username = NULL)
    {
        //该账号是否异地登录, 或者频繁登录
        $this->cache->get('');
        //判断是否开启COOKIES, 没开启不予注册或者登录

    }

}