<?php

require __DIR__ . '/bootstrap.php';


$db = parse_url(getenv('DATABASE_URL'));

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
            'adapter' => 'pgsql',
            'host' => $db['host'],
            'name' => ltrim($db['path'], '/'),
            'user' => $db['user'],
            'pass' => $db['pass'],
            'port' => $db['port'],
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
