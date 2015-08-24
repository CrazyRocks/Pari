<?php
namespace Pari\Models;

class Order extends BaseModel
{

    /**
     *
     * @var integer
     */
    public $oid;

    /**
     *
     * @var integer
     */
    public $uid;

    /**
     *
     * @var integer
     */
    public $banker_id;

    /**
     *
     * @var double
     */
    public $amount;

    /**
     *
     * @var integer
     */
    public $type;

    /**
     *
     * @var integer
     */
    public $pid;

    /**
     *
     * @var double
     */
    public $stake;

    /**
     *
     * @var integer
     */
    public $multiple;

    /**
     *
     * @var double
     */
    public $rate;

    /**
     *
     * @var double
     */
    public $bonus;

    /**
     *
     * @var double
     */
    public $banker_gain;

    /**
     *
     * @var double
     */
    public $brokerage;

    /**
     *
     * @var string
     */
    public $expect;

    /**
     *
     * @var integer
     */
    public $gid;

    /**
     *
     * @var double
     */
    public $refund;

    /**
     *
     * @var integer
     */
    public $refund_uid;

    /**
     *
     * @var integer
     */
    public $time;

    /**
     *
     * @var integer
     */
    public $addtime;

    /**
     *
     * @var integer
     */
    public $updatetime;

    /**
     *
     * @var integer
     */
    public $ip;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'order';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Order[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Order
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
            'oid' => 'oid',
            'uid' => 'uid',
            'banker_id' => 'banker_id',
            'amount' => 'amount',
            'type' => 'type',
            'pid' => 'pid',
            'stake' => 'stake',
            'multiple' => 'multiple',
            'rate' => 'rate',
            'bonus' => 'bonus',
            'banker_gain' => 'banker_gain',
            'brokerage' => 'brokerage',
            'expect' => 'expect',
            'gid' => 'gid',
            'refund' => 'refund',
            'refund_uid' => 'refund_uid',
            'time' => 'time',
            'addtime' => 'addtime',
            'updatetime' => 'updatetime',
            'ip' => 'ip',
            'status' => 'status'
        );
    }

}
