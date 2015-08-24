<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 6/1/2015
 * Time: 5:47 PM
 */
namespace Pari\Services;

use Kinhom\Services\BaseService;

class Order extends BaseService{

    /**
     * @todo  投注
     */

    /**
     * 投注
     * 1. 验证订单数据验证有用户数据
     * 2. 验证用户余额是否足够
     * 3.
     */
    public function bet()
    {
        //从Post读取数据流
        $http_raw_post_data = file_get_contents('php://input');
        if (empty($http_raw_post_data) ||
            !($cart = json_decode($http_raw_post_data, true)) ||
            empty($cart) ||
            !is_array($cart)
        ) {
            return $this->response->setJsonContent([
                'status' => 2,
                'info' => '订单数据不正确！'
            ]);
        }

        $first_cart = current($cart);
        if (!isset($first_cart['lottery_id']) ||
            empty($first_cart['lottery_id']) ||
            !isset($first_cart['expect']) ||
            empty($first_cart['expect'])
        ) {
            return $this->response->setJsonContent([
                'status' => 3,
                'info' => '订单数据不正确！'
            ]);
        }

        $lottery_model = \Models\Lottery::findFirst(array(
            'conditions' => 'id=?0 AND status=1 AND type!=3',
            'bind' => array($first_cart['lottery_id'])
        ));
        if (empty($lottery_model)) {
            return $this->response->setJsonContent(array(
                'status' => 4,
                'info' => '游戏不存在或已被删除！'
            ));
        }

        $now_timestamp = time();
        $fetch = new \Lottery\Fetch\Fetch($lottery_model->name, $now_timestamp);
        $fetch_platform = $fetch->getPlatform();
        $salesInfo = $fetch_platform->getCurrentSaleExpectInfo($lottery_model->deadline);
        if (empty($salesInfo['status'])) {
            return $this->response->setJsonContent(array(
                'status' => 5,
                'info' => '市场已关闭，官网停止开奖'
            ));
        }

        if ($first_cart['expect'] != $salesInfo['expect']) {
            return $this->response->setJsonContent(array(
                'status' => 6,
                'info' => '该期彩票还未开始销售或已结束销售！'
            ));
        }
        // TODO 判断是否中奖
        $assertion = new \Lottery\Assertion\Assertion();
        $lottery_bet_datas = $lottery_afternumber_data = $lottery_afternumber_bet_datas = array();
        $cart_num = ukey_next_id();
        $ip = \Utils\Func::getClientIp(1, \GET_CLIENTIP_MODE);
        $hardware_info = \Local\Func::getHardwareInfo();
        $bet_platform = 1;
        $is_software = intval($this->request->getPost('is_software'));
        if ($is_software && $hardware_info) {
            $bet_platform = 3;
        } elseif ($hardware_info) {
            $bet_platform = 2;
        }
        $total_bet_money = 0;

        foreach ($cart as $raw) {
            if (!isset($raw['lottery_id']) ||
                empty($raw['lottery_id']) ||
                $raw['lottery_id'] != $lottery_model->id ||
                !isset($raw['expect']) ||
                empty($raw['expect']) ||
                $raw['expect'] < $salesInfo['expect'] ||
                !isset($raw['mode']) ||
                !in_array($raw['mode'], array('角', '2角', '分', '2分', '1元', '2元', '元', '1角')) ||
                !isset($raw['times']) ||
                !isset($raw['rate']) ||
                !isset($raw['raw']) ||
                empty($raw['raw']) ||
                !isset($raw['data']) ||
                empty($raw['data']) ||
                !isset($raw['is_manual']) ||
                (!$raw['is_manual'] && !is_array($raw['data'])) ||
                ($raw['is_manual'] && !is_string($raw['data']))
            ) {
                return $this->response->setJsonContent(array(
                    'status' => 7,
                    'info' => '订单数据不正确！'
                ));
            }

            if (!isset($raw['playway_id']) ||
                empty($raw['playway_id']) ||
                !($playway = \Models\Lottery\Playway::getPlaywayByLotteryIdAndPlaywayId($lottery_model->id, $raw['playway_id']))
            ) {
                return $this->response->setJsonContent(array(
                    'status' => 8,
                    'info' => '您投注的玩法玩法不存在！'
                ));
            }

            $raw['times'] = intval($raw['times']);
            if ($raw['times'] < 1 || $raw['times'] > 9999) {
                return $this->response->setJsonContent(array(
                    'status' => 9,
                    'info' => '您投注的倍数不正确！'
                ));
            }

            $rate_max = $lottery_model->rate_max;
            if ($this->loggedInfo && ($rate_ctrl = \Models\Lottery\Ctrl::findFirst(array(
                    'conditions' => 'id=?0 AND type=4',
                    'bind' => array($this->loggedInfo->uid)
                )))
            ) {
                $rate_ctrl->value = json_decode($rate_ctrl->value, true);
                if (isset($rate_ctrl->value[$lottery_model->id])) {
                    $rate_max = $rate_ctrl->value[$lottery_model->id];
                }
            }

            $raw['rate'] = intval($raw['rate']);
            if ($raw['rate'] < $lottery_model->rate_min || $raw['rate'] > $rate_max) {
                return $this->response->setJsonContent(array(
                    'status' => 10,
                    'info' => '您投注的比例不正确！'
                ));
            }

            $raw['is_manual'] = intval($raw['is_manual']);
            if ($raw['is_manual']) {
                $raw['is_manual'] = 1;
            }

            $bet_count = 0;
            if ($raw['is_manual']) {
                if ($playway['split'] == 'ssc') {
                    //时时彩拆号,每注使用 空格或,;回车隔开
                    preg_match_all('/\d+/', $raw['data'], $matches);
                    $bet_count = isset($matches[0]) ? count($matches[0]) : 0;
                } elseif ($playway['split'] == '11x5') {
                    $matches = explode(',', $raw['data']);
                    $bet_count = count($matches);
                }
            } else {
                $assertionPlaywayType = $assertion->getAssertionPlaywayType($playway['playway_type_name']);
                if (!empty($assertionPlaywayType)) {
                    $bet_count = $assertionPlaywayType->getBetCount($playway, $raw['data']);
                }
            }
            if ($bet_count <= 0) {
                return $this->response->setJsonContent(array(
                    'status' => 11,
                    'info' => '订单数据不正确！'
                ));
            }

            $bet_money_rate = 1;
            if ($raw['mode'] == '角' || $raw['mode'] == '2角') {
                $bet_money_rate = 0.1;
            } elseif ($raw['mode'] == '分' || $raw['mode'] == '2分') {
                $bet_money_rate = 0.01;
            } else if ($raw['mode'] == '1元') {
                $bet_money_rate = 0.5;
            } else if ($raw['mode'] == '1角') {
                $bet_money_rate = 0.05;
            }
            $bet_money = bcmul($playway['price'] * $bet_money_rate, $raw['times'] * $bet_count, 2);
            if ($bet_money <= 0) {
                return $this->response->setJsonContent(array(
                    'status' => 12,
                    'info' => '订单数据不正确！'
                ));
            }
            $total_bet_money += $bet_money;

            if ($playway['playway_type_name'] == 'ssc-chonghaocount') {
                $raw['data']['number2'] = array($playway['min_bet_num']);
            }

            $lottery_bet_data = array(
                'id' => ukey_next_id(),
                'cart_num' => $cart_num,
                'uid' => $this->loggedInfo ? $this->loggedInfo->uid : $this->trial_user->uid,
                'lottery_id' => $raw['lottery_id'],
                'playway_id' => $raw['playway_id'],
                'afternumber_id' => 0,
                'expect' => $raw['expect'],
                'json_data' => $raw['data'],
                'data' => $raw['raw'],
                'bet_rate' => $raw['rate'],
                'bet_times' => $raw['times'],
                'bet_mode' => $raw['mode'],
                'is_manual' => $raw['is_manual'],
                'bet_platform' => $bet_platform,
                'bet_count' => $bet_count,
                'bet_money' => $bet_money,
                'valid_bet_money' => $bet_money,
                'bonus_count' => 0,
                'bonus_money' => 0,
                'refund_rate' => 0,
                'refund_money' => 0,
                'after_bet_money' => 0,
                'after_fresh_money' => 0,
                'ip' => $ip,
                'hardware_info' => $hardware_info,
                'addtime' => $now_timestamp,
                'updatetime' => $now_timestamp,
                'status' => 1
            );
            $lottery_bet_datas[] = $lottery_bet_data;

            //追号
            if ($this->loggedInfo) {
                $afternumber_start_expect = $lottery_bet_data['expect'];
                $afternumber_end_expect = $lottery_bet_data['expect'];
                $afternumber_expect_count = 1;
                if (isset($raw['afternumberData']) &&
                    !empty($raw['afternumberData']) &&
                    is_array($raw['afternumberData'])
                ) {
                    foreach ($raw['afternumberData'] as $v) {
                        if (!isset($v['expect']) ||
                            empty($v['expect']) ||
                            $v['expect'] < $salesInfo['expect'] ||
                            !isset($v['times'])
                        ) {
                            return $this->response->setJsonContent(array(
                                'status' => 13,
                                'info' => '订单数据不正确！'
                            ));
                        }
                        $v['times'] = intval($v['times']);
                        if ($v['times'] < 1) {
                            return $this->response->setJsonContent(array(
                                'status' => 14,
                                'info' => '订单数据不正确！'
                            ));
                        }

                        $lottery_afternumber_bet_data = $lottery_bet_data;
                        $lottery_afternumber_bet_data['id'] = ukey_next_id();
                        $lottery_afternumber_bet_data['expect'] = $v['expect'];
                        $lottery_afternumber_bet_data['bet_times'] = $v['times'];
                        $lottery_afternumber_bet_data['bet_money'] = ($lottery_bet_data['bet_money'] / $lottery_bet_data['bet_times']) * $v['times'];

                        $afternumber_expect_count++;
                        $afternumber_end_expect = $lottery_afternumber_bet_data['expect'];
                        $total_bet_money += $lottery_afternumber_bet_data['bet_money'];
                        $lottery_afternumber_bet_datas[] = $lottery_afternumber_bet_data;
                    }
                }
                if ($lottery_bet_data['expect'] > $salesInfo['expect'] ||
                    !empty($lottery_afternumber_bet_datas)
                ) {
                    $lottery_afternumber_data = array(
                        'uid' => $lottery_bet_data['uid'],
                        'lottery_id' => $lottery_bet_data['lottery_id'],
                        'start_expect' => $afternumber_start_expect,
                        'end_expect' => $afternumber_end_expect,
                        'expect_count' => $afternumber_expect_count,
                        'win_stop' => isset($raw['win_stop']) && $raw['win_stop'] ? 1 : 0,
                        'win_backout' => isset($raw['win_backout']) && $raw['win_backout'] ? 1 : 0,
                        'addtime' => $now_timestamp,
                        'updatetime' => $now_timestamp,
                        'status' => 1
                    );
                }
            }
        }

        /*
         * 非客户端投注判断
         */
        if ($bet_platform == 1 && $this->loggedInfo) {
            $lottery_ctrl = \Models\Lottery\Ctrl::findFirst(array(
                'conditions' => 'id=?0 AND type=2',
                'bind' => array($this->loggedInfo->uid)
            ));
            if ($lottery_ctrl && $lottery_ctrl->value) {
                return $this->response->setJsonContent(array(
                    'status' => -900,
                    'info' => '暂只允许使用客户端进行投注！'
                ));
            }
        }

        $db = $this->db;
        $db->begin();
        if ($this->loggedInfo) {
            $profile = \Models\User\Profile::findFirst(array(
                'conditions' => 'uid=?0',
                'bind' => array($this->loggedInfo->uid),
                'for_update' => true
            ));
        } else {
            $profile = \Models\Lottery\Trial\User::findFirst(array(
                'conditions' => 'uid=?0',
                'bind' => array($this->trial_user->uid),
                'for_update' => true
            ));
        }
        if (!$profile) {
            $db->rollback();
            return $this->response->setJsonContent(array(
                'status' => -500,
                'info' => '用户不存在！'
            ));
        }

        $before_money = $this->loggedInfo ? $profile->lottery_money : $profile->money;

        if ($before_money < $total_bet_money) {
            $db->rollback();
            return $this->response->setJsonContent(array(
                'status' => -501,
                'info' => '投注失败，余额不足！'
            ));
        }

        /*
         * 追号数据
         */
        if (!empty($lottery_afternumber_data)) {
            $lottery_afternumber_model = new \Models\Lottery\Afternumber();
            if (!$lottery_afternumber_model->create($lottery_afternumber_data)) {
                $db->rollback();
                $message = current($lottery_afternumber_model->getMessages());
                return $this->response->setJsonContent(array(
                    'status' => -601,
                    'info' => '投注失败：' . $message->getMessage() . '！'
                ));
            }
        }

        /*
         * 投注数据
         */
        foreach (array_merge($lottery_bet_datas, $lottery_afternumber_bet_datas) as $lottery_bet_data) {
            $lottery_bet_data['afternumber_id'] = $lottery_afternumber_model->id ? $lottery_afternumber_model->id : 0;
            $lottery_bet_data['after_bet_money'] = $before_money - $lottery_bet_data['bet_money'];
            if ($this->loggedInfo) {
                $lottery_bet_model = new \Models\Lottery\Bet();
            } else {
                $lottery_bet_model = new \Models\Lottery\Trial\Bet();
            }
            if (!$lottery_bet_model->create($lottery_bet_data)) {
                $db->rollback();
                $message = current($lottery_bet_model->getMessages());
                return $this->response->setJsonContent(array(
                    'status' => -502,
                    'info' => '投注失败：' . $message->getMessage() . '！'
                ));
            }

            if ($this->loggedInfo) {
                /*
                 * 帐表记录
                 */
                $moneyLogIsSuccess = false;
                try {
                    $moneyLog = new \Collections\User\Money\Log();
                    $moneyLog->account = 2;
                    $moneyLog->type = -1;
                    $moneyLog->uid = $this->loggedInfo->uid;
                    $moneyLog->username = $this->loggedInfo->username;
                    $moneyLog->oid = $lottery_bet_model->id;
                    $moneyLog->money = $lottery_bet_model->bet_money;
                    $moneyLog->before_money = $before_money;
                    $before_money -= $lottery_bet_model->bet_money;
                    $moneyLog->after_money = $before_money;
                    $moneyLog->message = '时时彩投注消费';
                    $moneyLogIsSuccess = $moneyLog->save();
                } catch (\MongoException $e) {
                }
                if (!$moneyLogIsSuccess) {
                    $db->rollback();
                    return $this->response->setJsonContent(array(
                        'status' => -503,
                        'info' => '订单数据提交失败，请重试！'
                    ));
                }
            }
        }

        if ($this->loggedInfo) {
            $profile->lottery_money -= $total_bet_money;
            $profile->bet_money += $total_bet_money;
        } else {
            $profile->money -= $total_bet_money;
        }
        if (!$profile->save()) {
            $db->rollback();
            $message = current($profile->getMessages());
            return $this->response->setJsonContent(array(
                'status' => -504,
                'info' => '投注失败：' . $message->getMessage() . '！'
            ));
        }

        $db->commit();
        $this->loggedInfo->profile = $profile;

        return $this->response->setJsonContent(array(
            'status' => 1,
            'info' => '投注成功！'
        ));
    }

    /**
     * 获取订单信息
     */
    public function getList(){

    }

    /**
     * 订单状态
     */
    public function status(){

    }


}