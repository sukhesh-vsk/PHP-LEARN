<?php

class Database
{
    static $DB_SERVER = "";
    static $DB_USER = "";
    static $DB_PASSWORD = "";
    static $DB_NAME = "";

    static $db;

    public static function getConnection() {
        
        $config_json = file_get_contents("env.json");
        $dbconfig = json_decode($config_json, true);

        Database::$DB_SERVER = $dbconfig["server"];
        Database::$DB_USER = $dbconfig["user"];
        Database::$DB_PASSWORD = $dbconfig["password"];
        Database::$DB_NAME = $dbconfig["database"];

        
        if (Database::$db != NULL) {
            return Database::$db;
        } else {
            Database::$db = mysqli_connect(Database::$DB_SERVER, Database::$DB_USER, Database::$DB_PASSWORD, Database::$DB_NAME);
            if (!Database::$db) {
                die("Connection failed: " . mysqli_connect_error());
            } else {
                return Database::$db;
            }
        }
    }
}
