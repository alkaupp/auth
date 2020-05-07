<?php

use Phinx\Migration\AbstractMigration;

class UpdateConstraints extends AbstractMigration
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
ALTER TABLE user_applications
    DROP CONSTRAINT user_applications_user_id,
    ADD CONSTRAINT user_applications_user_id
        FOREIGN KEY (user_id) REFERENCES "user" (id)
            ON DELETE CASCADE;
ALTER TABLE user_applications
    DROP CONSTRAINT user_applications_application_id,
    ADD CONSTRAINT user_applications_application_id
        FOREIGN KEY (application_id) REFERENCES application (id)
            ON DELETE CASCADE;
SQL;
        $this->execute($sql);
    }
}
