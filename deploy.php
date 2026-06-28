<?php
namespace Deployer;

// Custom Deployer script for Ekalexandria Theme-Only Deployment
// This script runs Git pulls directly in the theme directory rather than
// symlinking the entire WordPress installation.

set('application', 'ekalexandria-flagship');

// Hosts
host('production')
    ->setHostname('ekalexandria.org')
    ->set('remote_user', 'deploy') // Update with actual deployment user
    ->set('theme_path', '/var/www/ekalexandria.org/public/wp-content/themes/ekalexandria-flagship')
    ->set('wp_path', '/var/www/ekalexandria.org/public');

host('staging')
    ->setHostname('backstage.ekalexandria.org')
    ->set('remote_user', 'deploy') // Update with actual deployment user
    ->set('theme_path', '/var/www/backstage.ekalexandria.org/public/wp-content/themes/ekalexandria-flagship')
    ->set('wp_path', '/var/www/backstage.ekalexandria.org/public');

// Tasks
desc('Deploy the theme via Git Pull');
task('deploy:theme', function () {
    cd('{{theme_path}}');
    run('git pull origin master');
});

desc('Run Ekalexandria Tachydromos DB Migration');
task('eka:migrate_tachydromos', function () {
    cd('{{wp_path}}');
    run('wp eka migrate-tachydromos');
});

desc('Run Ekalexandria Board Member DB Migration');
task('eka:migrate_board', function () {
    cd('{{wp_path}}');
    run('wp eka migrate-board');
});

desc('Run all Ekalexandria custom CPT migrations');
task('eka:run_migrations', [
    'eka:migrate_tachydromos',
    'eka:migrate_board'
]);

desc('Deactivate legacy page builder plugins');
task('eka:clean_legacy_plugins', function () {
    cd('{{wp_path}}');
    run('wp plugin deactivate js_composer LayerSlider');
});

desc('Full Deployment and Migration run');
task('deploy', [
    'deploy:theme',
    'eka:run_migrations'
]);
