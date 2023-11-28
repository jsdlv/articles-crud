<?php

namespace App\Controllers;

use App\Collections\ArticleCollection;
use App\Database\Database;
use App\Models\Article;
use App\Response\RedirectResponse;
use App\Response\Response;
use App\Response\ViewResponse;
use Carbon\Carbon;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

class ArticleController extends Database
{
    public function index(): Response
    {
        $response = $this->selectAll();

        $articlesCollection = new ArticleCollection();

        foreach ($response as $article) {
            $articlesCollection->add(new Article(
                $article['title'],
                $article['description'],
                $article['picture'],
                $article['created_at'],
                (int)$article['id'],
                $article['updated_at']
            ));
        }

        return new ViewResponse('articles/index', [
            'articles' => $articlesCollection
        ]);
    }

    public function show(string $id): Response
    {
        $response = $this->selectArticle((int)$id);

        if (!$response) {
            return new ViewResponse('404');
        }

        $article = new Article(
            $response['title'],
            $response['description'],
            $response['picture'],
            $response['created_at'],
            (int)$response['id'],
            $response['updated_at']
        );

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
            $this->storeArticle($title, $description);
            $_SESSION['_flush'][] = ['success' => 'Article created'];
            return new RedirectResponse('/articles');
        } catch (ValidationException $e) {
            $_SESSION['_flush'][] = $e->getMessages();
            return new RedirectResponse("/article/create?title=$title&description=$description");
        }
    }

    public function edit(string $id): Response
    {
        $response = $this->selectArticle((int)$id);

        if (!$response) {
            return new ViewResponse('404');
        }

        $article = new Article(
            $response['title'],
            $response['description'],
            $response['picture'],
            $response['created_at'],
            (int)$response['id'],
            $response['updated_at']
        );

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
            $this->updateArticle($id, $title, $description);
            $_SESSION['_flush'][] = ['success' => 'Article updated'];
            return new RedirectResponse('/article/' . $id);
        } catch (ValidationException $e) {
            $_SESSION['_flush'][] = $e->getMessages();
            return new RedirectResponse('/article/edit/' . $id);
        }
    }

    public function delete(string $id): Response
    {
        $this->deleteArticle((int)$id);
        $_SESSION['_flush'][] = ['success' => 'Article deleted'];

        return new RedirectResponse('/articles');
    }

    private function selectAll(): array
    {
        return $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->fetchAllAssociative();
    }

    private function selectArticle(int $id)
    {
        return $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();
    }

    private function storeArticle(string $title, string $description): void
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

    private function updateArticle(int $id, string $title, string $description): void
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

    private function deleteArticle(int $id): void
    {
        $this->database->createQueryBuilder()
            ->delete('articles', 'id')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }
}