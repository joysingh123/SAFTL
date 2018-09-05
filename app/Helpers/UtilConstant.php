<?php
namespace App\Helpers;
class UtilConstant {
     
    public static $EXCLUDE_FROM_NAME = array("dr.","dr","kumar");
    
    public static $EXPLODE_EMAIL_VALUE = array(".","_","-");
    
    //constants For Email Formate
    
    const DOMAIN = "DOMAIN";
    const FIRST_NAME = "FIRSTNAME";
    const LAST_NAME = "LASTNAME";
    const FIRST_NAME_FIRST_CHARACTER = "F";
    const LAST_NAME_FIRST_CHARACTER = "L";
    const FIRST_NAME_FIRST_TWO_CHARACTER = "FI";
    const LAST_NAME_FIRST_TWO_CHARACTER = "LA";
    
}
?>