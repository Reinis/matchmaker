<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use Matchmaker\Entities\Collections\Images;
use Matchmaker\Entities\Image;


interface ImageRepository
{
    public function save(Image $image): void;

    public function getAllUserImages(string $username): Images;

    public function getById(int $id): Image;

    public function delete(int $id): void;
}
