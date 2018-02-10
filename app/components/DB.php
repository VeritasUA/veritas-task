<?php

namespace app\components;

use \Doctrine\DBAL\DriverManager;
use \Doctrine\DBAL\Configuration;

class DB
{
	private static $_conn = null;

	public function __construct(){}

    private function __clone() {}

    public static function getInstance()
	{
		if (self::$_conn === null) {
			$connectionParams = require_once(__DIR__ . '/../../config/db.php');
			$config = new Configuration();
			$conn = DriverManager::getConnection($connectionParams, $config);
            self::$_conn = $conn;
        }
        return self::$_conn;
    }
}