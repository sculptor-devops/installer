<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\StageBase;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class SuUser extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {
            $password = $env['password'];

            if (!sudo()) {
                $this->internal = 'This user is not an superuser';

                return false;
            }

            if (!$this->create(APP_PANEL_USER, $password, true)) {
                $this->internal = "Cannot create user " . APP_PANEL_USER;

                return false;
            }

            if (!$this->create(APP_PANEL_HTTP_USER, $password, false)) {
                $this->internal = "Cannot create user " . APP_PANEL_HTTP_USER;

                return false;
            }

            $conf = $this->template('sudoer.conf');

            $written = File::put('/etc/sudoers.d/' . APP_PANEL_USER, str_replace('{USERNAME}', APP_PANEL_USER, $conf));

            if (!$written) {
                $this->internal = 'Unable to write sudoer configuration';

                return false;
            }

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    private function create(string $user, string $password, bool $shell): bool
    {
        $home = "/home/{$user}";
        $bash = $shell ? '/bin/bash' : '/bin/false';
        $exists = $this->runner->run(['id', '-u', $user])->success();

        if ($exists) {
            $this->command(['userdel', $user]);
        }

        $this->command(['useradd', '-d', $home, $user, '-s', $bash, '-p', $this->encodePassword($password)]);

        if (!File::exists($home)) {
            File::makeDirectory($home);
        }

        $this->command(['chown', "{$user}:{$user}", $home]);

        return true;
    }

    private function encodePassword($password): string
    {
        return clearNl($this->runner->run([
                'openssl',
                'passwd',
                '-1',
                "{$password}"
            ]
        )->output());
    }

    public function name(): string
    {
        return 'Superuser';
    }

    public function env(): ?array
    {
        return null;
    }
}
