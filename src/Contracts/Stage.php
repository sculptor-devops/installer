<?php

namespace Sculptor\Contracts;

use Sculptor\Foundation\Contracts\Database;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Services\Daemons;
use Sculptor\Foundation\Services\Firewall;
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
     * @param Database $dbm
     * @param Firewall $firewall
     */
    public function __construct(Runner $runner, Daemons $daemons, Templates $templates, Database $dbm, Firewall $firewall);

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool;

    /**
     * @param Environment $env
     * @return Stage
     */
    public function env(Environment $env): Stage;

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function className(): string;

    /**
     * @return string|null
     */
    public function error(): ?string;
}
