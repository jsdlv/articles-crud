<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Collections\ArticleCollection;
use App\Models\Article;

interface ArticleRepository
{
    public function getAll(): ArticleCollection;

    public function getById(int $id): ?Article;

    public function save(Article $article): void;

    public function delete(Article $article): void;

}