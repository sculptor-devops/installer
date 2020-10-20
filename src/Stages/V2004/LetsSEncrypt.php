<?php

namespace Sculptor\Stages\V2004;

use Exception;
use Illuminate\Support\Facades\Log;
use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class LetsSEncrypt extends StageBase implements Stage
{

    public function run(Environment $env): bool
    {
        try {
	    $this->command(['snap', 'install', '--classic', 'certbot']);

	    $this->command(['ln -s', '/snap/bin/certbot', '/usr/bin/certbot']);

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
}
