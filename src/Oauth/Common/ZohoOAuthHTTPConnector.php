<?php
namespace Zoho\Oauth\Common;

use Zoho\Oauth\Common\ZohoOAuthConstants;
use Zoho\Oauth\Common\ZohoOAuthParams;

class ZohoOAuthHTTPConnector
{
    private $url;
    private $requestParams = array();
    private $requestHeaders = array();
    private $requestParamCount = 0;
    
    /**
     * @param ZohoOAuthParams $oAuthParams
     */
    public function __construct($oAuthParams) {
        $this->addParam('client_id', $oAuthParams->getClientId());
        $this->addParam('client_secret', $oAuthParams->getClientSecret());
        $this->addParam('redirect_uri', $oAuthParams->getRedirectUrl());
    }
    
    public function post() {
        $curl_pointer=curl_init();
        curl_setopt($curl_pointer, CURLOPT_URL, $this->getUrl());
        curl_setopt($curl_pointer, CURLOPT_HEADER, 1);
        curl_setopt($curl_pointer, CURLOPT_POSTFIELDS, $this->getUrlParamsAsString($this->requestParams));
        curl_setopt($curl_pointer, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_pointer, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($curl_pointer, CURLOPT_HTTPHEADER, $this->getRequestHeadersAsArray());
        curl_setopt($curl_pointer, CURLOPT_POST, $this->requestParamCount);
        curl_setopt($curl_pointer, CURLOPT_CUSTOMREQUEST, 'POST');
        $result = curl_exec($curl_pointer);
        curl_close($curl_pointer);
        
        return $result;
    }
    
    public function get() {
        $curl_pointer = curl_init();
        $url = $this->clearHttpString($this->getUrl() . "?" . http_build_query($this->requestParams));
        curl_setopt($curl_pointer, CURLOPT_URL, $url);
        curl_setopt($curl_pointer, CURLOPT_HEADER, 1);
        curl_setopt($curl_pointer, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_pointer, CURLOPT_HTTPHEADER, $this->getRequestHeadersAsArray());
        curl_setopt($curl_pointer, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($curl_pointer, CURLOPT_CUSTOMREQUEST, 'GET');
        $result = curl_exec($curl_pointer);
        curl_close($curl_pointer);
        
        return $result;
    }
    
    protected function clearHttpString($url) {
        return (preg_match('/%5B0%5D/', $url)) ? preg_replace('/%5B0%5D/', '', $url) : $url;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }
    public function addParam($key, $value)
    {
        if (!isset($this->requestParams[$key])) {
            $this->requestParams[$key]=array($value);
        } else {
            $valArray=$this->requestParams[$key];
            array_push($valArray, $value);
            $this->requestParams[$key]=$valArray;
        }
    }
    public function addHeadder($key, $value)
    {
        $this->requestHeaders[$key]=$value;
    }
    
    public function getRequestHeadersMap()
    {
        return $this->requestHeaders;
    }
    
    public function getUrlParamsAsString($urlParams)
    {
        $params_as_string="";
        foreach ($urlParams as $key=>$valueArray) {
            foreach ($valueArray as $value) {
                $params_as_string=$params_as_string.$key."=".$value."&";
                $this->requestParamCount++;
            }
        }
        $params_as_string=rtrim($params_as_string, "&");
        $params_as_string=str_replace(PHP_EOL, '', $params_as_string);
        return $params_as_string;
    }
    
    public function getRequestHeadersAsArray()
    {
        $headersArray=array();
        $headersMap=self::getRequestHeadersMap();
        foreach ($headersMap as $key => $value) {
            $headersArray[]=$key.":".$value;
        }
    
        return $headersArray;
    }
}
