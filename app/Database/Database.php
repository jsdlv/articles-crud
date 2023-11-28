<?php

namespace App\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

abstract class Database
{
    protected Connection $database;

    public function __construct()
    {
        $connectionParams = [
            'dbname' => 'articles-crud',
            'user' => 'root',
            'password' => $_ENV['DATABASE_PASSWORD'],
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];
        $this->database = DriverManager::getConnection($connectionParams);
    }
}