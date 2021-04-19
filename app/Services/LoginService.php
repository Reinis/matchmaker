<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use Matchmaker\Entities\User;
use Matchmaker\Repositories\UserRepository;
use PDOException;


class LoginService
{
    private UserRepository $userRepository;
    private ImageService $imageService;

    public function __construct(UserRepository $userRepository, ImageService $imageService)
    {
        $this->userRepository = $userRepository;
        $this->imageService = $imageService;
    }

    public function verify($username, $password): bool
    {
        $user = $this->userRepository->getUserByUsername($username);

        return password_verify($password, $user->getSecret());
    }

    public function new(User $user): bool
    {
        try {
            $this->userRepository->create($user);
        } catch (PDOException $e) {
            return false;
        }

        return true;
    }

    public function deleteAccount(string $username): void
    {
        $user = $this->userRepository->getUserByUsername($username);

        $this->imageService->deleteAllUserImages($user);
        $this->userRepository->delete($user->getId());
    }
}
