<?php
/**
 * 时时彩胆码(组选)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc\Danma;

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

        $check_pos = [];
        foreach ($betNumbers['pos'] as $pos) {
            if (!isset($this->awardNumbers[$pos])) {
                return 0;
            }
            $check_pos[$pos] = $this->awardNumbers[$pos];
        }

        if (!in_array($betNumbers['number'], $check_pos)) {
            return 0;
        }

        if ($this->isZu3($check_pos)) {
            return 1;
        }

        if ($this->isZu6($check_pos)) {
            return 2;
        }

        return 0;
    }

    /**
     *
     * @param $playway
     * @param $data
     * @return int
     */
    public function getBetCount($playway, $data)
    {
        if (!isset($data['number'])) {
            return 0;
        }
        $dd = ['1'=>'54','2'=>'98','3'=>'133','4'=>'160','5'=>'180','6'=>'194','7'=>'203','8'=>'208','9'=>'210','10'=>'210'];

        $n = count($data['number']);
        $betCount = 0;
        switch ($n) {
            case 1:
                $betCount = 54;
                break;
            case 2:
                $betCount = 98;
                break;
            case 3:
                $betCount = 133;
                break;
            case 4:
                $betCount = 160;
                break;
            case 5:
                $betCount = 180;
                break;
            case 6:
                $betCount = 194;
                break;
            case 7:
                $betCount = 203;
                break;
            case 8:
                $betCount = 208;
                break;
            case 9:
                $betCount = 210;
                break;
            case 10:
                $betCount = 210;
                break;
            default:
                $betCount = 0;
                break;
        }

        return $betCount;
    }
}
