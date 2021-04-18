<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use Matchmaker\Entities\Collections\Images;
use Matchmaker\Entities\Image;


interface ImageRepository
{
    public function save(string $originalName, string $originalFile, string $resizedFile, int $userId): void;

    public function getAllUserImages(string $username): Images;

    public function getById(int $id): Image;

    public function delete(int $id): void;
}
