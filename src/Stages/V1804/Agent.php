<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Agent extends StageBase implements Stage
{
    private $path = '/var/www/html';

    public function run(array $env = null): bool
    {
        try {
            $deploy = $this->template('agent-deploy.php');

            $written = File::put("{$this->path}/deploy.php", $deploy);

            if (!$written) {
                $this->internal = 'Cannot write deploy script';

                return false;
            }

            File::deleteDirectory("{$this->path}/current");

            $this->command([ 'dep', 'deploy'], false, $this->path);



            // ENV
            // /var/www/html/shared/.env
            // {PASSWORD}
            // {DB_PASSWORD}

            $this->command([ 'php', "{$this->path}/current/artisan", 'key:generate'], false, $this->path);

            // $this->command([ 'dep', 'deploy:migrate'], false, $this->path);

            $this->command([ 'dep', 'deploy:owner'], false, $this->path);

            File::put('/bin/sculptor', "php {$this->path}/current/artisan");

            File::chmod('/bin/sculptor', 755);





            // /etc/supervisor/conf.d/system.sculptor.conf
            // $replaced = new Replacer();

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    public function name(): string
    {
        return 'Agent';
    }

    public function env(): ?array
    {
        return null;
    }
}
