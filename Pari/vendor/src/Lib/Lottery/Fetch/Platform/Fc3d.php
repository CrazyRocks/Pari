<?php
/**
 * 福彩3d采集类
 */
namespace Lottery\Fetch\Platform;

Class Fc3d extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 1;

    const _168KAI_ID = 2002;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return '3d';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '20:30:00',
                'end' => '20:30:00',
                'interval' => 300
            )
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟60秒左右
        sleep(60);

        //每5秒从caipiao.163.com尝试获取开奖数据，共尝试50次
        $caipiao163 = new \Lottery\Fetch\Source\Caipiao163('x3d');
        for ($i = 0; $i < 50; $i++) {
            $awardNumberInfo = $caipiao163->resentAwardNum($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(60);
        }

        //每10从www.168kai.com尝试获取开奖数据，共尝试20次
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $source->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每5秒从www.cailele.com尝试获取开奖数据，共尝试10次
        $lecai = new \Lottery\Fetch\Source\Cailele('3d');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $lecai->getAwardNumberInfo($expect);
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

    public function formatExpect($expect, $opentime)
    {
        //休市日数
        $restDay = 0;
        $openY = date('Y', $opentime);
        foreach (\Lottery\Fetch\Rest::getRestTimeInfo($this->getName()) as $key => $item) {
		    if ($openY == $key && $opentime >= strtotime($item['end'])) {
		        $startDatetime = new \DateTime($item['start']);
                $endDatetime = new \DateTime($item['end']);
                $datetimeDiff = $startDatetime->diff($endDatetime);
		        $restDay += intval($datetimeDiff->format('%a'));
		    }
		}

        return $openY . str_pad(date('z', $opentime) + 1 - $restDay, 3, '0', STR_PAD_LEFT);
    }
}
