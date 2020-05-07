<?php

use Phinx\Migration\AbstractMigration;

class BaselineSchema extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $sql = <<<SQL
CREATE TABLE application (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    site VARCHAR(255) NOT NULL,
    secretKey VARCHAR(255) NOT NULL
);
CREATE TABLE "user" (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    UNIQUE (email)
);
CREATE TABLE user_applications (
    user_id CHAR(36) NOT NULL,
    application_id CHAR(36) NOT NULL,
    CONSTRAINT user_applications_user_id FOREIGN KEY (user_id) REFERENCES "user" (id),
    CONSTRAINT user_applications_application_id FOREIGN KEY (application_id) REFERENCES application (id)
);
SQL;
        $this->execute($sql);
    }
}
