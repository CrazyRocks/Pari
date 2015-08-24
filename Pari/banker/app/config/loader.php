<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2015/1/19
 * Time: 11:01
 */


$loader = new \Phalcon\Loader();


/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs([
    $config->application->PackDir,
])
    ->registerNamespaces(
    [
        'Pari\Banker\Controllers'  => $config->application->controllersDir,
        'Pari' => $config->application->pariDir,
    ]
)->register();