<?php

namespace Zoho\Oauth\Common;

use Zoho\CRM\Exception\Logger;

class OAuthLogger {

    public static $oAuthLoggerPath;

    public static function writeToFile($msg) {
        if (empty(OAuthLogger::$oAuthLoggerPath)) {
            Logger::writeToFile('Oauth Logger file path is not set in OAuthConfigurations.');
        } else {
            $filePointer = fopen(OAuthLogger::$oAuthLoggerPath, "a");
            fwrite($filePointer, sprintf("%s %s\n", date("Y-m-d H:i:s"), $msg));
            fclose($filePointer);
        }
    }

    public static function warn($msg) {
        self::writeToFile("WARNING: $msg");
    }

    public static function info($msg) {
        self::writeToFile("INFO: $msg");
    }

    public static function severe($msg) {
        self::writeToFile("SEVERE: $msg");
    }

    public static function err($msg) {
        self::writeToFile("ERROR: $msg");
    }

    public static function debug($msg) {
        self::writeToFile("DEBUG: $msg");
    }

}
