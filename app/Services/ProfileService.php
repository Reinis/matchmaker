<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use Matchmaker\Entities\User;
use Matchmaker\Repositories\UserRepository;


class ProfileService
{
    private UserRepository $userRepository;
    private ImageService $imageService;

    public function __construct(UserRepository $userRepository, ImageService $imageService)
    {
        $this->userRepository = $userRepository;
        $this->imageService = $imageService;
    }

    public function getUser(string $username): User
    {
        return $this->userRepository->getUserByUsername($username);
    }

    public function setPicture(string $username, int $imageId): void
    {
        $user = $this->userRepository->getUserByUsername($username);
        $image = $this->imageService->getById($imageId);

        $user->setProfilePic($image);

        $this->userRepository->update($user);
    }
}
