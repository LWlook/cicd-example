<?php

namespace Deployer;

// Include the Laravel & rsync recipes
require 'recipe/npm.php';
require 'recipe/rsync.php';

set('application', 'My React App');
set('ssh_multiplexing', true); // Speeds up deployments

set('rsync_src', function () {
    return __DIR__; // If your project isn't in the root, you'll need to change this.
});

set('writable_mode', 'chgrp');
set('http_group', 'WebAndDeploy');
set('writable_chmod_mode', '0775');
set('writable_recursive', false);

// Configuring the rsync exclusions.
// You'll want to exclude anything that you don't want on the production server.
add('rsync', [
    'exclude' => [
        '.git',
        '/.env',
        '/storage/',
        '/vendor/',
        '/node_modules/',
        '.github',
        'deploy.php',
    ],
]);

// Production Server
host('web-server') // Name of the server
->hostname('10.0.0.176') // Hostname or IP address
->stage('production') // Deployment stage (production, staging, etc)
->user('lukas') // SSH user
->set('deploy_path', '/var/www/my-react-app'); // Deploy path

after('deploy:failed', 'deploy:unlock'); // Unlock after failed deploy

desc('Deploy the application');

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync', // Deploy code & built assets
    'deploy:shared',
    'deploy:vendors',
	'npm:install'
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);
