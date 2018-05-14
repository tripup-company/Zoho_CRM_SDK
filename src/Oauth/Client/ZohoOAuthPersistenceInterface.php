<?php
namespace Zoho\OAuth\Client;

interface ZohoOAuthPersistenceInterface
{
    public function saveOAuthData($zohoOAuthTokens);
    public function getOAuthTokens($userEmailId);
    public function deleteOAuthTokens($userEmailId);
}
