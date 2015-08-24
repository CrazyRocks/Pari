<?php
/**
 * 时时彩三星组选(包2胆)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\ThreeZu;

class ThreeBao2 extends ThreeZu
{
    public function getBetCount($playway, $data)
    {
        $tmp = array_filter($data);
        $a = count(current($tmp));
        $b = count(next($tmp));
        return 10 * $a * $b;
    }
}
