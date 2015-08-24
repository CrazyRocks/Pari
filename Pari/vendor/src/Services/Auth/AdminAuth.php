<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 5/26/2015
 * Time: 2:20 PM
 */

namespace Pari\Services\Auth;

use Pari\Models\Admin,
    Pari\Models\AdminTokens,
    Phalcon\DI;

/**
 * 运营中心管理员认证
 * @package Pari\Services\Auth
 */
class AdminAuth implements AuthInterfacea
{
    /**
     * @var int 设置cookie过期时间
     */
    protected $lifetime;
    /**
     * @var string 设置保存cookie认证的key名称
     */
    protected $authKey;
    /**
     * @var string 设置保存cookie的域名，在不跨域时保存主域名
     */
    protected $domain;
    /**
     * @var string 设置保存用户信息对象的key名称
     */
    protected $sessionKey;
    /**
     * @var string 设置角色名称
     */
    protected $sessionRoles;

    public function setConfig($config){
        // 检查配置代码
        // ...
        // 初始化配置
        $this->lifetime = $config['lifetime'];
        $this->authKey = $config['authKey'];
        $this->domain = $config['domain'];
        $this->sessionKey = $config['sessionKey'];
        $this->sessionRoles = $config['sessionRoles'];
    }

    /**
     * @TODO 登录
     * @param string  $user
     * @param string  $password
     * @param boolean $remember 是否自动登录
     *
     * @return bool
     */
    public function login($user, $password, $remember = false)
    {
        if (!$user instanceof Admin) {
            $username = $user;
            if (!$username) {
                return false;
            }
            // Load the user
            $user = Admin::findFirst([
                'admin_name=:admin_name:',
                'bind' => ['admin_name' => $username]
            ]);
        }
        if ($user) {
            $roles = $this->getRoles($user);
            $password = $this->hash($password);

            //如果用户被允许登录且密码匹配，则完成登录
            if (isset($roles['login']) && $user->admin_password === $password) {
                // 如果需要保存登录令牌到cookie以实现自动登录
                if ($remember === true) {
                    // 创建一个新的自动登录令牌
                    $token = new AdminTokens();
                    $token->admin_id = $user->admin_id;
                    $token->user_agent = sha1($this->getRequest()->getUserAgent());
                    $token->token = $this->createToken();
                    $token->created = time();
                    $token->expires = time() + $this->lifetime;
                    if ($token->create() === true) {
                        //设置自动登录cookie
                        $this->getCookies()->set($this->authKey);
                        $this->getCookies()->set($this->authKey, $token->token, $token->expires, '/', false,
                            $this->domain, true);
                    }
                }
                // 更新登录次数
                $user->admin_login_num = $user->admin_login_num + 1;
                // 更新登录时间
                $user->admin_login_time = time();
                // 保存更新
                $user->save();

                // 生成新的session_id
                session_regenerate_id();

                // 用户信息保存到session
                $this->getSession()->set($this->sessionKey, $user);

                // 用户角色权限信息保存到session
                $this->getSession()->set($this->sessionRoles, $roles);

                return true; // 登录成功
            } else {
                return false; // 登录失败
            }
        }
        return false; // 没找到用户
    }

    /**
     * 获取用户角色
     *
     * @param Admin $user
     * @return array|bool
     */
    public function getRoles($user)
    {
        if (!$user instanceof Admin) {
            return false;
        }

        if ($user->admin_is_super === '1') {
            return ['login' => true, 'super' => true, 'admin' => true];
        }

        return ['login' => true, 'admin' => true];
    }

    /**
     * 密码加密算法
     */
    public function hash($password, $salt = null)
    {
        return md5(trim($password));
    }

    /**
     * 生成自动登录令牌
     *
     * @return string
     */
    private function createToken()
    {
        do {
            $token = sha1(uniqid(\Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM, 32), true));
        } while (AdminTokens::findFirstByToken($token));

        return $token;
    }

    /**
     * 退出
     *
     * @param boolean $destroy 退出后删除session
     * @param boolean $logoutAll 删除所有的token
     *
     * @return bool
     */
    public function logout($destroy = false, $logoutAll = false)
    {
        if ($this->getCookies()->has($this->authKey)) {
            $cookieToken = $this->getCookies()->get($this->authKey)->getValue();

            // 删除cookie中的自动登录令牌
            $this->getCookies()->set($this->authKey);
            $this->getCookies()->set($this->authKey, "", time() - 3600, '/', false, $this->domain, true);
            $this->getCookies()->delete($this->authKey);

            // 当前使用的token
            $token = AdminTokens::findFirstByToken($cookieToken);
            if ($token) {
                if ($logoutAll) {
                    // 删除所有关联member_id的token
                    $allTokens = AdminTokens::find(array(
                        'admin_id=:admin_id:',
                        'bind' => array(':admin_id' => $token->admin_id)
                    ));
                    foreach ($allTokens as $_token) {
                        $_token->delete();
                    }
                } else {
                    $token->delete(); // 删除当前使用的token
                }
            }
        }

        // 删除session
        if ($destroy === true) {
            $this->getSession()->destroy();
        } else {
            //删除session中的用户信息
            $this->getSession()->remove($this->sessionKey);
            //删除角色权限信息
            $this->getSession()->remove($this->sessionRoles);
            // 重新生成session_id
            session_regenerate_id();
        }
        // Double check
        return !$this->loggedIn();
    }

    /**
     * 检查是否登录
     *
     * @param mixed $role role name
     * @return boolean
     */
    public function loggedIn($role = null)
    {
        $user = $this->getUser();
        if ($user) {
            // 如果不需要角色权限检查
            if (!$role) {
                return true;
            }

            // 检查角色权限
            if ($this->sessionRoles && $this->getSession()->has($this->sessionRoles) ) {
                // Check in session
                $roles = $this->getSession()->get($this->sessionRoles);
                $role = isset($roles[$role]) ? $roles[$role] : null;
            } else {
                // Check in db
                //$role = $user->hasRole($role);
            }

            // Return true if user has role
            return $role ? true : false;
        }
        return false;
    }

    /**
     * 从session中获取当前登录用户
     *
     * @return Admin
     */
    public function getUser()
    {
        $user = $this->getSession()->get($this->sessionKey);

        // check fro remembered login
        if (!$user) {
            $user = $this->autoLogin();
        }

        return $user;
    }

    /**
     * 基于cookie的自动登录
     *
     * @return false 自动登录失败，Member 成功
     */
    private function autoLogin()
    {
        // 用户浏览器中保存有token的cookie
        if ($this->getCookies()->has($this->authKey)) {
            $cookieToken = $this->getCookies()->get($this->authKey)->getValue();

            // 获取服务端保存的token
            $token = AdminTokens::findFirstByToken($cookieToken);

            // 如果存在这个token
            if ($token) {
                // 通过外键关联查询到属于这个token的用户信息
                $user = $token->getAdmin();

                // 取用户的角色权限信息
                $roles = $this->getRoles($user);

                // 如果用户允许登录，且token是匹配的，则完成登录过程
                if (isset($roles['login']) && $token->user_agent === sha1($this->getRequest()->getUserAgent())) {
                    // 基于cookie的自动登录，token使用一次后作废
                    $token->token = $this->createToken();
                    // 每次自动登录更新过期时间
                    $token->expires = time() + $this->lifetime;
                    // 保存重新生成的token
                    $token->save();

                    // 在cookie中保存重新生成的token
                    $this->getCookies()->set($this->authKey);
                    $this->getCookies()->set($this->authKey, $token->token, $token->expires, '/', false,
                        $this->domain, true);

                    // 更新登录次数
                    $user->admin_login_num = $user->admin_login_num + 1;
                    // 更新登录时间
                    $user->admin_login_time = time();
                    // 保存更新
                    $user->save();

                    // 重新生成session id
                    session_regenerate_id();

                    // 保存登录后的用户信息到session
                    $this->getSession()->set($this->sessionKey, $user);
                    // 保存用户的角色权限信息到session

                    if ($this->sessionRoles) {
                        $this->getSession()->set($this->sessionRoles, $roles);
                    }

                    // 基于cookie的自动登录成功
                    return $user;
                }
                // 删除无效的token
                $token->delete();
            } else {
                // 服务端不存在token则删除cookie中的token
                $this->getCookies()->set($this->authKey);
                $this->getCookies()->set($this->authKey, "", time() - 3600);
                $this->getCookies()->delete($this->authKey);
            }
        }
        return false;
    }

    /**
     * 从数据库中更新保存在session中的用户信息
     */
    public function refreshUser()
    {
        /** @var Admin $user */
        $user = $this->getSession()->get($this->sessionKey);
        if (!$user) {
            return null;
        } else {
            // 从数据库中读取用户信息
            $user = Admin::findFirst($user->admin_id);
            $roles = $this->getRoles($user);

            // 重新生成session_id
            session_regenerate_id();

            // 保存用户信息到session
            $this->getSession()->set($this->sessionKey, $user);
            // 保存用户角色权限到session
            if ($this->sessionRoles) {
                $this->getSession()->set($this->sessionRoles, $roles);
            }

            return $user;
        }
    }

    // setter getter

    /**
     * @return \Phalcon\Http\Response\CookiesInterface
     */
    protected function getCookies()
    {
        // 由DI决定是否单例
        return DI::getDefault()->get('cookies');
    }

    /**
     * @return \Phalcon\Session\AdapterInterface
     */
    protected function getSession()
    {
        // 由DI决定是否单例
        return DI::getDefault()->get('session');
    }

    /**
     * @return \Phalcon\Http\RequestInterface
     */
    protected function getRequest()
    {
        // 由DI决定是否单例
        return DI::getDefault()->get('request');
    }
}
