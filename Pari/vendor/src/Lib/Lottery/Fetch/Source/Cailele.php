<?php
/**
 * 彩乐乐Cailele采集源类
 */
namespace Lottery\Fetch\Source;

class Cailele
{
    /**
     * @var string 彩乐乐 $lottery_name
     */
    protected $lottery_name = null;

    /**
     * @param int 乐彩 $lottery_type
     */
    public function __construct($lottery_name)
    {
        $this->lottery_name = $lottery_name;
    }

    public function getAwardNumberInfo($expect)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://www.cailele.com/static/' . $this->lottery_name . '/newlyopenlist.xml?cache=' . time(),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => array(
        		'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        		'Accept-Language:zh-CN,zh;q=0.8,en;q=0.6,zh-TW;q=0.4',
        		'X-Requested-With:XMLHttpRequest',
        		'Cache-Control:max-age=0',
        		'DNT:1',
        		'Connection:keep-alive',
        		'User-Agent:Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)',
        	)
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
