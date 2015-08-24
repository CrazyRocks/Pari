<?php
/**
 * c.apiplus.cn采集源类
 */
namespace Lottery\Fetch\Source;

class Apiplus
{
    const TOKEN = 'a172f4b9a9d6419d';
    const URL = 'http://121.40.89.67:88';

    /**
     * @var string $lottery_name
     */
    protected $lottery_name = null;

    /**
     * @param int $lottery_name
     */
    public function __construct($lottery_name)
    {
        $this->lottery_name = $lottery_name;
    }

    public function getAwardNumberInfo($expect)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => self::URL.'/newly.do?token=' . self::TOKEN . '&code=' . $this->lottery_name . '&cache=' . time(),
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
