<?php
/**
 * 基类.
 */
namespace Pari\Game\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

/**
 * Class ControllerBase
 * @author Linvas
 * @package Pari\Game\Controllers
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
    protected function initialize()
    {
        //渲染
        $volt = $this->di->get("voltService", [ $this->view,  $this->di ]);
        //        $this->tag->prependTitle('INVO | ');
        //        $this->view->setTemplateAfter('main');
        //set header's js and css
        $this->assets->collection('header');
        //set footer's js and css
        $this->assets->collection('footer');

    }

    /**
     * @param string $uri 'controller/action'
     * @return mixed
     */
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
     * TODO 输出信息
     *
     * @param string $msg 输出信息
     * @param string /array $url 跳转地址 当$url为数组时，结构为 array('msg'=>'跳转连接文字','url'=>'跳转连接');
     * @param string $msg_type 信息类型 succ 为成功，error为失败/错误
     * @param int $time 跳转时间，默认为2秒
     * @return string 字符串类型的返回结果
     */
    public function getShowMsg($msg, $url = '', $msg_type = 'succ', $time = 2000 , $type = 'html')
    {
        if(intval($time) < 1)
            $time = 2000;
        $data['url'] = ($url != '' ? $url : empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER']);
        $data['msg'] = $msg;
        $data['msg_type'] = in_array($msg_type, ['succ', 'err']) ? $msg_type : 'err';
        $data['time'] = $time;
        if($type == 'js'){
            $data['msg'] = "<script>alert('". $msg ."');location.href='". $data['url'] ."'</script>";
        }
        return $data;
    }

    /**
     * 基于cookie自动登录后，跳回前一个页面
     * @return \Phalcon\Http\ResponseInterface
     */
    public function goBackUrl($type = 'pc')
    {
        $referer = $this->request->getHTTPReferer();
        $url = '';
        if (!empty($referer)) {
            $host = parse_url($referer, PHP_URL_HOST);
            if ($host != 'sso.' && substr($host, strpos($host, '.')) == '.kinhom.com') {
                $url = $referer;
            }
        }
        if (empty($url)) {
            $type = strtolower($type);
            switch ($type) {
                case 'wap':
                    $url = 'http://' . $this->config->domain->wap;
                    break;

                case 'pc':
                    $url = 'http://' . $this->config->domain->home;
                    break;

                default:
                    $url = 'http://' .$this->config->domain->home;
            }
        }
        return $url;
    }

}