<?php

declare(strict_types=1);

namespace Auth\Repository;

use PDO;

class PDOFactory
{
    public function build(): PDO
    {
        $db = parse_url(getenv('DATABASE_URL'));

        return new PDO(
            sprintf(
                '%s:host=%s;port=%s;dbname=%s',
                'pgsql',
                $db['host'],
                $db['port'],
                ltrim($db['path'], '/')
            ),
            $db['user'],
            $db['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}
