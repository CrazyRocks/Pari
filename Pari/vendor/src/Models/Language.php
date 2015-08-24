<?php
namespace Pari\Models;
class Language extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var string
     */
    public $language;

    /**
     *
     * @var string
     */
    public $local_name;

    /**
     *
     * @var string
     */
    public $abbreviation;

    /**
     *
     * @var string
     */
    public $image;

    /**
     *
     * @var integer
     */
    public $sort;

    /**
     *
     * @var integer
     */
    public $is_default;

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
        return 'language';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Language[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Language
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
            'code' => 'code',
            'language' => 'language',
            'local_name' => 'local_name',
            'abbreviation' => 'abbreviation',
            'image' => 'image',
            'sort' => 'sort',
            'is_default' => 'is_default',
            'status' => 'status'
        );
    }

}
