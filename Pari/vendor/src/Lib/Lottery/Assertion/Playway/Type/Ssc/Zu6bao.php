<?php
/**
 * 时时彩组6(包号)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

class Zu6bao extends Zu6
{
    public function getBetCount($playway, $data)
    {
        if (!isset($data['number'])) {
            return 0;
        }

        $n = count($data['number']);
        return $n * ($n - 1) * ($n - 2) / 6;
    }
}
