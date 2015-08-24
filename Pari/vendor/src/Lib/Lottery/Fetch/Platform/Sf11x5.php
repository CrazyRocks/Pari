<?php
/**
 * 三分11选5(自定义11选5)采集类
 */
namespace Lottery\Fetch\Platform;

Class Sf11x5 extends Pt11x5
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 480;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'sf11x5';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '00:00:00',
                'end' => '23:57:00',
                'interval' => 180
            )
        );
    }
}
