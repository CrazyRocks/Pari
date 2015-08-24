<?php
/**
 * @package configuration
 * @author Linvas
 */

$config = include __DIR__ . '/../../../common/config/config.php';

$configM = new \Phalcon\Config([
        'application' => [
            'controllersDir' => __DIR__ . '/../controllers/',
            'viewsDir' => __DIR__ . '/../views/',
            'baseUri' => '/',
        ]
    ]
);

$config->merge($configM);

return $config;