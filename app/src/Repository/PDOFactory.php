<?php

declare(strict_types=1);

namespace Auth\Repository;

use PDO;

class PDOFactory
{
    public function build(): PDO
    {
        $db = parse_url(getenv('DATABASE_URL'));

        return new PDO('pgsql:' . sprintf(
            'host=%s;port=%s;user=%s;password=%s;dbname=%s',
            $db['host'],
            $db['port'],
            $db['user'],
            $db['pass'],
            ltrim($db['path'], '/')
        ));
    }
}
