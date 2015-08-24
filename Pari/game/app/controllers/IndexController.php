<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 5/23/2015
 * Time: 1:06 PM
 */
namespace Pari\Game\Controllers;

class IndexController extends ControllerBase{

    public function indexAction(){
        echo "yes";
    }

    public function show404Action(){
        echo 'Error! 404';
        die;
    }
}