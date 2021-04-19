<?php

declare(strict_types=1);


namespace Matchmaker\Controllers;


use Intervention\Image\Exception\NotSupportedException;
use League\Flysystem\FilesystemException;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Matchmaker\Services\ImageService;
use Matchmaker\Views\Flash;


class UploadController
{
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
    /**
     * @var string[]
     */
    private array $uploadErrors = [
        0 => 'Upload successful',
        1 => 'The file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The file exceeds the MAX_FILE_SIZE directive in the HTML form',
        3 => 'The file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
    ];

    public function __construct(
        ImageService $imageService,
        FinfoMimeTypeDetector $mimeTypeDetector
    )
    {
        $this->imageService = $imageService;
        $this->mimeTypeDetector = $mimeTypeDetector;
    }

    public function upload(): void
    {
        $targetDir = __DIR__ . '/../../storage/uploads/';
        $filename = basename($_FILES['imageFile']['name']);
        $mimeType = $this->mimeTypeDetector->detectMimeTypeFromFile($_FILES['imageFile']['tmp_name']);
        $targetFile = $targetDir . $filename;

        if ($_FILES['imageFile']['error'] !== 0) {
            flash($this->uploadErrors[$_FILES['imageFile']['error']], Flash::MESSAGE_CLASS_ERROR);
            header('Location: /');
        }

        if (isset($_POST['submit'])) {
            $size = getimagesize($_FILES['imageFile']['tmp_name']);
            if ($size === false) {
                flash("There is no file!", Flash::MESSAGE_CLASS_ERROR);
                header('Location: /');
            }
        }

        if (file_exists($targetFile)) {
            flash("File already exists!", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /');
        }

        if (!in_array($mimeType, $this->allowedTypes, true)) {
            flash("Image type '$mimeType' is not supported!", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /');
        }

        try {
            $this->imageService->save(
                $_SESSION['auth']['user'],
                $filename,
                $_FILES['imageFile']['tmp_name'],
            );
        } catch (FilesystemException $e) {
            flash("Saving '$filename' failed", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /');
        } catch (NotSupportedException $e) {
            flash("Internal server error: Saving file failed", Flash::MESSAGE_CLASS_ERROR);
            header('Location: /');
            die();
        }

        flash("File '" . htmlspecialchars($filename) . "' uploaded successfully", Flash::MESSAGE_CLASS_SUCCESS);
        header('Location: /');
    }
}
