<?php

namespace Zoho\Oauth\Client;

use Zoho\Resources\InterfaceOAthConfigurations;

class ZohoOAuth {

    /**
     * @var ZohoOAuth
     */
    protected static $instance;
    protected $oAuthConfigs;
    protected $authUrl;
    protected $grantUrl;
    protected $tokenUrl;
    protected $tokenRefreshUrl;
    protected $tokenRevokeUrl;
    protected $userInfoUrl;

    /**
     * @param InterfaceOAthConfigurations $oauthConfigs
     * @return ZohoOAuth
     */
    public static function getInstance($oauthConfigs) {
        if (is_null(static::$instance)) :
            static::$instance = new static($oauthConfigs);
        endif;

        return static::$instance;
    }

    /**
     * 
     * @param InterfaceOAthConfigurations $oauthConfigs
     * @throws \Exception
     */
    protected function __construct($oauthConfigs) {
        $this->oAuthConfigs = $oauthConfigs;
        $this->authUrl = $this->oAuthConfigs->getAccountsUrl();
        $this->grantUrl = $this->authUrl . '/oauth/v2/auth';
        $this->tokenUrl = $this->authUrl . '/oauth/v2/token';
        $this->tokenRefreshUrl = $this->authUrl . '/oauth/v2/token';
        $this->tokenRevokeUrl = $this->authUrl . '/oauth/v2/token/revoke';
        $this->userInfoUrl = $this->authUrl . '/oauth/user/info';
    }

    public function getIAMUrl() {
        return $this->authUrl;
    }

    public function getGrantUrl() {
        return $this->grantUrl;
    }

    public function getTokenUrl() {
        return $this->tokenUrl;
    }

    public function getRefreshTokenUrl() {
        return $this->tokenRefreshUrl;
    }

    public function getRevokeTokenURL() {
        return $this->tokenRevokeUrl;
    }

    public function getUserInfoUrl() {
        return $this->userInfoUrl;
    }

}
