<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2015/1/16
 * Time: 14:00
 */

error_reporting(E_ALL);
header("Content-type: text/html; charset=utf-8");
//defined('ROOT_PATH')||define('ROOT_PATH', dirname(dirname(__FILE__)));

try {

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
} catch (PDOException $e){
    echo $e->getMessage();
}