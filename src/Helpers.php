<?php

function userHome($path = null): string
{
    $user = posix_getpwuid(posix_getuid());

    if ($path == null) {
        return $user['dir'];
    }

    return "{$user['dir']}/{$path}";
}

function sudo(): bool
{
    return (posix_getuid() == 0);
}

function clearNl(string $data): string
{
    return str_replace(array("\r", "\n"), '', $data);
}

function quoted(string $data): string
{
    if (preg_match('/"([^"]+)"/', $data, $m)) {
        return $m[1];
    }

    return $data;
}
