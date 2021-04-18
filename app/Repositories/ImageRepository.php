<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


interface ImageRepository
{
    public function save(string $originalName, string $originalFile, string $resizedFile, int $userId): void;
}
