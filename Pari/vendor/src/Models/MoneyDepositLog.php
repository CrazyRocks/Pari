<?php
namespace Pari\Models;

class MoneyDepositLog extends \Phalcon\Mvc\Model
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
    public $bank_card_id;

    /**
     *
     * @var string
     */
    public $bank_card_info;

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
        return 'money_deposit_log';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return MoneyDepositLog[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return MoneyDepositLog
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
            'bank_card_id' => 'bank_card_id',
            'bank_card_info' => 'bank_card_info',
            'remark' => 'remark',
            'order_num' => 'order_num',
            'seq_num' => 'seq_num',
            'addtime' => 'addtime',
            'updatetime' => 'updatetime',
            'operator_type' => 'operator_type',
            'operator_id' => 'operator_id',
            'operator_name' => 'operator_name',
            'status' => 'status'
        );
    }

}
