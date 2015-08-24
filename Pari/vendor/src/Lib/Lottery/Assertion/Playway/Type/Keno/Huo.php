<?php
/**
 * 快乐彩“火”判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Keno;

use Lottery\Assertion\Playway\Type\Keno\AbstractKeno;

class Huo extends AbstractKeno
{
    /**
     * 是否中奖
     * @return int 中奖等级
     */
    public function assert($betNumbers = null, $star = null)
    {
        $awardNumbersTotal = $this->getAwardNumbersTotal();
		return intval($awardNumbersTotal >= 856 && $awardNumbersTotal <= 923);
    }
}
