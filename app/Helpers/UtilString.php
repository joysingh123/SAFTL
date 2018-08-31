<?php
namespace App\Helpers;
class UtilString {
    
    public static function trim_string($str) {
        $str = trim($str, '",()');
        return $str;
    }

    public static function remove_non_english_charector($str) {
        $str = preg_replace('/[^\00-\255]+/u', '', $str);
        return $str;
    }
    
    public static function get_domain_from_url($url){
        $url = rtrim($url,"/");
        $find = array('www.','WWW.','info@','http://','https://');
        $url = str_replace($find,"",$url);
        if(strpos($url,"/") != false){
            $lastpos = strpos($url,"/");
            $url = substr($url,0,$lastpos);
        }
        return $url;
    }

    public static function clean_string($str) {
        $str = self::trim_string($str);
        $str = self::remove_non_english_charector($str);
        return $str;
    }

    public static function get_company_id_from_url($url) {
        if ($url != "") {
            $parts = parse_url($url);
            parse_str($parts['query'], $query);
            return $query['companyId'];
        }
        return 0;
    }
}
?>

