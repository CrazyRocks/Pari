<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 6/18/2015
 * Time: 5:48 PM
 */

namespace Pari\Services;

/**
 * 验证信息
 * Class Validate
 * @package Pari\Services
 */
class Validate
{

    /**
     * @TODO 验证Email
     * @param $str
     * @return int
     */
    public function EmailMatch($str)
    {
        //最佳EMAIL匹配模式
        return preg_match('/^[a-z0-9]+([\_\-\.]?[a-z0-9]+)+@[a-z0-9]+(-)?[a-z0-9]+(.[a-z]+){1,2}$/i', trim($str));
    }

    /**
     * @TODO 完美匹配密码, 不允许其它字符只允许键盘上的字符
     * @param $str
     * @return int
     */
    public function PasswordMatch($str)
    {
        return preg_match('/^[\w~`!@#$%\^&*()+-={}|\\\[\]:";\'<>?,.\/"]{6,16}$/', $str) ? 'yes' : 'no';
    }

    /**
     * @TODO 用户密码加密
     * @param $password
     * @param null $salt
     * @return string
     */
    public function Encrypt($password, $salt = null)
    {
        //salt值默认用mt_rand(100000,999999)来生成
        return empty($salt) ? md5(trim($password)) : md5(trim($password) . $salt);
    }

    /**
     * @TODO 验证密码
     * @param $inputPwd
     * @param $checkPwd
     * @param null $salt
     * @return bool
     */
    public function MatchPwd($inputPwd, $checkPwd, $salt = null)
    {
        return $checkPwd == $this->Encrypt($inputPwd, $salt) ? true : false;
    }

    /**
     * @TODO 生成Salt值
     * @return int
     */
    public function Salt()
    {
        return mt_rand(100000, 999999);
    }


}