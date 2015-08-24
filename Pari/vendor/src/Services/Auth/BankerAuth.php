<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 5/26/2015
 * Time: 2:21 PM
 */

namespace Pari\Services\Auth;

use Pari\Models\OfflineUser;
use Pari\Models\OfflineTokens;
use Phalcon\DI;
/**
 * Class BankerAuthBankerAuthBankerAuth
 * @package Pari\Services\Auth
 *
 * @property \Phalcon\Http\Response\CookiesInterface  $cookies
 * @property \Phalcon\Session\AdapterInterface        $session
 * @property \Phalcon\Http\RequestInterface           $request
 */
class BankerAuth implements AuthInterface
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

    /**
     * 通过traits实现属性按需加载
     */

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

    public function signIn($ouser, $password, $remember = true)
    {
        if (!$ouser instanceof OfflineUser) {
            $ousername = $ouser;
            if (!$ousername) {
                return null;
            }
            // Load the user
            $ouser = OfflineUser::findFirstByOuserSn($ousername);
        }

        if ($ouser) {
            $roles = $this->getRoles($ouser);
            // Create a hashed password
            if (is_string($password)) {
                $password = $this->hash($password, $ouser->salt);
            }
            //如果用户被允许登录且密码匹配，则完成登录
            if (isset($roles['login']) && $ouser->opassword === $password) {

                // 如果需要保存登录令牌到cookie以实现自动登录
                if ($remember === true) {
                    // 创建一个新的自动登录令牌
                    $token = new OfflineTokens();
                    $token->ouser_id = intval($ouser->ouser_id);
                    $token->user_agent = sha1($this->getRequest()->getUserAgent());
                    $token->token = $this->createToken();
                    $token->created = time();
                    $token->expires = time() - 3600 - $this->lifetime;
                    if ($token->create() === true) {
                        //设置自动登录cookie
                        $this->getCookies()->set($this->authKey);
                        $this->getCookies()->set($this->authKey, $token->token, $token->expires, '/', false,
                            $this->domain, true);
                    }
                }

                // 更新登录次数、时间、IP信息
                $this->completeLogin($ouser, $password);

                // 生成新的session_id
                session_regenerate_id();

                // 用户信息保存到session
                $this->getSession()->set($this->sessionKey, $ouser);
                // 用户角色权限信息保存到session
                if ($this->sessionRoles) {
                    $this->getSession()->set($this->sessionRoles, $roles);
                }
                return true; // 登录成功
            } else {
                return false; // 登录失败
            }
        }
        return null; // 没找到用户
    }
    /**
     * 取角色权限
     *
     * @todo 还没有实现
     * @param \Pari\Models\Member $user
     * @return type
     */
    public function getRoles($ouser)
    {
        $roles = array();

        if ($ouser instanceof OfflineUser) {
            if ($ouser->is_work == 1) {
                $roles['login'] = true; //允许登录
            }
        }
        return $roles;
    }
    /**
     *
     * @param string $password
     * @return string
     */
    public function hash($password, $salt = null)
    {
        if (empty($salt)) {
            $password = trim($password);
        } else {
            $password = md5(trim($password) . $salt);
        }
        return $password;
    }

    /**
     * 线下用户退出登录，删除session相关变量，cookie中自动登录令牌
     *
     * @param boolean $destroy
     * @param boolean $logoutAll
     */
    public function signOut($destroy = false, $logoutAll = false)
    {
        if ($this->getCookies()->has($this->authKey)) {
            $cookieToken = $this->getCookies()->get($this->authKey)->getValue();

            // 删除cookie中的自动登录令牌
            $this->getCookies()->set($this->authKey);
            $this->getCookies()->set($this->authKey, "", time() - 3600, '/', false,
                $this->domain, true);
            $this->getCookies()->delete($this->authKey);

            // 当前使用的token
            $token = OfflineTokens::findFirstByToken($cookieToken);
            if ($token) {
                if ($logoutAll) {
                    // 删除所有关联member_id的token
                    $allTokens = OfflineTokens::find(array(
                        'ouser_id=:ouser_id:',
                        'bind' => array(':ouser_id' => $token->ouser_id)
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
            if ($this->sessionRoles) {
                $this->getSession()->remove($this->sessionRoles);
            }
            // 重新生成session_id
            session_regenerate_id();
        }

        // Double check
        return !$this->loggedIn();
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
            /* @var $token OfflineTokens */
            $token = OfflineTokens::findFirstByToken($cookieToken);
            // 如果存在这个token
            if ($token) {
                // 通过外键关联查询到属于这个token的用户信息
                $ouser = $token->getOfflineUser();

                // 取用户的角色权限信息
                $roles = $this->getRoles($ouser);

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

                    // 更新登录次数、时间、IP信息
                    $this->completeLogin($ouser);

                    // 重新生成session id
                    session_regenerate_id();

                    // 保存登录后的用户信息到session
                    $this->getSession()->set($this->sessionKey, $ouser);
                    // 保存用户的角色权限信息到session

                    if ($this->sessionRoles) {
                        $this->getSession()->set($this->sessionRoles, $roles);
                    }

                    // 基于cookie的自动登录成功
                    return $ouser;
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
     * 生成自动登录令牌
     *
     * @return string
     */
    private function createToken()
    {
        do {
            $token = sha1(uniqid(\Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM, 32), true));
        } while (OfflineTokens::findFirstByToken($token));

        return $token;
    }
    /**
     * 登录成功后，登录次数加1，更新最后登录时间
     *
     * @param \Pari\Models\Member $user
     * @param string $password 明文密码
     */
    private function completeLogin(OfflineUser $ouser, $password = '')
    {
        //更新最后登录时间
        $ouser->last_time = time();

        //保存
        $ouser->update();
    }
    /**
     * 判断是否具有角色权限，未完成
     *
     * Example .volt
     * {% if auth.loggedIn() %}
     *
     * @todo 实现角色权限检查
     * @param type $role 角色权限？
     * @return boolean
     */
    public function isSignIn($role = null)
    {
        // 从session中取用户信息
        $ouser = $this->getUser();
        if (!$ouser) {
            return false;
        }

        if ($ouser) {
            // 如果不需要角色权限检查
            if (!$role) {
                return true;
            }

            // 检查角色权限
            if ($this->sessionRoles &&
                $this->getSession()->has($this->sessionRoles)
            ) {
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
    }
    /**
     * 取当前登录用户，当session中不存在时，读取cookie尝试自动登录。
     *
     * Example .php
     * <code>
     * $user = $this->auth->getUser();
     * if($ouser instanceof OfflineUser){
     *     $id = $ouser.ouser_id;
     * }
     * </code>
     * Example .volt
     * <code>
     * {% if ouserAuth.getUser() %}
     * {% set ouser = ouserAuth.getUser %}
     * {{ ouser.member_id }}
     * </code>
     * @return Member|boolean 成功返回Member，失败返回false。
     */
    public function getUser()
    {
        $ouser = $this->getSession()->get($this->sessionKey);

        // check fro remembered login
        if (!$ouser) {
            $ouser = $this->autoLogin();
        }

        return $ouser;
    }
    public function refreshUser()
    {
        $user = $this->getSession()->get($this->sessionKey);

        if (!$user) {
            return null;
        } else {
            // 从数据库中读取用户信息
            $ouser = OfflineUser::findFirstByOuserId($user->member_id);
            $roles = $this->getRoles($ouser);

            // 重新生成session_id
            session_regenerate_id();

            // 保存用户信息到session
            $this->getSession()->set($this->sessionKey, $ouser);
            // 保存用户角色权限到session
            if ($this->sessionRoles) {
                $this->getSession()->set($this->sessionRoles, $roles);
            }

            return $ouser;
        }
    }

    /**
     * @return \Phalcon\Http\Response\CookiesInterface
     */
    public function getCookies()
    {
        // 由DI决定是否单例
        return DI::getDefault()->get('cookies');
    }

    /**
     * @return \Phalcon\Session\AdapterInterface
     */
    public function getSession()
    {
        // 由DI决定是否单例
        return DI::getDefault()->get('session');
    }

    /**
     * @return \Phalcon\Http\RequestInterface
     */
    public function getRequest()
    {
        // 由DI决定是否单例
        return DI::getDefault()->get('request');
    }
}