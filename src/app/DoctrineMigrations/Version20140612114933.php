<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140612114933 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql("DROP TABLE user_connect");
        $this->addSql("CREATE TABLE user_connect (source_user_id INT NOT NULL, target_user_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, status INT NOT NULL, INDEX IDX_2CC2E715EEB16BFD (source_user_id), INDEX IDX_2CC2E7156C066AFE (target_user_id), PRIMARY KEY(source_user_id, target_user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE user_connect ADD CONSTRAINT FK_2CC2E715EEB16BFD FOREIGN KEY (source_user_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE user_connect ADD CONSTRAINT FK_2CC2E7156C066AFE FOREIGN KEY (target_user_id) REFERENCES User (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE user_connect");
    }
}
