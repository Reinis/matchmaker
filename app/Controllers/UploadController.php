<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use League\Flysystem\FilesystemException;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Matchmaker\Services\ImageService;
use Matchmaker\Views\View;

class UploadController
{
    private View $view;
    private ImageService $imageService;
    private FinfoMimeTypeDetector $mimeTypeDetector;
    /**
     * @var string[]
     */
    private array $allowedTypes = [
        'image/png',
        'image/jpeg',
        'image/gif',
    ];

    public function __construct(
        View $view,
        ImageService $imageService,
        FinfoMimeTypeDetector $mimeTypeDetector
    )
    {
        $this->view = $view;
        $this->imageService = $imageService;
        $this->mimeTypeDetector = $mimeTypeDetector;
    }

    public function upload(): string
    {
        $targetDir = __DIR__ . '/../../storage/uploads/';
        $filename = basename($_FILES['imageFile']['name']);
        $mimeType = $this->mimeTypeDetector->detectMimeTypeFromFile($_FILES['imageFile']['tmp_name']);
        $targetFile = $targetDir . $filename;
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

        if (!in_array($mimeType, $this->allowedTypes, true)) {
            $ok = false;
            $errors[] = "Image type '$mimeType' is not supported!";
        }

        if (!$ok) {
            $message = "Failed to upload the file";
            return $this->view->render('error', compact('message', 'errors'));
        }

        try {
            $this->imageService->save(
                $_SESSION['auth']['user'],
                $filename,
                $_FILES['imageFile']['tmp_name'],
            );
        } catch (FilesystemException $e) {
            $message = "Saving '$filename' failed";
            return $this->view->render('error', compact('message'));
        }

        $message = "File '" . htmlspecialchars($filename) . "' uploaded.";
        return $this->view->render('success', compact('message'));
    }
}
