<?php

namespace App\Services;

use App\Controller\AdminController;
use App\Controller\ArticleController;
use App\Controller\BlogController;
use App\Controller\CommentController;
use App\Controller\HomeController;
use App\Controller\LogInController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RouterService
{


    public function __construct()
    {
    }

    /**
     * @return int|null
     */
    public static function getUrlParameter(string $parameter): ?int
    {
        //on recupere les parametre de notre url
        $components = parse_url($_SERVER['REQUEST_URI']);
        if (isset($components['query'])) {
            parse_str($components['query'], $results);
            if (isset($results[$parameter])) {
                return (int)$results[$parameter];
            }
        }
        return null;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * Permet d'appeler la fonction qui retournera la vue adéquat
     */
    public static function navigate()
    {
        $id = self::getUrlParameter("id");
        //switch en fonction de l'uri, on donne la page demandée
        switch ($_SERVER['QUERY_STRING']) {
            case ArticleController::BASE_URL :
                (new ArticleController())->showArticle($id);
                break;
            case BlogController::BASE_URL:
                (new BlogController())->showBlog();
                break;
            case LogInController::LOGIN_URL:
                (new LogInController())->showLoginPage();
                break;
            case LogInController::REGISTER_URL:
                (new LogInController())->showRegisterPage();
                break;
            case LogInController::SIGNOUT_URL:
                (new LogInController())->signOut();
                break;
            case AdminController::BASE_URL:
                (new AdminController())->showDashboard();
                break;
            case AdminController::ARTICLES_URL:
                (new AdminController())->showArticlesManagementPage();
                break;
            case AdminController::COMMENTS_URL:
                (new AdminController())->showCommentsManagementPage();
                break;
            case AdminController::VALIDATE_COMMENTS_URL:
                $status = self::getUrlParameter("status");
                if (isset($status)) {
                    (new AdminController())->validateComment($id, $status);
                }
                break;
            case AdminController::DELETE_COMMENTS_URL:
                (new AdminController())->deleteComment($id);
                break;
            case AdminController::EDIT_ARTICLE_URL:
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    (new AdminController())->editArticle($id);
                    break;
                }
                (new AdminController())->showEditArticlePage($id);
                break;
            case AdminController::DELETE_ARTICLE_URL:
                (new AdminController())->deleteArticle($id);
                break;
            case CommentController::ADD_COMMENT_URL:
                if ($_SERVER['REQUEST_METHOD'] === "POST") {
                    (new CommentController())->addComment($id);
                }
                (new ArticleController())->showArticle($id);
                break;
            case AdminController::ADD_ARTICLE_URL:
                if ($_SERVER['REQUEST_METHOD'] === "POST") {
                    (new AdminController())->addArticle();
                    break;
                }
                (new AdminController())->showAddArticlePage();
                break;
            case HomeController::BASE_HOME:
                if ($_SERVER['REQUEST_METHOD'] === "POST" && $_POST["form"] === HomeController::BASE_CONTACT_FORM) {
                    (new HomeController())->sendMail();
                    break;
                    break;
                }
                (new HomeController())->showHome();
                break;
            default:
                (new HomeController())->showHome();
        }
    }
}
