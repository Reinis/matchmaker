<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use InvalidArgumentException;
use Matchmaker\Services\ProfileService;
use Matchmaker\Views\View;


class HomeController
{
    private View $view;
    private ProfileService $profileService;

    public function __construct(View $view, ProfileService $profileService)
    {
        $this->view = $view;
        $this->profileService = $profileService;
    }

    public function index(): string
    {
        $user = null;

        if (isset($_SESSION['auth']['user'])) {
            try {
                $user = $this->profileService->getUser($_SESSION['auth']['user']);
            } catch (InvalidArgumentException $e) {
                header('Location: /logout');
                die();
            }
        }

        return $this->view->render('home', compact('user'));
    }
}
