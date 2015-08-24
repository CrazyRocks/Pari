<?php
/**
 * 韩国快乐彩采集类
 */
namespace Lottery\Fetch\Platform;

Class Jlottokeno extends AbstractKeno
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 879;

    const _168KAI_ID = 10051;

    protected $fetchAwardNumberInfoDelay = 0;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'jlottokeno';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '00:00:00',
                'end' => '04:58:00',
                'interval' => 90
            ),
            array(
                'start' => '07:00:00',
                'end' => '23:58:30',
                'interval' => 90
            )
        );
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

        //关盘时间为05:00:00 - 06:59:59
        if ($this->nowTimestamp >= strtotime($now_day . ' 05:00:00') &&  $this->nowTimestamp <= strtotime($now_day . ' 06:59:59')) {
            $openingInfo = array(
                'isOpening' => false,
                'estimateOpentime' => $now_day . ' 07:00:00',
                'estimateOpenCountdown' => strtotime($now_day . ' 07:00:00') - $this->nowTimestamp
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
        if ($deviation >= -70 && $deviation < 0) {
            $this->fetchAwardNumberInfoDelay = abs($deviation);
	        return array(
    		    'expect' => $this->formatExpect($currentSaleExpectInfo['expect'], $currentSaleExpectInfo['opentime']),
	            'opendate' => strtotime(date('Y-m-d', $this->nowTimestamp)),
    		    'opentime' => $currentSaleExpectInfo['opentime']
    		);
	    }

		return false;
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟2秒左右
        $fetchAwardNumberInfoDelay = $this->fetchAwardNumberInfoDelay + 2;
        if ($fetchAwardNumberInfoDelay > 0) {
            sleep($fetchAwardNumberInfoDelay);
        }

        //每5秒从www.168kai.com尝试获取开奖数据，共尝试10次
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 10; $i++) {
            $awardNumberInfo = $source->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每2秒从官网尝试获取开奖数据，共尝试10次
        for ($i = 0; $i < 10; $i++) {
            $awardNumberInfo = $this->fetchAwardNumberInfoByJlotto($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(2);
        }

        return false;
    }

    protected function fetchAwardNumberInfoByJlotto($expect)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://www.jlotto.kr/keno.aspx?method=kenoWinNoList',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        if (!empty($content)) {
            $doc = new \DOMDocument();
	        @$doc->loadHTML($content);
	        $form = $doc->getElementById('frm');
	        if ($form) {
				$table = $form->getElementsByTagName('table')->item(1);
				if ($table) {
					$tbody = $table->getElementsByTagName('tbody')->item(0);
					if ($tbody) {
						$trs = $tbody->getElementsByTagName('tr');
						foreach ($trs as $tr) {
							$tds = $tr->getElementsByTagName('td');
							$td1 = $tds->item(1);
							$td2 = $tds->item(2);
							if ($td1 && $td2) {
								if ($expect == trim($td1->nodeValue)) {
								    return array(
                                        'expect' => $expect,
                                        'number' => str_replace(',', ' ', trim($td2->nodeValue)),
                                        'opentime' => 0
                                    );
								}
							}
						}
					}
				}
			}
	    }

        return false;
    }

    public function getCurrentSaleExpectInfo($deadline = 15)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://www.jlotto.kr/keno.aspx?method=kenoWinNoList',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        if (!empty($content)) {
            $doc = new \DOMDocument();
	        @$doc->loadHTML($content);
	        $form = $doc->getElementById('frm');
    		if ($form) {
    			$table = $form->getElementsByTagName('table')->item(1);
    			if ($table) {
    				$tbody = $table->getElementsByTagName('tbody')->item(0);
    				if ($tbody) {
    					$tr = $tbody->getElementsByTagName('tr')->item(0);
    					if ($tr) {
    						$tds = $tr->getElementsByTagName('td');
    						$td0 = $tds->item(0);
    						$td1 = $tds->item(1);
    						if ($td0 && $td1) {
    						    $opentime = strtotime(trim($td0->nodeValue));
    						    //关盘前最后一期开奖时间间隔两小时90秒
    						    if (date('G', $opentime) == 5 && date('i', $opentime) == 58 && date('s', $opentime) == 30) {
    						        $opentime += 7290;
    						    //其他时间开奖间隔90秒
    						    } else {
    						        $opentime += 90;
    						    }
    						    //韩国比中国快3600秒
    						    $opentime -= 3600;
    							return array(
                        			'status' => 1,
                        			'expect' => trim($td1->nodeValue) + 1,
                        			'opentime' => $opentime
                        		);
    						}
    					}
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
        return $awardNumberList;
    }

    public function formatExpect($expect, $opentime)
    {
        return $expect;
    }
}
