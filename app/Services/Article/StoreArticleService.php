<?php

declare(strict_types=1);

namespace App\Services\Article;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\MysqlArticleRepository;

class StoreArticleService
{
    private ArticleRepository $mysqlArticleRepository;

    public function __construct()
    {
        $this->mysqlArticleRepository = new MysqlArticleRepository();
    }

    public function execute(string $title, string $description): void
    {
        $article = new Article(
            $title,
            $description,
            'https://random.imagecdn.app/500/150'
        );

        $this->mysqlArticleRepository->save($article);
    }
}