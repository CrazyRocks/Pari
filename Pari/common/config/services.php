<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2015/1/15
 * Time: 11:25
 */

use Phalcon\DI\FactoryDefault,
    Phalcon\Mvc\View,
    Phalcon\Mvc\Url as UrlResolver,
    Phalcon\Mvc\Model\MetaData\Files as MetaDataFiles,
    Phalcon\Mvc\Model\MetaData\Memory as MetaDataMemory ,
    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
//    Phalcon\Session\Adapter\Files as SessionAdapterFiles,
    Phalcon\Events\Manager as EventsManager,
    Phalcon\Queue\Beanstalk,
    Phalcon\Logger,
    Phalcon\Logger\Adapter\File as FileLogger;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */

$di = new FactoryDefault();

//inject config
$di->set('config', $config, true);

/**
 * set crypt, 在cookies使用时候可以进行加密
 */
$di->set('crypt', function () {
    $crypt = new Phalcon\Crypt();
    /**
     * 别用PADDING_DEFAULT影响cookies的取值
     */
    $crypt->setPadding(\Phalcon\Crypt::PADDING_ZERO);
    $crypt->setKey('?.aX/^~j1V$#1dj7'); // crypt string 必须为 16 , 24 ,32 三种长度
    return $crypt;
});

/**
 * 设置cookies和加密模式
 */
$di->setShared('cookies', function () {
    $cookies = new Phalcon\Http\Response\Cookies();
//    $cookies->useEncryption(false); //如果为true则使用下面的crypt加密 , 如果使用加密那速度非常慢
    return $cookies;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () use ($config) {
    $url = new Phalcon\Mvc\Url();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});


//=================================以下为未确认===========================================
/**
 * 把volt注册成服务，在controller中改变volt默认行为
 * example:
 * 给模板引擎添加unserialize函数
 * <code>
 *     $volt = $this->di->get("voltService", array($this->view, $this->di));
 *     $compiler = $volt->getCompiler();
 *     $compiler->addFunction('unserialize', 'unserialize');
 * </code>
 */
$di->setShared('voltService', function ($view, $di) use ($config) {

    $volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);

    $volt->setOptions([
        'compiledPath' => $config->application->cacheDir,
        'compiledSeparator' => '_',
        'compileAlways' => true, //true为总是编译成php输出
    ]);

    // TODO: 偶尔使用的function不要在这里添加，要在controller中设置
    $compiler = $volt->getCompiler();
    $compiler->addFunction('current', 'current');
    $compiler->addFunction('explode', 'explode');
    $compiler->addFunction('strpos', 'strpos');
    $compiler->addFunction('basename', 'basename');
    $compiler->addFunction('array_shift', 'array_shift');
    $compiler->addFunction('array_push', 'array_push');
    $compiler->addFilter('substr_replace', 'substr_replace');

    return $volt;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => 'voltService',
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ]);

    return $view;
});

/**
 * debug模式记录查询SQL日志
 */
$di->setShared('db', function () use ($config) {
    $db = new DbAdapter([
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset,
        'persistent' => $config->database->persistent, //使用长连接
        'options' => [
            PDO::ATTR_TIMEOUT => 1,
            // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ],
    ]);

    if ($config->debug) {
        $eventsManager = new EventsManager();
        $logger = new FileLogger(PROJ_DIR . '/common/logs/debug.log');
        $eventsManager->attach('db', function ($event, $connection) use ($logger) {
            if ($event->getType() == 'beforeQuery') {
                $logger->log($connection->getSQLStatement(), Logger::INFO);
            }
        });
        $db->setEventsManager($eventsManager);
    }

    return $db;

});
$di->setShared('dbSlave', function () use ($config) {
    $db = new DbAdapter([
        'host' => $config->db->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset,
        "options" => [
            PDO::ATTR_TIMEOUT => "1",
            // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ],
    ]);

    if ($config->debug) {
        $eventsManager = new EventsManager();
        $logger = new FileLogger(PROJ_DIR . '/common/logs/debug.log');
        $eventsManager->attach('db', function ($event, $connection) use ($logger) {
            if ($event->getType() == 'beforeQuery') {
                $logger->log($connection->getSQLStatement(), Logger::INFO);
            }
        });
        $db->setEventsManager($eventsManager);
    }
    return $db;
});

/**
 * /**
 * debug模式不缓存
 */
$di->set('modelsMetadata', function () use ($config) {
    if ($config->debug) {
        return new MetaDataMemory();
    } else {
        return new MetaDataFiles([
            'metaDataDir' => PROJ_DIR . '/common/cache/metadata/'
        ]);
    }
});
/**
 * MongoDB 接口
 */
$di->setShared('mongo', function () use ($config) {
    $mongo = new MongoClient($config->mongo->server);
    return $mongo->selectDb($config->mongo->db);
});
// 设置MongoDB ODM
$di->setShared('collectionManager', function () {
    $modelsManager = new Phalcon\Mvc\Collection\Manager();
    return $modelsManager;
});

/**
 * 设置session, debug模式使用File存储，正式使用redis
 */
$di->setShared('session', function () use ($config) {
    if ($config->debug) {
        $session = new Phalcon\Session\Adapter\Files();
        //下面第四个参数, 在SSL下要设置为true
        session_set_cookie_params(10800, '/', $config->domain->cookie, false, true);
        session_name('pari'); // 不使用默认的PHPSESSID

    } else {
        $session = new Phalcon\Session\Adapter\Redis([
            'path' => $config->session->path,
            'name' => $config->session->name,
            'lifetime' => $config->session->lifetime,
            'cookie_lifetime' => $config->session->cookie_lifetime,
            'cookie_domain' => $config->session->cookie_domain
        ]);
    }
    $session->start();
    return $session;
});


/**
 * debug模式使用File存储，正式使用redis
 */
$di->setShared('cache', function () use ($config) {
    // 默认15分钟
    $frontCache = new \Phalcon\Cache\Frontend\Data([
        "lifetime" => 900
    ]);

    if ($config->debug == true) {
        return new \Phalcon\Cache\Backend\File($frontCache, [
            "cacheDir" => PROJ_DIR . "/common/cache/"
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
// Redis
$di->setShared('redis', function () use ($config) {
//    if($config->redis->status){
    $redis = new Redis();
    $redis->pconnect($config->redis->host, $config->redis->port, $config->redis->timeout);
    return $redis;
//    }
});

/**
 * Beanstalk消息队列
 */
//$di->set('queue', function() use ($config) {
//    $queue = new \Phalcon\Queue\Beanstalk($config->beanstalk->toArray());
//    return $queue;
//}, true);


$di->setShared('service', function (){
    return new Pari\Services;
});



















