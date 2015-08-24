<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 6/18/2015
 * Time: 5:48 PM
 */

namespace Pari\Services;

/**
 * 检测用户
 * Class detect
 * @package Pari\Services
 */
class Filter
{
    /**
     * @TODO 去除所有的额外符号, 只保留 @-_. 四种符号
     * @param $str
     * @return mixed
     */
    public function FilterStr($str)
    {
        return preg_replace('/[^\w\@\-\.]/', '', strtolower($str));
    }
}