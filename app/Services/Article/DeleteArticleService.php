<?php

namespace App\Services\Article;

use Carbon\Carbon;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class DeleteArticleService
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

    public function execute(int $id): void
    {
        $this->database->createQueryBuilder()
            ->delete('articles', 'id')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }
}