<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use Matchmaker\Repositories\UserRepository;


class ImageService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function save(string $username, string $filename): void
    {
        $user = $this->userRepository->getUserByUsername($username);

        $user->setProfilePic($filename);

        $this->userRepository->update($user);
    }

    public function getProfilePic(string $username): string
    {
        $user = $this->userRepository->getUserByUsername($username);

        return $user->getProfilePic();
    }
}
