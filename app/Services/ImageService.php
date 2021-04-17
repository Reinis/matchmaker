<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Matchmaker\Repositories\UserRepository;


class ImageService
{
    private Filesystem $filesystem;
    private UserRepository $userRepository;

    public function __construct(Filesystem $filesystem, UserRepository $userRepository)
    {
        $this->filesystem = $filesystem;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws FilesystemException
     */
    public function save(string $username, string $originalFilename, string $sourceFilename): void
    {
        $user = $this->userRepository->getUserByUsername($username);

        $encodedName = $this->encodeFilename($username, $originalFilename);

        $this->filesystem->writeStream($this->encodePath($encodedName), fopen($sourceFilename, 'rb'));

        $user->setProfilePic($encodedName);

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
