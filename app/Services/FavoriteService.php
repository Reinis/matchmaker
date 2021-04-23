<?php

declare(strict_types=1);


namespace Matchmaker\Services;


use InvalidArgumentException;
use Matchmaker\Entities\Collections\Users;
use Matchmaker\Entities\Favorite;
use Matchmaker\Entities\User;
use Matchmaker\Repositories\FavoriteRepository;
use Matchmaker\Repositories\UserRepository;


class FavoriteService
{
    private UserRepository $userRepository;
    private FavoriteRepository $favoriteRepository;

    /**
     * @var int[]
     */
    private array $ratingValues = [
        'like' => 1,
        'dislike' => -1,
    ];

    public function __construct(
        UserRepository $userRepository,
        FavoriteRepository $favoriteRepository
    )
    {
        $this->favoriteRepository = $favoriteRepository;
        $this->userRepository = $userRepository;
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

    public function get(User $user): Users
    {
        $favorites = $this->favoriteRepository->getMatches($user->getId());

        $users = new Users();

        foreach ($favorites as $favorite) {
            $users->add($this->userRepository->get($favorite->getUserId()));
        }

        return $users;
    }
}
