<?php
namespace Pari\Models;
class PayBank extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $bank_id;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $shorten;

    /**
     *
     * @var string
     */
    public $logo;

    /**
     *
     * @var string
     */
    public $home;

    /**
     *
     * @var integer
     */
    public $epay;

    /**
     *
     * @var string
     */
    public $bankcode;

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
        return 'pay_bank';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return PayBank[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return PayBank
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
            'bank_id' => 'bank_id',
            'title' => 'title',
            'name' => 'name',
            'shorten' => 'shorten',
            'logo' => 'logo',
            'home' => 'home',
            'epay' => 'epay',
            'bankcode' => 'bankcode',
            'status' => 'status'
        );
    }

}
