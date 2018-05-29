<?php
namespace Zoho\OAuth\Client;

use Zoho\OAuth\Common\ZohoOAuthConstants;
use Zoho\OAuth\Common\ZohoOAuthParams;
use Zoho\OAuth\ClientApp\ZohoOAuthPersistenceHandler;
use Zoho\OAuth\ClientApp\ZohoOAuthPersistenceByFile;
use Zoho\OAuth\Common\OAuthLogger;
use Zoho\OAuth\Common\ZohoOAuthException;
use Zoho\OAuth\Client\ZohoOAuthClient;

class ZohoOAuth
{
    protected static $configProperties =array();
    
    public static function initializeWithOutInputStream()
    {
        self::initialize(false);
    }
    
    public static function initialize($configFilePointer)
    {
        try {
            $configPath=realpath(dirname(__FILE__)."/../../Config/zoho.php");
            self::$configProperties = array_merge(require $configPath, self::$configProperties);

            $oAuthParams=new ZohoOAuthParams();
            $oAuthParams->setAccessType(self::getConfigValue(ZohoOAuthConstants::ACCESS_TYPE));
            $oAuthParams->setClientId(self::getConfigValue(ZohoOAuthConstants::CLIENT_ID));
            $oAuthParams->setClientSecret(self::getConfigValue(ZohoOAuthConstants::CLIENT_SECRET));
            $oAuthParams->setRedirectURL(self::getConfigValue(ZohoOAuthConstants::REDIRECT_URL));
            ZohoOAuthClient::getInstance($oAuthParams);
        } catch (IOException $ioe) {
            OAuthLogger::warn("Exception while initializing Zoho OAuth Client.. ". ioe);
            throw ioe;
        }
    }
    
    public static function getConfigValue($key)
    {
        return self::$configProperties[$key];
    }
    
    public static function getAllConfigs()
    {
        return self::$configProperties;
    }
    
    public static function getIAMUrl()
    {
        return self::getConfigValue(ZohoOAuthConstants::IAM_URL);
    }
    
    public static function getGrantURL()
    {
        return self::getIAMUrl()."/oauth/v2/auth";
    }
    
    public static function getTokenURL()
    {
        return self::getIAMUrl()."/oauth/v2/token";
    }
    
    public static function getRefreshTokenURL()
    {
        return self::getIAMUrl()."/oauth/v2/token";
    }
    
    public static function getRevokeTokenURL()
    {
        return self::getIAMUrl()."/oauth/v2/token/revoke";
    }
    
    public static function getUserInfoURL()
    {
        return self::getIAMUrl()."/oauth/user/info";
    }
    
    public static function getClientID()
    {
        return self::getConfigValue(ZohoOAuthConstants::CLIENT_ID);
    }
    
    public static function getClientSecret()
    {
        return self::getConfigValue(ZohoOAuthConstants::CLIENT_SECRET);
    }
    
    public static function getRedirectURL()
    {
        return self::getConfigValue(ZohoOAuthConstants::REDIRECT_URL);
    }
    
    public static function getAccessType()
    {
        return self::getConfigValue(ZohoOAuthConstants::ACCESS_TYPE);
    }
    
    public static function getPersistenceHandlerInstance()
    {
        try {
            $persistenceHandlerClass = ZohoOAuth::getConfigValue("persistence_handler_class");
            $tpt = ZohoOAuth::getConfigValue("token_persistence_type");
            //check for persistence_handler_class default to handling from class
            if ($tpt=="class") {
                //cheking for class set in config (this allows external handling of persistence tokens)
                if (class_exists($persistenceHandlerClass)) {
                    //use defined class
                    return new $persistenceHandlerClass;
                } elseif (ZohoOAuth::getConfigValue("token_persistence_path")!="") {
                    return new ZohoOAuthPersistenceByFile();
                } else {
                    throw new ZohoOAuthException("Persistence Handler Class doesn't exist or Persistence Path was not set in the config.");
                }
            } elseif ($tpt=="file") {
                if (ZohoOAuth::getConfigValue("token_persistence_path")!="") {
                    return new ZohoOAuthPersistenceByFile();
                } else {
                    throw new ZohoOAuthException("Token Persistence Path wasn't set or Persistence Path was not found");
                }
            }
        } catch (Exception $ex) {
            throw new ZohoOAuthException($ex);
        }
    }
    
    public static function getClientInstance()
    {
        if (ZohoOAuthClient::getInstanceWithOutParam() == null) {
            throw new ZohoOAuthException("ZohoOAuth.initializeWithOutInputStream() must be called before this.");
        }
        return ZohoOAuthClient::getInstanceWithOutParam();
    }
}
