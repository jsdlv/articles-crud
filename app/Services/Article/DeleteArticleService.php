<?php

namespace App\Services\Article;

use App\Repositories\ArticleRepository;
use App\Repositories\MysqlArticleRepository;

class DeleteArticleService
{
    private ArticleRepository $articleRepository;

    public function __construct()
    {
        $this->articleRepository = new MysqlArticleRepository();
    }

    public function execute(int $id): void
    {
        $response = $this->articleRepository->getById($id);

        if ($response == null) {
            return;
        }

        $this->articleRepository->delete($response);


    }
}