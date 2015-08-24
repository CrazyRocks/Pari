<?php
namespace Pari\Models;
class CurrencyRelate extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $currency_id;

    /**
     *
     * @var integer
     */
    public $region_id;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'currency_relate';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return CurrencyRelate[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return CurrencyRelate
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
            'currency_id' => 'currency_id',
            'region_id' => 'region_id'
        );
    }

}
