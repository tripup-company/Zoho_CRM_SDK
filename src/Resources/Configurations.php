<?php

namespace Zoho\Resources;

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
        if (!empty($configurations)) {
            //@todo Separate Exception handler
            try {
                $this->setDataFromArray($configurations);
            } catch (\Exception $e) {
                echo 'Warrning: ' . $e->getMessage() . "\n";
            }
        }
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
