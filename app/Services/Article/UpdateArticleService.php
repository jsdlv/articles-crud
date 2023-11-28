<?php

declare(strict_types=1);

namespace App\Services\Article;

use Carbon\Carbon;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class UpdateArticleService
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

    public function execute(int $id, string $title, string $description): void
    {
        $this->database->createQueryBuilder()
            ->update('articles')
            ->set('title', ':title')
            ->set('description', ':description')
            ->set('updated_at', ':updated_at')
            ->where('id = :id')
            ->setParameters(
                [
                    'title' => $title,
                    'description' => $description,
                    'updated_at' => Carbon::now(),
                    'id' => $id
                ]
            )->executeQuery();
    }
}