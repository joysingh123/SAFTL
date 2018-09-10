<?php
namespace App\Helpers;
class UtilString {
    
    public static function trim_string($str) {
        $str = trim($str, '",(),-');
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
        $company_id = 0;
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $parts = parse_url($url);
            parse_str($parts['query'], $query);
                $company_id = str_replace("/", "", $query['companyId']);
        }
        return $company_id;
    }
    public static function starts_with($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function ends_with($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }
    
    public static function is_empty_string($str) {
        if ($str == NULL || strlen(trim($str)) == 0) {
            return true;
        }
        return false;
    }
    
    public static function contains($haystack, $needle) {
        if (strpos($haystack, $needle) !== false) {
            return true;
        }
        return false;
    }
    
    public static function is_email($email){
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
          return true;
        }
        return false;
    }
    
    public static function explode_email_string($str){
        $explode_array = UtilConstant::$EXPLODE_EMAIL_VALUE;
        $explode = array();
        foreach($explode_array AS $ea){
            if(self::contains($str, $ea)){
                $explode['explode_data'] = explode($ea, $str);
                $explode['explode_by'] = $ea;
                break;
            }
        }
        if(count($explode) > 0){
            return $explode;
        }
        return $str;
    } 
}
?>

