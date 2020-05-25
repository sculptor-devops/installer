<?php namespace Sculptor\Services;

class Logs
{
    public static function filename()
    {
        $log = app('log');

        return $log->driver()->getHandlers()[0]->getUrl();
    }
}
