<?php

declare(strict_types=1);

namespace Auth\Repository;

use ParseError;
use PDO;

class PDOFactory
{
    public function build(): PDO
    {
        $db = (array) parse_url($_ENV['DATABASE_URL'] ?? '');
        foreach (['host', 'port', 'path', 'user', 'pass'] as $key) {
            if (!array_key_exists($key, $db)) {
                throw new ParseError('Failed parsing DATABASE_URL');
            }
        }

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
