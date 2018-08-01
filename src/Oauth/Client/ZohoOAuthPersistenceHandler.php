<?php

namespace Zoho\Oauth\Client;

use Zoho\Oauth\Common\OAuthLogger;

class ZohoOAuthPersistenceHandler implements ZohoOAuthPersistenceInterface {

    protected $dataBaseHost;
    protected $dataBaseName;
    protected $dataBaseUserName;
    protected $dataBaseUserPass;

    /**
     * @var ZohoOAuthParams $oAuthParams
     */
    protected $oAuthParams;

    /**
     * @type Zoho\Resources\DbConfigurations $databaseConfigs
     */
    public static $databaseConfigs;

    /*
     * @param ZohoOAuthParams $oAuthParams
     */

    public function __construct($oAuthParams) {
        $this->oAuthParams = $oAuthParams;
        $dbConfigs = ZohoOAuthPersistenceHandler::$databaseConfigs;

        if (!empty($dbConfigs) && get_class($dbConfigs) === 'Zoho\Resources\DbConfigurations') {
            $this->dataBaseHost = $dbConfigs->getDatabaseHost();
            $this->dataBaseName = $dbConfigs->getDatabaseName();
            $this->dataBaseUserName = $dbConfigs->getUserName();
            $this->dataBaseUserPass = $dbConfigs->getUserPassword();
        }
    }

    public function saveOAuthData($zohoOAuthTokens) {
        try {
            $userID = $this->oAuthParams->getClientId();
            $accessToken = $zohoOAuthTokens->getAccessToken();
            $refreshToken = $zohoOAuthTokens->getRefreshToken();
            $expiryTime = $zohoOAuthTokens->getExpiryTime();

            $mySqlConnection = $this->getMysqlConnection();
            $query = "UPDATE crm_oauth_tokens SET grant_token='NULL', access_token='$accessToken', refresh_token='$refreshToken', expiry_time='$expiryTime' WHERE user_id='$userID'";
            $mySqlConnection->query($query);
            
            if (!$result) {
                OAuthLogger::severe("OAuth token insertion failed: (" . $mySqlConnection->errorCode() . ") " . $mySqlConnection->errorInfo());
            }
        } catch (\PDOException $e) {
            Logger:severe("Exception occured while inserting OAuthTokens into DB(file::ZohoOAuthPersistenceHandler)({$e->getMessage()})\n{$e}");
        } finally {
            unset($mySqlConnection);
        }
    }

    public function getOAuthTokens($userEmailId = null) {
        $oAuthTokens = null;
        $userID = $this->oAuthParams->getClientId();

        try {
            $mySqlConnection = $this->getMysqlConnection();
            $query = "SELECT * FROM crm_oauth_tokens where user_id='" . $userID . "'";
            $result = $mySqlConnection->query($query)->fetch();
            
            if (!empty($result) && !empty($result['access_token'])) {
                return [
                    'accessToken' => $result['access_token'],
                    'refreshToken' => $result['refresh_token'],
                    'expiryTime' => $result['expiry_time'],
                    'userEmailId' => $userEmailId
                ];
            }
        } catch (\Exception $ex) {
            OAuthLogger::severe("Exception occured while getting OAuthTokens from DB(file::ZohoOAuthPersistenceHandler)({$ex->getMessage()})\n{$ex}");
        } finally {
            unset($mySqlConnection);
        }

        return $oAuthTokens;
    }

    public function deleteOAuthTokens($userEmailId) {
        exit();
    }

    public function getGrantToken() {
        try {
            $mySqlConnection = $this->getMysqlConnection();
            $query = "SELECT * FROM crm_oauth_tokens WHERE user_id='" . $this->oAuthParams->getClientId() . "'";
            $result = $mySqlConnection->query($query)->fetch();

            if (empty($result)) {
                $this->createNewClientRow();
                $this->getGrantToken();
            } else if (empty($result['grant_token'])){
                throw new \Exception('Please, set grant token for your Client ID in MySQL. You can generate it here: https://accounts.zoho.eu/developerconsole');
            } else {
                return $result['grant_token'];
            }
        } catch (\Exception $e) {
            echo 'Notice [' . $e->getCode() . ']: ' . $e->getMessage();
            exit();
        } catch (\PDOException $e) {
            OAuthLogger::severe("Exception occured while Deleting OAuthTokens from DB(file::ZohoOAuthPersistenceHandler)({$e->getMessage()})\n{$e}");
        } finally {
            unset($mySqlConnection);
        }
    }

    protected function getMysqlConnection() {
        try {
            $mySqlConnection = new \PDO('mysql:host=' . $this->dataBaseHost . ';dbname=' . $this->dataBaseName, $this->dataBaseUserName, $this->dataBaseUserPass);
            $creationTableQuery = "CREATE TABLE IF NOT EXISTS crm_oauth_tokens ("
                    . "ID int(11) NOT NULL auto_increment,"
                    . "user_id varchar(255) NOT NULL default '',"
                    . "grant_token varchar(255) NULL,"
                    . "access_token varchar(255) NULL,"
                    . "refresh_token varchar(255) NULL,"
                    . "expiry_time varchar(255) NULL,"
                    . "PRIMARY KEY (ID))";
            $mySqlConnection->query($creationTableQuery);
            return $mySqlConnection;
        } catch (\PDOException $e) {
            OAuthLogger::severe("Failed to connect to MySQL: (" . $e->getCode() . ") " . $e->getMessage());
            echo "Failed to connect to MySQL: (" . $e->getCode() . ") " . $e->getMessage();
        }
    }

    protected function createNewClientRow() {
        try {
            $mySqlConnection = $this->getMysqlConnection();
            $query = "INSERT INTO crm_oauth_tokens (user_id) VALUES('" . $this->oAuthParams->getClientId() . "')";
            $mySqlConnection->query($query);
        } catch (\PDOException $e) {
            OAuthLogger::severe("Failed to insert a new client ID to MySQL: (" . $e->getCode() . ") " . $e->getMessage());
            echo "Failed to insert a new client ID to MySQL: (" . $e->getCode() . ") " . $e->getMessage();
        } finally {
            unset($mySqlConnection);
        }
    }

}
