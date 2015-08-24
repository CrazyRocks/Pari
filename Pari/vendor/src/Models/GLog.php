<?php

namespace Pari\Models;

class GLog extends \Phalcon\Mvc\Model
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
    public $gid;

    /**
     *
     * @var integer
     */
    public $uid;

    /**
     *
     * @var integer
     */
    public $bid;

    /**
     *
     * @var double
     */
    public $bet;

    /**
     *
     * @var integer
     */
    public $result;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'g_log';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return GLog[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return GLog
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
            'gid' => 'gid',
            'uid' => 'uid',
            'bid' => 'bid',
            'bet' => 'bet',
            'result' => 'result'
        );
    }

}
