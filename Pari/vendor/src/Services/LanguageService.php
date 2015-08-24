<?php
/**
 * Created by PhpStorm.
 * User: linva
 * Date: 2015/7/23
 * Time: 11:40
 */

namespace Pari\Services;

use Pari\Models\Language;
use Phalcon\Mvc\Model\Query\Lang;
use Phalcon\Mvc\User\Component;

/**
 * 检测用户
 * Class detect
 * @package Pari\Services
 */
class Detect extends Component
{

    public $default_lang ;

    public function __construct(){
        $this->default_lang =  \Pari\Data\LanguageData::getDefaultLang();
    }

    /**
     * @TODO 获取用户语言
     */
    public function getClientLang()
    {
        //如果获取得到语言
        if (!empty($lang = strval($this->cookies->get('lang')))) {
            //查找该语言状态是否激活
            $langInfo = Language::findFirst("lang = '$lang'");
            $langInfo['status'] == 1 ? $lang : 'en';
            //设置默认语言
            $langInfo != null && $langInfo['status'] == 1 ? $this->cookies->set('lang', $langInfo['lang']) : $this->cookies->set('lang', 'en');
        } else {
            $this->setClientLang();
            $this->cookies->get('lang');
        }

        return;

    }

    /**
     * @TODO 设置客户端语言
     * @param string $language
     */
    public function setClientLang($language = 'en')
    {
        //语言长度
        $bestLang = strtolower($this->request->getBestLanguage());

        //语言首两位
        if (empty($lang = mb_substr($bestLang, 0, 2) )) {
            $lang = Language::findFirst('is_default = true');//$language
        }
        if ($lang == 'zh') {
            //IE显示的是 zh-hans-cn chrome和其它是zh-cn 或者zh
            $lang = mb_substr($bestLang, 0, 7) == 'zh-hans' || $bestLang == 'zh' || $bestLang == 'zh-cn' ? 'zhs' : 'zht';
        }

        $this->cookies->set('lang', $lang);

    }

    public function readLang($module, $msg)
    {
        $lang = $this->cookies->get('lang');
        $translate = Language::findFirst("lang = '$lang' AND module = '$module' AND word = '$msg'");
    }

}