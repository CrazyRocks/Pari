<?php
/**
 * PT五分彩(自定义时时彩)采集类
 */ 
namespace Lottery\Fetch\Platform;

Class Ptwfc extends Ptssc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 288;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'ptwfc';
    }
    
    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '00:00:00',
                'end' => '23:55:00',
                'interval' => 300
            )
        );
    }
}
