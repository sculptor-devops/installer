<?php namespace Sculptor\Stages;

use Sculptor\Services\Env;

class Version
{
    public static function get()
    {
        $env = new Env('/etc/os-release');

        return $env->get('VERSION_ID');
    }

    public static function compatible(): bool
    {
        return in_array(static::get(), APP_COMPATIBLE);
    }
}
