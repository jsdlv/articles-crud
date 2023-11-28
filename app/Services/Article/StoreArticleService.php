<?php

declare(strict_types=1);

namespace App\Services\Article;

use Carbon\Carbon;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class StoreArticleService
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

    public function execute(string $title, string $description): void
    {
        $this->database->createQueryBuilder()
            ->insert('articles')
            ->values(
                [
                    'title' => ':title',
                    'description' => ':description',
                    'picture' => ':picture',
                    'created_at' => ':created_at'
                ]
            )->setParameters(
                [
                    'title' => $title,
                    'description' => $description,
                    'picture' => 'https://random.imagecdn.app/500/150',
                    'created_at' => Carbon::now()
                ]
            )->executeQuery();
    }
}