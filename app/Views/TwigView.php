<?php

declare(strict_types=1);


namespace Matchmaker\Views;


use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class TwigView implements View
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(string $name, array $context = []): string
    {
        if (!str_ends_with($name, '.twig')) {
            $name .= '.twig';
        }

        return $this->twig->render($name, $context);
    }
}
