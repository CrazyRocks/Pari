<?php

/**
 * 用户处理
 * @author tom
 */

namespace Pari\Services;

use Phalcon\Filter;
use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Message\Group as MessageGroup;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;
use Pari\Models\User;
use Pari\Data\UserData;



/**
 * 用户注册，用户重置密码
 *
 */
class UserService extends BaseService
{

    /**
     * @var array
     */
    public $retCode  = ['001'=>'校验错误','002'=>'用户名/邮箱已经存在'];
    /**
     *
     * @var \Phalcon\Validation\Message\Group | other
     */
    public $messages;

    /**
     * @var int
     */
    protected $memberId;


    /**
     * @todo 用户登陆
     * @param array $postData
     */
    public function signIn($postData = []){
        //读取POST数据流, 整个过程全部用Ajax提交

        //查询是否需要验证码 , 如果需要验证码就返回验证码进行验证


        //查询数据库
    }

    public function testSignUp(){
        //TODO 判断数据是否正确
        $filter = new Filter();

        //TODO 插入数据

        //TODO 发送验证邮件

        //TODO 返回正确信息


    }
    public function testSignIn($name , $password , $captcha){
        //验证数据, 数据过滤
        $this->escaper
    }


	
    /**
     * @author Tom
     * @todo 用户注册
     * @param array $data
     * @author tom
     * @return boolean|\Pari\Models\User
     */
    public function signUp($data)
    {

    	// 校验失败
        if ($this->validateSignUpData($data) == false) {
        	// TODO 返回对象的校验码

           	return ['error'=>'001','errorMsg'=>$this->retCode['001']];
        }

        $member = new UserData();
		$result = $member->signUp($data);
        if($result === true){
        	return true;
        }else{
        	var_dump($result);
        	$this->setMessages($result);
        	return false;
        }
    }

    /**
     * @todo 从cookies和session中获取用户信息
     */
    public function getInfo(){


    }

    /**
     * @todo 取回登录密码 retrieve password
     */
    public function retrievePwd(){

    }

    /**
     * @todo 取回取款密码  retrieve security password
     */
    public function retrieveSecPwd(){

    }


    /**
     * 注册时检验用户提交的表单数据
     *
     * 帐号唯一性检测在Model层处理 @see Member
     *
     * @param $data array()
     *
     * @return bool
     *
     * @todo 删除无用的注释掉的代码
     */
    public function validateSignUpData($data)
    {
        $validation = new Validation();

        $validation->add('name', new StringLength([
            'max' => 32
        ]));
        $validation->add('email', new PresenceOf([
            'message' => '手机号不能为空'
        ]));

        $validation->add('password', new PresenceOf([
            'message' => '密码不能为空'
        ]));

        if (isset($data['ConfirmVerifyCode'])) {
            $validation->add('verifyCode', new PresenceOf([
                'message' => '验证码不能为空'
            ]));
            $validation->add('verifyCode', new Confirmation(array(
                'message' => '验证码错误',
                'with'    => 'ConfirmVerifyCode'
            )));
        }

        $messages = $validation->validate($data);
//         var_dump($messages);die;
        $this->setMessages($messages);

        return $this->validationHasFailed() != true;
    }

    
    /**
     * 重置用户密码，用户名=（手机号或者邮箱）
     *
     * @param array $data [username, password, repassword]
     *
     * @return bool
     */
    public function resetPassword($data)
    {
        if ($this->validateResetPasswordData($data) == false) {
            return false;
        }

        $username = $data['username'];
        $password = $data['password'];

        /* @var $user Member */
        $user = Member::findFirstByMemberName($username);
        if ($user) {
            $auth = MemberAuth::instance();
            $user->salt = mt_rand(1000, 100000);
            $user->member_passwd = $auth->hash($password, $user->salt);
            if ($user->save() == false) {
                $this->setMessages($user->getMessages());
                return false;
            }
            return true; // 重置密码成功
        }
        return false;
    }

    /**
     * 重置密码表单校验
     *
     * @param $data
     *
     * @return bool
     */
    public function validateResetPasswordData($data)
    {
        $validation = new Validation();
        $validation->add('password', new PresenceOf([
            'message' => '密码不能为空',
        ]));
        $validation->add('password', new StringLength(array(
            'max'            => 32,
            'min'            => 6,
            'messageMaximum' => '密码必需小于32位',
            'messageMinimum' => '密码最少6位',
        )));
        $validation->add('repassword', new Confirmation(array(
            'message' => '两次输入的密码不一致',
            'with'    => 'password'
        )));

        $messages = $validation->validate($data);
        $this->setMessages($messages);
        return $this->validationHasFailed() != true;
    }

    /**
     * @param $password
     *
     * @return bool|Member|null
     *
     * @todo 其实DI已经实现了单例，没必要auth自己实现单例；server层可以直接使用$this->auth->hash()
     */
    public function checkPassword($password)
    {
        $memberId = $this->getMemberId();
        if (!$memberId) {
            return null;
        }
        $member = Member::findFirst($memberId);
        $auth = MemberAuth::instance();
        if ($auth->hash($password, $member->salt) == $member->member_passwd) {
            return $member;
        }
        return false;
    }

    /**
     * 登录表单校验
     *
     * @param $data
     *
     * @return bool
     */
    public function validateSigninData($data)
    {
        $validation = new Validation();
        $validation->add('username', new PresenceOf([
            'message' => 'The name is required',
        ]));

        $validation->add('username', new StringLength(array(
            'max'            => 50,
            'min'            => 5,
            'messageMaximum' => 'We don\'t like really long names',
            'messageMinimum' => 'We want more than just their initials'
        )));

        $validation->add('password', new PresenceOf([
            'message' => 'The password is required',
        ]));

        $validation->add('password', new StringLength(array(
            'max'            => 50,
            'min'            => 6,
            'messageMaximum' => 'We don\'t like really long names',
            'messageMinimum' => 'We want more than just their initials'
        )));

        $messages = $validation->validate($data);
        $this->setMessages($messages);
        return $this->validationHasFailed() != true;
    }

    /*
     * 注册填写详细信息领取优惠券100元
     * @param $data
     * @return bool
     *
     * @todo 删除无用的注释掉的代码
     * */

    public function validateRegister_surpriceData($data)
    {
        $validation = new Validation();

        $validation->add('true_name', new PresenceOf([
            'message' => '收货人姓名不能为空'
        ]));
        $validation->add('mob_phone', new PresenceOf([
            'message' => '收货人手机号码不能为空'
        ]));
        $validation->add('province_id', new PresenceOf([
            'message' => '必须选择省'
        ]));
        $validation->add('city_id', new PresenceOf([
            'message' => '必须选择市'
        ]));
        $validation->add('area_id', new PresenceOf([
            'message' => '必须选择区'
        ]));
        $validation->add('address', new PresenceOf([
            'message' => '详细地址不能为空'
        ]));

        $messages = $validation->validate($data);
        $this->setMessages($messages);
        return $this->validationHasFailed() != true;
    }

    /**
     *   检查当前用户是否已经注册过,针对wap注册，避免通过微信注册重复恶意获得验证码
     */
    public function reged($user)
    {
        $conditions = "member_name = ?1 OR member_phone = ?2";
        $parameters = array(1 => $user, 2 => $user);
        $userInfo = Member::findFirst(array(
            $conditions,
            "bind" => $parameters
        ));
        if (!empty($userInfo)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *  更新体验店服务号
     */
    public function updateMemberFiled($filed, $member_info)
    {
        if (isset($filed) and intval($filed) > 0) {
            $member_info->guid_sn = intval($filed);
            $member_info->save();
        }
    }

    /**
     * @return int
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @param int $memberId
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
    }
    
    /**
     * 
     */
    public function getMemberModel()
    {
        return new Member();
    }
    
    public function findFirst($sql)
    {
        return Member::findFirst($sql);
    }
    
    public function find($sql)
    {
        return Member::find($sql);
    }
    
    /* ==========新规则下的函数 应用于会员系统，后期不合规范的改正========= */
    
    /**
     * 获取单个用户信息
     * @param   array('member_id'=>'用户Id','member_name'=>'用户名')     $params 用户名
     * @return  \Kinhom\Models\Member
     */
    public function getMemberOne($params)
    {
        if (isset($params['member_id']) && !empty($params['member_id'])) {
            return Member::findFirstByMemberId(intval($params['member_id']));
        }
        if (isset($params['member_name']) && !empty($params['member_name']))
        {
            return Member::findFirstByMemberName(trim($params['member_name']));
        }
        if (isset($params['member_email']) && !empty($params['member_email']))
        {
            return Member::findFirstByMemberEmail(trim($params['member_email']));
        }
        if (isset($params['member_phone']) && !empty($params['member_phone']))
        {
            return Member::findFirstByMemberPhone(trim($params['member_phone']));
        }
        if (isset($params['val']) && !empty($params['val']))
        {
            return Member::findFirst(" member_id = '".$params['val']."' or member_name = '".$params['val']."' or 
                                       member_email = '".$params['val']."' or member_phone = '".$params['val'].
                                       "'");
            
        }
        
        return 0;
    }
    
    /**
     * 增加
     */
    public function addMember($params)
    {
        $model = new Member();
    
        if(is_array($params)) {
            foreach($params as $k=>$v) {
                $model->$k = $v;
            }
        }
        if ($model->save() == false) {
            foreach ($model->getMessages() as $message) {
                //echo $message;
                throw new Exception('can not save');
            }
        }
        return true;
    }
    
    /**
     * 更新 会员
     */
    public function updateMember($params)
    {
        $model = $this->getMemberOne($params);
        
        if(is_array($params)) {
            foreach($params as $k=>$v) {
                $model->$k = $v;
            }
        }
        if ($model->save() == false) {
            foreach ($model->getMessages() as $message) {
                throw new Exception('can not update');
            }
        }
        return true;
    }
    
    /**
     * 获取
     */
    public function getMemberAccountOne($params)
    {
        $sql = ' 1 ';
        if(isset($params['member_id'])) {
            $sql .=' and member_id = '.$params['member_id'];
        }
        
        return MemberAccount::findFirst($sql);
        
        
    }
    
    /**
     * 更新 会员账户
     */
    public function saveMemberAccount($params)
    {
        
        $model = $this->getMemberAccountOne($params);
        if(empty($model)) {
            $model = new MemberAccount();
        }
    
        if(is_array($params)) {
            foreach($params as $k=>$v) {
                $model->$k = $v;
            }
        }
        if ($model->save() == false) {
            foreach ($model->getMessages() as $message) {
                throw new Exception('can not update');
            }
        }
        return true;
    }
    
    
    /**
     * 获取
     */
    public function getMemberInfoOne($params)
    {
        $sql = ' 1 ';
        if(isset($params['member_id'])) {
            $sql .=' and member_id = '.$params['member_id'];
        }
    
        return MemberInfo::findFirst($sql);
    
    
    }
    
    /**
     * 更新 会员账户
     */
    public function saveMemberInfo($params)
    {
    
        $model = $this->getMemberInfoOne($params);
        if(empty($model)) {
            $model = new MemberInfo();
        }
    
        if(is_array($params)) {
            foreach($params as $k=>$v) {
                $model->$k = $v;
            }
        }
        if ($model->save() == false) {
            foreach ($model->getMessages() as $message) {
                throw new Exception('can not update');
            }
        }
        return $model;
    }
    
    /**
     * 登陆后得处理
     */
    public function initLogin($member_id)
    {
        //处理会员的基本信息 =》 登陆时间，次数，ip
        $login_time = time();
        $now_ip = $this->request->getClientAddress();
        $mem = $this->getMemberOne(array('member_id'=>$member_id));
        $now_num = $mem->member_goldnum;
        $last_login_time = $mem->member_login_time;
        $last_ip = $mem->member_login_ip;
        
        $new_d = strtotime(date('Y-m-d',$login_time));
        $old_d = strtotime(date('Y-m-d',$last_login_time));
        if($new_d == $old_d) {
            $num = $now_num+1;
        } else {
            $num = 1;
        }
        $mem->member_login_num = $mem->member_login_num + 1;
        $mem->member_login_ip = $now_ip;
        $mem->member_login_time = $login_time;
        $mem->member_old_login_ip = $last_ip;
        $mem->member_old_login_time = $last_login_time;
        $mem->member_goldnum = $num;
        $mem->save();
        //支付了之后才有返积分时间  返利时间
        //返积分，返现订单到期处理=》增加accountlog，更改ordergoods状态
        //订单是否到实践自动确定收货
    }
    
    /* ===========结束======== */

    
}
