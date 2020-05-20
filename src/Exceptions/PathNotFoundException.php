<?php namespace Eppak\Exceptions;

use Exception;
use Throwable;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class PathNotFoundException extends Exception
{
    public function __construct($path, $code = 0, Throwable $previous = null)
    {
        parent::__construct("PATH {$path} NOT FOUND", $code, $previous);
    }
}
