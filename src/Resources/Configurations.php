<?php

namespace Zoho\Resources;

use Zoho\Oauth\Client\ZohoOAuthPersistenceHandler;

class Configurations implements InterfaceConfigurations {

    use TraitConfigurations;

    /**
     * @type InterfaceConfigurations
     */
    protected static $instance;

    /**
     * @var string $apiBaseUrl
     */
    protected $apiBaseUrl = 'www.zohoapis.com';

    /**
     * @var string $apiVersion
     */
    protected $apiVersion = 'v2';

    /**
     * @var bool $sandbox
     */
    protected $sandbox = false;

    /**
     * @var string $applicationLogFilePath
     */
    protected $applicationLogFilePath;

    /**
     * @var string $currentUserEmail
     */
    protected $currentUserEmail;

    /**
     * @param array $configurations
     * @return InterfaceConfigurations
     */
    public static function getInstance($configurations = null) {
        if (is_null(static::$instance)) :
            static::$instance = new static($configurations);
        endif;

        return static::$instance;
    }

    /**
     * @param array $configurations
     */
    protected function __construct($configurations) {
        $configurations = $this->separateDbConfigs($configurations);
        
        try {
            if (empty($configurations)) {
                throw new \Exception('Set full list of required configurations.');
            }
            $this->setDataFromArray($configurations);
        } catch (\Exception $e) {
            echo 'Warrning: ' . $e->getMessage() . "\n";
        }
    }
    
    /**
     * @param array $configurations
     * @return array
     */
    protected function separateDbConfigs($configurations) {
        $dbConfigsList = null;
        
        if ($configurations['database'] || $configurations['databaseType']) {
            foreach ($configurations as $key => $configItem) {
                if (preg_match('/database/', $key)) {
                    $dbConfigsList[$key] = $configItem;
                    unset($configurations[$key]);
                }
            }
        }
        
        if (!empty($dbConfigsList)) {
            $db = new DbConfigurations();
            $type = $dbConfigsList['databaseType'];
            $db->setDatabaseType($type);
            $db->setDatabaseHost($dbConfigsList['database'][$type]['host']);
            $db->setDatabaseName($dbConfigsList['database'][$type]['db']);
            $db->setUserName($dbConfigsList['database'][$type]['user']);
            $db->setUserPassword($dbConfigsList['database'][$type]['pass']);
            ZohoOAuthPersistenceHandler::$databaseConfigs = $db;
        }
        
        return $configurations;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getApiBaseUrl() {
        return $this->apiBaseUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getApiVersion() {
        return $this->apiVersion;
    }

    /**
     * {@inheritDoc}
     */
    public function getSandbox() {
        return $this->sandbox;
    }

    /**
     * {@inheritDoc}
     */
    public function getAppLogFilePath() {
        return $this->applicationLogFilePath;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentUserEmail() {
        return $this->currentUserEmail;
    }

}
