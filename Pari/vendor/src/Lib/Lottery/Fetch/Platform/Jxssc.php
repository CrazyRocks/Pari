<?php
/**
 * 江西时时彩采集类
 */
namespace Lottery\Fetch\Platform;

Class Jxssc extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 84;

    const _168KAI_ID = 1002;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'jxssc';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '09:10:00',
                'end' => '23:00:00',
                'interval' => 600
            )
        );
    }

    /**
     * 获取最新采集期号信息
     *
     * @return array(
                    'expect' => '141106036',
                    'opentime' => 1402143035
                )
     */
    public function getLatestAwardExpectInfo()
    {
        $start_time = microtime(true);
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://result.168kai.com/?code=1002&_=' . time(),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 10
        ));
        $content = curl_exec($ch);
        curl_close($ch);
        $content = ltrim($content, '(');
        $content = rtrim($content, ')');

        if ($content &&
            ($content = json_decode($content, true)) &&
            isset($content['nTerm']) && !empty($content['nTerm']) &&
            isset($content['nTermDT']) && !empty($content['nTermDT'])
        ) {
            $opentime = strtotime($content['nTermDT']);
            $this->sleep_seconds = $opentime - $this->nowTimestamp;
            if ($this->sleep_seconds > 60) {
                return false;
            }
            return array(
                'expect' => $content['nTerm'],
                'opentime' => $opentime
            );
        }

        return false;
    }

    /*public function getCurrentSaleExpectInfo($deadline = 30)
    {
        $start_time = microtime(true);
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://caipiao.163.com/order/preBet_periodInfoTime.html?gameEn=jxssc&cache=' . time(),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 10
        ));
        $content = curl_exec($ch);
        curl_close($ch);
        if ($content &&
            ($content = json_decode($content, true)) &&
            isset($content['secondsLeft']) && !empty($content['secondsLeft']) &&
            isset($content['previousPeriod']) && !empty($content['previousPeriod']) &&
            isset($content['currentPeriod']) && !empty($content['currentPeriod'])
        ) {
            list($now_usec, $now_sec) = explode(" ", microtime());
            $deadline = round($now_sec + $now_usec - $start_time + $content['secondsLeft'] / 1000) + $now_sec;
            return array(
                'status' => 1,
                'expect' => $content['currentPeriod'],
                'opentime' => $deadline + 180
            );
        }

        return array('status' => 0);
    }*/

    public function fetchAwardNumberInfo($expect)
    {
        if ($this->sleep_seconds > 0) {
            sleep($this->sleep_seconds);
        }

        //每10秒从www.168kai.com尝试获取开奖数据，共尝试30次
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 30; $i++) {
            $awardNumberInfo = $source->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每5秒从apiplus尝试获取开奖数据，共尝试20次
        $apiplus = new \Lottery\Fetch\Source\Apiplus('jxssc');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $apiplus->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每60秒从caipiao.163.com尝试获取开奖数据，共尝试20次
        $caipiao163 = new \Lottery\Fetch\Source\Caipiao163('jxssc');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $caipiao163->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(60);
        }

        return false;
    }

    public function getAwardNumberListByDate($date)
    {
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        $awardNumberList = $source->getAwardNumberListByDate($date);
        return $awardNumberList;
    }

    /**
     * 获取余下即将销售期号
     * @param int $deadline
     * @param int $expect_num
     */
    public function getSaleExpectList($deadline = 30, $expect_num = 120)
    {
    	$expect_num = 84;
        $expect_list = array();
        $fetch_time = time();
        $fetch_platform = new self($fetch_time);
        $currentSaleExpectInfo = $fetch_platform->getCurrentSaleExpectInfo($deadline);
        if (!$currentSaleExpectInfo['status']) {
            return array();
        }

        $expect_list[] = $currentSaleExpectInfo;
        $expect = intval(substr($currentSaleExpectInfo['expect'], -3));
        $opentime = $currentSaleExpectInfo['opentime'];
        for ($i = 1; $i < $expect_num; $i++) {
            $expect++;
            if ($expect > self::DAILY_OPEN_EXPECT_NUM) {
                $expect = 1;
                $date = date('Ymd', strtotime('+1 day', $opentime));
                $opentime = strtotime($date . ' 09:10:00');
            } else {
                $opentime = $opentime + 600;
            }
            // echo $this->formatExpect($expect, $opentime).'|'.date('Y-m-d H:i:s',$opentime).'+';
            //$expect = $this->formatExpect($expect, $opentime);
            $expect_list[] = array(
                'status' => 1,
                'expect' => $this->formatExpect($expect, $opentime),
                'opentime' => $opentime
            );
        }

        return $expect_list;
    }

    public function formatExpect($expect, $opentime)
    {
        return date('Ymd', $opentime) . str_pad($expect, 3, '0', STR_PAD_LEFT);
    }
}
