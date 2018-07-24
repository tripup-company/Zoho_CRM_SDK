<?php

namespace Zoho\Resources;

interface InterfaceConfigurations {

    /**
     * @return string
     */
    public function getApiBaseUrl();

    /**
     * @return string
     */
    public function getApiVersion();

    /**
     * @return bool
     */
    public function getSandbox();

    /**
     * @return string
     */
    public function getAppLogFilePath();

    /**
     * @return string
     */
    public function getCurrentUserEmail();
}
