<?php

require __DIR__ . '/codeceptionBootstrap.php';

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
        'bootstrap' => '%%PHINX_CONFIG_DIR%%/codeceptionBootstrap.php'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_environment' => 'default',
        'default' => [
            'adapter' => '%%PHINX_AUTH_DB_DRIVER%%',
            'host' => '%%PHINX_AUTH_DB_HOST%%',
            'name' => '%%PHINX_AUTH_DB_NAME%%',
            'user' => '%%PHINX_AUTH_DB_USER%%',
            'pass' => '%%PHINX_AUTH_DB_PASSWORD%%',
            'port' => '%%PHINX_AUTH_DB_PORT%%',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
