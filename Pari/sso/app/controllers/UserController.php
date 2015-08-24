<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 5/23/2015
 * Time: 6:10 PM
 * @
 */

namespace Pari\Sso\Controllers;


use Phalcon\Filter;

class UserController extends ControllerBase
{

    public function indexAction()
    {
        //如果已经登陆了, 跳转回之前的页面
        if ($this->service->user()->getInfo()) {
            $this->service->detect()->device();
            $this->goBack();
        } else {
            $this->response->redirect('user/sign');
        }

        $this->view->pick('user/sign');
    }



    public function signInAction()
    {
        //判断是否Ajax的post过来的
        if ($this->request->isPost() && $this->request->isAjax()) {
//            $name = $this->request->getPost('name', 'trim');
//            $password = $this->request->getPost('password', 'trim');
            $mark = strstr($this->request->getPost('name', 'trim'), '@') ? 1 : 0;//判断是否使用email登陆
            //登陆服务
            $retInfo = $this->service->user()->auth()->signIn($this->request->getPost('name', 'trim'),$this->request->getPost('password', 'trim'),$mark);
            //如果登陆成功, 跳转

            $user = new UserAuth($this->request->getPost('name', 'trim'), $this->request->getPost('password', 'trim'));
            if (!$user) {
                $this->response->setJsonContent(['code' => '201', 'data' => '', 'msg' => '用户名或密码错误', 'token' => '']);
            }
            $this->response->setJsonContent(['code' => '201', 'data' => '', 'msg' => '登陆成功', 'token' => $this->security->getToken()]);
        }
        $this->response->setJsonContent(['code' => '201', 'data' => '', 'msg' => '数据错误', 'token' => '']);
    }

    /**
     * @todo 读取请求的Token , 并返回
     * @param $array
     * @return string
     */
    public function responseJsonByAjax($array)
    {
        $callback = $this->request->get('token', 'string', '');
        $response = $this->request->get($callback) ? $this->request->get($callback, 'string') . '(' . json_encode($array) . ')' : '';
        return $response;
    }

    public function testSignUpAction(){
//        if ($this->request->isPost() && $this->request->isAjax()) {

            $name = $this->request->get('name');
            $password = $this->request->get('password','string');
            $email = $this->request->get('email','email');
        //检测是否能注册, 同一IP在12小时内最多只能注册5个账号. cookies不开启, cookies中已经存在过注册信息都无法进行注册
//          $this->service->detect()->signUp();
        // 注册成功自动发短信通知
            $this->service->user()->testSignUp($name,$email,$password);
            //发Email确认信息
            $this->session->set('allow_send_sms', true);


//        }
    }

    public function testSignInAction(){
        //首先验证captcha
        $this->security->checkToken();
        $this->service->captcha()->check($captcha);

        //验证用户信息,并过滤数据
        $filter = new Filter();
        $user = $filter->sanitize($this->request->getPost('user'),'email');
        $password = $this->request->getPost('password','trim');
        $result = $this->service->user()->signIn($user,$password);

        $result == true ? $this->response->setJsonContent('100', '登录成功'):$this->response->setJsonContent('101', '用户或密码错误');

    }

    /**
     * @todo 注册
     */
    public function signUpAction()
    {

        // 判断是否POST请求和是否是Ajax过来的数据
        // if ($this->request->isPost() && $this->request->isAjax()) {
        $userName = $this->request->get('username','');
        $password = $this->request->get('password', 'string');
        $email = $this->request->get('email', 'email');

        // 进行登录
        $result = $this->getUserService()->signUp(['name' => $userName, 'password' => $password, 'email' => $email]);
        // 构建返回信息
        $retData = $this->responseJsonByAjax($result);


        // }
        $this->response->setContent($retData);

        return false;
    }
//     /**
//      * 注册
//      */
//     public function signupAction()
//     {
//     	$this->session->set('allow_send_sms', true);

//     	$this->tag->setTitle('注册');

//     	if ($this->request->isPost()) {
//     		$data['username'] = $this->request->getPost('username');
//     		$data['password'] = $this->request->getPost('password');
//     		$data['repassword'] = $this->request->getPost('repassword');
//     		$data['verifyCode'] = $this->request->getPost('verifyCode');

//     		$keyName = 'notify_sms_' . $data['username'] . '.cache';
//     		$data['ConfirmVerifyCode'] = $this->cache->get($keyName, 600); // 缓存校验码10分钟

//     		$member = $this->getMemberService();

//     		// TODO: 其实，在发送验证码时，就该判断是否注册过，否则就不发送了。
//     		// 验证该手机是否注册过，避免恶意获得验证码,如果注册滚回老家
//     		if (!$member->reged($data['username'])) {
//     			$this->response->redirect('user/signup/' . $data['username']);
//     		}

//     		$signup = $member->signup($data);

//     		if ($signup != false && $signup instanceof Member) {
//     			/* @var $signup Member */
//     			$this->session->set('signup_username', $signup->member_name);
//     			// 注册成功，进入登录状态
//     			$this->getMemberAuth()->login($data['username'], $data['password'], true);

//     			$this->getCart()->addCartCookie();

//     			$this->response->redirect('user/signupDone');
//     		} else {
//     			$this->view->setVar('errors', $member->getMessages());
//     		}
//     	}
//     }
    /**
     * @todo 登出
     */
    public function signOutAction()
    {

    }


}