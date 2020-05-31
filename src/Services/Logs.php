<?php

namespace Sculptor\Services;

use Exception;
use Illuminate\Container\Container;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Logs
{
    /**
     * @return mixed
     */
    public static function filename()
    {
        try {
            $log = Container::getInstance()->make('log');

            return $log->driver()->getHandlers()[0]->getUrl();
        } catch (Exception $e) {
            return "Unable to get log file : {$e->getMessage()}";
        }
    }
}
