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
        return $this->view->render('home');
    }

    public function register(): string
    {
        return $this->view->render('home');
    }
}
