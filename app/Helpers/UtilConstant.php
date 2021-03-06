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
    
    
    // cron jobs
    
    
    const MATCHED_CRON_JOB_NAME = "Matched Contacts";
    const MATCHED_CRON_EMAIL_FORMAT = "Email Format";
    const MATCHED_CRON_EMAIL_CREATE = "Email Create";
    const CRON_EMAIL_VALIDATION = "Email Validation";
    const CRON_EMAIL_VALIDATION_2 = "Email Validation 2";
    const CRON_EMAIL_VALIDATION_3 = "Email Validation 3";
    const CRON_EMAIL_VALIDATION_4 = "Email Validation 4";
    const CRON_EMAIL_VALIDATION_5 = "Email Validation 5";
    const CRON_EMAIL_VALIDATION_6 = "Email Validation 6";
    const CRON_EMAIL_VALIDATION_7 = "Email Validation 7";
    const CRON_EMAIL_VALIDATION_8 = "Email Validation 8";
    const CRON_EMAIL_VALIDATION_9 = "Email Validation 9";
    const CRON_EMAIL_VALIDATION_10 = "Email Validation 10";
    const CRON_IMPORT_EMAIL_VALIDATION = "Import Email Validation";
    const CRON_IMPORT_EMAIL_VALIDATION_2 = "Import Email Validation 2";
    const CRON_IMPORT_EMAIL_VALIDATION_3 = "Import Email Validation 3";
    const CRON_REOMOVE_EMAIL_FROM_EMAILS = "Remove Email";
    const CRON_REOMOVE_API_VALID_EMAIL_FROM_EMAILS = "Remove Api Valid Email";
    const CRON_SCRAPE_URL_HUNTER = "Scrape Url Hunter";
    const CRON_SCRAPE_DOMAIN_HUNTER = "Scrape Domain Hunter";
    const CRON_SCRAPE_DOMAIN_EMAIL_FORMAT = "Scrape Domain Email Format";
    const CRON_CALCULATE_DOMAIN_EMAIL_FORMAT_PERCENTAGE = "Calculate Domain Email Format Percentage";
    const CRON_COMPANY_IMPORT = "Company Import";
    const CRON_GENERATE_DEFAULT_EMAIL_FORMAT = "Genrate Default Email Format";
    const CRON_POPULATE_COMPANY_DATA = "Populate Company Data";
    const EMAIL_VALIDATION_API_MAILBOXLAYER_NAME = "mailboxlayer";
    const EMAIL_VALIDATION_API_ZEROBOUNCE_NAME = "zerobounce";
    
    const CRON_DG_SCRAPPER = "Dg Scraper";
    const CRON_DG_CONTACT_SCRAPPER = "Dg Contact Scraper";
    const CRON_99CORPORATES_DOMAIN_SCRAPPER = "99Corporates Domain Scrapper";
    const CRON_UPDATE_COMPANY_STATS = "Update Company Stats";
    const CRON_POPULATE_COMPANY_MASTER = "Populate Company Master";
    const CRON_POPULATE_CONTACT_MASTER = "Populate Contact Master";
    const CRON_POPULATE_SALESBOT_COMPANIES = "Populate Salesbot Companies";
    const CRON_SEND_EMAIL = "Send Email";
    const CRON_UPDATE_FORMAT_COUNT = "Update Format Count";
    const CRON_CREATE_EMAIL_FOR_INVALID = "Create Email For Invalid";
    
    // Email Send Service
    
    const SENDGRID_EMAIL_SERVICE = "sendgrid";
}
?>