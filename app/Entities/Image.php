<?php

declare(strict_types=1);


namespace Matchmaker\Entities;


class Image
{
    private ?int $id;
    private string $originalName;
    private string $originalFileName;
    private string $resizedFileName;
    private int $userId;

    public function __construct(
        string $originalName,
        string $originalFileName,
        string $resizedFileName,
        int $userId,
        ?int $id = null
    )
    {
        $this->id = $id;
        $this->originalName = $originalName;
        $this->originalFileName = $originalFileName;
        $this->resizedFileName = $resizedFileName;
        $this->userId = $userId;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getResizedFileName(): string
    {
        return $this->resizedFileName;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
