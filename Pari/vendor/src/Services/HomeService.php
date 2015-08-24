<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 6/1/2015
 * Time: 5:39 PM
 */


namespace Pari\Services;
;

use Pari\Models\Article;

/**
 * @todo 首页展示
 */
class HomeService extends BaseService
{
    /**
     * @todo 获取头部信息
     */
    public function getHeader()
    {
            //TODO 获取轮播广告
        

    }

    /**
     * @param int $num
     * @return null|string
     */
    public function getBulletin($num = 5)
    {
        $num = intval($num) < 1 ? $num : 5;
        $bulletin = Article::find([
            'type = 0',
            'order' => 'addtime DESC',
            'limit' => $num,
        ]);
        return empty($bulletin) || count($bulletin) < 1 ? null : json_encode($bulletin->toArray());
    }

    public function getContainer()
    {
        //返回热门推荐游戏
        GList::find('is_recommend = 1');

        //返回热门游戏
    }

    public function getLeftAd()
    {

    }

    public function getRightAd()
    {
        1
    }

    /**
     * 获取友情链接
     */
    public function getLinks()
    {
        $friendLink = Link::find("1 order by link_sort desc")->toarray();
        return $friendLink;
    }

    /**
     *
     */
    public function getFooter()
    {

    }
}