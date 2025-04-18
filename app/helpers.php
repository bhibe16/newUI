<?php

use Illuminate\Support\Str;

if (!function_exists('initials')) {
    function initials(?string $name): string
    {
        if (empty($name)) {
            return '';
        }

        return Str::of($name)
            ->explode(' ')
            ->filter()
            ->map(fn ($word) => strtoupper(mb_substr($word, 0, 1)))
            ->implode('');
    }
}