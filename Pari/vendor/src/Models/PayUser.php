<?php
namespace Pari\Models;
class PayUser extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $uid;

    /**
     *
     * @var integer
     */
    public $bank_id;

    /**
     *
     * @var double
     */
    public $amount;

    /**
     *
     * @var double
     */
    public $given_amount;

    /**
     *
     * @var double
     */
    public $bet;

    /**
     *
     * @var integer
     */
    public $deposit_count;

    /**
     *
     * @var double
     */
    public $deposit_amount;

    /**
     *
     * @var double
     */
    public $withdraw_amount;

    /**
     *
     * @var double
     */
    public $bonus;

    /**
     *
     * @var double
     */
    public $refund;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'pay_user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return PayUser[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return PayUser
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
            'bank_id' => 'bank_id',
            'amount' => 'amount',
            'given_amount' => 'given_amount',
            'bet' => 'bet',
            'deposit_count' => 'deposit_count',
            'deposit_amount' => 'deposit_amount',
            'withdraw_amount' => 'withdraw_amount',
            'bonus' => 'bonus',
            'refund' => 'refund'
        );
    }

}
