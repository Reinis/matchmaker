<?php

declare(strict_types=1);


namespace Matchmaker\Entities;


use DateTime;


class Image
{
    private ?int $id;
    private string $originalName;
    private string $originalFileName;
    private string $resizedFileName;
    private DateTime $uploadTime;
    private int $userId;

    public function __construct(
        string $originalName,
        string $originalFileName,
        string $resizedFileName,
        DateTime $uploadTime,
        int $userId,
        ?int $id = null
    )
    {
        $this->id = $id;
        $this->originalName = $originalName;
        $this->originalFileName = $originalFileName;
        $this->resizedFileName = $resizedFileName;
        $this->uploadTime = $uploadTime;
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

    public function getResizedFileLocation(): string
    {
        $name = $this->getResizedFileName();

        return sprintf(
            "%s/%s/%s",
            substr($name, 0, 2),
            substr($name, 2, 2),
            $name,
        );
    }

    public function getResizedFileName(): string
    {
        return $this->resizedFileName;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUploadTime(): DateTime
    {
        return $this->uploadTime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
