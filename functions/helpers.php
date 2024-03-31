<?php

if (!function_exists('ci')) {
    function ci(array $command): string
    {
        return implode(' ', $command);
    }
}
