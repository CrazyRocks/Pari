<?php
/**
 * 时时彩杀码(组选)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc\Shama;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Zu extends AbstractSsc
{
    /**
     * 是否中奖
     *
     * @param string|array $betNumbers
     *            投注号码
     * @return int 中奖等级
     */
    public function assert($betNumbers = null, $star = null)
    {
        $betNumbers = $this->filterEmpty($betNumbers);
        if (!isset($betNumbers['pos']) ||
            empty($betNumbers['pos']) ||
            !is_array($betNumbers['pos']) ||
            !isset($betNumbers['number'])
        ) {
            return 0;
        }

        $check_pos = array();
        foreach ($betNumbers['pos'] as $pos) {
            if (!isset($this->awardNumbers[$pos])) {
                return 0;
            }
            $check_pos[$pos] = $this->awardNumbers[$pos];
        }

        foreach ($betNumbers['pos'] as $pos) {
            if ($this->awardNumbers[$pos] == $betNumbers['number']) {
                return 0;
            }
        }

        if ($this->isZu3($check_pos)) {
            return 1;
        }

        if ($this->isZu6($check_pos)) {
            return 2;
        }

        return 0;
    }

    public function getBetCount($playway, $data)
    {
        if (!isset($data['number'])) {
            return 0;
        }

        $n = count($data['number']);
        switch ($n) {
            case 1:
                $betCount = 156;
                break;
            case 2:
                $betCount = 112;
                break;
            case 3:
                $betCount = 77;
                break;
            case 4:
                $betCount = 50;
                break;
            case 5:
                $betCount = 30;
                break;
            case 6:
                $betCount = 16;
                break;
            case 7:
                $betCount = 7;
                break;
            case 8:
                $betCount = 2;
                break;
            default:
                $betCount = 0;
                break;
        }

        return $betCount;
    }
}
