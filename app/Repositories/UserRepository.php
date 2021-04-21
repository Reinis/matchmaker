<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use Matchmaker\Entities\Collections\Users;
use Matchmaker\Entities\User;


interface UserRepository
{
    public function create(User $user): void;

    public function getAll(): Users;

    public function update(User $user): void;

    public function delete(int $id): void;

    public function getUserByUsername(string $username): User;

    public function getOtherUsers(User $user): Users;
}
