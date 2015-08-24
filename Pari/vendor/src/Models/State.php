<?php
namespace Pari\Models;
class State extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id_state;

    /**
     *
     * @var integer
     */
    public $id_country;

    /**
     *
     * @var integer
     */
    public $id_zone;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $iso_code;

    /**
     *
     * @var integer
     */
    public $tax_behavior;

    /**
     *
     * @var integer
     */
    public $active;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'state';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return State[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return State
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
            'id_state' => 'id_state',
            'id_country' => 'id_country',
            'id_zone' => 'id_zone',
            'name' => 'name',
            'iso_code' => 'iso_code',
            'tax_behavior' => 'tax_behavior',
            'active' => 'active'
        );
    }

}
