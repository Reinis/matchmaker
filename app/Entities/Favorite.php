<?php

declare(strict_types=1);


namespace Matchmaker\Entities;


class Favorite
{
    private ?int $id;
    private int $userId;
    private int $favoriteId;
    private int $rating;

    public function __construct(int $userId, int $favoriteId, int $rating, ?int $id = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->favoriteId = $favoriteId;
        $this->rating = $rating;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getFavoriteId(): int
    {
        return $this->favoriteId;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function addRating(int $value): void
    {
        $this->rating += $value;
    }
}
