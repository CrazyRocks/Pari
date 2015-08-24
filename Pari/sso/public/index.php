<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2015/1/16
 * Time: 14:00
 */

error_reporting(E_ALL);
header('Content-type: text/html; charset=utf-8');

//允许跨域携带cookies
header( 'Access-Control-Allow-Credentials:true' );
// 指定可信任的域名来接收响应信息， 推荐


//defined('ROOT_PATH')||define('ROOT_PATH', dirname(dirname(__FILE__)));

try {
    define('APP_PATH', realpath(__DIR__ . '/..') . '/');
    /**
     * Read the configuration
     */
    $config = include __DIR__ . '/../app/config/config.php';

    /**
     * Read the auto-loader
     */
    include __DIR__ . '/../app/config/loader.php';

    /**
     * Read services
     */
    include __DIR__ . '/../app/config/services.php';

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);
    echo $application->handle()->getContent();

} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
}