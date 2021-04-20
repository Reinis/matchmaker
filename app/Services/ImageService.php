<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use DateTime;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemException;
use Matchmaker\Config;
use Matchmaker\Entities\Collections\Images;
use Matchmaker\Entities\Image;
use Matchmaker\Entities\User;
use Matchmaker\Repositories\FilesystemRepository;
use Matchmaker\Repositories\ImageRepository;
use Matchmaker\Repositories\UserRepository;
use PDOException;


class ImageService
{
    private Config $config;
    private ImageManager $imageManager;
    private UserRepository $userRepository;
    private ImageRepository $imageRepository;
    private FilesystemRepository $filesystemRepository;

    public function __construct(
        Config $config,
        ImageManager $imageManager,
        UserRepository $userRepository,
        ImageRepository $imageRepository,
        FilesystemRepository $filesystemRepository
    )
    {
        $this->config = $config;
        $this->imageManager = $imageManager;
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
        $this->filesystemRepository = $filesystemRepository;
    }

    /**
     * @throws FilesystemException
     */
    public function save(string $username, string $originalFilename, string $sourceFilename): void
    {
        $user = $this->userRepository->getUserByUsername($username);

        $encodedName = $this->encodeFilename($username, $originalFilename);
        $image = $this->imageManager
            ->make($sourceFilename)
            ->encode();
        $this->filesystemRepository->write($encodedName, $image);

        $encodedResizedName = $this->encodeFilename($username, $originalFilename . '_600x600');
        $image = $this->imageManager
            ->make($sourceFilename)
            ->resize(600, 600)
            ->encode();
        $this->filesystemRepository->write($encodedResizedName, $image);

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
            $this->filesystemRepository->deleteAndClean($encodedName);
            $this->filesystemRepository->deleteAndClean($encodedResizedName);
            throw $e;
        }
    }

    private function encodeFilename(string $username, string $originalFilename): string
    {
        return sha1($username . '/' . $originalFilename . '@' . date('Y-m-d H:i:s'));
    }

    public function getById(int $id): Image
    {
        return $this->imageRepository->getById($id);
    }

    /**
     * @throws FilesystemException
     */
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

    /**
     * @throws FilesystemException
     */
    public function delete(int $id): void
    {
        $image = $this->imageRepository->getById($id);

        $this->filesystemRepository->deleteAndClean($image->getResizedFilePath());
        $this->filesystemRepository->deleteAndClean($image->getOriginalFilePath());
        $this->imageRepository->delete($id);
    }
}
