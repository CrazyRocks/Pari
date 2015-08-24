<?php
/**
 * 时时彩三星组选(包1胆)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\ThreeZu;

class ThreeBao1 extends ThreeZu
{
    public function getBetCount($playway, $data)
    {
        return isset($data['number']) ? (55 * count($data['number'])) : 0;
    }
}
