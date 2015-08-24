<?php
namespace Pari\Models;
class UserFriend extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $recommend_uid;

    /**
     *
     * @var integer
     */
    public $friend_uid;

    /**
     *
     * @var integer
     */
    public $addtime;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user_friend';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserFriend[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserFriend
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
            'recommend_uid' => 'recommend_uid',
            'friend_uid' => 'friend_uid',
            'addtime' => 'addtime'
        );
    }

}
