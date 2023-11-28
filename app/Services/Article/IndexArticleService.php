<?php

declare(strict_types=1);

namespace App\Services\Article;

use App\Collections\ArticleCollection;
use App\Models\Article;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class IndexArticleService
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

    public function execute(): ArticleCollection
    {
        $response = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->orderBy('id', 'desc')
            ->fetchAllAssociative();

        $articlesCollection = new ArticleCollection();

        foreach ($response as $article) {
            $articlesCollection->add($this->buildModel($article));
        }

        return $articlesCollection;
    }

    private function buildModel(array $article): Article
    {
        return new Article(
            $article['title'],
            $article['description'],
            $article['picture'],
            $article['created_at'],
            (int)$article['id'],
            $article['updated_at']
        );
    }
}