<?php

declare(strict_types=1);


namespace Matchmaker\Entities;


class User
{
    private ?int $id;
    private string $firstName;
    private string $lastName;
    private string $gender;
    private string $profilePic;

    public function __construct(
        string $firstName,
        string $lastName,
        string $gender = 'Unknown',
        string $profilePic = 'Default',
        ?int $id = null
    )
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->gender = $gender;
        $this->profilePic = $profilePic;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function getProfilePic(): string
    {
        return $this->profilePic;
    }

    public function setProfilePic(string $profilePic): void
    {
        $this->profilePic = $profilePic;
    }

    public function getName(): string
    {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
}
