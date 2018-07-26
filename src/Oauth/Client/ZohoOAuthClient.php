<?php

namespace Zoho\Oauth\Client;

use Zoho\Oauth\Client\ZohoOAuth;
use Zoho\Oauth\Common\OAuthLogger;
use Zoho\Oauth\Common\ZohoOAuthHTTPConnector;
use Zoho\Oauth\Common\ZohoOAuthTokens;
use Zoho\Oauth\Common\ZohoOAuthParams;
use Zoho\Oauth\Common\ZohoOAuthException;
use Zoho\Resources\InterfaceOAuthConfigurations;
use Zoho\Oauth\Client\ZohoOAuthPersistenceByFile;
use Zoho\Oauth\Client\ZohoOAuthPersistenceInterface;
use Zoho\Oauth\Client\ZohoOAuthPersistenceByCookie;

class ZohoOAuthClient {

    /**
     * @type ZohoOAuthClient
     */
    protected static $instance;

    /**
     *
     * @type 
     */
    protected $oAuthUrls;

    /**
     * @type ZohoOAuthParams
     */
    protected $oAuthParams;

    /**
     * @var ZohoOAuthPersistenceInterface $persistenceHandler
     */
    protected $persistenceHandler;

    /**
     * @param 
     * @param InterfaceOAuthConfigurations $oAuthConfigs
     * @return ZohoOAuthClient
     */
    public static function getInstance($oAuthConfigs) {
        if (is_null(static::$instance)) :
            static::$instance = new static($oAuthConfigs);
        endif;

        return static::$instance;
    }

    /**
     * @param InterfaceOAuthConfigurations $oAuthConfigs
     */
    protected function __construct($oAuthConfigs) {
        $this->oAuthParams = $this->setOAuthParams($oAuthConfigs);
        $this->oAuthUrls = ZohoOAuth::getInstance($oAuthConfigs);
        $this->persistenceHandler = $this->getPersistenceHandlerInstance($oAuthConfigs->getPersistenceHandlerClass(), $oAuthConfigs->getTokenPersistencePath());
    }

    /**
     * @param InterfaceOAuthConfigurations $oAuthConfigs
     * @return ZohoOAuthParams
     */
    protected function setOAuthParams($oAuthConfigs) {
        $oAuthParams = new ZohoOAuthParams();
        $oAuthParams->setAccessType($oAuthConfigs->getAccessType());
        $oAuthParams->setClientId($oAuthConfigs->getClientID());
        $oAuthParams->setClientSecret($oAuthConfigs->getClientSecret());
        $oAuthParams->setRedirectURL($oAuthConfigs->getRedirectUri());

        return $oAuthParams;
    }

    /**
     * @param string $handlerClass
     * @param string $tokenPath
     * @return ZohoOAuthPersistenceInterface
     * @throws ZohoOAuthException
     */
    protected function getPersistenceHandlerInstance($handlerClass, $tokenPath) {
        if (empty($tokenPath)) {
            switch ($handlerClass) {
                case 'ZohoOAuthPersistenceHandler' :
                    $persistenceHandler = new ZohoOAuthPersistenceHandler();
                    break;
                case 'ZohoOAuthPersistenceByCookie' :
                    $persistenceHandler = new ZohoOAuthPersistenceByCookie($this->oAuthParams->getClientSecret());
                    break;
            }
        } else {
            $persistenceHandler = new ZohoOAuthPersistenceByFile();
        }

        return $persistenceHandler;
    }

    public function getAccessToken($userEmailId = null) {
        try {
            $tokensAsArray = $this->persistenceHandler->getOAuthTokens($userEmailId);
        } catch (ZohoOAuthException $ex) {
            OAuthLogger::severe("Exception while retrieving tokens from persistence - " . $ex);
            throw $ex;
        } catch (\Exception $ex) {
            OAuthLogger::severe("Exception while retrieving tokens from persistence - " . $ex);
            throw new ZohoOAuthException($ex);
        }

        try {
            $tokens = new ZohoOAuthTokens();
            $tokens->setAccessToken($tokensAsArray['accessToken']);
            $tokens->setRefreshToken($tokensAsArray['refreshToken']);
            $tokens->setExpiryTime($tokensAsArray['expiryTime']);
            $tokens->setUserEmailId($tokensAsArray['userEmailId']);

            if (!$tokens->isValidAccessToken()) {
                $tokens = $this->generateAccessTokenFromRefreshToken($tokens->getRefreshToken());
            }

            return $tokens->getAccessToken();
        } catch (ZohoOAuthException $ex) {
            OAuthLogger::info("Access Token has expired. Hence refreshing.");
            $tokens = $this->refreshAccessToken($tokens->getRefreshToken(), $userEmailId);
            return $tokens->getAccessToken();
        }
    }

    public function generateAccessToken($grantToken) {
        try {
            if (empty($grantToken)) {
                throw new ZohoOAuthException("Grant Token is not provided.");
            }

            $conn = new ZohoOAuthHTTPConnector($this->oAuthParams);
            $conn->setUrl($this->oAuthUrls->getTokenUrl());
            $conn->addParam('grant_type', 'authorization_code');
            $conn->addParam('code', $grantToken);
            $response = $conn->post();
            $responseJSON = $this->processResponse($response);

            if (array_key_exists('access_token', $responseJSON)) {
                $tokens = $this->getTokensFromJSON($responseJSON);
                //@todo getUserEmailIdFromIAM request doesn't work in original SDK
                //$tokens->setUserEmailId($this->getUserEmailIdFromIAM($tokens->getAccessToken()));
                $this->persistenceHandler->saveOAuthData($tokens);
                return $tokens;
            } else {
                throw new ZohoOAuthException("Exception while fetching access token from grant token - " . $response);
            }
        } catch (ZohoOAuthException $ex) {
            echo $ex->getMessage();
            exit();
        }
    }

    /**
     * @param string $refreshToken
     * @param mixed $userEmailId
     */
    public function generateAccessTokenFromRefreshToken($refreshToken, $userEmailId = null) {
        return $this->refreshAccessToken($refreshToken, $userEmailId);
    }

    public function refreshAccessToken($refreshToken, $userEmailId = null) {
        try {
            $conn = new ZohoOAuthHTTPConnector($this->oAuthParams);
            $conn->setUrl($this->oAuthUrls->getRefreshTokenUrl());
            $conn->addParam('grant_type', 'refresh_token');
            $conn->addParam('refresh_token', $refreshToken);
            $response = $conn->post();
            $responseJSON = $this->processResponse($response);
            if (array_key_exists('access_token', $responseJSON)) {
                $tokens = $this->getTokensFromJSON($responseJSON);
                //@todo getUserEmailIdFromIAM request doesn't work in original SDK
                //$tokens->setUserEmailId($userEmailId);
                $this->persistenceHandler->saveOAuthData($tokens);
                return $tokens;
            } else {
                throw new ZohoOAuthException("Exception while fetching access token from refresh token - " . $response);
            }
        } catch (ZohoOAuthException $ex) {
            throw new ZohoOAuthException($ex);
        }
    }

    /**
     * @param array $responseObj
     * @return ZohoOAuthTokens
     */
    private function getTokensFromJSON($responseObj) {
        $oAuthTokens = new ZohoOAuthTokens();
        $oAuthTokens->setExpiryTime($oAuthTokens->getCurrentTimeInMillis() + $responseObj['expires_in']);
        $accessToken = $responseObj['access_token'];
        $oAuthTokens->setAccessToken($accessToken);

        if (array_key_exists('refresh_token', $responseObj)) {
            $refreshToken = $responseObj['refresh_token'];
            $oAuthTokens->setRefreshToken($refreshToken);
        }

        return $oAuthTokens;
    }

    /**
     * @return ZohoOAuthParams
     */
    public function getZohoOAuthParams() {
        return $this->oAuthParams;
    }

    /**
     * @param ZohoOAuthParams $zohoOAuthParams
     */
    public function setZohoOAuthParams($zohoOAuthParams) {
        $this->oAuthParams = $zohoOAuthParams;
    }

    /**
     * @todo
     * 
     * @param string $accessToken
     * @return string
     */
    public function getUserEmailIdFromIAM($accessToken) {
        $connector = new ZohoOAuthHTTPConnector($this->oAuthParams);
        $connector->setUrl($this->oAuthUrls->getUserInfoUrl());
        $connector->addHeadder('Authorization', 'Zoho-oauthtoken ' . $accessToken);
        $apiResponse = $connector->get();
        $jsonResponse = $this->processResponse($apiResponse);

        return $jsonResponse['Email'];
    }

    /**
     * @param string $apiResponse
     * @return string
     */
    public function processResponse($apiResponse) {
        list($headers, $content) = explode("\r\n\r\n", $apiResponse, 2);
        $jsonResponse = json_decode($content, true);

        return $jsonResponse;
    }

}
