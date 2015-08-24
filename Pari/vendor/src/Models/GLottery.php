<?php
namespace Pari\Models;
class GLottery extends \Phalcon\Mvc\Model
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
    public $type;

    /**
     *
     * @var integer
     */
    public $is_custom;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $short_title;

    /**
     *
     * @var string
     */
    public $code_list;

    /**
     *
     * @var integer
     */
    public $award_ball;

    /**
     *
     * @var string
     */
    public $home;

    /**
     *
     * @var string
     */
    public $info;

    /**
     *
     * @var integer
     */
    public $deadline;

    /**
     *
     * @var integer
     */
    public $rate_min;

    /**
     *
     * @var integer
     */
    public $rate_max;

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
    public $status;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'g_lottery';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return GLottery[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return GLottery
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
            'type' => 'type',
            'is_custom' => 'is_custom',
            'name' => 'name',
            'title' => 'title',
            'short_title' => 'short_title',
            'code_list' => 'code_list',
            'award_ball' => 'award_ball',
            'home' => 'home',
            'info' => 'info',
            'deadline' => 'deadline',
            'rate_min' => 'rate_min',
            'rate_max' => 'rate_max',
            'addtime' => 'addtime',
            'updatetime' => 'updatetime',
            'status' => 'status'
        );
    }

}
