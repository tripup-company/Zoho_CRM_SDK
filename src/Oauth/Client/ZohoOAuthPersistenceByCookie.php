<?php

namespace Zoho\Oauth\Client;

use Zoho\Oauth\Common\ZohoOAuthTokens;
use Zoho\Oauth\Common\ZohoOAuthException;
use \Firebase\JWT\JWT;

class ZohoOAuthPersistenceByCookie implements ZohoOAuthPersistenceInterface {
    
    protected $key;
    
    /**
     * @param string $clientSecret
     */
    public function __construct($clientSecret) {
        $this->key = $clientSecret;
    }
    
    /**
     * @param string $userEmailId
     */
    public function deleteOAuthTokens($userEmailId = null) {
        if (!empty(filter_input(INPUT_COOKIE, 'CRM_OAUTH'))) {
            setcookie('CRM_OAUTH', null, -1);
        }
    }

    /**
     * 
     * @param string $userEmailId
     * @return array
     * @throws \Exception
     */
    public function getOAuthTokens($userEmailId = null) {
        try {
            if (empty($cookieTokenJwt = filter_input(INPUT_COOKIE, 'CRM_OAUTH'))) {
                throw new \Exception('Cookie CRM_OAUTH is not set. Generate it once more or change the type of persistence handler.');
            }
            $decodedJwt = JWT::decode($cookieTokenJwt, $this->key, ['HS256']);

            return (array) json_decode($decodedJwt);
        } catch (\Exception $e) {
            echo "Warning: " . $e->getMessage() . "\n";
        }
    }

    protected function isCookieTokenSet() {
        return !empty(filter_input(INPUT_COOKIE, 'CRM_OAUTH'));
    }
    
    /**
     * @param ZohoOAuthTokens $zohoOAuthTokens
     */
    public function saveOAuthData($zohoOAuthTokens) {
        try {
            if ($this->isCookieTokenSet()) {
                new ZohoOAuthException('CRM_OAUTH cookie is already set. If you want to set new tokens, please, clean your old cookies.');
            }
            
            $jwt = JWT::encode(json_encode($zohoOAuthTokens->toArray()), $this->key);
            setcookie('CRM_OAUTH', $jwt, time() + 3600 * 24 * 30);
        } catch (ZohoOAuthException $e) {
           echo $e->getMessage();
           exit();
        }
    }

    public function getGrantToken() {
        throw new \Exception('Method has not implemented.');
        exit();
    }

}