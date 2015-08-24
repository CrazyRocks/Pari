<?php
namespace Pari\Models;
use Phalcon\Mvc\Model\Validator\Email as Email;

class Admin extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $dept_id;

    /**
     *
     * @var integer
     */
    public $type;

    /**
     *
     * @var string
     */
    public $username;

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
     * @var integer
     */
    public $salt;

    /**
     *
     * @var integer
     */
    public $addtime;

    /**
     *
     * @var integer
     */
    public $status;

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
        return 'admin';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Admin[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Admin
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
            'id' => 'id',
            'dept_id' => 'dept_id',
            'type' => 'type',
            'username' => 'username',
            'name' => 'name',
            'email' => 'email',
            'password' => 'password',
            'salt' => 'salt',
            'addtime' => 'addtime',
            'status' => 'status'
        );
    }

}
