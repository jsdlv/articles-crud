<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Collections\ArticleCollection;
use App\Models\Article;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class MysqlArticleRepository implements ArticleRepository
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

    public function getAll(): ArticleCollection
    {
        $response = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->orderBy('id', 'desc')
            ->fetchAllAssociative();

        $articleCollection = new ArticleCollection();

        foreach ($response as $article) {
            $articleCollection->add($this->buildModel($article));
        }

        return $articleCollection;
    }

    public function getById(int $id): ?Article
    {
        $response = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (empty($response)) {
            return null;
        }

        return $this->buildModel($response);
    }

    public function save(Article $article): void
    {
        $builder = $this->database->createQueryBuilder();

        if ($article->getId()) {
            $builder
                ->update('articles')
                ->set('title', ':title')
                ->set('description', ':description')
                ->set('updated_at', ':updated_at')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $article->getId(),
                        'title' => $article->getTitle(),
                        'description' => $article->getDescription(),
                        'picture' => $article->getPicture(),
                        'updated_at' => $article->getUpdatedAt()
                    ]
                )->executeQuery();
            return;
        }

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
                    'title' => $article->getTitle(),
                    'description' => $article->getDescription(),
                    'picture' => $article->getPicture(),
                    'created_at' => $article->getCreatedAt()
                ]
            )->executeQuery();
    }

    public function delete(Article $article): void
    {
        $this->database->createQueryBuilder()
            ->delete('articles', 'id')
            ->where('id = :id')
            ->setParameter('id', $article->getId())
            ->executeQuery();
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