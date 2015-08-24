<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 5/26/2015
 * Time: 2:20 PM
 */

namespace Pari\Services\Auth;


interface AuthInterface
{

    /**
     * 获取用户角色
     *
     * @param model $user
     */
    public function getRoles($user);

    /**
     * 从session中获取当前登录用户
     *
     * @return null|user
     */
    public function getUser();

    /**
     * 从配置文件中获取密码加密算法
     */
    public function hash($password, $salt = null);

    /**
     * 检查是否登录
     *
     * @param mixed $role role name
     * @return boolean
     */
    public function IsSignIn($role = null);

    /**
     * 登录
     *
     * @param string $user
     * @param string $password
     * @param boolean $remember 是否自动登录
     */
    public function signIn($user, $password, $remember = false);

    /**
     * 退出
     *
     * @param boolean $destroy 退出后删除session
     * @param boolean $logoutAll 删除所有的token
     */
    public function signOut($destroy = false, $logoutAll = false);

    /**
     * 从数据库中更新保存在session中的用户信息
     */
    public function refreshUser();
}
