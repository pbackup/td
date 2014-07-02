<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140527172406 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE user_connect (id INT AUTO_INCREMENT NOT NULL, source_user_id INT DEFAULT NULL, target_user_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, status INT NOT NULL, INDEX IDX_2CC2E715EEB16BFD (source_user_id), INDEX IDX_2CC2E7156C066AFE (target_user_id), INDEX IDX_2CC2E715A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, headerLogo VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, header LONGTEXT NOT NULL, footer LONGTEXT NOT NULL, title VARCHAR(255) NOT NULL, updatedAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE user_message (id INT AUTO_INCREMENT NOT NULL, source_user_id INT DEFAULT NULL, target_user_id INT DEFAULT NULL, user_id INT DEFAULT NULL, message_id INT DEFAULT NULL, INDEX IDX_EEB02E75EEB16BFD (source_user_id), INDEX IDX_EEB02E756C066AFE (target_user_id), INDEX IDX_EEB02E75A76ED395 (user_id), INDEX IDX_EEB02E75537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, isRead TINYINT(1) NOT NULL, isDeleted TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, upatedAt DATETIME NOT NULL, INDEX IDX_B6BD307FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE user_connect ADD CONSTRAINT FK_2CC2E715EEB16BFD FOREIGN KEY (source_user_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE user_connect ADD CONSTRAINT FK_2CC2E7156C066AFE FOREIGN KEY (target_user_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE user_connect ADD CONSTRAINT FK_2CC2E715A76ED395 FOREIGN KEY (user_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E75EEB16BFD FOREIGN KEY (source_user_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E756C066AFE FOREIGN KEY (target_user_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E75A76ED395 FOREIGN KEY (user_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E75537A1329 FOREIGN KEY (message_id) REFERENCES message (id)");
        $this->addSql("ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE category ADD icon VARCHAR(255) DEFAULT NULL, ADD color VARCHAR(255) DEFAULT NULL, ADD order_by INT NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE user_message DROP FOREIGN KEY FK_EEB02E75537A1329");
        $this->addSql("DROP TABLE user_connect");
        $this->addSql("DROP TABLE setting");
        $this->addSql("DROP TABLE user_message");
        $this->addSql("DROP TABLE message");
        $this->addSql("ALTER TABLE category DROP icon, DROP color, DROP order_by");
    }
}
