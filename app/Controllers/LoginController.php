<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Entities\User;
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

    public function registration(): string
    {
        return $this->view->render('register');
    }

    public function register(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $gender = $_POST['gender'] ?? 'Unknown';

        if ($username === '' || $password === '') {
            flash("Please provide a username an password", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /login');
        }

        if (!in_array($gender, ['Unknown', 'male', 'female'])) {
            flash("Invalid value for gender", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /register');
        }

        $user = new User(
            $username,
            password_hash($password, PASSWORD_DEFAULT),
            $firstName,
            $lastName,
            $gender
        );

        if (!$this->loginService->new($user)) {
            flash("Failed to create a new user", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /login');
        }

        flash("Registration successful", Flash::MESSAGE_CLASS_SUCCESS);
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
