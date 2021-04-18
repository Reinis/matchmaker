<?php

declare(strict_types=1);


function flash(string $text, string $class)
{
    $_SESSION['_flash'][] = compact('text', 'class');
}
