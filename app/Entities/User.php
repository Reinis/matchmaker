<?php

declare(strict_types=1);


namespace Matchmaker\Entities;


class User
{
    private ?int $id;
    private string $username;
    private string $secret;
    private string $firstName;
    private string $lastName;
    private string $gender;
    private ?Image $profilePic;

    public function __construct(
        string $username,
        string $secret,
        string $firstName,
        string $lastName,
        string $gender = 'Unknown',
        ?Image $profilePic = null,
        ?int $id = null
    )
    {
        $this->id = $id;
        $this->username = $username;
        $this->secret = $secret;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->gender = $gender;
        $this->profilePic = $profilePic;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getSecret(): string
    {
        return $this->secret;
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

    public function getProfilePic(): ?Image
    {
        return $this->profilePic;
    }

    public function setProfilePic(?Image $profilePic): void
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

    public function getProfilePicId(): ?int
    {
        return $this->profilePic ? $this->profilePic->getId() : null;
    }
}
