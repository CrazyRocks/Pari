<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 6/1/2015
 * Time: 5:46 PM
 */
namespace Pari\Services;



class ArticleService extends BaseService{


    public function getText($ArticleId){
        $article = \Pari\Models\Article::findFirst($ArticleId);
        return $article == false ? null : $article->toArray();
    }

    /**
     * @param null $type
     * @return \Pari\Models\Article[]
     */
    public function getAll($type = null){

        $articles = $type == null ?  \Pari\Models\Article::find(['order'=>'updatetime DESC']) : \Pari\Models\Article::find(["type = '$type'" ,'order'=>'updatetime DESC']);

        return count($articles) > 0 ? json_encode($articles->toArray()) : false;
    }


}