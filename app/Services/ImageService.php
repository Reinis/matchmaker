<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use Intervention\Image\ImageManager;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Matchmaker\Repositories\ImageRepository;
use Matchmaker\Repositories\UserRepository;


class ImageService
{
    private Filesystem $filesystem;
    private ImageManager $imageManager;
    private UserRepository $userRepository;
    private ImageRepository $imageRepository;

    public function __construct(
        Filesystem $filesystem,
        ImageManager $imageManager,
        UserRepository $userRepository,
        ImageRepository $imageRepository
    )
    {
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

        $this->imageRepository->save($originalFilename, $encodedName, $encodedResizedName, $user->getId());
        $user->setProfilePic($encodedResizedName);

        $this->userRepository->update($user);
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

    public function getProfilePic(string $username): string
    {
        $user = $this->userRepository->getUserByUsername($username);

        return $this->encodePath($user->getProfilePic());
    }
}
