<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 5/26/2015
 * Time: 2:20 PM
 */

namespace Pari\Services\Auth;

use Pari\Models\User,
    Pari\Models\UserTokens,
    Phalcon\DI;
use Zend\Cache\Storage\Adapter\ZendServerDisk;
use Zend\Crypt\Utils;

/**
 * 用户认证
 * @package Pari\Services\Auth
 */
class UserAuth implements AuthInterface
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
     * 登录
     *
     * @param string|User $user
     * @param string $password
     * @param boolean $reUser
     * @return boolean
     */
    public function signIn($user, $password, $reUser = false)
    {
        if (!$user instanceof User) {
            $username = $user;
            if (!$username) {
                return null;
            }
            // Load the user
            $user = User::findFirstByUserName($username);
            if(!$user) {
                $user = User::findFirstByUserPhone($username);
            }
            if(!$user) {
                $user = User::findFirstByUserEmail($username);
            }
        }
        if ($user) {
            $roles = $this->getRoles($user);
            // Create a hashed password
            if (is_string($password)) {
                $hashPassword = $this->hash($password, $user->salt);
            }

            //如果用户被允许登录且密码匹配，则完成登录
            if (isset($roles['login']) && $user->User_passwd === $hashPassword) {


                // 早期密码是不加盐的，为了兼容早期代码
                // 旧密码验证通过后，保存加盐后的新密码
                if (empty($user->salt) && !empty($password)) {
                    $user->salt = mt_rand(1000, 100000);
                    $user->User_passwd = $this->hash($password, $user->salt);
                }

                $this->performLogin($user, $reUser, $roles);
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
     * @param \Pari\Models\User $user
     * @return array
     */
    public function getRoles($user)
    {
        $roles = array();

        if ($user instanceof User) {
            if ($user->User_state == 1) {
                $roles['login'] = true; //允许登录
            }
        }

        return $roles;
    }

    /**
     * 执行登录
     * @param $user User
     * @param $roles array
     * @param $reUser bool
     */
    public function performLogin($user, $roles, $reUser = false)
    {
        // 如果需要保存登录令牌到cookie以实现自动登录
        if ($reUser === true) {
            // 创建一个新的自动登录令牌
            $token = new UserTokens();
            $token->User_id = $user->User_id;
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

        // 更新登录次数、时间、IP信息
        $this->completeLogin($user);

        // 生成新的session_id
        session_regenerate_id();

        // 用户信息保存到session
        $this->getSession()->set($this->sessionKey, $user);
        // 用户角色权限信息保存到session
        if ($this->sessionRoles) {
            $this->getSession()->set($this->sessionRoles, $roles);
        }
    }

    /**
     *
     * @param string $password
     * @return string
     */
    public function hash($password, $salt = null)
    {
        if (empty($salt)) {
            $password = md5(trim($password));
        } else {
            $password = md5(md5(trim($password)) . $salt);
        }
        return $password;
    }

    /**
     * 退出登录，删除session相关变量，cookie中自动登录令牌
     *
     * @param boolean $destroy
     * @param boolean $logoutAll
     *
     * @return bool
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
            $token = UserTokens::findFirstByToken($cookieToken);
            if ($token) {
                if ($logoutAll) {
                    // 删除所有关联User_id的token
                    $allTokens = UserTokens::find(array(
                        'User_id=:User_id:',
                        'bind' => array(':User_id' => $token->User_id)
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
        $user = $this->getUser();
        if (!$user) {
            return false;
        }

        if ($user) {
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
     * if($user instanceof User){
     *     $id = $user.User_id;
     * }
     * </code>
     * Example .volt
     * <code>
     * {% if auth.getUser() %}
     * {% set user = auth.getUser %}
     * {{ user.User_id }}
     * </code>
     * @return User|boolean 成功返回User，失败返回false。
     */
    public function getUser()
    {
        $user = $this->getSession()->get($this->sessionKey);

        // check fro reUsered login
        if (!$user) {
            $user = $this->autoLogin();
        }

        return $user;
    }

    public function refreshUser()
    {
        $user = $this->getSession()->get($this->sessionKey);

        if (!$user) {
            return null;
        } else {
            // 从数据库中读取用户信息
            $user = User::findFirst($user->User_id);
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

    /**
     * 基于cookie的自动登录
     *
     * @return false 自动登录失败，User 成功
     */
    private function autoLogin()
    {
        // 用户浏览器中保存有token的cookie
        if ($this->getCookies()->has($this->authKey)) {
            $cookieToken = $this->getCookies()->get($this->authKey)->getValue();

            // 获取服务端保存的token
            /* @var $token UserTokens */
            $token = UserTokens::findFirstByToken($cookieToken);

            // 如果存在这个token
            if ($token) {
                // 通过外键关联查询到属于这个token的用户信息
                $user = $token->getUser();

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

                    // 更新登录次数、时间、IP信息
                    $this->completeLogin($user);

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
     * 生成自动登录令牌
     *
     * @return string
     */
    private function createToken()
    {
        do {
            $token = sha1(uniqid(\Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM, 32), true));
        } while (UserTokens::findFirstByToken($token));

        return $token;
    }

    /**
     * 登录成功后，登录次数加1，更新最后登录时间
     *
     * @param \Pari\Models\User $user
     * @param string $password 明文密码
     */
    private function completeLogin(User $user)
    {
        //更新登录次数
        $user->User_login_num = $user->User_login_num + 1;

        //更新登录时间
        $user->User_old_login_time = $user->User_login_time;
        $user->User_login_time = time();

        //更新登录IP
        $user->User_old_login_ip = $user->User_login_ip;
        $user->User_login_ip = $this->getRequest()->getClientAddress();

        //保存
        $user->save();
    }

    // =============================================== setter getter

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

    public function test(){
        echo '111111111';
    }

}
