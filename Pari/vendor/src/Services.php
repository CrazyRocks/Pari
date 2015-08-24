<?php
/**
 * Copyright (c) 2012-2014 t8mbet.com
 * Service 详细使用方法
 * 1. 创建service
 * 2. 在本类引用 use
 * 3.
 */

namespace Pari;

use Pari\Services\Auth\UserAuth;


/**
 * Class Services
 * @package Pari\Services
 */
class Services
{

    /**
     * @TODO 首页
     * @return Services\Home
     */
    public function home(){
        return new \Pari\Services\Home();
    }


    /**
     * @TODO 通用用户验证
     * @return Services\Auth
     */
    public function Auth(){
        return new Services\Auth();
    }

    /**
     * @TODO 后台用户验证
     * @return Services\Auth\AdminAuth
     */
    public function AdminAuth(){
        return new Services\Auth\AdminAuth();
    }

    /**
     * @TODO 普通用户认证
     * @return UserAuth
     */
    public static function UserAuth()
    {

        $key = 'UserAuth';
        if (!isset(static::$instances[$key])) {
            $auth = new UserAuth();
            $config = \Phalcon\DI::getDefault()->get('config')->auth;
            $auth->setConfig($config);

            static::$instances[$key] = $auth;
        }

        return static::$instances[$key];
    }

    public function BankerAuth(){

    }

    /**
     * 添加如下语句就可以在IDE显示提示
     * @property \Pari\Services\Response\Ajax;
     */
    public function sample(){
        return new \Pari\Services\Response\Ajax();
    }

    /**
     * @TODO 订单
     * @return Services\Order
     */
    public function order(){
        return new \Pari\Services\Order();
    }

    /**
     * @return Services\UserService
     */
    public function user(){
        return new \Pari\Services\UserService();
    }

    /**
     * @return Services\Article
     */
    public function article(){
        return new \Pari\Services\Article();
    }

    /**
     * @TODO 活动
     * @return Services\Activity
     */
    public function activity(){
        return new \Pari\Services\Activity();
    }

    /**
     * @TODO 数据验证
     * @return Services\Validate
     */
    public function validate(){
        return new \Pari\Services\Validate\Validate();
    }





}