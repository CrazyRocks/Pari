<?php
namespace Pari\Models;
class MoneyBrokerageLog extends \Phalcon\Mvc\Model
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
    public $oid;

    /**
     *
     * @var integer
     */
    public $gid;

    /**
     *
     * @var integer
     */
    public $brokerage;

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
        return 'money_brokerage_log';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return MoneyBrokerageLog[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return MoneyBrokerageLog
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
            'oid' => 'oid',
            'gid' => 'gid',
            'brokerage' => 'brokerage',
            'addtime' => 'addtime'
        );
    }

}
