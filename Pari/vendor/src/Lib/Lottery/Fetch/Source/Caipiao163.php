<?php
/**
 * caipiao 163 采集源类
 */
namespace Lottery\Fetch\Source;

class Caipiao163
{
    /**
     * @var string 163 $gameEn
     */
    protected $gameEn = null;

    /**
     * @param string 163 $gameEn
     */
    public function __construct($gameEn)
    {
        $this->gameEn = $gameEn;
    }

    public function getAwardNumberInfo($expect)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://caipiao.163.com/award/getAwardNumberInfo.html?gameEn=' . $this->gameEn . '&cache=' . time() . '&period=' . $expect,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        if (!empty($content)) {
            $jsonArr = json_decode($content, true, 512, \JSON_BIGINT_AS_STRING);
            if (isset($jsonArr['status']) &&
                $jsonArr['status'] == '0' &&
                isset($jsonArr['awardNumberInfoList'][0]['period']) &&
                $jsonArr['awardNumberInfoList'][0]['period'] == $expect &&
                isset($jsonArr['awardNumberInfoList'][0]['winningNumber']) &&
                !empty($jsonArr['awardNumberInfoList'][0]['winningNumber'])
            ) {
                return array(
                    'number' => $jsonArr['awardNumberInfoList'][0]['winningNumber'],
                    'expect' => $expect,
                    'opentime' => 0
                );
            }
        }

        return false;
    }

    public function resentAwardNum($expect)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://caipiao.163.com/order/preBet_resentAwardNum.html?gameEn=' . $this->gameEn . '&cache=' . time(),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        if (!empty($content)) {
            $jsonArr = json_decode($content, true, 512, \JSON_BIGINT_AS_STRING);
            if (!isset($jsonArr['successful']) ||
                $jsonArr['successful'] != 'true' ||
                !isset($jsonArr['latestPeriods']) ||
                empty($jsonArr['latestPeriods']) ||
                !is_array($jsonArr['latestPeriods'])
            ) {
                return false;
            }

            foreach ($jsonArr['latestPeriods'] as $v) {
                if (isset($v['period']) &&
                    $v['period'] == $expect &&
                    isset($v['number']) &&
                    !empty($v['number'])
                ) {
                    return array(
                        'number' => $v['number'],
                        'expect' => $expect,
                        'opendate' => 0,
                        'opentime' => 0
                    );
                }
            }
        }

        return false;
    }

    public function getAwardNumberListByDate($date)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://caipiao.163.com/award/' . $this->gameEn . '/?gameEn=' . $this->gameEn . '&date=' . str_replace('-', '', $date),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        $opendate = strtotime($date);

        $awardNumberList = array();
        if (!empty($content)) {
            $doc = new \DOMDocument();
            @$doc->loadHTML($content);
            $mainAreaTable = $doc->getElementById('mainArea')->getElementsByTagName('table')->item(0);
            if ($mainAreaTable) {
                foreach ($mainAreaTable->getElementsByTagName('tr') as $k => $tr) {
                    if ($k > 0) {
                        foreach ($tr->getElementsByTagName('td') as $k2 => $td) {
                            if ($k2 % 6 == 0) {
                                $number = $td->getAttribute('data-win-number');
                                $expect = $td->getAttribute('data-period');
                                if ($number && $expect) {
                                    $awardNumberList[] = array(
                                        'number' => $number,
                                        'expect' => $expect,
                                        'opendate' => $opendate,
                                        'opentime' => $opendate
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }

        usort($awardNumberList, function ($a, $b) {
            if ($a['expect'] == $b['expect']) {
                return 0;
            }
            return ($a['expect'] < $b['expect']) ? -1 : 1;
        });

        return $awardNumberList;
    }

    public function getAwardNumberListByDateAndHtml($date)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
        CURLOPT_URL => 'http://caipiao.163.com/award/' . $this->gameEn . '/' . str_replace('-', '', $date) . '.html',
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        $opendate = strtotime($date);

        $awardNumberList = array();
        if (!empty($content)) {
            $doc = new \DOMDocument();
            @$doc->loadHTML($content);
            $mainAreaTable = $doc->getElementsByTagName('table')->item(0);
            if ($mainAreaTable) {
                foreach ($mainAreaTable->getElementsByTagName('tr') as $k => $tr) {
                    if ($k > 0) {
                        foreach ($tr->getElementsByTagName('td') as $k2 => $td) {
                            if ($k2 % 4 == 0) {
                                $number = $td->getAttribute('data-award');
                                $expect = $td->getAttribute('data-period');
                                if ($number && $expect) {
                                    $awardNumberList[] = array(
                                        'number' => $number,
                                        'expect' => $expect,
                                        'opendate' => $opendate,
                                        'opentime' => $opendate
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }

        usort($awardNumberList, function ($a, $b) {
            if ($a['expect'] == $b['expect']) {
                return 0;
            }
            return ($a['expect'] < $b['expect']) ? -1 : 1;
        });

        return $awardNumberList;
    }
}
