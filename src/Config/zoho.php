<?php
//check if we are running from laravel if so let's pull configurations from laravel env
if (function_exists('env') && class_exists("Illuminate\Foundation\Application")) {
    return [
        "apiBaseUrl"                => env('ZOHO_API_BASE_URL', "www.zohoapis.com"),
        "apiVersion"                => env('ZOHO_API_VERSION', "v2"),
        'client_id'                 => env('ZOHO_CLIENT_ID', false),
        'client_secret'             => env('ZOHO_SECRET', false),
        'redirect_uri'              => env('ZOHO_REDIRECT_URI', false),
        "accounts_url"              => env('ZOHO_ACCOUNTS_URL', "https://accounts.zoho.com"),
        "currentUserEmail"          => env('ZOHO_USER_EMAIL', false),
        "token_persistence_type"    => env('ZOHO_TOKEN_PERSISTENCE_TYPE', "class"),
        "token_persistence_path"    => env('ZOHO_TOKEN_PERSISTENCE_PATH', realpath(dirname(__FILE__).'/../Tokens/')),
        "persistence_handler_class" => env('ZOHO_PERSISTENCE_HANDLER_CLASS', "Zoho\OAuth\ClientApp\ZohoOAuthPersistenceHandler"),
        "access_type"               => env('ZOHO_REDIRECT_URI', "offline"),
        'sandbox'                   => env('ZOHO_SANDBOX', false),
        "applicationLogFilePath"    => env('ZOHO_LOG_PATH', realpath(dirname(__FILE__).'/../Logs/')),


        //still refactoring
        'access_token_url'          => env('ZOHO_ACCESS_TOKEN_URL', false),
        'refresh_token_url'         => env('ZOHO_REFRESH_TOKEN_URL', false),
        'authorize_url'             => env('ZOHO_AUTHORIZE_URL', false),
        'v1_auth'                   => env('ZOHO_V1_AUTH', false),
        'grant_token'               => env('ZOHO_GRANT_TOKEN', false)
    ];
} else {
    //well we're not running in laravel so lets store configs here (ugly)
    return [
        "apiBaseUrl"                => "www.zohoapis.com",
        "apiVersion"                => "v2",
        "sandbox"                   => false,
        "applicationLogFilePath"    => "",
        "currentUserEmail"          => "",
        "client_id"                 => "",
        "client_secret"             => "",
        "redirect_uri"              => "",
        "accounts_url"              => "https://accounts.zoho.com",
        "token_persistence_path"    => "",
        "access_type"               => "offline",
        "persistence_handler_class" => "ZohoOAuthPersistenceHandler"
    ];
}
