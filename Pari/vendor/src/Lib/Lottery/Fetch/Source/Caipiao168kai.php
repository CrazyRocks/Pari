<?php
/**
 * Caipiao168kai采集源类
 */
namespace Lottery\Fetch\Source;

class Caipiao168kai
{
    /**
     * @var int $lottery_type
     */
    protected $lottery_id = null;

    /**
     * @param int  $lottery_id
     */
    public function __construct($lottery_id)
    {
        $this->lottery_id = $lottery_id;
    }

    public function getAwardNumberInfo($expect)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://www.168kai.com/History/HisOpenList?id=' . $this->lottery_id . '&cache=' . time(),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        if (!empty($content)) {
            $jsonArr = json_decode($content, true, 512, \JSON_BIGINT_AS_STRING);
            if (isset($jsonArr['list']) &&
                !empty($jsonArr['list'])
            ) {
                foreach ($jsonArr['list'] as $v) {
                    if ($v['cTerm'] == $expect) {
                        return array(
                            'number' => str_replace(',', ' ', $v['cTermResult']),
                            'expect' => $expect,
                            'opendate' => 0,
                            'opentime' => 0
                        );
                    }
                }
            }
        }

        return false;
    }

    public function getAwardNumberListByDate($date)
    {
        $ch = curl_init();
            curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://www.168kai.com/History/HisList?id=' . $this->lottery_id . '&date=' . $date . '&cache=' . time(),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        $opendate = strtotime($date);

        $awardNumberList = array();
        if (!empty($content)) {
            $jsonArr = json_decode($content, true, 512, \JSON_BIGINT_AS_STRING);
            if (isset($jsonArr['list']) && is_array($jsonArr['list'])) {
                foreach ($jsonArr['list'] as $item) {
                    if (in_array($this->lottery_id, array(2002, 2008))) {
                        $opentime = strtotime($item['cTermDT'] . ' 20:30:00');
                        $opendate = strtotime($item['cTermDT']);
                    } else {
                        $opentime = strtotime($date . ' ' . $item['cTermDT']);
                    }

                    $awardNumberList[] = array(
                        'number' => str_replace(',', ' ', $item['cTermResult']),
                        'expect' => $item['cTerm'],
                        'opendate' => $opendate,
                        'opentime' => $opentime
                    );
                }
            }
        }

        return array_reverse($awardNumberList);
    }
}
