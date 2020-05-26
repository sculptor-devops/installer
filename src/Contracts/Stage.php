<?php namespace Sculptor\Contracts;

use Sculptor\Services\Daemons;
use Sculptor\Services\Templates;
use Sculptor\Stages\Environment;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Stage
{
    /**
     * Stage constructor.
     * @param Runner $runner
     * @param Daemons $daemons
     * @param Templates $templates
     * @param DatabaseManager $db
     */
    public function __construct(Runner $runner, Daemons $daemons, Templates $templates, DatabaseManager $db);

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool;

    /**
     * @param Environment $env
     * @return bool
     */
    public function remove(Environment $env = null): bool;

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @param bool $short
     * @return string
     */
    public function className(bool $short = true): string;

    /**
     * @return string|null
     */
    public function error(): ?string;

    /**
     * @return Environment
     */
    public function env(): ?Environment;
}
