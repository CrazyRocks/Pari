<?php
/**
 * 时时彩组3(包号)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

class Zu3bao extends Zu3
{
    public function getBetCount($playway, $data)
    {
        if (!isset($data['number'])) {
            return 0;
        }

        $n = count($data['number']);
        return $n * ($n - 1);
    }
}
