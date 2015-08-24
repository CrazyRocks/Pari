<?php
namespace Pari\Models;

class UserProfile extends BaseModel
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
    public $fullname;

    /**
     *
     * @var integer
     */
    public $type;

    /**
     *
     * @var integer
     */
    public $level;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var integer
     */
    public $withdraw_password;

    /**
     *
     * @var string
     */
    public $secure_question;

    /**
     *
     * @var string
     */
    public $secure_answer;

    /**
     *
     * @var string
     */
    public $remark;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user_profile';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserProfile[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserProfile
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
            'fullname' => 'fullname',
            'type' => 'type',
            'level' => 'level',
            'status' => 'status',
            'withdraw_password' => 'withdraw_password',
            'secure_question' => 'secure_question',
            'secure_answer' => 'secure_answer',
            'remark' => 'remark'
        );
    }

}
