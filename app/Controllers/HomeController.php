<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Services\ImageService;
use Matchmaker\Views\View;


class HomeController
{
    private View $view;
    private ImageService $imageService;

    public function __construct(View $view, ImageService $imageService)
    {
        $this->view = $view;
        $this->imageService = $imageService;
    }

    public function index(): string
    {
        $image = null;

        if (isset($_SESSION['auth']['user'])) {
            $image = htmlspecialchars('/static/images/' . $this->imageService->getProfilePic($_SESSION['auth']['user']));
        }

        return $this->view->render('home', compact('image'));
    }
}
