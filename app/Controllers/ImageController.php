<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Services\ImageService;
use Matchmaker\Services\ProfileService;
use Matchmaker\Views\Flash;
use Matchmaker\Views\View;


class ImageController
{
    private View $view;
    private ProfileService $profileService;
    private ImageService $imageService;

    public function __construct(View $view, ProfileService $profileService, ImageService $imageService)
    {
        $this->view = $view;
        $this->profileService = $profileService;
        $this->imageService = $imageService;
    }

    public function images(): string
    {
        if (!isset($_SESSION['auth']['user'])) {
            header('Location: /login');
        }

        $username = $_SESSION['auth']['user'];
        $user = $this->profileService->getUser($username);
        $images = $this->imageService->getAllUserImages($username);

        return $this->view->render('images', compact('user', 'images'));
    }

    public function image(array $vars): string
    {
        if (!isset($_SESSION['auth']['user'])) {
            header('Location: /login');
        }

        $id = filter_var($vars['id'], FILTER_VALIDATE_INT);

        if (false === $id) {
            flash("Invalid operation", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /images');
        }

        $username = $_SESSION['auth']['user'];
        $user = $this->profileService->getUser($username);
        $image = $this->imageService->getById($id);

        return $this->view->render('image', compact('user', 'image'));
    }

    public function deleteImage(array $vars): void
    {
        if (!isset($_SESSION['auth']['user'])) {
            header('Location: /login');
        }

        $id = filter_var($vars['id'], FILTER_VALIDATE_INT);

        if (false === $id) {
            flash("Invalid operation", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /images');
        }

        $this->imageService->delete($id);

        flash("Image deleted successfully", Flash::MESSAGE_CLASS_SUCCESS);
        header('Location: /images');
    }
}
