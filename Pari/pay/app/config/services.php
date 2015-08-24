<?php

use Phalcon\Mvc\Dispatcher as MvcDispatcher,
    Phalcon\Mvc\Dispatcher\Exception as DispatchException,
    Phalcon\Events\Manager as EventsManager;

include __DIR__ . '/../../../common/config/services.php';

/* @var $di Phalcon\DI\FactoryDefault */
$di->setShared('router', function() {
    return include __DIR__ . '/routes.php';
});

$di->setShared('dispatcher', function () {
    //Create an EventsManager
    $eventsManager = new EventsManager();

    //Attach a listener
    $eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {

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
        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward([
                        'controller' => 'index',
                        'action'     => 'show404',
                        'params'    => ['message' => $exception->getMessage()]
                    ]);
                    return false;
            }
        }
    });

    /**
     * We listen for events in the dispatcher using the Security plugin
     */
//    $security = new Security($di);
//    $eventsManager->attach('dispatch', $security);

    $dispatcher = new MvcDispatcher();

    $dispatcher->setDefaultNamespace('\Pari\\Controllers');

    //Bind the EventsManager to the dispatcher
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});


