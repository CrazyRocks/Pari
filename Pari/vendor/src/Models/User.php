<?php
namespace Pari\Models;

use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\StringLength;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\Email as Email;
use Pari\Models\BaseModel;
use Utils\Func;

class User extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $uid;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $salt;

    /**
     *
     * @var string
     */
    public $phone;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var integer
     */
    public $region;

    /**
     *
     * @var integer
     */
    public $reg_time;

    /**
     *
     * @var string
     */
    public $last_sign_ip;

    /**
     *
     * @var integer
     */
    public $last_sign_time;

    /**
     *
     * @var integer
     */
    public $signin_count;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $this->validate(
            new Email(
                array(
                    'field'    => 'email',
                    'required' => true,
                )
            )
        );

        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return User[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return User
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap()
    {
        return array(
            'uid' => 'uid',
            'name' => 'name',
            'email' => 'email',
            'password' => 'password',
            'salt' => 'salt',
            'phone' => 'phone',
            'status' => 'status',
            'region' => 'region',
            'reg_time' => 'reg_time',
            'last_sign_ip' => 'last_sign_ip',
            'last_sign_time' => 'last_sign_time',
            'signin_count' => 'signin_count'
        );
    }

    /**
     * 新增记录并在验证字段之前触发
     */
    public function beforeValidationOnCreate()
    {
        $this->reg_time = $this->last_sign_time = time();
        $this->last_sign_ip = Func::getClientIp(1);
    }

    /**
     * 修改记录并在验证字段之前触发
     */
    public function beforeValidationOnUpdate()
    {
        $this->last_sign_time = time();
        $this->last_sign_ip = Func::getClientIp(1);
    }

    /**
     * 数据保存之前触发
     */
    public function beforeSave()
    {

    }

}
