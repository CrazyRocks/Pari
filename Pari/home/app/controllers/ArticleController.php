<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 5/23/2015
 * Time: 12:04 PM
 */
namespace Pari\Home\Controllers;

use Phalcon\Mvc\View;

/**
 * 个人中心
 */
class ArticleController extends ControllerBase
{
    /**
     * (non-PHPdoc)
     * @see \Caino\Www\Controllers\ControllerBase::initialize()
     */
    public function initialize()
    {
        parent::initialize();
    }

    public function indexAction($article_id){

        $this->service->article();

    }

}