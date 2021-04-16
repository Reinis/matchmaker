<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Matchmaker\Views\View;

class UploadController
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function upload(): string
    {
        $targetDir = __DIR__ . '/../../storage/uploads/';
        $targetFile = $targetDir . basename($_FILES['imageFile']['name']);
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

        $message = "File '" . htmlspecialchars(basename($_FILES['imageFile']['name'])) . "' uploaded.";
        return $this->view->render('success', compact('message'));
    }
}
