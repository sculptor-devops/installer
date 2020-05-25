<?php namespace Sculptor\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
interface DatabaseManager
{
    public function set(string $password): self;
    public function db(string $name): bool;
    public function user(string $user, string $password, string $db, string $host = 'localhost'): bool;
}
