<?php
/**
 * 彩票休市配置类
 */
namespace Lottery\Fetch;

class Rest
{
    protected static $noRestLotteryNames = array('hnwfc');

    //指定彩票休市时间配置（主要为跨天彩票处理）
    protected static $platformRestTimeInfo = array(
        //新疆时时彩休市时间配置
        'xjssc' => array(
            '2015' => array(
                'start' => '2015-02-18 02:00:01',
                'end' => '2015-02-25 02:00:01'
            )
        ),
        //重庆时时彩休市时间配置
        'cqssc' => array(
            '2015' => array(
                'start' => '2015-02-18 00:00:01',
                'end' => '2015-02-25 00:00:01'
            )
        )
    );

    //默认彩票休市时间配置
    protected static $defaultRestTimeInfo = array(
        '2015' => array(
            'start' => '2015-02-18 00:00:00',
            //注意休市结束时间为休市结束后第一天00:00:00，便于计算休市时间结束后第一期开奖时间
            'end' => '2015-02-25 00:00:00'
        )
    );

    /**
     * 获取休市时间列表
     * @return array
     */
    public static function getRestTimeInfo($platformName = null)
    {
        if (isset(self::$noRestLotteryNames[$platformName])) {
            return array();
        }

        if (isset(self::$platformRestTimeInfo[$platformName])) {
            return self::$platformRestTimeInfo[$platformName];
        } else if (isset(self::$defaultRestTimeInfo)) {
            return self::$defaultRestTimeInfo;
        } else {
            return array();
        }
    }
}
