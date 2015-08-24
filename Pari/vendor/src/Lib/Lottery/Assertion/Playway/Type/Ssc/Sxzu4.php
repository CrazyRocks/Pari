<?php
/**
 * 时时彩四星组4判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

class Sxzu4 extends Wxzu120
{
    public function getBetCount($playway, $raw)
    {
        if (!isset($raw['number']) || !isset($raw['number2'])) {
            return 0;
        }

        $numberCount = count($raw['number']);
        $number2Count = count($raw['number2']);
        $minCounts = array("a" => array("ssc-wxzu60" => 1, "ssc-wxzu30" => 2, "ssc-wxzu20" => 1, "ssc-wxzu10" => 1, "ssc-wxzu5" => 1, "ssc-sxzu12" => 1, "ssc-sxzu4" => 1), "b" => array("ssc-wxzu60" => 3, "ssc-wxzu30" => 1, "ssc-wxzu20" => 2, "ssc-wxzu10" => 1, "ssc-wxzu5" => 1, "ssc-sxzu12" => 2, "ssc-sxzu4" => 1));
        if ($numberCount < $minCounts['a'][$playway['playway_type_name']] || $number2Count < $minCounts['b'][$playway['playway_type_name']]) {
            $betCount = 0;
        } else {
            $pailie_count = function ($m, $n) {
                $t = $m;
                for ($i = 1; $i < $n; $i++) {
                    $t = $t * ($m - $i);
                }
                return $t;
            };
            $betCount = ($pailie_count($numberCount, $minCounts['a'][$playway['playway_type_name']]) /
                        $pailie_count($minCounts['a'][$playway['playway_type_name']], $minCounts['a'][$playway['playway_type_name']])) *
                        ($pailie_count($number2Count, $minCounts['b'][$playway['playway_type_name']]) /
                           $pailie_count($minCounts['b'][$playway['playway_type_name']], $minCounts['b'][$playway['playway_type_name']]));
        }

        return $betCount;
    }
}
