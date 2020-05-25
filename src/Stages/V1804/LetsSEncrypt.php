<?php namespace Sculptor\Stages\V1804;

use Sculptor\Stages\StageBase;
use Sculptor\Contracts\Stage;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class LetsSEncrypt extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {

            $this->command(['add-apt-repository', '-y', 'ppa:certbot/certbot']);

            $this->command(['apt-get', '-y', 'install', 'python-certbot-nginx']);

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    public function name(): string
    {
        return "Let's Encrypt";
    }

    public function env(): ?array
    {
        return null;
    }
}
