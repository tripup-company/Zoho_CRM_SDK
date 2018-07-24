<?php

namespace Zoho\CRM\Setup\RestClient;

use Zoho\CRM\Api\Handler\MetaDataAPIHandler;
use Zoho\CRM\Api\Handler\OrganizationAPIHandler;
use Zoho\CRM\Crud\ZCRMModule;
use Zoho\CRM\Crud\ZCRMRecord;
use Zoho\Oauth\Common\ZohoOAuthParams;
use Zoho\Oauth\Common\OAuthLogger;
use Zoho\Oauth\Client\ZohoOAuth;
use Zoho\Resources\InterfaceConfigurations;
use Zoho\Resources\InterfaceOAuthConfigurations;
use Zoho\Oauth\Client\ZohoOAuthClient;

use Zoho\CRM\Common\APIConstants;

use Zoho\CRM\Setup\MetaData\ZCRMOrganization;

class ZCRMRestClient {

    /**
     * @type ZCRMRestClient
     */
    protected static $instance;

    /**
     * @type InterfaceConfigurations
     */
    protected $configs;

    /**
     * @type InterfaceOAuthConfigurations
     */
    protected $oAuthConfigs;

    /**
     * @type ZohoOAuthClient
     */
    protected $oAuthClient;

    /**
     * @param InterfaceConfigurations $configurations
     * @param InterfaceOAuthConfigurations $oAuthconfigurations
     * @return ZCRMRestClient
     */
    public static function getInstance($configurations, $oAuthconfigurations) {
        if (is_null(static::$instance)) :
            static::$instance = new static($configurations, $oAuthconfigurations);
        endif;

        return static::$instance;
    }

    /**
     * @param InterfaceConfigurations $configurations
     * @param InterfaceOAuthConfigurations $oAuthconfigurations
     */
    protected function __construct($configurations, $oAuthconfigurations) {
        $this->configs = $configurations;
        $this->oAuthConfigs = $oAuthconfigurations;
    }

    /**
     * @todo May be move to constructor without requesting this extra method to work with API?
     * 
     * @return ZohoOAuthClient
     * @throws \Exception
     */
    public function getOAuthClient() {
        return empty($this->oAuthClient) ?$this->oAuthClient = ZohoOAuthClient::getInstance($this->oAuthConfigs) : $this->oAuthClient;
    }

    /**
     * @todo Remove??
     * 
     * @return type
     * @throws ZCRMException
     */
    public function getAccessToken() {
        $currentUserEmail = $this->configs->getCurrentUserEmail();

        if (empty($currentUserEmail)) {
            throw new ZCRMException("You need to set 'currentUserEmail' in Zoho\Resources\Configurations");
        }
        
        if (empty($this->oAuthClient)) {
             $this->oAuthClient = $this->getOAuthClient();
        }
        
        return $this->oAuthClient->getAccessToken($currentUserEmail);
    }
    
    /**
     * @return BulkAPIResponse
     */
    public function getAllModules() {
        return MetaDataAPIHandler::getInstance($this->configs, $this->getAccessToken())->getAllModules();
    }
    
    /**
     * @param string $moduleName
     * @return APIResponse
     */
    public function getModule($moduleName) {
        return MetaDataAPIHandler::getInstance($this->configs, $this->getAccessToken())->getModule($moduleName);
    }
    
   
    /**
     * @return getBulkAPIResponse
     */
    public function getCurrentUser() {
        return OrganizationAPIHandler::getInstance($this->configs, $this->getAccessToken())->getCurrentUser();
    }
    
    /**
     * @return mixed
     */
    public static function getCurrentUserEmailID() {
        return $this->configs->getCurrentUserEmail();
    }
    
    /**
     * @return APIResponse
     */
    public function getOrganizationDetails() {
        return OrganizationAPIHandler::getInstance($this->configs, $this->getAccessToken())->getOrganizationDetails();
    }

}