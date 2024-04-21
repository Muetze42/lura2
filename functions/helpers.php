<?php

// Todo: Remove

if (! function_exists('ci')) {
    function ci(array $command): string
    {
        return implode(' ', $command);
    }
}

if (! function_exists('replace_nth')) {
    function replace_nth(string $pattern, string $replace, string $subject, int $occurrence = 1): string
    {
        return preg_replace_callback($pattern, function ($m) use (&$counter, $replace, $occurrence) {
            if ($counter++ == $occurrence) {
                return $replace;
            }

            return $m[0];
        }, $subject);
    }
}
