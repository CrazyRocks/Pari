<?php
namespace Pari\Models;

class MoneyWithdrawLog extends \Phalcon\Mvc\Model
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
    public $uid;

    /**
     *
     * @var double
     */
    public $amount;

    /**
     *
     * @var integer
     */
    public $bankcardid;

    /**
     *
     * @var string
     */
    public $info;

    /**
     *
     * @var string
     */
    public $remark;

    /**
     *
     * @var string
     */
    public $order_num;

    /**
     *
     * @var string
     */
    public $seq_num;

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
    public $operator_type;

    /**
     *
     * @var integer
     */
    public $operator_id;

    /**
     *
     * @var string
     */
    public $operator_name;

    /**
     *
     * @var string
     */
    public $notify;

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
        return 'money_withdraw_log';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return MoneyWithdrawLog[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return MoneyWithdrawLog
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
            'uid' => 'uid',
            'amount' => 'amount',
            'bankcardid' => 'bankcardid',
            'info' => 'info',
            'remark' => 'remark',
            'order_num' => 'order_num',
            'seq num' => 'seq num',
            'addtime' => 'addtime',
            'updatetime' => 'updatetime',
            'operator_type' => 'operator_type',
            'operator_id' => 'operator_id',
            'operator_name' => 'operator_name',
            'notify' => 'notify',
            'status' => 'status'
        );
    }

}
