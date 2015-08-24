<?php
/**
 * 时时彩组2(包号)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

class Zu2bao extends Zu2
{
    public function getBetCount($playway, $data)
    {
        if (!isset($data['number'])) {
            return 0;
        }

        $n = count($data['number']);
        return $n * ($n - 1) / 2;
    }
}
