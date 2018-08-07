<?php

namespace Zoho\Resources;

class OAuthConfigurations implements InterfaceOAuthConfigurations {

    use TraitConfigurations;

    /**
     * @type InterfaceOAthConfigurations
     */
    protected static $instance;

    /**
     * @var string $clientID
     */
    protected $clientID;

    /**
     * @var string $clientSecret
     */
    protected $clientSecret;

    /**
     * @var string $redirectUri
     */
    protected $redirectUri;

    /**
     * @var string $accountsUrl
     */
    protected $accountsUrl = 'https://accounts.zoho.com';

    /**
     * @var string $tokenPersistencePath
     */
    protected $tokenPersistencePath;

    /**
     * NOTICE: Currently now you can use only offline access type
     * 
     * @var string $accessType
     */
    protected $accessType = 'offline';

    /**
     * @var string $persistenceHandlerClass
     */
    protected $persistenceHandlerClass = 'ZohoOAuthPersistenceHandler';
    
    /**
     * @var mixed $oAuthLogerPath
     */
    protected $oAuthLogerPath;

    /**
     * @param array $oathConfigurations
     * @return InterfaceOAthConfigurations
     */
    public static function getInstance($oathConfigurations = null) {
        if (is_null(static::$instance)) :
            static::$instance = new static($oathConfigurations);
        endif;

        return static::$instance;
    }

    /**
     * @param array $oathConfigurations
     */
    protected function __construct($oathConfigurations) {
        if (!empty($oathConfigurations)) {
            //@todo Separate Exception handler
            try {
                $this->setDataFromArray($oathConfigurations);
            } catch (\Exception $e) {
                echo 'Warrning: ' . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessType() {
        return $this->accessType;
    }

    /**
     * {@inheritDoc}
     */
    public function getAccountsUrl() {
        return $this->accountsUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientID() {
        return $this->clientID;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientSecret() {
        return $this->clientSecret;
    }

    /**
     * {@inheritDoc}
     */
    public function getPersistenceHandlerClass() {
        return $this->persistenceHandlerClass;
    }

    /**
     * {@inheritDoc}
     */
    public function getRedirectUri() {
        return $this->redirectUri;
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenPersistencePath() {
        return $this->tokenPersistencePath;
    }

}
