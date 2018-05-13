<?php
//check if we are running from laravel if so let's pull configurations from laravel env
if (function_exists('env') && class_exists("Illuminate\Foundation\Application")) {
    return [
        'sandbox'                   => env('ZOHO_SANDBOX', false),
        'authorize_url'             => env('ZOHO_AUTHORIZE_URL', false),
        'client_id'                 => env('ZOHO_CLIENT_ID', false),
        'client_secret'             => env('ZOHO_SECRET', false),
        'redirect_uri'              => env('ZOHO_REDIRECT_URI', false),
        'access_token_url'          => env('ZOHO_ACCESS_TOKEN_URL', false),
        'refresh_token_url'         => env('ZOHO_REFRESH_TOKEN_URL', false),
        'token_path'                => env('ZOHO_TOKEN_PERSISTENCE_PATH', false),
        'authorization'             => env('ZOHO_AUTH', false),
        'self_code'                 => env('ZOHO_SELF_CODE', false),
        'v1_auth'                   => env('ZOHO_V1_AUTH', false),
        'grant_token'               => env('ZOHO_GRANT_TOKEN', false)
    ];
} else {
    //well we're not running in laravel so lets store configs here (ugly)
    return [
        "apiBaseUrl"                => "",
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
