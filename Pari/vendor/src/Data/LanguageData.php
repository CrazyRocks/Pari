<?php
/**
 * Created by PhpStorm.
 * User: Linvas
 * Date: 2015/6/25
 * Time: 14:46
 */

namespace Pari\Data;

use Pari\Models\Language;

class LanguageData{

    public static function getDefaultLang(){

        if(Language::count('is_default = 1') != 1 ){
            exit('默认语言必须唯一, 请确认后再继续');
        }
        $langInfo = Language::findFirst('is_default = 1');

        return $lang;
    }

    public function setDefaultLang($lang = 'en'){

    }

    public function saveLang($langId = null ){

    }

    public function delLang(){

    }

}