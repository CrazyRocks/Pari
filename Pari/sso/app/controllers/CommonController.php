<?php
/**
 * 首页
 * User: Admin
 * Date: 2015/1/15
 * Time: 12:23
 */
namespace Pari\Sso\Controllers;

class CommonController extends ControllerBase{


    public function IndexAction(){
        echo "hello";
    }

    public function show404Action(){
        echo '404 page';
    }

    public function show500Action(){
        echo '500 page';
    }

}