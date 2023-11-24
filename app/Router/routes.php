<?php

use App\Controllers\ArticleController;

return [
    ['GET', '/articles', [ArticleController::class, 'index']],
    ['GET', '/article/{id:\d+}', [ArticleController::class, 'show']],
    ['GET', '/article/create', [ArticleController::class, 'create']],
    ['POST', '/articles', [ArticleController::class, 'store']],
    ['GET', '/article/edit/{id:\d+}', [ArticleController::class, 'edit']],
    ['POST', '/article/{id:\d+}', [ArticleController::class, 'update']],
    ['POST', '/article/delete/{id:\d+}', [ArticleController::class, 'delete']],
];