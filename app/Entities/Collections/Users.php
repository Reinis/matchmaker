<?php

declare(strict_types=1);


namespace Matchmaker\Entities\Collections;


use ArrayIterator;
use Countable;
use IteratorAggregate;
use Matchmaker\Entities\User;


class Users implements IteratorAggregate, Countable
{
    /**
     * @var User[]
     */
    private array $users;

    public function __construct(User ...$users)
    {
        foreach ($users as $user) {
            $this->add($user);
        }
    }

    public function add(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @return ArrayIterator|User[]
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->users);
    }

    public function count(): int
    {
        return count($this->users);
    }
}
