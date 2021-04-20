<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Services\ProfileService;
use Matchmaker\Views\Flash;
use Matchmaker\Views\View;


class ProfileController
{
    private View $view;
    private ProfileService $profileService;

    public function __construct(View $view, ProfileService $profileService)
    {
        $this->view = $view;
        $this->profileService = $profileService;
    }

    public function profile(): string
    {
        if (!isset($_SESSION['auth']['user'])) {
            header('Location: /login');
            die();
        }

        $user = $this->profileService->getUser($_SESSION['auth']['user']);

        return $this->view->render('profile', compact('user'));
    }

    public function setPicture(): void
    {
        if (!isset($_SESSION['auth']['user'])) {
            header('Location: /login');
            die();
        }

        $imageId = $_POST['image_id'] ?? 'none';
        $imageId = filter_var($imageId, FILTER_VALIDATE_INT);

        if (false === $imageId) {
            flash("Failed to set profile image", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /images');
            die();
        }

        $this->profileService->setPicture($_SESSION['auth']['user'], $imageId);

        header('Location: /images');
    }
}
