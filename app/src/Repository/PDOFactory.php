<?php
declare(strict_types=1);

namespace Auth\Repository;

use PDO;

class PDOFactory
{
    public function build(): PDO
    {
        return new PDO(
            sprintf(
                "%s:host=%s;dbname=%s",
                getenv("AUTH_DB_DRIVER"),
                getenv("AUTH_DB_HOST"),
                getenv("AUTH_DB_NAME")
            ),
            getenv("AUTH_DB_USER"),
            getenv("AUTH_DB_PASSWORD")
        );
    }
}
