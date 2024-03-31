<?php

namespace NormanHuth\Luraa;

function ci(array $command): string
{
    return implode(' ', $command);
}
