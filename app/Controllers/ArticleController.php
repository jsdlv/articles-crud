<?php

namespace App\Controllers;

use App\Database\Database;
use App\Response\RedirectResponse;
use App\Response\Response;
use App\Response\ViewResponse;
use App\Services\Article\DeleteArticleService;
use App\Services\Article\IndexArticleService;
use App\Services\Article\ShowArticleService;
use App\Services\Article\StoreArticleService;
use App\Services\Article\UpdateArticleService;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

class ArticleController extends Database
{
    public function index(): Response
    {
        $service = new IndexArticleService();
        $articles = $service->execute();

        return new ViewResponse('articles/index', [
            'articles' => $articles
        ]);
    }

    public function show(string $id): Response
    {
        $service = new ShowArticleService();
        $article = $service->execute($id);

        return new ViewResponse('articles/show', [
            'article' => $article
        ]);
    }

    public function create(): Response
    {
        $title = $_GET['title'] ?? '';
        $description = $_GET['description'] ?? '';

        return new ViewResponse('articles/create', [
            'titleContent' => $title,
            'descriptionContent' => $description,
        ]);
    }

    public function store(): Response
    {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';

        $validator = v::key(
            'title',
            v::stringType()->notEmpty()->setTemplate('Title is required'))
            ->key(
                'description',
                v::stringType()->notEmpty()->setTemplate('Description is required'));

        try {
            $validator->assert(['title' => $title, 'description' => $description]);

            $service = new StoreArticleService();
            $service->execute($title, $description);

            $_SESSION['flush'][] = ['success' => 'Article created'];
            return new RedirectResponse('/articles');
        } catch (ValidationException $e) {
            $_SESSION['flush'][] = $e->getMessages();
            return new RedirectResponse("/article/create?title=$title&description=$description");
        }
    }

    public function edit(string $id): Response
    {
        $service = new ShowArticleService();
        $article = $service->execute($id);

        return new ViewResponse('articles/edit', [
            'article' => $article
        ]);
    }

    public function update(string $id): Response
    {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';

        $validator = v::key(
            'title',
            v::stringType()->notEmpty()->setTemplate('Title is required'))
            ->key(
                'description',
                v::stringType()->notEmpty()->setTemplate('Description is required'));

        try {
            $validator->assert(['title' => $title, 'description' => $description]);

            $service = new UpdateArticleService();
            $service->execute($id, $title, $description);

            $_SESSION['flush'][] = ['success' => 'Article updated'];
            return new RedirectResponse('/article/' . $id);
        } catch (ValidationException $e) {
            $_SESSION['flush'][] = $e->getMessages();
            return new RedirectResponse('/article/edit/' . $id);
        }
    }

    public function delete(string $id): Response
    {
        $service = new DeleteArticleService();
        $service->execute($id);

        $_SESSION['flush'][] = ['success' => 'Article deleted'];

        return new RedirectResponse('/articles');
    }
}