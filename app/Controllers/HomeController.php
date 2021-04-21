<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use InvalidArgumentException;
use Matchmaker\Services\ProfileService;
use Matchmaker\Services\UserService;
use Matchmaker\Views\View;


class HomeController
{
    private View $view;
    private ProfileService $profileService;
    private UserService $userService;

    public function __construct(View $view, ProfileService $profileService, UserService $userService)
    {
        $this->view = $view;
        $this->profileService = $profileService;
        $this->userService = $userService;
    }

    public function index(): string
    {
        $user = null;
        $others = null;

        if (isset($_SESSION['auth']['user'])) {
            try {
                $user = $this->profileService->getUser($_SESSION['auth']['user']);
                $others = $this->userService->getOtherUsers($user);
            } catch (InvalidArgumentException $e) {
                header('Location: /logout');
                die();
            }
        }

        return $this->view->render('home', compact('user', 'others'));
    }
}
