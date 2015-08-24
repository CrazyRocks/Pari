<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2015/1/19
 * Time: 11:08
 */


namespace Pari\Dev\Controllers;
use Phalcon\Mvc\Controller;

/**
 * Class ControllerBase
 * @package Pari\Www\Controllers
 * @property \Phalcon\Queue\Beanstalk            $queue
 * @property \Phalcon\Tag                        $tag
 * @property \Phalcon\Cache\BackendInterface     $cache
 * @property \Phalcon\Http\Response\Cookies      $cookies
 * @property \Phalcon\Escaper                    $escaper
 */
class ControllerBase extends Controller
{
    protected function initialize()
    {
//        $this->tag->prependTitle('INVO | ');
//        $this->view->setTemplateAfter('main');
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
}