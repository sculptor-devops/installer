<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\RunnerResult;
use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Composer extends StageBase implements Stage
{
    /**
     * @var RunnerResult
     */
    private $result;

    public function run(array $env = null): bool
    {
        $setup = '/tmp/composer-setup.php';

        $copy = copy('https://getcomposer.org/installer', $setup);

        if (!$copy) {
            $this->internal = "Unable to download setup";

            return false;
        }

        $this->result = $this->runner->run(['php', $setup, '--install-dir=/bin', '--filename=composer']);

        $delete = unlink($setup);

        if (!$delete) {
            $this->internal = "Unable to delete setup";

            return false;
        }

        return $this->result->success() && $copy && $delete;
    }

    public function name(): string
    {
        return 'Composer';
    }

    public function env(): ?array
    {
        return null;
    }
}
