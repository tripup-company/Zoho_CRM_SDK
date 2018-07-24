<?php

namespace Zoho\Resources;

interface InterfaceOAuthConfigurations {

    /**
     * @return string
     */
    public function getClientID();

    /**
     * @return string
     */
    public function getClientSecret();

    /**
     * @return string
     */
    public function getRedirectUri();

    /**
     * @return string
     */
    public function getAccountsUrl();

    /**
     * @return mixed
     */
    public function getTokenPersistencePath();

    /**
     * @return string
     */
    public function getAccessType();

    /**
     * @return string
     */
    public function getPersistenceHandlerClass();
}
