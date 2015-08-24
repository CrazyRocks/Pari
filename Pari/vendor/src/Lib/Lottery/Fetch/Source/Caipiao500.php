<?php
/**
 * 500彩票网采集源类
 */
namespace Lottery\Fetch\Source;

class Caipiao500
{
    /**
     * @var string 500彩票网 $lottery_name
     */
    protected $lottery_name = null;

    /**
     * @param int 500彩票网 $lottery_type
     */
    public function __construct($lottery_name)
    {
        $this->lottery_name = $lottery_name;
    }

    public function getAwardNumberInfo($expect)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://www.500.com/static/info/kaijiang/xml/' . $this->lottery_name . '/newlyopen.xml?cache=' . time(),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        if (!empty($content)) {
            $xml = @\simplexml_load_string($content);
            if (isset($xml->row)) {
                foreach ($xml->row as $row) {
                    $attributes = $row->attributes();
                    if (isset($attributes->expect) &&
                        $expect == $attributes->expect &&
                        isset($attributes->opencode) &&
                        !empty($attributes->opencode)
                    ) {
                        $opentime = strtotime($attributes->opentime);
                        $opendate = strtotime(date('Y-m-d', $opentime));

                        return array(
                            'number' => str_replace(',', ' ', $attributes->opencode),
                            'expect' => $expect,
                            'opendate' => $opendate,
                            'opentime' => $opentime
                        );
                    }
                }
            }
        }

        return false;
    }
}
