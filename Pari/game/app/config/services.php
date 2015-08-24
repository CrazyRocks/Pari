<?php

use Phalcon\Mvc\Dispatcher as MvcDispatcher,
    Phalcon\Mvc\Dispatcher\Exception as DispatchException,
    Phalcon\Events\Manager as EventsManager;

include __DIR__ . '/../../../common/config/services.php';

error_reporting(7);

/* @var $di Phalcon\DI\FactoryDefault */
$di->setShared('router', function () {
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
        'action' => 'index'
    ]);
    return $router;
});


/**
 * debug模式使用File存储，正式使用memcache，未来使用redis
 */
$di->setShared('cache', function () use ($config) {
    // 默认15分钟
    $frontCache = new \Phalcon\Cache\Frontend\Data([
        "lifetime" => 900
    ]);

    if ($config->debug) {
        return new \Phalcon\Cache\Backend\File($frontCache, [
            "cacheDir" => __DIR__ . "/../cache/"
        ]);
    } else {
        return new \Phalcon\Cache\Backend\Redis($frontCache, [
            "host" => $config->redis->host,
            "port" => $config->redis->port,
            'persistent' => $config->redis->persistent,
            "prefix" => $config->redis->prefix
        ]);
    }

});


$di->setShared('dispatcher', function () {
    //Create an EventsManager
    $eventsManager = new EventsManager();
    //Attach a listener
    $eventsManager->attach("dispatch:beforeException", function ($event, $dispatcher, $exception) {
        //Handle 404 exceptions
        if ($exception instanceof DispatchException) {
            $dispatcher->forward([
                'controller' => 'index',
                'action'     => 'show404',
                'params'    => ['message' => $exception->getMessage()]
            ]);
            return false;
        }
        //Alternative way, controller or action doesn't exist
//        if ($event->getType() == 'beforeException') {
//            switch ($exception->getCode()) {
//                case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
//                case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
//                    $dispatcher->forward([
//                        'controller' => 'index',
//                        'action' => 'show404',
//                        'params' => ['message' => $exception->getMessage()]
//                    ]);
//                    return false;
//            }
//        }
    });
    /**
     * We listen for events in the dispatcher using the Security plugin
     */
//    $security = new Security($di);
//    $eventsManager->attach('dispatch', $security);

    $dispatcher = new MvcDispatcher();

    $dispatcher->setDefaultNamespace('\Pari\Game\Controllers');
    //Bind the EventsManager to the dispatcher
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});


