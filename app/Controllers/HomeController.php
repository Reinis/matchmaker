<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Views\View;


class HomeController
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function index(): string
    {
        return $this->view->render('home');
    }
}
