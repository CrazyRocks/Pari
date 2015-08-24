<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 5/26/2015
 * Time: 9:46 PM
 */

namespace Pari\Home\Controllers;

use Pari\Models\User;

class TestController extends ControllerBase
{


    public function indexAction()
    {

    }


    public function checkAction($key = NULL, $value = null)
    {
        var_dump($this->security->checkToken($key, $value));
    }

    public function redisAction()
    {
        $this->redis->set('sss', 'adsfdsdf');
        echo $this->redis->get('sss');
        $this->redis->del('sss');
        $this->redis->delete('sss');
    }

    public function mongoAction()
    {
        echo \Utils\Func::getClientIp(1, 1);
    }


    public function sampleAction()
    {
        //直接使用服务
        $this->service->sample()->say();

    }


}