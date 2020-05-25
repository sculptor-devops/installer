<?php namespace Sculptor\Stages;

use Sculptor\Services\Env;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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
