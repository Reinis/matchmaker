<?php

declare(strict_types=1);


namespace Matchmaker\Entities\Collections;


use ArrayIterator;
use Countable;
use IteratorAggregate;
use Matchmaker\Entities\Favorite;


class Favorites implements IteratorAggregate, Countable
{
    /**
     * @var Favorite[]
     */
    private array $favorites = [];

    public function __construct(Favorite ...$favorites)
    {
        foreach ($favorites as $favorite) {
            $this->add($favorite);
        }
    }

    public function add(Favorite $favorite): void
    {
        $this->favorites[] = $favorite;
    }

    /**
     * @return ArrayIterator|Favorite[]
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->favorites);
    }

    public function count(): int
    {
        return count($this->favorites);
    }
}
