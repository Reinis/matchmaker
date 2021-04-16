<?php

declare(strict_types=1);


namespace Matchmaker\Views;


interface View
{
    public function render(string $name, array $context = []): string;
}
