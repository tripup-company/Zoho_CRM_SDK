<?php

namespace Zoho\Oauth\Common;

use Zoho\Oauth\Common\ZohoOAuthException;

class ZohoOAuthTokens {

    private $refreshToken;
    private $accessToken;
    private $expiryTime;
    private $userEmailId;

    public function getRefreshToken() {
        return $this->refreshToken;
    }

    public function setRefreshToken($refreshToken) {
        $this->refreshToken = $refreshToken;
    }

    public function getAccessToken() {
        if ($this->isValidAccessToken()) {
            return $this->accessToken;
        }
        throw new ZohoOAuthException("Access token got expired!");
    }

    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }

    public function getExpiryTime() {
        return $this->expiryTime;
    }

    public function setExpiryTime($expiryTime) {
        return $this->expiryTime = $expiryTime;
    }

    public function isValidAccessToken() {
        return empty($this->getExpiryTime()) ? false : ($this->getExpiryTime() - $this->getCurrentTimeInMillis()) > 10;
    }

    public function getCurrentTimeInMillis() {
        return round(microtime(true) * 1000);
    }

    /**
     * userEmailId
     * @return String
     */
    public function getUserEmailId() {
        return $this->userEmailId;
    }

    /**
     * userEmailId
     * @param mixed $userEmailId
     */
    public function setUserEmailId($userEmailId = null) {
        $this->userEmailId = $userEmailId;
    }

    /**
     * @return array
     */
    public function toArray() {
        return get_object_vars($this);
    }

}
