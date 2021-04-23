<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use Matchmaker\Entities\Collections\Favorites;
use Matchmaker\Entities\Favorite;


interface FavoriteRepository
{
    public function create(Favorite $favorite): void;

    public function get(int $userId, int $favoriteId): Favorite;

    public function update(Favorite $favorite): void;

    public function getMatches(int $userId): Favorites;
}
