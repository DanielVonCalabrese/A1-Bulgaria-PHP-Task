<?php

namespace TestTaskA1;

class Connection
{
    private static $connection;

    protected function __construct()
	{
        
    }
	
    public function connect()
	{
        $params = parse_ini_file('database.ini');
		
        if ( $params === false ) {
            throw new \Exception("Error reading database configuration file");
        }
		
        $connectionString = sprintf("mysql:host=%s;dbname=%s", 
                $params['host'],
                $params['database']
		);

        $pdo = new \PDO($connectionString, $params['user'], $params['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
	
    public static function get()
	{
        if(static::$connection === null)
		{
            static::$connection = new static();
        }

        return static::$connection;
    }
}