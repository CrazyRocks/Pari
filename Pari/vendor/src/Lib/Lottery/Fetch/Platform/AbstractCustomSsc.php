<?php
/**
 * 自定义时时彩彩采集抽象类
 */
namespace Lottery\Fetch\Platform;

abstract class AbstractCustomSsc extends AbstractSsc
{
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
        $expect = $opentime = 0;
        foreach ($this->getAwardTimeInfo() as $item) {
            $start = strtotime($item['start'], $this->nowTimestamp);
            $end = strtotime($item['end'], $this->nowTimestamp);
            for ($i = $start; $i <= $end; $i += $item['interval']) {
                $expect++;
                $deviation = $this->nowTimestamp - $i;
                $minDeviation = isset($item['minDeviation']) ? $item['minDeviation'] : 0;
                $maxDeviation = isset($item['maxDeviation']) ? $item['maxDeviation'] : 30;
                if ($deviation >= $minDeviation && $deviation <= $maxDeviation) {
                    $opentime = $i;
                    break 2;
                }
            }
        }

        if (!$opentime) {
            return false;
        }

        return array(
            'expect' => $this->formatExpect($expect, $opentime),
            'opentime' => $opentime
        );
    }

    /**
     * 获取当前销售期号信息
     *
     * @return array(
                   'status' => 1, //1 成功 0 失败
                   'expect' => '141106036',
                   'opentime' => 1402143035
               )
     */
    public function getCurrentSaleExpectInfo($deadline = 15)
    {
        $countTimestamp = $this->nowTimestamp + $deadline;
		$expect = $opentime = 0;
		$awardTimeInfo = $this->getAwardTimeInfo();
		foreach ($awardTimeInfo as $item) {
		    $start = strtotime($item['start'], $this->nowTimestamp);
		    $end = strtotime($item['end'], $this->nowTimestamp);
			for ($i = $start; $i <= $end; $i += $item['interval']) {
			    $expect++;
			    if ($countTimestamp < $i) {
			        $opentime = $i;
			        break 2;
			    }
			}
		}

		if (!$opentime) {
		    //当日最后一期后当前销售期号为次日第一期
		    if (isset($awardTimeInfo[0]['start'])) {
		        $expect = 1;
		        $opentime = strtotime(date('Y-m-d', strtotime('+1 day', $this->nowTimestamp)) . ' ' . $awardTimeInfo[0]['start']);
		    } else {
		        return array('status' => 0);
		    }
		}

		return array(
		    'status' => 1,
		    'expect' => $this->formatExpect($expect, $opentime),
		    'opentime' => $opentime
		);
    }
}
