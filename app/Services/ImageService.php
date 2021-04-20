<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use DateTime;
use Intervention\Image\ImageManager;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Matchmaker\Config;
use Matchmaker\Entities\Collections\Images;
use Matchmaker\Entities\Image;
use Matchmaker\Entities\User;
use Matchmaker\Repositories\ImageRepository;
use Matchmaker\Repositories\UserRepository;
use PDOException;


class ImageService
{
    private Config $config;
    private Filesystem $filesystem;
    private ImageManager $imageManager;
    private UserRepository $userRepository;
    private ImageRepository $imageRepository;

    public function __construct(
        Config $config,
        Filesystem $filesystem,
        ImageManager $imageManager,
        UserRepository $userRepository,
        ImageRepository $imageRepository
    )
    {
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->imageManager = $imageManager;
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @throws FilesystemException
     */
    public function save(string $username, string $originalFilename, string $sourceFilename): void
    {
        $user = $this->userRepository->getUserByUsername($username);

        $encodedName = $this->encodeFilename($username, $originalFilename);
        $this->filesystem->writeStream($this->encodePath($encodedName), fopen($sourceFilename, 'rb'));

        $encodedResizedName = $this->encodeFilename($username, $originalFilename . '_600x600');
        $image = $this->imageManager
            ->make($sourceFilename)
            ->resize(600, 600)
            ->encode();
        $this->filesystem->writeStream($this->encodePath($encodedResizedName), $image->stream()->detach());

        $imagePDO = new Image(
            $originalFilename,
            $this->config->getStorageLocation(),
            $encodedName,
            $encodedResizedName,
            new DateTime('now'),
            $user->getId(),
        );

        try {
            $this->imageRepository->create($imagePDO);
        } catch (PDOException $e) {
            $this->deleteAndClean($this->encodePath($encodedName));
            $this->deleteAndClean($this->encodePath($encodedResizedName));
            throw $e;
        }
    }

    private function encodeFilename(string $username, string $originalFilename): string
    {
        return sha1($username . '/' . $originalFilename . '@' . date('Y-m-d H:i:s'));
    }

    private function encodePath(string $encodedName): string
    {
        return sprintf(
            "%s/%s/%s",
            substr($encodedName, 0, 2),
            substr($encodedName, 2, 2),
            $encodedName,
        );
    }

    private function deleteAndClean(string $location): void
    {
        $this->filesystem->delete($location);

        $location = substr($location, 0, 5);

        if (0 === count($this->filesystem->listContents($location)->toArray())) {
            $this->filesystem->deleteDirectory($location);
        }

        $location = substr($location, 0, 2);

        if (0 === count($this->filesystem->listContents($location)->toArray())) {
            $this->filesystem->deleteDirectory($location);
        }
    }

    public function getById(int $id): Image
    {
        return $this->imageRepository->getById($id);
    }

    public function deleteAllUserImages(User $user): void
    {
        $images = $this->getAllUserImages($user->getUsername());

        foreach ($images as $image) {
            $this->delete($image->getId());
        }
    }

    public function getAllUserImages(string $username): Images
    {
        return $this->imageRepository->getAllUserImages($username);
    }

    public function delete(int $id): void
    {
        $image = $this->imageRepository->getById($id);

        $this->deleteAndClean($image->getResizedFilePath());
        $this->deleteAndClean($image->getOriginalFilePath());
        $this->imageRepository->delete($id);
    }
}
