<?php
/**
 * 自定义快乐彩采集抽象类
 */
namespace Lottery\Fetch\Platform;

abstract class AbstractCustomKeno extends AbstractKeno
{

    protected $fetchAwardNumberInfoDelay = 0;

    /**
     * 获取开始统计时间
     */
    abstract public function getStartCountDate();

    /**
     * 获取开始统计期号
     */
    abstract public function getStartCountExpect();

    /**
     * 获取每日开奖期数
     */
    abstract public function getDailyOpenExpectNum();

    /**
     * 返回开盘情况
     *
     * @return Array
     */
    public function getOpeningInfo()
    {
        return array('isOpening' => true, 'estimateOpentime' => 0);
    }

    /**
     * 获取当前销售期号信息
     *
     * @return array( 'status' => 1, //1 成功 0 失败 -1 休市
     *         'expect' => '141106036',
     *         'opentime' => 1402143035
     *         )
     */
    public function getCurrentSaleExpectInfo($deadline = 15)
    {
        // 获取开始统计日期到现在时间的天数
        $startCountDatetime = new \DateTime($this->getStartCountDate());
        $nowDatetime = new \DateTime(date('Y-m-d', $this->nowTimestamp));
        $datetimeDiff = $startCountDatetime->diff($nowDatetime);
        $diffDay = intval($datetimeDiff->format('%a'));
        // 今天第一期期号
        $expect = $this->getStartCountExpect() + ($diffDay * $this->getDailyOpenExpectNum());
        $opentime = 0;
        $awardTimeInfo = $this->getAwardTimeInfo();
        foreach ($awardTimeInfo as $item) {
            $start = strtotime($item['start'], $this->nowTimestamp);
            $end = strtotime($item['end'], $this->nowTimestamp);
            for ($i = $start; $i <= $end; $i += $item['interval']) {
                if ($this->nowTimestamp < $i) {
                    $opentime = $i;
                    break 2;
                }
                $expect ++;
            }
        }

        if (! $opentime) {
            // 当日最后一期后当前销售期号为次日第一期
            if (isset($awardTimeInfo[0]['start'])) {
                $opentime = strtotime(date('Y-m-d', strtotime('+1 day', $this->nowTimestamp)) . ' ' . $awardTimeInfo[0]['start']);
            } else {
                return array(
                    'status' => 0
                );
            }
        }

        return array(
            'status' => 1,
            'expect' => $this->formatExpect($expect, $opentime),
            'opentime' => $opentime
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        if ($this->fetchAwardNumberInfoDelay > 0) {
            sleep($this->fetchAwardNumberInfoDelay);
        }

        $code_list = explode(',', '01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80');
        shuffle($code_list);
        return array(
            'expect' => $expect,
            'number' => implode(' ', array_splice($code_list, 0, 20)),
            'opendate' => 0,
            'opentime' => 0
        );
    }

    public function formatExpect($expect, $opentime)
    {
        return $expect;
    }
}
