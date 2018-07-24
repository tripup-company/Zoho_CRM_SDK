<?php

namespace Zoho\CRM\Common;

use Zoho\Oauth\Client\ZohoOAuth;
use Zoho\Resources\InterfaceConfigurations;
use Zoho\Resources\InterfaceOAthConfigurations;
use Zoho\CRM\Exception\ZCRMException;

class ZCRMConfigUtil {

    /**
     * @var ZCRMRestClient
     */
    protected static $instance;

    /**
     * @type InterfaceConfigurations $configs
     */
    protected $configs;

    /**
     * @type InterfaceOAthConfigurations $oauthConfigs
     */
    protected $oauthConfigs;

    /**
     * @type ZohoOAuth
     */
    protected $oAuth;
    
    
    /**
     * @return ZCRMConfigUtil
     */
    public static function getInstance() {
        if (is_null(static::$instance)) :
            static::$instance = new static();
        endif;

        return static::$instance;
    }

    /**
     * @param InterfaceConfigurations $configs
     * @param InterfaceOAthConfigurations $oauthConfigs
     */
    protected function __construct(InterfaceConfigurations $configs, InterfaceOAthConfigurations $oauthConfigs) {
        $this->configs = $configs;
        $this->oauthConfigs = $oauthConfigs;
        $this->oAuth = ZohoOAuth::getInstance($oauthConfigs); //@todo ??!!
    }
    
    /**
     * 
     * @return type
     * @throws ZCRMException
     */
    public function getAccessToken() {
        $currentUserEmail = $this->configs->getCurrentUserEmail();

        if (empty($currentUserEmail)) {
           throw new ZCRMException("You need to set 'currentUserEmail' in Zoho\Resources\Configurations");
        }
        
        $oAuthClient = $this->oAuth->getClientInstance();
        return $oAuthClient->getAccessToken($currentUserEmail);
    }

    /**
     * @return string
     */
    public function getApiBaseUrl() {
        return $this->configs->getApiBaseUrl();
    }

    /**
     * @return string
     */
    public function getApiVersion() {
        return $this->configs->getApiVersion();
    }

    /**
     * @return InterfaceConfigurations
     */
    public function getAllConfigs() {
        return $this->configs;
    }
}
