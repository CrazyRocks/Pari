<?php

// 如果不使用默认的路由规则,请传入参数false
// 此时必需完全匹配路由表,否则调用默认的index/index
$router = new Phalcon\Mvc\Router();
// 如果URL以/结尾,删除这个/
$router->removeExtraSlashes(false);

// use $_SERVER['REQUEST_URI'] (default)
$router->setUriSource($router::URI_SOURCE_SERVER_REQUEST_URI);

// Not Found Paths
$router->notFound([
    'controller' => 'index',
    'action' => 'show404'
]);

$router->add('/', [
    'controller' => 'home',
    'action'     => 'index'
]);

return $router;
