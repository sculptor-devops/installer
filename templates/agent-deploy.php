<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'scupltor');

// Project repository
set('repository', 'https://github.com/laravel/laravel');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);
set('http_user', 'www-data');
set('http_group', 'www-data');
set('writable_mode', 'chown');
set('branch', 'master');
set('writable_recursive', true); // Common for all modes
set('writable_chmod_mode', '0755'); // For chmod mode
set('writable_chmod_recursive', true);

// Shared files/dirs between deploys
set('shared_dirs', ['storage']);
set('shared_files', ['.env']);
set('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);
set('log_files', 'storage/logs/*.log');

// Writable dirs by web server
set('allow_anonymous_stats', false);

// Hosts

localhost()
    ->set('deploy_path', '/var/www/html')
    ->set('http_user', 'www-data');

desc('Deploy sculptor agent');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

task('deploy:owner', function () {
    run("chown -R www-data:www-data /var/www/html");
});

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
