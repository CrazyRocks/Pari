<?php
/**
 * 澳洲快乐彩采集类
 */
namespace Lottery\Fetch\Platform;

Class Acttabkeno extends AbstractKeno
{
    const _168KAI_ID = 10045;

    protected $fetchAwardNumberInfoDelay = 0;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'acttabkeno';
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

        $now_w = date('w', $this->nowTimestamp);
        //【周一到周四】关盘时间：00:30:00 - 05:44:59
        if ($now_w >= 1 && $now_w <= 4) {
            $close_start_time = ' 00:30:00';
            $close_end_time = ' 05:44:59';
            $open_start_time = ' 05:45:00';
            //【周五到周六】关盘时间：01:30:00 - 05:44:59
        } else if ($now_w >= 5 && $now_w <= 6) {
            $close_start_time = ' 01:30:00';
            $close_end_time = ' 05:44:59';
            $open_start_time = ' 05:45:00';
            //【星期天】关盘时间：00:30:00 - 07:44:59
        } else {
            $close_start_time = ' 00:30:00';
            $close_end_time = ' 07:44:59';
            $open_start_time = ' 07:45:00';
        }

        //关盘时间为05:00:00 - 06:59:59
        if ($this->nowTimestamp >= strtotime($now_day . $close_start_time) &&  $this->nowTimestamp <= strtotime($now_day . $close_end_time)) {
            $openingInfo = array(
                'isOpening' => false,
                'estimateOpentime' => $now_day . $open_start_time,
                'estimateOpenCountdown' => strtotime($now_day . $open_start_time) - $this->nowTimestamp
            );
        }

        return $openingInfo;
    }

    public function getLatestAwardExpectInfo()
    {
		$currentSaleExpectInfo = $this->getCurrentSaleExpectInfo(0);
		if ($currentSaleExpectInfo['status'] != 1) {
		    return false;
		}

		$deviation = $this->nowTimestamp - $currentSaleExpectInfo['opentime'];
        if ($deviation >= -59 && $deviation <= 0) {
            $this->fetchAwardNumberInfoDelay = abs($deviation);
	        return array(
    		    'expect' => $currentSaleExpectInfo['expect'],
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

        $fetchExpect = substr($expect, 8);

        //每2秒从官网尝试获取开奖数据，共尝试10次
        for ($i = 0; $i < 10; $i++) {
            $awardNumberInfo = $this->fetchAwardNumberInfoByActtab($fetchExpect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['expect'] = $expect;
                return $awardNumberInfo;
            }
            sleep(2);
        }

        //每10从www.168kai.com尝试获取开奖数据，共尝试20次
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $source->getAwardNumberInfo($fetchExpect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                $awardNumberInfo['expect'] = $expect;
                return $awardNumberInfo;
            }
            sleep(10);
        }

        return false;
    }

    protected function fetchAwardNumberInfoByActtab($expect)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://www.acttab.com.au/js/acttab-common/keno-draw',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        if (!empty($content)) {
            $content = json_decode($content, 0, 512, \JSON_BIGINT_AS_STRING);
            if (isset($content->draw_content)) {
                $doc = new \DOMDocument();
		        @$doc->loadHTML($content->draw_content);
		        $article = $doc->getElementsByTagName('article')->item(0);
    			if ($article) {
    				$numbersAndPeriod = explode(' ', trim($article->getAttribute('class')));
    				if (isset($numbersAndPeriod[20]) && $numbersAndPeriod[20] == $expect) {
    					return array(
                            'expect' => $expect,
                            'number' => implode(' ', array_slice($numbersAndPeriod, 0, 20)),
    					    'opendate' => strtotime(date('Y-m-d', $this->nowTimestamp)),
                            'opentime' => 0
                        );
    				}
    			}
    		}
        }

        return false;
    }

    public function getCurrentSaleExpectInfo($deadline = 15)
    {
        $sendStartTime = time();
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://www.acttab.com.au/js/acttab-common/keno-draw',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        if (!empty($content)) {
            $content = json_decode($content, 0, 512, \JSON_BIGINT_AS_STRING);
            if (isset($content->draw_content)) {
                $doc = new \DOMDocument();
		        @$doc->loadHTML($content->draw_content);
		        $article = $doc->getElementsByTagName('article')->item(0);
    			$timer_time = $doc->getElementsByTagName('strong')->item(3);
    			if ($article && $timer_time) {
    				$numbersAndPeriod = explode(' ', trim($article->getAttribute('class')));
    				$timer_times = explode(':', trim($timer_time->nodeValue));
    				if (isset($numbersAndPeriod[20]) && isset($timer_times[0]) && isset($timer_times[1])) {
    				    $opentime = $this->nowTimestamp + $timer_times[0] * 60 + $timer_times[1] + time() - $sendStartTime;
    					return array(
                			'status' => 1,
                			'expect' => $this->formatExpect($numbersAndPeriod[20] == 9999 ? 0 : $numbersAndPeriod[20] + 1, $opentime),
                			'opentime' => $opentime
                		);
    				}
    			}
    		}
        }

        return array('status' => 0);
    }

    public function getAwardNumberListByDate($date)
    {
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        $awardNumberList = $source->getAwardNumberListByDate($date);
        $date_prev = str_replace('-', '', $date);
        foreach ($awardNumberList as &$awardNumber) {
            $awardNumber['expect'] = $date_prev . $awardNumber['expect'];
        }
        return $awardNumberList;
    }

    public function formatExpect($expect, $opentime)
    {
        return date('Ymd', $opentime) . $expect;
    }
}
