<?php
/**
 * 快乐彩采集抽象类
 */ 
namespace Lottery\Fetch\Platform;

abstract class AbstractKeno implements PlatformInterface
{
    /**
     * 当前时间戳
     *
     * @return string
     */
    protected $nowTimestamp;
    
    public function __construct($nowTimestamp = null)
    {
        $this->nowTimestamp = $nowTimestamp ?: time();
    }

    /**
     * 获取余下即将销售期号
     * @param int $deadline
     * @param int $expect_num
     */
    public function getSaleExpectList($deadline = 30, $expect_num = 120)
    {
        return array();
    }
}
