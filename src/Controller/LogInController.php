<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\TwigService;
use App\Services\UserService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LogInController
{

    private UserService $userService;
    private TwigService $twig;
    const LOGIN_URL = "/login";
    const REGISTER_URL = "/register";
    const SIGNOUT_URL = "/signout";

    public function __construct()
    {
        $this->userService = new UserService();
        $this->twig = TwigService::getInstance();
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showLoginPage(): void
    {
        echo $this->twig->getTwig()->render("login.html.twig", [
            "user" =>  $this->userService,
            "session" => $_SESSION
        ]);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showRegisterPage(): void
    {
        echo $this->twig->getTwig()->render("register.html.twig", [
            "user" =>  $this->userService,
            "session" => $_SESSION
        ]);
    }

    /**
     * @return void
     */
    public function signOut(): void
    {
        //on vide nos variables de session liées a la connexion
        $this->userService->signOut();
        //on renvoie a la page d'accueil
        header("Location: ./home");
    }

}
