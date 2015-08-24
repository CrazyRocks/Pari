<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 6/18/2015
 * Time: 5:48 PM
 */

namespace Pari\Services;

use Pari\Models\Language;


/**
 * 检测用户
 * Class detect
 * @package Pari\Services
 */
class Utils extends BaseService
{
    /**
     * @TODO 获取客户IP
     * @param int $type 1.获取长地址(默认), 0.获取普通地址,例如:127.0.0.1
     * @param bool|true $adv
     */
    public function getClientIp($type = 1, $adv = true)
    {
        \Utils\Func::getClientIp($type, $adv);
    }



    //检测账号安全
    public function security()
    {


    }


}