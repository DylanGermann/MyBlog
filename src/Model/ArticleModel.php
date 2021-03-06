<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\ArticleEntity;
use App\Services\DatabaseService;
use JetBrains\PhpStorm\Pure;
use PDO;

class ArticleModel
{
    private DatabaseService $database;

    public function __construct()
    {
        $this->database = DatabaseService::getInstance();
    }

    /**
     * @return bool|array
     */
    public function getAllArticles(): bool|array
    {
        $query = 'SELECT * FROM article WHERE status = 1 OR status = 2';
        $result = $this->getPdo()->query($query, PDO::FETCH_CLASS, ArticleEntity::class)->fetchAll();
        if ($result === false) {
            return [];
        }
        return $result;
    }
    /**
     * @return bool|array
     */
    public function getAllPublishedArticles(): bool|array
    {
        $query = 'SELECT * FROM article WHERE status = 2 ORDER BY created_at DESC';
        $result = $this->getPdo()->query($query, PDO::FETCH_CLASS, ArticleEntity::class)->fetchAll();
        if ($result === false) {
            return [];
        }
        return $result;
    }

    #[Pure] private function getPdo(): PDO
    {
        return $this->database->getPdo();
    }

    /**
     * @param int $numberOfArticles
     * @return array|null
     */
    public function getArticlesByNumber(int $numberOfArticles): array
    {
        $query = "SELECT * FROM article LIMIT $numberOfArticles";
        $result = $this->getPdo()->query($query, PDO::FETCH_CLASS, ArticleEntity::class)->fetchAll();
        if ($result === false) {
            return [];
        }
        return $result;
    }

    public function getPublishedArticlesByNumber(int $numberOfArticles): array
    {
        $query = "SELECT * FROM article WHERE status = 2 ORDER BY created_at DESC LIMIT $numberOfArticles  ";
        $result = $this->getPdo()->query($query, PDO::FETCH_CLASS, ArticleEntity::class)->fetchAll();
        if ($result === false) {
            return [];
        }
        return $result;
    }

    /**
     * @param int $idArticle
     * @return mixed|null
     */
    public function getArticleById(int $idArticle): ?ArticleEntity
    {
        $query = "SELECT * FROM article WHERE id = $idArticle";
        $result = $this->getPdo()->query($query, PDO::FETCH_CLASS, ArticleEntity::class)->fetch();
        if ($result === false) {
            return null;
        }
        return $result;
    }

    /**
     * @return int|null
     */
    public function countTotalArticles(): ?int
    {
        $query = "SELECT COUNT(*) as total FROM article";
        $result = $this->database->getPdo()->query($query)->fetchObject();
        if ($result === false) {
            return null;
        }
        return $result->total;
    }

    /**
     * @return int|null
     */
    public function countTotalPendingArticles(): null|int
    {
        $query = "SELECT COUNT(*) as total FROM article WHERE status = 1";
        $result = $this->database->getPdo()->query($query)->fetchObject();
        if ($result === false) {
            return null;
        }
        return $result->total;
    }

    /**
     * @param int $articleId
     * @return void
     * Supprimer un article par l'id
     */
    public function deleteArticleById(int $articleId): void
    {
        $query = "DELETE FROM article WHERE id = $articleId";
        $query = "UPDATE article SET status = :status WHERE id = :article";
        $data = [
            'status' => 3,
            'article' => $articleId
        ];
        $this->getPdo()->prepare($query)->execute($data);
    }


    /**
     * @param int $articleId
     * @param string $title
     * @param string $header
     * @param string $content
     * @param string $status
     * @return void
     */
    public function updateArticleById(int $articleId, string $title, string $header, string $content, string $status): void
    {
        $query = "UPDATE article SET title = :title, header = :header, content = :content, status = :status, updated_at = NOW()
            WHERE id = :article";
        $data = [
            'title' => $title,
            'header' => $header,
            'content' => $content,
            'status' => $status,
            'article' => $articleId
        ];
        $this->getPdo()->prepare($query)->execute($data);
    }
    public function createArticle(string $title, string $header, string $content, string $status, int $userId): void
    {
        $query = "INSERT INTO article (title, header, content, created_at, updated_at, User_id, status)
            VALUES (:title, :header, :content, NOW() , NOW(), :user, :status)";
        $data = [
            'title' => $title,
            'header' => $header,
            'content' => $content,
            'user' => $userId,
            'status' => $status
        ];
        $this->getPdo()->prepare($query)->execute($data);
    }
}
