<?php
namespace Zoho\OAuth\Common;

use Zoho\OAuth\Common\ZohoOAuthException;

class ZohoOAuthTokens
{
    private $refreshToken;
    private $accessToken;
    private $expiryTime;
    private $zuid;
    
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken=$refreshToken;
    }
    
    public function getAccessToken()
    {
        if ($this->isValidAccessToken()) {
            return $this->accessToken;
        }
        throw new ZohoOAuthException("Access token got expired!");
    }
    public function setAccessToken($accessToken)
    {
        $this->accessToken=$accessToken;
    }
    
    public function getExpiryTime()
    {
        return $this->expiryTime;
    }
    public function setExpiryTime($expiryTime)
    {
        return $this->expiryTime=$expiryTime;
    }
    
    public function isValidAccessToken()
    {
        return ($this->getExpiryTime()-$this->getCurrentTimeInMillis())>10;
    }
    
    public function getCurrentTimeInMillis()
    {
        return round(microtime(true)*1000);
    }

    /**
     * userEmailId
     * @return String
     */
    public function getZUID()
    {
        return $this->zuid;
    }

    /**
     * Zoho User Id
     * @param String $zuid
     */
    public function setZUID($zuid)
    {
        $this->zuid = $zuid;
    }
}
