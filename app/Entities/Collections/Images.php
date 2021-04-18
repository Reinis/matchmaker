<?php

declare(strict_types=1);


namespace Matchmaker\Entities\Collections;


use ArrayIterator;
use Countable;
use IteratorAggregate;
use Matchmaker\Entities\Image;


class Images implements IteratorAggregate, Countable
{
    /**
     * @var Image[]
     */
    private array $images = [];

    public function __construct(Image ...$images)
    {
        foreach ($images as $image) {
            $this->add($image);
        }
    }

    public function add(Image $image): void
    {
        $this->images[] = $image;
    }

    /**
     * @return ArrayIterator|Image[]
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->images);
    }

    public function count(): int
    {
        return count($this->images);
    }
}
