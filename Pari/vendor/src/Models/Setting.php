<?php
namespace Pari\Models;
class Setting extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $value;

    /**
     *
     * @var string
     */
    public $description;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'setting';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Setting[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Setting
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
            'name' => 'name',
            'value' => 'value',
            'description' => 'description'
        );
    }

}
