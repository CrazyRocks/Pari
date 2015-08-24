<?php
/**
 * @todo Configuration
 */


defined('PROJ_DIR') || define('PROJ_DIR', str_replace('\\','/',realpath(__DIR__ . '/../../')));
//defined('DOMAIN') || define('DOMAIN', $_SERVER['HTTP_HOST']);
defined('DOMAIN') || define('DOMAIN', '8m.com');

// 设置时区 set timezone
date_default_timezone_set('Asia/Hong_Kong');

return new \Phalcon\Config([
    'debug' => true,
    'payment' => 0,
    'charset' => 'utf-8',
    'https' => 'false',
    'domain' => [
        'admin' => 'admin.' . DOMAIN,
        'api' => 'api.' . DOMAIN,
        'banker' => 'banker.' . DOMAIN,
        'cdn' => '',
        'developer' => 'dev.' . DOMAIN,
        'game' => 'g.' . DOMAIN,
        'img' => 'img.' . DOMAIN,
        'member' => 'member.pdber.com',
        'mobile' => 'm.' . DOMAIN,
        'oa' => 'oa.' . DOMAIN,
        'pay' => 'pay.' . DOMAIN,
        'profile' => 'profile.' . DOMAIN,
        'sso' => 'sso.' . DOMAIN,  //单点登陆SSO
        'home' => DOMAIN, //主页
        'cookie' => '8m.com',
    ],
    'application' => [
        'pariDir' => PROJ_DIR . '/vendor/src',
        'modelsDir' => PROJ_DIR . '/vendor/src/Models/',
        'cacheDir' => PROJ_DIR . '/common/cache/',
        'packDir' => PROJ_DIR . '/vendor/pack',
        'staticBaseUri' => 'http://cdn.' . DOMAIN, //静态资源路径
        'baseUri' => '/',
    ],
    //主从DB
    'database' => [
        'adapter' => 'Mysql',
        'host' => '162.243.153.227',
        'port' => '3306',
        'username' => 'root',
        'password' => '123123',
        'dbname' => 'casino',
        'charset' => 'utf8',
        'persistent' => true, //开启长连接
    ],
    'dbSlave' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'root',
        'password' => 'root',
        'dbname' => 'casino',
        'prefix' => '',
    ],
    //redis
    'redis' => [
        'host' => '162.243.153.227',
        'port' => '6379',
        'timeout' => '2.5',
        'persistent' => true,

    ],
    'redisSlave' => [
        'host' => '127.0.0.1',
        'port' => '6379',
    ],
    'mongo' => [
        'host' => '162.243.153.227',
        'port' => '27017',
        'user' => '',
        'password' => '',
    ],
    'mongoSlave' => [
        'host' => '127.0.0.1',
        'port' => '27017',
        'user' => '',
        'password' => '',
    ],
    'beanstalkd' => [
        'host' => '127.0.0.1'
    ],
    'auth' => [
        'authKey' => '',
        'lifetime' => '86400',
        'domain' => DOMAIN,
        'sessionKey' => 'auth_user',
        'sessionRoles' => 'auth_user_roles',
    ],
    'sms' => [
        'url' => '',
        'uid' => '',
        'pwd' => ''
    ],
    'email' => [
        'CharSet' => 'utf-8',
        'ContentType' => 'text/html',
        'Encoding' => '8bit',
        'From' => 'service@' . DOMAIN,
        'FromName' => '',
        'Mailer' => 'smtp',
        'Host' => 'mail.' . DOMAIN,
        'Port' => 25,
        'SMTPSecure' => "",
        'SMTPAuth' => true,
        'Username' => "",
        'Password' => "",
    ],
    'url' => [
        'imgUrl' => '',
        'miscUrl' => '',
    ],
    'cookies' => [
        'cookie' => '',
        'prefix' => '',
    ],
    'session' => [
        'path' => 'tcp://162.243.153.227:6379?weight=1&timeout=2',
        'name' => 'pari',
        'lifetime' => 86400,
        'cookie_lifetime' => 0,
        'cookie_domain' => DOMAIN,
        'token' => '', //session加密串
        'encrypt' => '', //session和cookies加密串
        'timeout' => 600, //锁屏的超时时间
    ],
    //搜索引擎以及中文分词
    'sphinx' => [
        'scws' => '10.250.1.209/scws.php',
        'sphinxHost' => '10.250.1.42',
        'sapHost' => '218.19.130.54',
        'sapPort' => 8888,
    ],
    //语言支持
    'lang' => [
        'en' => 'English',
        'zh_cn' => '简体中文',
        'zh_tw' => '繁體中文',
        'jp' => '',
        'kr' => '',
        'fr' => '',
        'de' => '',
    ],
]);