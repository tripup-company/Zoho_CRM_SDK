<?php

namespace Zoho\Resources;

class DbConfigurations {

    /**
     * @type InterfaceConfigurations
     */
    protected static $instance;
    protected $dbHost;
    protected $dbName;
    protected $userName;
    protected $userPass;
    protected $databaseType;

    /**
     * @param array $configurations
     * @return DbConfigurations
     */
    public static function getInstance() {
        if (is_null(static::$instance)) :
            static::$instance = new static();
        endif;

        return static::$instance;
    }

    public function setDatabaseHost($host) {
        $this->dbHost = $host;
    }

    public function setDatabaseName($name) {
        $this->dbName = $name;
    }

    public function setUserName($name) {
        $this->userName = $name;
    }

    public function setUserPassword($pass) {
        $this->userPass = $pass;
    }

    public function setDatabaseType($type) {
        $this->databaseType = $type;
    }

    public function getDatabaseHost() {
        return $this->dbHost;
    }

    public function getDatabaseName() {
        return $this->dbName;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function getUserPassword() {
        return $this->userPass;
    }

    public function getDatabaseType() {
        return $this->databaseType;
    }

}
