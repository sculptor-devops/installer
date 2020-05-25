<?php namespace Sculptor\Services;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Logs
{
    public static function filename()
    {
        $log = app('log');

        return $log->driver()->getHandlers()[0]->getUrl();
    }
}
