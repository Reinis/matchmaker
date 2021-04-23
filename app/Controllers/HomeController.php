<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use InvalidArgumentException;
use Matchmaker\Services\FavoriteService;
use Matchmaker\Services\ProfileService;
use Matchmaker\Services\UserService;
use Matchmaker\Views\View;


class HomeController
{
    private View $view;
    private ProfileService $profileService;
    private UserService $userService;
    private FavoriteService $favoriteService;

    public function __construct(
        View $view,
        ProfileService $profileService,
        UserService $userService,
        FavoriteService $favoriteService
    )
    {
        $this->view = $view;
        $this->profileService = $profileService;
        $this->userService = $userService;
        $this->favoriteService = $favoriteService;
    }

    public function index(): string
    {
        $user = null;
        $others = null;
        $matches = null;

        if (isset($_SESSION['auth']['user'])) {
            try {
                $user = $this->profileService->getUser($_SESSION['auth']['user']);
                $others = $this->userService->getOtherUsers($user);
                $matches = $this->favoriteService->get($user);
            } catch (InvalidArgumentException $e) {
                header('Location: /logout');
                die();
            }
        }

        return $this->view->render('home', compact('user', 'others', 'matches'));
    }
}
