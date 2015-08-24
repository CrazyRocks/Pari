<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2015/1/19
 * Time: 11:08
 */


namespace Pari\Sso\Controllers;
use Phalcon\Mvc\Controller,
    Pari\Services\UserService;

/**
 * Class ControllerBase
 * @package Pari\Sso\Controllers
 * @property \Phalcon\Queue\Beanstalk            $queue
 * @property \Phalcon\Tag                        $tag
 * @property \Phalcon\Cache\BackendInterface     $cache
 * @property \Phalcon\Http\Response\Cookies      $cookies
 * @property \Phalcon\Escaper                    $escaper
 * @property \Pari\Services                    $service
 * @property \Redis                              $redis
 */
class ControllerBase extends Controller
{
    public function initialize()
    {
        // 模板中引用auth对象显示用户信息
//        $this->view->auth = $this->getMemberAuth();

        // 使用模板继承后，需要指定Action渲染级别
//        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        //TODO 获取用户信息
        $ouser = $this->getOuserAuth()->getUser();
        if ($ouser) {
            $this->ouser = $ouser;
            $this->view->ouserAuth = $ouser;
            $this->view->rand = rand(1, 9999);
        }else {
            $this->response->redirect('http://'.$this->config->domain->o2o.'/login');
        }
    }

    protected function forward($uri)
    {
        $uriParts = explode('/', $uri);
        $params = array_slice($uriParts, 2);
        return $this->dispatcher->forward(
            [
                'controller' => $uriParts[0],
                'action' => $uriParts[1],
                'params' => $params
            ]
        );
    }


    /**
     * filter $_GET request
     * @return array
     */
    protected function getQuery()
    {
        $get = $this->request->getQuery();
        unset($get['_url']);
        $data = [];
        foreach($get as $k=>$v){
            $k = htmlspecialchars($k);
            $v = htmlspecialchars($v);
            $data[$k] = $v;
        }
        return $data;
    }

    /**
     * 生成带参数的url, 包装一层是为了统一生成算法
     * @param $arr
     * @return string
     */
    protected function buildQuery($arr)
    {
        return http_build_query($arr, '', '&amp;');
    }

    /**
     * 异步头部处理
     */
    public function ajaxView()
    {
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');
    }

    /**
     * 数组改成字符串处理，方便查询
     */
    public function arrayChageString($data)
    {
        if(is_array($data) and !empty($data)) {
            $data = implode(',', $data);
        }
        return $data;
    }

    /**
     * @todo 基于cookie自动登录后，跳回前一个页面, 该跳转不支持.com.cn此类双重后缀,不支持2级子域名, 如:m.wap.qq.com.cn
     * @param string $type
     */
    public function goBack($type = 'pc')
    {
        $type = strtolower($type);

        $referer = parse_url($this->request->getHTTPReferer(), PHP_URL_HOST);

        $host = $this->request->getHttpHost();

        $url = 'http://' . (($type == 'mobile' || $type == 'wap') ? $this->config->domain->mobile : $this->config->domain->home);

        if (substr($referer, strpos($referer, '.')) == substr($host, strpos($host, '.'))) {
            $url = $this->request->getHTTPReferer();
        }

        $this->response->redirect($url);
    }
}