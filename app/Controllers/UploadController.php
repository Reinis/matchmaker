<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Services\ImageService;
use Matchmaker\Views\View;

class UploadController
{
    private View $view;
    private ImageService $imageService;

    public function __construct(View $view, ImageService $imageService)
    {
        $this->view = $view;
        $this->imageService = $imageService;
    }

    public function upload(): string
    {
        $targetDir = __DIR__ . '/../../storage/uploads/';
        $filename = basename($_FILES['imageFile']['name']);
        $targetFile = $targetDir . $filename;
        $imageType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $ok = true;
        $errors = [];

        if (isset($_POST['submit'])) {
            $size = getimagesize($_FILES['imageFile']['tmp_name']);
            if ($size === false) {
                $ok = false;
                $errors[] = "There is no file!";
            }
        }

        if (file_exists($targetFile)) {
            $ok = false;
            $errors[] = "File already exists!";
        }

        if ($_FILES['imageFile']['size'] > 10_000_000) {
            $ok = false;
            $errors[] = "File too large!";
        }

        if (!in_array($imageType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $ok = false;
            $errors[] = "Image type '$imageType' is not supported!";
        }

        if (!$ok) {
            $message = "Failed to upload the file";
            return $this->view->render('error', compact('message', 'errors'));
        }

        if (!move_uploaded_file($_FILES['imageFile']['tmp_name'], $targetFile)) {
            $message = "Moving file to '$targetFile' failed.";
            return $this->view->render('error', compact('message'));
        }

        $this->imageService->save($_SESSION['auth']['user'], $filename);

        $message = "File '" . htmlspecialchars($filename) . "' uploaded.";
        return $this->view->render('success', compact('message'));
    }
}
