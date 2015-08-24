<?php
/**
 * 用户数据处理
 * @author tom
 * @data 2015-6-9 上午11:07:48
 */
namespace Pari\Data;

use Pari\Models\User,
    Utils\Func;

class UserData
{
    public function run(){
        return new self();
    }
    /**
     * 根据用户名查找用户
     * @author tom
     * @param string $name
     * @return \Pari\Models\User
     */
    public function findUserByName($name)
    {
        // Load the user
        $data = User::findFirst([
            'name=:name:',
            'bind' => ['name' => $name]
        ]);
        return $data;
    }

    /**
     * 根据email查找用户
     * @param string $email
     * @return \Pari\Models\User
     */
    public function findUserByEmail($email)
    {
        // Load the user
        $data = User::findFirst([
            'email=:email:',
            'bind' => ['email' => $email]
        ]);
        return $data;
    }

    public function testSignIn($user,$password){
        $validate = User::findFirst("name = $user OR email = $user");
        if($validate == false) {
            return false;
        }
        return $this->hash($validate->password,$validate->salt) == $this->hash($password,$validate->salt) ? true : false;
    }

    public function testSignUp($name, $password, $email)
    {
        if (User::findFirst("name = $name OR email = $email")) {
            return false;
        }
        //制造SALT值
        $salt = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->salt = $salt;
        $user->reg_time = $user->last_sign_time =  time();
        $user->last_sign_ip = Func::getClientIp(1);
        return $user->create() === true ? true : false;
    }



    public function signUp($data)
    {
        // 用户名或email被占用了
        if ($this->findUserByName($data['name']) || $this->findUserByName($data['email'])) {
            return false;
        }

        $salt = mt_rand(1000, 100000);
        // auth hash
        $password = $this->hash($data['password'], $salt);
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->salt = $salt;
        $user->password = $password;
        $user->reg_time = time();
        $user->last_sign_time = time();
        $user->signin_count = 0;
        $user->region = 0;
        $user->status = 0; // 0.未激活 1.激活并状态正常 2.已激活,未填写真实资料 3.用户主动申请锁定 4.作弊被锁定 5.注销
        $user->phone = 1;
        $user->last_sign_ip = 123123;

        if ($user->save() === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @todo 用户密码加密
     * @param $password
     * @param null $salt
     * @return string
     */
    public function hash($password, $salt = null)
    {
        $password = empty($salt) ? md5(trim($password)) : md5(md5(trim($password)) . $salt);

        return $password;
    }
}

?>
