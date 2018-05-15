<?php
namespace Zoho\CRM\Setup\RestClient;

use Zoho\CRM\Api\Handler\MetaDataAPIHandler;
use Zoho\CRM\Common\ZCRMConfigUtil;
use Zoho\CRM\Setup\Metadata\ZCRMOrganization;
use Zoho\CRM\Common\APIConstants;

class ZCRMRestClient
{
    private function __construct()
    {
    }
    
    public static function getInstance()
    {
        return new ZCRMRestClient();
    }
    public static function initialize()
    {
        ZCRMConfigUtil::initialize(true);
    }
    
    public function getAllModules()
    {
        return MetaDataAPIHandler::getInstance()->getAllModules();
    }
    
    public function getModule($moduleName)
    {
        return MetaDataAPIHandler::getInstance()->getModule($moduleName);
    }
    
    public function getOrganizationInstance()
    {
        return ZCRMOrganization::getInstance();
    }
    
    public function getModuleInstance($moduleAPIName)
    {
        return ZCRMModule::getInstance($moduleAPIName);
    }
    
    public function getRecordInstance($moduleAPIName, $entityId)
    {
        return ZCRMRecord::getInstance($moduleAPIName, $entityId);
    }
    
    public function getCurrentUser()
    {
        return OrganizationAPIHandler::getInstance()->getCurrentUser();
    }
    
    public static function getCurrentUserEmailID()
    {
        return isset($_SERVER[APIConstants::USER_EMAIL_ID])?$_SERVER[APIConstants::USER_EMAIL_ID]:null;
    }
    
    public static function getOrganizationDetails()
    {
        return OrganizationAPIHandler::getInstance()->getOrganizationDetails();
    }
}
