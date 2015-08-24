<?php
/**
 * @package configuration
 * @author Linvas
 * @description 配置文件
 */

defined('APP_PATH') || define('APP_PATH', realpath(__DIR__ . '/../..') . '/');

$config = include __DIR__ . '/../../../common/config/config.php';

$configM = new \Phalcon\Config([
        'application' => [
            'controllersDir' => __DIR__ . '/../controllers/',
            'viewsDir' => __DIR__ . '/../views/',
            'formsDir'       => APP_PATH . 'app/forms/',
            'libraryDir'     => APP_PATH . 'app/library',
            'pluginsDir'     => APP_PATH . 'app/plugins/',
            'baseUri' => '/',
        ]
    ]
);

$config->merge($configM);

return $config;