<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Services\LoginService;
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

    public function authenticate(): string
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $message = "Please provide a username an password.";
            return $this->view->render('error', compact('message'));
        }

        if (!$this->loginService->verify($username, $password)) {
            $message = "Login failed.";
            return $this->view->render('error', compact('message'));
        }

        $_SESSION['auth']['user'] = $username;

        header('Location: /');
    }

    public function register(): string
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $message = "Please provide a username an password.";
            return $this->view->render('error', compact('message'));
        }

        if (!$this->loginService->new($username, $password)) {
            $message = "Failed to create a new user";
            return $this->view->render('error', compact('message'));
        }

        header('Location: /');
    }

    public function logout(): void
    {
        if (ini_get("session.use_cookies")) {
            setcookie(session_name(), '', session_get_cookie_params());
        }

        session_destroy();

        header('Location: /');
    }
}
