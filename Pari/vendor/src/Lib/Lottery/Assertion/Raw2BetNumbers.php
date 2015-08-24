<?php
/**
 * 根据原始投注数据，生成复试投注号码类
 * @package Raw2BetNumber
 */
namespace Pari\Lib\Lottery\Assertion;

class Raw2BetNumbers
{

    protected $code_list;

    public function __construct($code_list)
    {
        $this->setCodeList($code_list);
    }

    public function setCodeList($code_list)
    {
        if (is_string($code_list)) {
            $code_list = explode(',', $code_list);
        }
        $this->code_list = $code_list;
        sort($this->code_list);
    }

    public function getBetNumbers($betModel)
    {
        $betNumbers = array();

        if ($betModel->is_manual == 1 && $betModel->playway->split == 'ssc') {
            $betNumbers = $this->sscManual($betModel->playway->playway_type->name, $betModel->data, $betModel->playway->pos);
        } elseif ($betModel->is_manual == 1 && $betModel->playway->split == '11x5') {
            $betNumbers = $this->c11x5Manual($betModel);
        } elseif ($betModel->is_manual == 0) {
            $star = $this->getStarByBetModel($betModel);
            $betNumbers = $this->convert($betModel->playway->playway_type->name, $betModel->json_data, $star);
        }

        return $betNumbers;
    }

    public function convert($playway_type, $json_data, $star)
    {
        $sub = array();
        if (is_string($json_data)) {
            $data = json_decode($json_data, true);
        } else {
            $data = $json_data;
        }
        if ($playway_type == 'ssc-3zu') {
            $sub = $this->sanZu($data);
        } elseif ($playway_type == 'ssc-3bao1') {
            $sub = $this->sanbao1($data);
        } elseif ($playway_type == 'ssc-3bao2') {
            $sub = $this->sanbao2($data);
        } elseif ($playway_type == 'ssc-2bao1') {
            $sub = $this->ren2zu($data);
        } elseif ($playway_type == 'ssc-3zhi1') {
            $sub = $this->sanzhi1($data);
        } elseif ($playway_type == 'ssc-2zhi1') {
            $sub = $this->erzhi1($data);
        } elseif ($playway_type == 'ssc-fen') {
            $sub = $this->fen($data);
        } elseif ($playway_type == 'ssc-zu2bao') {
            $sub = $this->zu2bao($data);
        } elseif ($playway_type == 'ssc-zu3bao') {
            $sub = $this->zu3bao($data);
        } elseif ($playway_type == 'ssc-zu6bao') {
            $sub = $this->zu6bao($data);
        } elseif ($playway_type == 'ssc-lian') {
            $sub = $this->lian($data);
        } elseif ($playway_type == 'ssc-zu2') {
            $sub = $this->zu2($data);
        } elseif ($playway_type == 'ssc-zu3') {
            $sub = $this->zu3($data);
        } elseif ($playway_type == 'ssc-zu6') {
            $sub = $this->zu6($data);
        } elseif ($playway_type == 'ssc-ding') {
            $sub = $this->ding($data);
        } elseif ($playway_type == 'ssc-shama-zu') {
            $sub = $this->shaHaoZu($data);
        } elseif ($playway_type == 'ssc-shama-zhi') {
            $sub = $this->shaHaoZhi($data);
        } elseif ($playway_type == 'ssc-danma-zu') {
            $sub = $this->danMaZu($data);
        } elseif ($playway_type == 'ssc-danma-zhi') {
            $sub = $this->danMaZhi($data);
        } elseif ($playway_type == 'ssc-zhi' ||
                  $playway_type == 'ssc-tong' ||
                  $playway_type == 'ssc-danshuang' ||
                  $playway_type == 'ssc-zhongwei' ||
                  $playway_type == 'ssc-daxiaodanshuang'
        ) {
            $sub = $this->normal($data);
        } elseif ($playway_type == 'ssc-ren' ||
                  $playway_type == 'ssc-k3renxuan' ||
                  $playway_type == 'ssc-k3shama'
        ) {
            $sub = $this->ren($data, $star);
        } elseif ($playway_type =='ssc-wxzu120' || $playway_type == 'ssc-sxzu24') {
            $sub = $this->wxzu120($data, $star);
        } elseif ($playway_type == 'ssc-threezhi2') {
            $sub = $this->threezhi2($data, $star);
        } elseif ($playway_type =='ssc-wxzu60' || $playway_type =='ssc-sxzu12') {
            $sub = $this->wxzu60($data, $star);
        } elseif ($playway_type =='ssc-wxzu30') {
            $sub = $this->wxzu30($data, $star);
        } elseif ($playway_type =='ssc-wxzu20') {
            $sub = $this->wxzu20($data, $star);
        } elseif ($playway_type =='ssc-wxzu10') {
            $sub = $this->wxzu10($data, $star);
        } elseif ($playway_type =='ssc-wxzu5' || $playway_type =='ssc-sxzu4') {
            $sub = $this->wxzu5($data, $star);
        } elseif ($playway_type == 'ssc-sxzu6') {
            $sub = $this->sxzu6($data, $star);
        } elseif ($playway_type == 'ssc-baodian' ||
                  $playway_type == 'ssc-kuadu' ||
                  $playway_type == 'ssc-hezhi' ||
                  $playway_type == 'ssc-jicount' ||
                  $playway_type == 'ssc-oucount' ||
                  $playway_type == 'ssc-baozi' ||
                  $playway_type == 'ssc-k3hezhi'
        ) {
            $sub = $this->baodian($data, $star);
        } elseif ($playway_type == 'ssc-hongheidan' || $playway_type == 'ssc-chonghaocount') {
            $sub = $this->hongheidan($data, $star);
        } elseif (strpos($playway_type, 'keno') !== false) {
            $sub = array(1);
        }
        return $sub;
    }

    public function getStarByBetModel($betModel)
    {
        if (in_array($betModel->playway->playway_type->name, array('ssc-ren', 'ssc-danshuang', 'ssc-zhongwei', 'ssc-k3renxuan', 'ssc-k3shama', 'ssc-threezhi2'))) {
            $star = $betModel->playway->min_bet_num;
        } else {
            $star = count($betModel->playway->pos);
        }
        return $star;
    }

    /**
     * 时时彩单式投注号生成
     * @param string $playway_type 玩法名称
     * @param string $data
     *            例如： 123,234,567
     * @param string $pos 有效投注位置 a,b,c,d,e
     *
     * @return array 例如：array( array('c'=>1,'b'=>2,'a'=>3),array('c'=>2,'b'=>3,'a'=>4),array('c'=>5,'b'=>6,'a'=>7) )
     */
    public function sscManual($playway_type, $data, $pos)
    {
        // 时时彩拆号,每注使用 空格或,;回车隔开
        preg_match_all('/\d+/', $data, $matches);
        $sub = [];
        sort($pos);
        foreach ($matches[0] as $value) {
            $value = preg_split('/(?<=\d)(?=\d)/', $value);
            if (count($pos) == count($value)) {
                $value = array_reverse($value);
                $sub[] = array_combine($pos, $value);
            }
        }

        if ($playway_type == 'ssc-lian') {
            foreach ($sub as $v) {
                krsort($v);
                if (count($v) == 5) {
                    $sub[] = array_slice($v, -3, 3, true);
                    $sub[] = array_slice($v, -2, 2, true);
                    $sub[] = array_slice($v, -1, 1, true);
                }
                if (count($v) == 4 || count($v) == 3) {
                    $sub[] = array_slice($v, -2, 2, true);
                    $sub[] = array_slice($v, -1, 1, true);
                }
                if (count($v) == 2) {
                    $sub[] = array_slice($v, -1, 1, true);
                }
            }
        }

        return $sub;
    }

    /**
     * 11选5单式投注号生成
     * @param object $betModel 投注单模型
     * @return array 例如：array( array('c'=>1,'b'=>2,'a'=>3),array('c'=>2,'b'=>3,'a'=>4),array('c'=>5,'b'=>6,'a'=>7) )
     */
    public function c11x5Manual($betModel)
    {
        // 11选5拆号,每注使用,隔开
        $matches = explode(',', $betModel->data);
        $pos = $betModel->playway->pos;
        $code_list = explode(',', $betModel->playway->code_list);
        $sub = [];
        sort($pos);

        foreach ($matches as $value) {
            $value = preg_split('/\s/', $value);
            if (count($value) == $betModel->playway->numbers) {
                $isContinue = false;
                foreach ($value as $v2) {
                    if (!in_array($v2, $code_list)) {
                        $isContinue = true;
                    }
                }
                if ($isContinue) {
                    continue;
                }
                if ($betModel->playway->playway_type->name == 'ssc-ren') {
                    $sub[] = $value;
                } else {
                    $value = array_reverse($value);
                    $sub[] = array_combine($pos, $value);
                }
            }
        }

        return $sub;
    }

    /**
     * 过滤掉 空字符串,false,null,空数组,保留 0 '0'
     *
     * @param
     *            $arr
     *
     * @return array
     */
    public function filterEmpty($arr)
    {
        if (!is_array($arr)) {
            return false;
        }

        return array_filter($arr, function ($v)
        {
            if ($v === '' || $v === false || $v === [] || $v === null) {
                return false;
            }
            return true;
        });
    }

    /**
     * 根据原始投注数据　生成复试投注号码(适用于任何直选和通选玩法,不适用于连选)
     *
     * @param array $data
     *            [[1],[2],[3],[4,8],[5,6,7]]
     * @param array $dataKeys
     *            ['a','b','c','d','e']
     * @param array $pailieRaw
     * @param array $group
     * @param int $val
     * @param int $i
     */
    protected function pailieRaw($data, $dataKeys, &$pailieRaw, $group = array(), $val = null, $i = 0)
    {
        if (isset($val)) {
            $group[] = $val;
        }

        if ($i >= count($data)) {
            $pailieRaw[] = array_combine($dataKeys, $group);
        } else {
            foreach ($data[$i] as $v) {
                $this->pailieRaw($data, $dataKeys, $pailieRaw, $group, $v, $i + 1);
            }
        }
    }

    protected function renCombination($arr, $len=0, $str="", &$res)
    {
        $arr_len = count($arr);
        if($len == 0){
            $res[] = $str;
        }else{
            for($i=0; $i<$arr_len-$len+1; $i++){
                $tmp = array_shift($arr);
                $this->renCombination($arr, $len-1, $str . " " . $tmp, $res);
            }
        }
    }

    /**
     *
     * @param string|array $raw
     *            {"pos":["e","d"],"number":["6"]}
     *            根据原始投注数据　生成复试投注号码(适用于红黑胆玩法)
     * @return array 例如：array( array('pos'=>["e","d"],'number'=>'6'));
     */
    public function hongheidan($raw, $star)
    {
        if (!isset($raw['pos']) ||
            count($raw['pos']) != $star ||
            !isset($raw['number']) ||
            !is_array($raw['number']) ||
            !isset($raw['number2']) ||
            !is_array($raw['number2'])
        ) {
            return array();
        }

        $result = array();
        foreach ($raw['number'] as $number) {
            foreach ($raw['number2'] as $number2) {
                $result[] = array(
                    'pos' => $raw['pos'],
                    'number' => $number,
                    'number2' => $number2
                );
            }
        }

        return $result;
    }

    /**
     *
     * @param string|array $raw
     *            {"pos":["e","d"],"number":["6"]}
     *            根据原始投注数据　生成复试投注号码(适用于包点玩法)
     * @return array 例如：array( array('pos'=>["e","d"],'number'=>'6'));
     */
    public function baodian($raw, $star)
    {
        if (!isset($raw['pos']) ||
            count($raw['pos']) != $star ||
            !isset($raw['number'])
        ) {
            return array();
        }

        $result = array();
        foreach ($raw['number'] as $number) {
            $result[] = array(
                'pos' => $raw['pos'],
                'number' => $number
            );
        }

        return $result;
    }


    /**
     *
     * @param string|array $raw
     *            {"a":[1],"b":[2],"c":[3],"d",[4,8],"e":[5,6,7]}
     *            根据原始投注数据　生成复试投注号码(适用于任选玩法)
     * @return array 例如：array( array('a'=>'2','b'=>'3','c'=>'9'),array('a'=>'4','b'=>'2','c'=>'8'), );
     */
    public function ren($raw, $star)
    {
        $result = array();
        $this->renCombination($raw['a'], $star, '', $result);
        foreach ($result as &$v) {
            $v = trim($v);
            $v = explode(' ', $v);
        }
        return $result;
    }

    public function threezhi2($raw, $star)
    {
        $result = array();
        $this->renCombination($raw['number'], $star, '', $result);
        $return = array();
        foreach ($result as &$v) {
            $v = trim($v);
            $v = explode(' ', $v);
            $return[] = array(
                'pos' => $raw['pos'],
                'number' => $v
            );
        }
        return $return;
    }

    public function wxzu120($raw, $star)
    {
        $result = array();
        $this->renCombination($raw['number'], $star, '', $result);
        $return = array();
        foreach ($result as &$v) {
            $v = trim($v);
            $v = explode(' ', $v);
            $return[] = array_combine($raw['pos'], $v);
        }
        return $return;
    }

    public function wxzu60($raw, $star)
    {
        if (!isset($raw['number']) ||
            !is_array($raw['number']) ||
            !isset($raw['number2']) ||
            !is_array($raw['number2'])
        ) {
            return array();
        }

        $return = array();
        $result = array();
        $this->renCombination($raw['number2'], $star - 2, '', $result);
        foreach ($result as &$r) {
            $r = trim($r);
            $r = explode(' ', $r);
        }
        if ($result) {
            foreach (array_unique($raw['number']) as $v) {
                $data = array($v, $v);
                foreach ($result as $v2) {
                    $return[] = array_combine($raw['pos'], array_merge($data, $v2));
                }
            }
        }

        return $return;
    }

    public function wxzu30($raw, $star)
    {
        if (!isset($raw['number']) ||
            !is_array($raw['number']) ||
            !isset($raw['number2']) ||
            !is_array($raw['number2'])
        ) {
            return array();
        }

        $return = array();
        $result = array();
        $this->renCombination($raw['number'], 2, '', $result);
        foreach ($result as &$r) {
            $r = trim($r);
            $r = explode(' ', $r);
            $r = array_merge($r, $r);
        }
        if ($result) {
            foreach (array_unique($raw['number2']) as $v) {
                $data = array($v);
                foreach ($result as $v2) {
                    $return[] = array_combine($raw['pos'], array_merge($data, $v2));
                }
            }
        }

        return $return;
    }

    public function wxzu20($raw, $star)
    {
        if (!isset($raw['number']) ||
            !is_array($raw['number']) ||
            !isset($raw['number2']) ||
            !is_array($raw['number2'])
        ) {
            return array();
        }

        $return = array();
        $result = array();
        $this->renCombination($raw['number2'], 2, '', $result);
        foreach ($result as &$r) {
            $r = trim($r);
            $r = explode(' ', $r);
        }
        if ($result) {
            foreach (array_unique($raw['number']) as $v) {
                $data = array($v, $v, $v);
                foreach ($result as $v2) {
                    $return[] = array_combine($raw['pos'], array_merge($data, $v2));
                }
            }
        }

        return $return;
    }

    public function wxzu10($raw, $star)
    {
        if (!isset($raw['number']) ||
            !is_array($raw['number']) ||
            !isset($raw['number2']) ||
            !is_array($raw['number2'])
        ) {
            return array();
        }

        $return = array();
        foreach (array_unique($raw['number']) as $v) {
            $data = array($v, $v, $v);
            foreach (array_unique($raw['number2']) as $v2) {
                $return[] = array_combine($raw['pos'], array_merge($data, array($v2, $v2)));
            }
        }

        return $return;
    }

    public function wxzu5($raw, $star)
    {
        if (!isset($raw['number']) ||
            !is_array($raw['number']) ||
            !isset($raw['number2']) ||
            !is_array($raw['number2'])
        ) {
            return array();
        }

        $return = array();
        foreach (array_unique($raw['number']) as $v) {
            $data = array_fill(0, $star - 1, $v);
            foreach (array_unique($raw['number2']) as $v2) {
                $return[] = array_combine($raw['pos'], array_merge($data, array($v2)));
            }
        }

        return $return;
    }

    public function sxzu6($raw, $star)
    {
        if (!isset($raw['number']) ||
            !is_array($raw['number'])
        ) {
            return array();
        }

        $result = array();
        $this->renCombination($raw['number'], 2, '', $result);
        foreach ($result as &$r) {
            $r = trim($r);
            $r = explode(' ', $r);
        }

        $return = array();
        foreach ($result as $v) {
            $return[] = array_combine($raw['pos'], array_merge(array($v[0], $v[0]), array($v[1], $v[1])));
        }

        return $return;
    }

    /**
     *
     * @param string|array $raw
     *            {"a":[1],"b":[2],"c":[3],"d",[4,8],"e":[5,6,7]}
     *            根据原始投注数据　生成复试投注号码(适用于任何直选和通选玩法,不适用于连选)
     * @return array 例如：array( array('a'=>'2','b'=>'3','c'=>'9'),array('a'=>'4','b'=>'2','c'=>'8'), );
     */
    public function normal($raw)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        } else
            if (! is_array($raw)) {
                return array();
            }
        if (empty($raw)) {
            return array();
        }

        $pailieRaw = array();
        $this->pailieRaw(array_values($raw), array_keys($raw), $pailieRaw);

        return $pailieRaw;
    }

    /**
     * 过滤生成的投注号,例如 {"a":1,"b":2,"c":3}与{"a":1,"b":3,"c":2} 被当做相同的投注号,只保留一个
     *
     * @param array $list
     * @return array
     */
    public function unique($list)
    {
        $arr = [];
        $keys = array_keys(current($list));
        foreach ($list as $item) {
            sort($item);
            $arr[] = implode(',', $item);
        }
        $arr = array_unique($arr);
        $result = [];
        foreach ($arr as $item) {
            $item = explode(',', $item);
            $result[] = array_combine($keys, $item);
        }
        return $result;
    }

    /**
     * 杀号直选投注号生成
     *
     * @param string|array $raw
     * @return array|bool
     */
    public function shaHaoZhi($raw)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        $pos = $raw['pos'];
        $number = array_diff($this->code_list, $raw['number']);
        if (count($pos) !== 3) {
            return false;
        }
        sort($number);
        $data = [
            $pos[0] => $number,
            $pos[1] => $number,
            $pos[2] => $number
        ];
        $list = $this->normal($data);
        return $list;
    }

    /**
     * 生成杀号组选投注号
     *
     * @param string|array $raw
     * @return bool
     */
    public function shaHaoZu($raw)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        $result = [];
        if (isset($raw['number'])) {
            foreach ($raw['number'] as $v) {
                $result[] = array(
                    'pos' => $raw['pos'],
                    'number' => $v
                );
            }
        }

        return $result;
    }

    /**
     * 生成三星胆码组选投注号
     *
     * @param string|array $raw
     * @return bool
     */
    public function danMaZu($raw)
    {
        return $this->shaHaoZu($raw);
    }

    /**
     * 生成三星胆码直选投注号
     *
     * @param string|array $raw
     *            ['pos'=>['a','b','c'],'number'=>[2,3,4]]
     * @return array
     */
    public function danMaZhi($raw)
    {
        $result = [];
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        $pos = $raw['pos'];
        $number = $raw['number'];
        if (count($pos) !== 3) {
            return false;
        }
        $data = [
            $pos[0] => $number,
            $pos[1] => $this->code_list,
            $pos[2] => $this->code_list
        ];
        $list = $this->normal($data);
        if (! $list) {
            return false;
        }

        $data = [
            $pos[0] => $this->code_list,
            $pos[1] => $number,
            $pos[2] => $this->code_list
        ];
        $list = array_merge($list, $this->normal($data));
        $data = [
            $pos[0] => $this->code_list,
            $pos[1] => $this->code_list,
            $pos[2] => $number
        ];
        $list = array_merge($list, $this->normal($data));

        $temp = [];
        foreach ($list as $v) {
            $temp[] = serialize($v);
        }
        $temp = array_unique($temp);
        foreach ($temp as $v) {
            $result[] = unserialize($v);
        }
        return $result;
    }

    /**
     * 生成定位胆投注号
     *
     * @param string|array $raw
     * @return array
     */
    public function ding($raw)
    {
        $result = [];
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        foreach ($raw as $key => $value) {
            foreach ($value as $v) {
                $result[] = [
                    $key => $v
                ];
            }
        }
        return $result;
    }

    /**
     * 生成连选投注号
     *
     * @param string|array $raw
     *            {"a":[1],"b":[2],"c":[3],"d",[4,8],"e":[5,6,7]}
     * @return array
     */
    public function lian($raw)
    {
        $result = [];
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        ksort($raw);
        $temp = [];
        foreach ($raw as $value) {
            $temp = array_merge($temp, $value);
        }
        $temp = $this->filterEmpty($temp);
        sort($temp);
        if ($temp != array_intersect($temp, $this->code_list)) {
            return [];
        }

        $list = $this->normal($raw);
        $result = array_merge($result, $list); // 一等奖
        krsort($raw);
        if (count($raw) == 5) {
            $temp = array_slice($raw, -3, 3, true);
            $list = $this->normal($temp);
            $result = array_merge($result, $list); // 二等奖
            $temp = array_slice($raw, -2, 2, true);
            $list = $this->normal($temp);
            $result = array_merge($result, $list); // 三等奖
            $temp = array_slice($raw, -1, 1, true);
            $list = $this->normal($temp);
            $result = array_merge($result, $list); // 四等奖
        } elseif (count($raw) == 4 || count($raw) == 3) {
            reset($raw);
            if (key($raw) == 'a') {
                // 后三后四
                $temp = array_slice($raw, -2, 2, true);
                $list = $this->normal($temp);
                $result = array_merge($result, $list); // 二等奖
                $temp = array_slice($raw, -1, 1, true);
                $list = $this->normal($temp);
                $result = array_merge($result, $list); // 三等奖
            } elseif (count($raw) == 3 && key($raw) == 'b') {
                // 中三
                $temp = array_slice($raw, -2, 2, true);
                $list = $this->normal($temp);
                $result = array_merge($result, $list); // 二等奖
                $temp = array_slice($raw, -1, 1, true);
                $list = $this->normal($temp);
                $result = array_merge($result, $list); // 三等奖
            } else {
                // 前三前四
                $temp = array_slice($raw, -2, 2, true);
                $list = $this->normal($temp);
                $result = array_merge($result, $list); // 二等奖
                $temp = array_slice($raw, -1, 1, true);
                $list = $this->normal($temp);
                $result = array_merge($result, $list); // 三等奖
            }
        } else {
            return false;
        }
        return $result;
    }

    /**
     * 生成 二星组选投注号
     *
     * @param string|array $raw
     * @param int $star
     * @return array
     *
     */
    public function zu2($raw, $star = 2)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (count($raw) != $star) {
            return [];
        }
        $list = $this->normal($raw);
        // 二星组选两个号不允许相同
        foreach ($list as $key => $value) {
            if (count(array_unique($value)) != $star) {
                unset($list[$key]);
            }
        }
        return $list;
    }

    /**
     * 生成二星分位投注号(ok)
     *
     * @param string|array $raw
     * @return array
     */
    public function fen($raw)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (count($raw) != 2) {
            return [];
        }
        $list = $this->normal($raw);
        return $list;
    }

    /**
     * 生成3星组6(直选玩法)投注号
     *
     * @param string|array $raw
     * @return array
     */
    public function zu6($raw)
    {
        return $this->zu2($raw, 3);
    }

    /**
     * 生成3星组3(直选玩法)投注号
     *
     * @param string|array $raw
     * @return array
     */
    public function zu3($raw)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (count($raw) != 3) {
            return [];
        }
        $list = $this->normal($raw);
        // 二星组选两个号不允许相同
        foreach ($list as $key => $value) {
            if (count(array_unique($value)) != 2) {
                unset($list[$key]);
            }
        }
        return $list;
    }

    /**
     * 生成组2包号投注号(ok)
     * 总注数：(n)*(n-1)/2
     *
     * @param string|array $raw
     * @param array $pos_list
     * @return array
     */
    public function zu2bao($raw, $pos_list = ['a','b','c','d','e'])
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        sort($raw['number']);
        if (count($raw['pos']) != 2 || count($raw['number']) < 2 || $raw['number'] != array_values(array_intersect($this->code_list, $raw['number'])) || // 参数顺序不能反，因为不允许number有重复数字
$raw['pos'] != array_intersect($raw['pos'], $pos_list)) {
            return [];
        } else {
            $pos = $raw['pos'];
            $a = $b = $raw['number'];
            foreach ($a as $v) {
                foreach ($b as $v2) {
                    if ($v2 > $v) {
                        $item[$pos[0]] = $v;
                        $item[$pos[1]] = $v2;
                        $result[] = $item;
                    }
                }
            }
            return $result;
        }
    }

    /**
     * 生成三星组3包号玩法投注号(ok)
     *
     * 总注数：n*(n-1)
     *
     * @param array $raw
     *            格式： ['pos'=>['a','b','c'],'number'=>['1','3','5']
     * @param array $pos_list
     *
     * @return array
     *
     */
    public function zu3bao($raw, $pos_list = ['a','b','c','d','e'])
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        sort($raw['number']);
        if (count($raw['pos']) != 3 || count($raw['number']) < 2 || $raw['number'] != array_values(array_intersect($this->code_list, $raw['number'])) || // 参数顺序不能反，因为不允许number有重复数字
$raw['pos'] != array_intersect($raw['pos'], $pos_list)) {
            return [];
        } else {
            $pos = $raw['pos'];
            $a = $b = $raw['number'];
            foreach ($a as $v) {
                foreach ($b as $v2) {
                    if ($v2 !== $v) {
                        $item[$pos[0]] = $v;
                        $item[$pos[1]] = $v;
                        $item[$pos[2]] = $v2;
                        $result[] = $item;
                    }
                }
            }
            return $result;
        }
    }

    /**
     * 生成组6(包号玩法)投注号(ok)
     *
     * @param string|array $raw
     * @param array $pos_list
     *            总注数：n*(n-1)*(n-2)/6
     *            每位每个数字出现次数：(n-1)*(n-2)/2
     *
     * @return array
     *
     */
    public function zu6bao($raw, $pos_list = ['a','b','c','d','e'])
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        sort($raw['number']);
        if (count($raw['pos']) != 3 || count($raw['number']) < 3 || $raw['number'] != array_values(array_intersect($this->code_list, $raw['number'])) || // 参数顺序不能反，因为不允许number有重复数字
$raw['pos'] != array_intersect($raw['pos'], $pos_list)) {
            return [];
        } else {
            $pos = $raw['pos'];
            $a = $b = $c = $raw['number'];
            foreach ($a as $v) {
                foreach ($b as $v2) {
                    if ($v2 > $v) {
                        foreach ($c as $v3) {
                            if ($v3 > $v && $v3 > $v2) {
                                $item[$pos[0]] = $v;
                                $item[$pos[1]] = $v2;
                                $item[$pos[2]] = $v3;
                                $result[] = $item;
                            }
                        }
                    }
                }
            }
            return $result;
        }
    }

    /**
     * 为任2组选(包1胆)生成投注号
     * 例如:ren2zu(['a'=>[],'c'=>[3,4]],[0,1,2,3,4,5,6,7,8,9]);//可生成20注
     */
    public function ren2zu($raw)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (isset($raw['pos'])) {
            $tmp[$raw['pos'][0]] = [];
            $tmp[$raw['pos'][1]] = $raw['number'];
            $raw = $tmp;
        }
        $number = $this->filterEmpty($raw);
        $current = current($number); // 胆位数字数组
                                     // 参数合法性验证
        if (count($number) != 1 || count($raw) != 2 || $current != array_intersect($current, $this->code_list)) {
            return false;
        }
        $key = array_keys($raw, []); // 空位的key
        $raw[$key[0]] = $this->code_list;
        return $this->normal($raw);
    }

    /**
     * 为任三组选包胆生成投注号
     *
     * @param string|array $raw
     * @return array|bool
     */
    public function sanZu($raw)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (isset($raw['row_setting']) && $raw['row_setting'] == 'two') {
            return $this->sanbao2($raw);
        } else {
            return $this->sanbao1($raw);
        }
    }

    /**
     * 为任三组选包1胆生成投注号,支持两种参数格式
     * 例如:sanbao1(['a'=>[],'b'=>[],'c'=>[3,4]],[0,1,2,3,4,5,6,7,8,9]);//可生成110注
     * 例如:sanbao1(['pos'=>['a','b','c'],'number'=>[3,4]],[0,1,2,3,4,5,6,7,8,9]);//可生成110注
     *
     * @param string|array $raw
     *
     * @return bool
     */
    public function sanbao1($raw)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        if (isset($raw['pos'])) {
            $tmp[$raw['pos'][0]] = [];
            $tmp[$raw['pos'][1]] = [];
            $tmp[$raw['pos'][2]] = $raw['number'];
            $raw = $tmp;
        }
        // 参数合法性验证
        $number = $this->filterEmpty($raw);
        $key = key($number); // 胆位位号
        $current = current($number); // 胆位数字数组
        sort($current);
        if (count($raw) != 3 || count($number) != 1 || $current != array_intersect($current, $this->code_list)) {
            return false;
        }

        $pos = array_keys($raw);
        $key0 = $pos[0];
        $key1 = $pos[1];
        $key2 = $pos[2];

        $result = [];
        foreach ($current as $value) {
            if ($key == $key0) {
                $k1 = $key1;
                $k2 = $key2;
            }
            if ($key == $key1) {
                $k1 = $key0;
                $k2 = $key2;
            }
            if ($key == $key2) {
                $k1 = $key0;
                $k2 = $key1;
            }

            $a = $this->code_list;
            $b = $this->code_list;
            $array = [];
            foreach ($a as $v) {
                foreach ($b as $vv) {
                    $tmp = [
                        $v,
                        $vv
                    ];
                    sort($tmp);
                    if (! in_array($tmp, $array)) {
                        $array[] = $tmp;
                    }
                }
            }

            foreach ($array as $n) {
                $tmp2[$key] = $value;
                $tmp2[$k1] = $n[0];
                $tmp2[$k2] = $n[1];
                krsort($tmp2);
                $result[] = $tmp2;
            }
        }
        return $result;
    }

    /**
     * 任三组选包2胆生成投注号,支持两种参数格式
     * 例如:sanbao2(['a'=>[3,4],'b'=>[2],'c'=>[]],[0,1,2,3,4,5,6,7,8,9]);//可生成20注
     * 例如:sanbao1(['pos'=>['a','b','c'],'number'=>[3,4],'number2'=>[2],[0,1,2,3,4,5,6,7,8,9]);//可生成20注
     *
     * @param string|array $raw
     *
     * @return array|bool
     */
    public function sanbao2($raw)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        $result = array();
        $this->renCombination($raw['number'], 2, '', $result);
        $return = array();
        foreach ($result as &$v) {
            $v = trim($v);
            $v = explode(' ', $v);
            foreach ($this->code_list as $code) {
                $t_v = $v;
                $t_v[] = $code;
                $return[] = array_combine($raw['pos'], $t_v);
            }
        }

        return $return;

        if (isset($raw['pos'])) {
            $tmp[$raw['pos'][0]] = $raw['number'];
            $tmp[$raw['pos'][1]] = $raw['number2'];
            $tmp[$raw['pos'][2]] = [];
            $raw = $tmp;
        }

        $number = $this->filterEmpty($raw);
        $current = current($number); // 胆位数字数组
        // 参数合法性验证
        if (count($number) != 2 || count($raw) != 3 || $current != array_intersect($current, $this->code_list)) {
            return false;
        }
        next($number);
        $current = current($number); // 胆位数字数组
        if ($current != array_intersect($current, $this->code_list)) {
            return false;
        }

        $key = array_keys($raw, []); // 空位的key
        $raw[$key[0]] = $this->code_list;
        return $this->normal($raw); // 不过滤
    }

    /**
     * 三星直选包1胆投注号生成(ok)
     *
     * @param string|array $raw
     *            格式1: {"a":[],"b":[],"c":[1,2,3]},格式2：{"pos":["a","b","c"],"number":[1,2,3]}
     * @param int $n
     *
     * @return array
     *
     */
    public function sanzhi1($raw, $n = 3)
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (isset($raw['pos'])) {
            $tmp[$raw['pos'][0]] = $raw['number'];
            $tmp[$raw['pos'][1]] = '';
            if ($n == 3) {
                $tmp[$raw['pos'][2]] = '';
            }
            $raw = $tmp;
        }
        if (count($raw) != $n) {
            return [];
        }
        $filted = $this->filterEmpty($raw);
        $a = array_values($filted);
        sort($a);
        $result = [];
        if (count($a) == 1 && $a[0] == array_intersect($a[0], $this->code_list)) {
            $key = key($filted);
            foreach ($a[0] as $v) {
                $item = $raw;
                $item[$key] = $v;
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * 2星直选包1胆投注号生成(ok)
     *
     * @param string|array $raw
     * @return array
     *
     */
    public function erzhi1($raw)
    {
        return $this->sanzhi1($raw, 2);
    }
}