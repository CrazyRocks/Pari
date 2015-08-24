<?php
namespace Pari\Lib\Games\Lottery;


/**
 * Class Fetch
 * @package Pari\Lib\Games\Lottery
 */
class Fetch
{
    /**
     * @var Platform\PlatformInterface
     */
    protected $platform = null;

    protected $latestAwardExpectInfo;

    /**
     * @param string|Platform\PlatformInterface $platform
     */
    public function __construct($platform, $nowTimestamp = null)
    {
        if (!$nowTimestamp) {
            $nowTimestamp = time();
        }
        if ($platform instanceof Platform\PlatformInterface) {
            $this->platform = $platform;
        } else {
            $this->platform = $this->createPlatform($platform, $nowTimestamp);
        }
    }

    /**
     * @return Platform\PlatformInterface
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param string $platformName
     * @throws Exception\InvalidArgumentException
     * @return Platform\PlatformInterface
     */
    protected function createPlatform($platformName, $nowTimestamp)
    {
        if ($platformName == '3d') {
            $className = 'Lottery\Fetch\Platform\Fc3d';
        } else {
            $className = 'Lottery\Fetch\Platform\\' . ucfirst($platformName);
        }
        if (!class_exists($className)) {
            throw new Exception('platformName not found');
        }

        return new $className($nowTimestamp);
    }

    public function getLatestAwardExpectInfo()
    {
        return $this->latestAwardExpectInfo;
    }

    /**
     * 采集最新开奖结果
     *
     * @return array(
                   'number' => '2 6 6 1 4',
                   'expect' => '141106036',
                   'opentime' => 1402143035
               )
     */
    public function fetchLatestAwardNumberInfo()
    {
        $this->latestAwardExpectInfo = $this->platform->getLatestAwardExpectInfo();
        if (empty($this->latestAwardExpectInfo)) {
            return false;
        }

        $latestAwardNumberInfo = $this->platform->fetchAwardNumberInfo($this->latestAwardExpectInfo['expect']);
        if (empty($latestAwardNumberInfo)) {
            throw new Exception('第' . $this->latestAwardExpectInfo['expect'] . '期采集错误。');
        }

        $latestAwardNumberInfo['opentime'] = $latestAwardNumberInfo['opentime'] ?: $this->latestAwardExpectInfo['opentime'];

        return $latestAwardNumberInfo;
    }
}
