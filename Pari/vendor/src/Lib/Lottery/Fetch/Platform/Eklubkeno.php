<?php
/**
 * 斯诺伐克快乐彩采集类
 */
namespace Lottery\Fetch\Platform;

Class Eklubkeno extends AbstractKeno
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 174;

    protected $fetchAwardNumberInfoDelay = 0;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'eklubkeno';
    }

    public function getAwardTimeInfo()
    {
        // todo
        return array();
    }

    /**
     * 返回开盘情况
     *
     * @return Array
     */
    public function getOpeningInfo()
    {
        $openingInfo = array('isOpening' => true, 'estimateOpentime' => 0);
        $now_day = date('Y-m-d', $this->nowTimestamp);

        //关盘时间为05:55:00 - 10:59:59
        if ($this->nowTimestamp >= strtotime($now_day . ' 05:55:00') &&  $this->nowTimestamp <= strtotime($now_day . ' 10:59:59')) {
            $openingInfo = array(
                'isOpening' => false,
                'estimateOpentime' => $now_day . ' 11:00:00',
                'estimateOpenCountdown' => strtotime($now_day . ' 11:00:00') - $this->nowTimestamp
            );
        }

        return $openingInfo;
    }

    public function getLatestAwardExpectInfo()
    {
        //官网平均延迟2秒
        sleep(2);
		$currentSaleExpectInfo = $this->getCurrentSaleExpectInfo(0);
		if ($currentSaleExpectInfo['status'] != 1) {
		    return false;
		}

		$deviation = $this->nowTimestamp - $currentSaleExpectInfo['opentime'];
        if ($deviation >= -59 && $deviation <= 0) {
            $this->fetchAwardNumberInfoDelay = abs($deviation);
	        return array(
    		    'expect' => $this->formatExpect($currentSaleExpectInfo['expect'], $currentSaleExpectInfo['opentime']),
    		    'opentime' => $currentSaleExpectInfo['opentime']
    		);
	    }

		return false;
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟2秒左右
        $fetchAwardNumberInfoDelay = $this->fetchAwardNumberInfoDelay + 2;
        sleep($fetchAwardNumberInfoDelay);

        $awardNumberInfo = false;
        //每2秒从官网尝试获取开奖数据，共尝试10次
        for ($i = 0; $i < 10; $i++) {
            $awardNumberInfo = $this->fetchAwardNumberInfoByEklubkeno($expect);
            if (!empty($awardNumberInfo)) {
                return $awardNumberInfo;
            }
            sleep(2);
        }

        return false;
    }

    protected function fetchAwardNumberInfoByEklubkeno($expect)
    {
        $client = new \SoapClient("https://eklubkeno.etipos.sk/keno5service.asmx?WSDL");
        $lastDraw = $client->__soapCall('GetLastDraw', array());
        if (isset($lastDraw['DrawId']) &&
            $lastDraw['DrawId'] == $expect &&
            isset($lastDraw['DrawNumbers']) &&
            !empty($lastDraw['DrawNumbers'])
        ) {
            return array(
                'expect' => $expect,
                'number' => str_replace(',', ' ', $lastDraw['DrawNumbers']),
                'opendate' => strtotime(date('Y-m-d', $this->nowTimestamp)),
                'opentime' => 0
            );
        }

        return false;
    }

    public function getCurrentSaleExpectInfo($deadline = 15)
    {
        $sendStartTime = time();
        $client = new \SoapClient("https://eklubkeno.etipos.sk/keno5service.asmx?WSDL");
        $lastDraw = $client->__soapCall('GetLastDraw', array());
        if (isset($lastDraw['DrawId']) && isset($lastDraw['NextDrawCountdown'])) {
            return array(
    			'status' => 1,
    			'expect' => $lastDraw['DrawId'] + 1,
    			'opentime' => $this->nowTimestamp + $lastDraw['NextDrawCountdown'] + time() - $sendStartTime
    		);
        }

        return array('status' => 0);
    }

    public function getAwardNumberListByDate($date)
    {
        $awardNumberList = array();
        return $awardNumberList;
    }

    public function formatExpect($expect, $opentime)
    {
        return $expect;
    }
}
