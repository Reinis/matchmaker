<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Views\View;


class LoginController
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function login(): string
    {
        return $this->view->render('login');
    }

    public function authenticate(): string
    {
        $message = "Authenticating...";

        return $this->view->render('home', compact('message'));
    }

    public function register(): string
    {
        $message = "Registering...";

        return $this->view->render('home', compact('message'));
    }
}
