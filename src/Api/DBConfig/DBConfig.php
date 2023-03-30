<?php
namespace NiloBlack\NotificacoesSlack\Api\DBConfig;

class DBConfig {
    private $driver;
    private $host;
    private $userName;
    private $password;
    private $database;
    
    function getDriver() {
        return $this->driver;
    }

    function getHost() {
        return $this->host;
    }

    function getUserName() {
        return $this->userName;
    }

    function getPassword() {
        return $this->password;
    }

    function getDatabase() {
        return $this->database;
    }

    function setDriver($driver) {
        $this->driver = $driver;
    }

    function setHost($host) {
        $this->host = $host;
    }

    function setUserName($userName) {
        $this->userName = $userName;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setDatabase($database) {
        $this->database = $database;
    }

    public function __construct() {
        $this->setDriver("mysql");
        $this->setHost(MYSQL_HOST);
        $this->setDatabase(MYSQL_DATABASE);
        $this->setUserName(MYSQL_USERNAME);
        $this->setPassword(MYSQL_PASSWORD);
    }
}