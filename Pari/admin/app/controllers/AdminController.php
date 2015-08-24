<?php
/**
 * 首页
 * User: Admin
 * Date: 2015/1/15
 * Time: 12:23
 */
namespace Pari\Admin\Controllers;

use Pari\Models\Admin;
use Pari\Models\Language;
use Phalcon\DI;
use Phalcon\Mvc\Model;


/**
 * Class AdminController
 * @package Pari\Admin\Controllers
 */
class AdminController extends ControllerBase
{


    public function initialize()
    {
        parent::initialize();
    }

    public function IndexAction()
    {
        // 校验CSRF
        if (!$this->security->checkToken()) {
            return $this->response->setJsonContent(['102', '校验失败']);
        }

        //判断是否IP黑名单, 或者IP是登录已经超过次数,还有CAPTCHA
        if (!empty($getIpSes = $this->session->get($this->clientIp))) {
            trim($this->request->getPost('captcha', 'lower'));
            $sessionInfo = $this->session->get($this->clientIp);
            //查看是否黑名单
            if ($sessionInfo['bl'] != 0) {  // BL == BLACKLIST 黑名单
                return $this->response->setJsonContent(['102', '你所在IP异常, 请2小时候后重试']);
            }
            //校验captcha
            if (!empty($sessionInfo['cc']) && strtolower($sessionInfo['cc']) == trim($this->request->getPost('captcha', 'lower'))) {
                return $this->response->setJsonContent(['102', '验证码不正确哦']);
            }
        }

        //获取用户名和密码
        $name = $this->request->getPost('nickname', 'email');
        $password = $this->request->getPost('password');

        //判断是否为空
        if (empty($name) || empty($password)) {
            return $this->response->setJsonContent(['101', '用户名或密码不能为空']);
        }

        $auth = Admin::findFirst("username = $name OR email = $name");

        if ($auth == false) {
            return $this->response->setJsonContent(['301', '用户不存在']);
        }

        if ($auth['status'] != 1) {
            return $this->response->setJsonContent(['102', '账号异常，请联系客服']);
        }

        if ($this->Encrypt($auth['password'], $auth['salt']) != $this->Encrypt($password, $auth['salt'])) {
            return $this->response->setJsonContent(['102', '用户或密码错误']);
        }

        //  2小时登录大于100次就要输入验证码
        empty($getIpSes) ? $this->session->set($this->clientIp, ['t' => '1']) : $getIpSes['t'] > 100 ? $this->session->set($this->clientIp, ['t' => ++$getIpSes['t'], 'bl' => 1]) : $this->session->set($this->clientIp, ['t' => ++$getIpSes['t']]);

        //开始写入session
        $this->session->set('adminAuth', [
            'uid' => $auth['uid'],
            'name' => $auth['username'],
            'sign_time' => time(),
        ]);
        $this->session->set('lock_time', time());

        //防止不同机器登录, 对应services->auth->singleSign()
        $this->cache->save('admin_' . $auth['uid'], time());


        //TODO 记录登录信息
        $this->service->admin()->logInfo();

    }

    /**
     * @TODO 单点登录
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function singleSign()
    {
        $adminAuth = $this->session->get('adminAuth');
        $adminCache = $this->cache->get('admin_sign_time_' . $adminAuth['uid']);
        if ($adminAuth['sign_time'] != $adminCache && $adminAuth['sign_time'] < $adminCache ) {
            $adminSignIp = $this->cache->get('admin_sign_Ip_'. $adminAuth['uid']);
            echo '你的账号已经在另外一个地点登录, 登录IP:   XXXX, 登录地点 : XXX <br>如不是你登录, 请及时修改密码';
            $this->session->destroy();
            //跳转到另外一个页面
            return $this->response->redirect();
        };
    }

    /**
     * TODO 锁定时间
     * @return bool
     */
    public function LockTime()
    {

        if (time() - $this->session->get('lock_time') > 600) {
            $this->session->destroy(); //这里可能不使用销毁session, 可能跳转然后使用手势解锁, 刚开始先使用销毁
            return false;
        }
        $this->session->set('lock_time', time());
        return true;

    }


    /**
     * @TODO 动态AJAX 获取验证码
     */
    public function CaptchaAction()
    {
        //判断是否异地登录

        //判断是否频繁登录

        if ($this->request->isPost() && $this->request->isAjax()) {

        }
    }


    public function TestAction()
    {
        $str = '123';
        echo $this->service->validate()->Salt();

    }


    public function SignInAction()
    {
        if ($this->request->isPost()) {

        }
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

    public function GetUser()
    {
        $di = DI::getDefault()->get('session');
        $di->get($this->sessionKey);
    }

    /**
     *
     */
    public function start()
    {
        $this->cache->save();
    }



}