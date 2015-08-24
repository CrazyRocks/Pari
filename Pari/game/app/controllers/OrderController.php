<?php
/**
 * 首页
 * User: Admin
 * Date: 2015/1/15
 * Time: 12:23
 */
namespace Pari\Game\Controllers;

/**
 * Class OrderController
 * @package Pari\Game\Controllers
 * @author Linvas
 */
class OrderController extends ControllerBase
{
    /**
     * @var string 订单类型
     */
    public $type;


    public function IndexAction(){
        echo 'This is Bet';
    }

    /**
     * @param string $type
     */
    public function betAction($type){
        $this->service->order()->bet()
    }

    /**
     *
     * @todo 检查订单
     */
    public function checkAction(){

    }
    /**
     * 取消订单
     */
    public function cancel($oid){

    }

}