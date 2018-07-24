<?php

namespace Zoho\CRM\Api;

use Zoho\CRM\Common\ZohoHTTPConnector;
use Zoho\CRM\Exception\ZCRMException;
use Zoho\CRM\Api\Response\APIResponse;
use Zoho\CRM\Api\Response\BulkAPIResponse;
use Zoho\CRM\Api\Response\FileAPIResponse;
use Zoho\CRM\Api\Handler\MetaDataAPIHandler;

/**
 * API Request initialization
 */
class APIRequest {
    
    /**
     * @var APIHandler $apiHandler
     */
    protected $apiHandler;
    
    /**
     * @var string $url
     */
    private $url = null;
    
    /**
     * @var array $requestParams
     */
    private $requestParams = array();
    
    /**
     * @var array $requestHeaders
     */
    private $requestHeaders = array();
    
    /**
     * @var string $requestBody
     */
    private $requestBody;
    
    /**
     * @var string $requestMethod
     */
    private $requestMethod;
    
    /**
     * @var string $apiKey
     */
    private $apiKey = null;
    
    /**
     * @var string $response
     */
    private $response = null;
    
    /**
     * @var array $responseInfo
     */
    private $responseInfo;
    
    /**
     * @param APIHandler $apiHandler
     */
    public function __construct($apiHandler) {
        $this->apiHandler = $apiHandler;
        $this->constructAPIUrl();
        $this->setUrl($this->url . $apiHandler->getUrlPath());
        if (substr($apiHandler->getUrlPath(), 0, 4) !== 'http') {
            $this->setUrl('https://' . $this->url);
        }
        $this->setRequestParams($apiHandler->getRequestParams());
        $this->setRequestHeaders($apiHandler->getRequestHeaders());
        $this->setRequestBody($apiHandler->getRequestBody());
        $this->setRequestMethod($apiHandler->getRequestMethod());
        $this->setApiKey($apiHandler->getApiKey());
    }

    /**
     * Method to construct the API Url
     */
    public function constructAPIUrl() {
        $configs = $this->apiHandler->getConfigs();
        $hitSandbox = $configs->getSandbox();
        $baseUrl = $hitSandbox ? str_replace('www', 'sandbox', $configs->getApiBaseUrl()) : $configs->getApiBaseUrl();
        $this->url = $baseUrl . '/crm/' . $configs->getApiVersion() . '/';
    }

    protected function authenticateRequest() {
        try {
            $accessToken = $this->apiHandler->getApiKey();
            $this->requestHeaders['Authorization'] = 'Zoho-oauthtoken ' . $accessToken;
        } catch (ZCRMException $ex) {
            throw $ex;
        }
    }

    /**
     * initiate the request and get the API response
     * @return APIResponse
     */
    public function getAPIResponse() {
        try {
            $connector = new ZohoHTTPConnector();
            $connector->setUrl($this->url);
            $this->authenticateRequest();
            $connector->setRequestHeadersMap($this->requestHeaders);
            $connector->setRequestParamsMap($this->requestParams);
            $connector->setRequestBody($this->requestBody);
            $connector->setRequestType($this->requestMethod);
            $connector->setApiKey($this->apiKey);
            $response = $connector->fireRequest();
            $this->response = $response[0];
            $this->responseInfo = $response[1];
            return new APIResponse($this->response, $this->responseInfo['http_code']);
        } catch (ZCRMException $e) {
            throw $e;
        }
    }

    /**
     * initiate the request and get the API response
     * @return BulkAPIResponse
     */
    public function getBulkAPIResponse() {
        try {
            $connector = new ZohoHTTPConnector();
            $connector->setUrl($this->url);
            $this->authenticateRequest();
            $connector->setRequestHeadersMap($this->requestHeaders);
            $connector->setRequestParamsMap($this->requestParams);
            $connector->setRequestBody($this->requestBody);
            $connector->setRequestType($this->requestMethod);
            $connector->setApiKey($this->apiKey);
            $connector->setBulkRequest(true);
            $response = $connector->fireRequest();
            $this->response = $response[0];
            $this->responseInfo = $response[1];
            return new BulkAPIResponse($this->response, $this->responseInfo['http_code']);
        } catch (ZCRMException $e) {
            throw $e;
        }
    }

    public function uploadFile($filePath) {
        try {
            $fileContent = file_get_contents($filePath);
            $filePathArray = explode('/', $filePath);
            $fileName = $filePathArray[sizeof($filePathArray) - 1];
            if (function_exists('curl_file_create')) { // php 5.6+
                $cFile = curl_file_create($filePath);
            } else { //
                $cFile = '@' . realpath($filePath);
            }
            $post = array('file' => $cFile);

            $connector = ZohoHTTPConnector::getInstance();
            $connector->setUrl($this->url);
            self::authenticateRequest();
            $connector->setRequestHeadersMap($this->requestHeaders);
            $connector->setRequestParamsMap($this->requestParams);
            $connector->setRequestBody($post);
            $connector->setRequestType($this->requestMethod);
            $connector->setApiKey($this->apiKey);
            $response = $connector->fireRequest();
            $this->response = $response[0];
            $this->responseInfo = $response[1];
            return new APIResponse($this->response, $this->responseInfo['http_code']);
        } catch (ZCRMException $e) {
            throw $e;
        }
    }

    public function uploadLinkAsAttachment($linkURL) {
        try {
            $post = array('attachmentUrl' => $linkURL);

            $connector = ZohoHTTPConnector::getInstance();
            $connector->setUrl($this->url);
            self::authenticateRequest();
            $connector->setRequestHeadersMap($this->requestHeaders);
            $connector->setRequestBody($post);
            $connector->setRequestType($this->requestMethod);
            $connector->setApiKey($this->apiKey);
            $response = $connector->fireRequest();
            $this->response = $response[0];
            $this->responseInfo = $response[1];
            return new APIResponse($this->response, $this->responseInfo['http_code']);
        } catch (ZCRMException $e) {
            throw $e;
        }
    }

    public function downloadFile() {
        try {
            $connector = ZohoHTTPConnector::getInstance();
            $connector->setUrl($this->url);
            self::authenticateRequest();
            $connector->setRequestHeadersMap($this->requestHeaders);
            $connector->setRequestParamsMap($this->requestParams);
            $connector->setRequestType($this->requestMethod);
            $response = $connector->downloadFile();
            return (new FileAPIResponse())->setFileContent($response[0], $response[1]['http_code']);
        } catch (ZCRMException $e) {
            throw $e;
        }
    }

    /**
     * Get the request url
     * @return String
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set the request url
     * @param String $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * Get the request parameters
     * @return Array
     */
    public function getRequestParams() {
        return $this->requestParams;
    }

    /**
     * Set the request parameters
     * @param Array $requestParams
     */
    public function setRequestParams($requestParams) {
        $this->requestParams = $requestParams;
    }

    /**
     * Get the request headers
     * @return Array
     */
    public function getRequestHeaders() {
        return $this->requestHeaders;
    }

    /**
     * Set the request headers
     * @param Array $requestHeaders
     */
    public function setRequestHeaders($requestHeaders) {
        $this->requestHeaders = $requestHeaders;
    }

    /**
     * Get the request body
     * @return JSON
     */
    public function getRequestBody() {
        return $this->requestBody;
    }

    /**
     * Set the request body
     * @param JSON $requestBody
     */
    public function setRequestBody($requestBody) {
        $this->requestBody = $requestBody;
    }

    /**
     * Get the request method
     * @return String
     */
    public function getRequestMethod() {
        return $this->requestMethod;
    }

    /**
     * Set the request method
     * @param String $requestMethod
     */
    public function setRequestMethod($requestMethod) {
        $this->requestMethod = $requestMethod;
    }

    /**
     * Get the API Key used in the input json data(like 'modules', 'data','layouts',..etc)
     * @return String
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     *  Set the API Key used in the input json data(like 'modules', 'data','layouts',..etc)
     * @param String $apiKey
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

}

?>