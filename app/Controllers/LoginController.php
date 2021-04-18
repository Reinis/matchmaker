<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Services\LoginService;
use Matchmaker\Views\Flash;
use Matchmaker\Views\View;


class LoginController
{
    private View $view;
    private LoginService $loginService;

    public function __construct(View $view, LoginService $loginService)
    {
        $this->view = $view;
        $this->loginService = $loginService;
    }

    public function login(): string
    {
        return $this->view->render('login');
    }

    public function authenticate(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            flash("Please provide a username an password", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /login');
        }

        if (!$this->loginService->verify($username, $password)) {
            flash("Login failed", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /');
        }

        $_SESSION['auth']['user'] = $username;

        header('Location: /');
    }

    public function register(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            flash("Please provide a username an password", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /login');
        }

        if (!$this->loginService->new($username, $password)) {
            flash("Failed to create a new user", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /login');
        }

        header('Location: /');
    }

    public function logout(): void
    {
        if (ini_get("session.use_cookies")) {
            setcookie(session_name(), '', 1);
        }

        session_destroy();

        header('Location: /');
    }
}
