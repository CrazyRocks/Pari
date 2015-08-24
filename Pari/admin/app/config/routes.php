<?php

// 如果不使用默认的路由规则,请传入参数false
// 此时必需完全匹配路由表,否则调用默认的index/index
$router = new Phalcon\Mvc\Router();
// 如果URL以/结尾,删除这个/
$router->removeExtraSlashes(false);

$router->add('/', [
    'controller' => 'home',
    'action'     => 'index'
]);

// Not Found Paths
$router->notFound([
    'controller' => 'index',
    'action' => 'show404'
]);

return $router;
