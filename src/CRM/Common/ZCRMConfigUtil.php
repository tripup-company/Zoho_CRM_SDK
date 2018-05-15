<?php
namespace Zoho\CRM\Common;

use Zoho\CRM\Common\CommonUtil;
use Zoho\OAuth\Client\ZohoOAuth;
use Zoho\CRM\Setup\RestClient\ZCRMRestClient;
use Zoho\CRM\Exception\ZCRMException;

class ZCRMConfigUtil
{
    private static $configProperties = [];
    
    public static function getInstance()
    {
        return new ZCRMConfigUtil();
    }
    public static function initialize($initializeOAuth)
    {
        $path=realpath(dirname(__FILE__)."/../../Config/zoho.php");
        self::$configProperties = array_merge(require $path, self::$configProperties);
        if ($initializeOAuth) {
            ZohoOAuth::initializeWithOutInputStream();
        }
    }
        
    public static function getConfigValue($key)
    {
        return isset(self::$configProperties[$key])?self::$configProperties[$key]:'';
    }
    
    public static function setConfigValue($key, $value)
    {
        self::$configProperties[$key]=$value;
    }
    
    public static function getAPIBaseUrl()
    {
        return self::getConfigValue("apiBaseUrl");
    }
    
    public static function getAPIVersion()
    {
        return self::getConfigValue("apiVersion");
    }
    public static function getAccessToken()
    {
        $currentUserEmail= ZCRMRestClient::getCurrentUserEmailID();
        
        if ($currentUserEmail == null && self::getConfigValue("currentUserEmail") == null) {
            throw new ZCRMException("Current user should either be set in ZCRMRestClient or in zoho.php configuration file /src/Config/zoho.php");
        } elseif ($currentUserEmail == null) {
            $currentUserEmail = self::getConfigValue("currentUserEmail");
        }
        $oAuthCliIns = ZohoOAuth::getClientInstance();
        $zat = $oAuthCliIns->getAccessToken($currentUserEmail);
        return $zat;
    }
    
    public static function getAllConfigs()
    {
        return self::$configProperties;
    }
}
