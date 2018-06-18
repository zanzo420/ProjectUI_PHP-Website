<?php

class DB {
    private static $dbhost = "localhost";
    private static $dbuser = "Mayron"; // placeholder?
    private static $dbpass = "Fireicexv83445!!"; // placeholder?
    private static $dbname = "projectui";

    /** @var PDO */
    private static $db;

    public static function connect() {
        try {
            $dsn = "mysql:dbname=" . self::$dbname . ";host=" . self::$dbhost; // data source name
            self::$db = new PDO($dsn, self::$dbuser, self::$dbpass);

            // PDO::ERRMODE_SILENT is the default. No need to use errorCode(), errorInfo()
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo $e->getMessage();
			exit();
        }
    }

    public static function beginTransaction() {
        self::$db->beginTransaction();
    }

    public static function commit() {
        self::$db->commit();
    }

    public static function rollBack() {
        self::$db->rollBack();
    }

    public static function query($query, $params = null) {
        if (empty($params) || gettype($params) != "array") {
            return self::$db->query($query);
        }

        $statement = self::$db->prepare($query);
        $result = $statement->execute($params);
        if (!$result) {
            return false;
        } else {
            return $statement;
        }
    }

    // PDO does not use .close();
    public static function close() {
        if (isset(self::$db)) unset(self::$db);
    }
}