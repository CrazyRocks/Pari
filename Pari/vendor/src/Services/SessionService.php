<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 6/18/2015
 * Time: 5:48 PM
 */

namespace Pari\Services;

use Pari\Models\Lang;

/**
 * 检测用户
 * Class detect
 * @package Pari\Services
 */
class SessionService extends BaseService
{
    /**
     * @TODO
     * @return bool
     */
    public function lockSession()
    {
        time() - $this->session->get('locktime') > $this->config->session->timeout ?
            $this->session->destroy('clientID') : $this->session->set('locktime', time());
        return empty($_SESSION['clientID']) ? false : true;
    }

    public function checkSession()
    {
        echo '111111111';
    }
}