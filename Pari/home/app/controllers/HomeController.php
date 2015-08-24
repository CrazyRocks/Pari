<?php
/**
 * 首页
 * index只做基本的模板渲染, 其它控制器
 * User: Admin
 * Date: 2015/1/15
 * Time: 12:23
 */
namespace Pari\Home\Controllers;

class HomeController extends ControllerBase{


    /**
     * @todo 网站首页
     */
    public function IndexAction(){

        //TODO 读取用户登录信息

        //TODO 读取网站公告

        //TODO 读取游戏列表

        //TODO 从COOKIES读取最近游戏列表

        //TODO 读取左侧图片广告

        //TODO 读取右侧商家推广广告

        //TODO 读取热门游戏

        //TODO 读取信誉房间
        echo $this->service->validate()->Salt();
//        $this->view->pick('home/index');
    }

    /**
     * @todo 公告栏
     */
    public function bulletinAction(){
        if($this->config->debug == false){
            $bulletin = $this->cache->get('HomeBulletin') ;
        }

        if(empty($bulletin)){
            $bulletin = $this->service->home()->getBulletin();
            $this->config->debug == false ? $this->cache->save('HomeBulletin') : '';
        }
        return $this->response->setJsonContent();
    }

    public function headerAction(){
        $header = $this->cache->get('HomeHeader') ;

    }

    /**
     * @todo 左侧广告
     */
    public function leftAdAction(){

    }

    /**
     * @todo 右侧广告
     */
    public function rightAdAction(){

    }

    /**
     * @todo 首页游戏列表
     */
    public function gameListAction(){
        //读取客户的cookies列表
        $gList = $this->cookies->get('fav');
        //读取游戏列表
        $gList .= $this->service->home()->gameList();

        return $this->response->setJsonContent($gList);
    }

    /**
     * @todo 首页中间部分
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function containerAction(){
        $container = '1';
        return $this->response->setJsonContent($container);
    }

    /**
     * @todo 首页底部信息
     */
    public function footerAction(){
        //底部链接和文字
        $this->service->home()->footer();
    }

    public function cache(){
        //删除所有缓存
        $this->service->home()->getHeader();;
        //重建所有缓存
    }




}