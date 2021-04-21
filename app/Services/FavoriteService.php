<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use InvalidArgumentException;
use Matchmaker\Entities\Favorite;
use Matchmaker\Repositories\FavoriteRepository;


class FavoriteService
{
    private FavoriteRepository $favoriteRepository;

    /**
     * @var int[]
     */
    private array $ratingValues = [
        'like' => 1,
        'dislike' => -1,
    ];

    public function __construct(FavoriteRepository $favoriteRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
    }

    public function add(int $userId, int $favoriteId, string $rating): void
    {
        try {
            $favorite = $this->favoriteRepository->get($userId, $favoriteId);
        } catch (InvalidArgumentException $e) {
            $favorite = new Favorite($userId, $favoriteId, $this->ratingValues[$rating]);
            $this->favoriteRepository->create($favorite);
            return;
        }

        $favorite->addRating($this->ratingValues[$rating]);

        $this->favoriteRepository->update($favorite);
    }
}
