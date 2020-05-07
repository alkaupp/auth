<?php

require __DIR__ . '/bootstrap.php';

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
        'bootstrap' => '%%PHINX_CONFIG_DIR%%/bootstrap.php'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_environment' => 'default',
        'default' => [
            'adapter' => $_ENV['AUTH_DB_DRIVER'],
            'host' => $_ENV['AUTH_DB_HOST'],
            'name' => $_ENV['AUTH_DB_NAME'],
            'user' => $_ENV['AUTH_DB_USER'],
            'pass' => $_ENV['AUTH_DB_PASSWORD'],
            'port' => $_ENV['AUTH_DB_PORT'],
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
