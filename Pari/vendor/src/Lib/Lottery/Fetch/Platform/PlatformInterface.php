<?php
/**
 * 采集彩种类接口 , 必须统一按照此接口
 */
namespace Lottery\Fetch\Platform;

interface PlatformInterface
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * 获取开奖时间信息
     *
     * @return array(
                   array(
                       'start' => '00:00:00',
                       'end' => '02:00:00',
                       'interval' => 300
                   ),
                   array(
                       'start' => '10:00:00',
                       'end' => '22:00:00',
                       'interval' => 600
                   ),
                   array(
                       'start' => '22:05:00',
                       'end' => '23:55:00',
                       'interval' => 300
                   ),
               )
     */
    public function getAwardTimeInfo();

    /**
     * 获取最新采集期号信息
     *
     * @return array(
                   'expect' => '141106036',
                   'opentime' => 1402143035
               )
     */
    public function getLatestAwardExpectInfo();

    /**
     * 获取当前销售期号信息
     * @param int $deadline 截止时间
     *
     * @return array(
                   'expect' => '141106036',
                   'opentime' => 1402143035
               )
     */
    public function getCurrentSaleExpectInfo($deadline);

    /**
     * 采集开奖结果
     *
     * @param string $expect 期号
     * @return array(
                   'number' => '2 6 6 1 4',
                   'expect' => '141106036',
                   'opentime' => 1402143035
               )
     */
    public function fetchAwardNumberInfo($expect);

    /**
     * 格式化期号，以符合ptbet平台
     *
     * @param string $expect 期号
     * @return string
     */
    public function formatExpect($expect, $opentime);
}
