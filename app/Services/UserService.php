<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use Matchmaker\Entities\Collections\Users;
use Matchmaker\Entities\User;
use Matchmaker\Repositories\UserRepository;


class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll(): Users
    {
        return $this->userRepository->getAll();
    }

    public function getOtherUsers(User $user): Users
    {
        return $this->userRepository->getOtherUsers($user);
    }
}
