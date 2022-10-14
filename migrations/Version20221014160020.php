<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221014160020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD client_id INT DEFAULT NULL, CHANGE address address VARCHAR(255) NOT NULL, CHANGE zipcode zipcode VARCHAR(5) NOT NULL, CHANGE city city VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES api_clients (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64919EB6921 ON user (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64919EB6921');
        $this->addSql('DROP INDEX IDX_8D93D64919EB6921 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP client_id, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE zipcode zipcode VARCHAR(5) DEFAULT NULL, CHANGE city city VARCHAR(100) DEFAULT NULL');
    }
}
