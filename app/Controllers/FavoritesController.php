<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Services\FavoriteService;
use Matchmaker\Services\ImageService;
use Matchmaker\Services\ProfileService;
use Matchmaker\Views\Flash;
use Matchmaker\Views\View;


class FavoritesController
{
    private View $view;
    private ProfileService $profileService;
    private ImageService $imageService;
    private FavoriteService $favoriteService;

    public function __construct(
        View $view,
        ProfileService $profileService,
        ImageService $imageService,
        FavoriteService $favoriteService
    )
    {
        $this->view = $view;
        $this->profileService = $profileService;
        $this->imageService = $imageService;
        $this->favoriteService = $favoriteService;
    }

    public function rating(array $vars): string
    {
        if (!isset($_SESSION['auth']['user'])) {
            header('Location: /login');
            die();
        }

        $id = filter_var($vars['id'], FILTER_VALIDATE_INT);

        if (false === $id) {
            flash("Invalid operation", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /');
            die();
        }

        $username = $_SESSION['auth']['user'];
        $user = $this->profileService->getUser($username);
        $image = $this->imageService->getById($id);

        return $this->view->render('rate', compact('user', 'image'));
    }

    public function rate(array $vars): void
    {
        if (!isset($_SESSION['auth']['user'])) {
            header('Location: /login');
            die();
        }

        $username = $_SESSION['auth']['user'];
        $user = $this->profileService->getUser($username);
        $userId = $user->getId();

        $id = filter_var($vars['id'], FILTER_VALIDATE_INT);
        $rating = $vars['rating'];

        if (null === $userId || false === $id || ('like' !== $rating && 'dislike' !== $rating)) {
            flash("Invalid operation", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /');
            die();
        }

        $this->favoriteService->add($userId, $id, $rating);

        header('Location: /');
    }
}
