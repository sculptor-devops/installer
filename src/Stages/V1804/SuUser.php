<?php

namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
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
    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            $password = $env->get('password');

            $dbPassword = $env->get('db_password');

            if (!sudo()) {
                $this->internal = 'This user is not an superuser';

                return false;
            }

            if (!$this->create(APP_PANEL_USER, $password, $dbPassword, false)) {
                $this->internal = "Cannot create user " . APP_PANEL_USER;

                return false;
            }

            if (!$this->create(APP_PANEL_HTTP_USER, $password, '', false)) {
                $this->internal = "Cannot create user " . APP_PANEL_HTTP_USER;

                return false;
            }

            $this->command(['usermod', '-G', 'adm', APP_PANEL_HTTP_USER]);

            $conf = $this->replaceTemplate('sudoer.conf')
                ->replace('{USERNAME}', APP_PANEL_USER)
                ->value();

            if (!$this->write('/etc/sudoers.d/' . APP_PANEL_USER, $conf, 'Unable to write sudoer configuration')) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @param string $user
     * @param string $password
     * @param string $dbPassword
     * @param bool $shell
     * @return bool
     * @throws Exception
     */
    private function create(string $user, string $password, string $dbPassword, bool $shell): bool
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

        if ($dbPassword == '') {
            $this->command(['chown', "-R", "{$user}:{$user}", $home]);

            return true;
        }

        if (!$this->dbPassword($home, $dbPassword)) {
            $this->internal = "Unable to write db password in {$home}";

            return false;
        }

        $this->command(['chown', "-R", "{$user}:{$user}", $home]);

        return true;
    }

    /**
     * @param string $home
     * @param string $dbPassword
     * @return bool
     * @throws Exception
     */
    private function dbPassword(string $home, string $dbPassword): bool
    {
        $passwordFile = "{$home}/.db_password";

        if ($dbPassword == '') {
            return true;
        }

        if (!File::put($passwordFile, $dbPassword)) {
            $this->internal = "Unable to write {$passwordFile}";

            return false;
        }

        $this->command(['chmod', "600", $passwordFile]);

        return  true;
    }

    /**
     * @param string $password
     * @return string
     */
    private function encodePassword(string $password): string
    {
        return clearNewLine($this->runner->run([
                'openssl',
                'passwd',
                '-1',
                "{$password}"
            ])->output());
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Superuser';
    }
}
