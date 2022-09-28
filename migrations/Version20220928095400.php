<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220928095400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649523CAB89');
        $this->addSql('ALTER TABLE user CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649523CAB89 FOREIGN KEY (franchise_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649523CAB89');
        $this->addSql('ALTER TABLE `user` CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649523CAB89 FOREIGN KEY (franchise_id) REFERENCES user (id) ON DELETE CASCADE');
    }
}
