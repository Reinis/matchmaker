<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use Matchmaker\Entities\User;
use Matchmaker\Repositories\UserRepository;
use PDOException;


class LoginService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
}
