<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220916094143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE structure ADD branch_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA11F59C00 FOREIGN KEY (branch_id_id) REFERENCES api_clients_grants (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6F0137EA11F59C00 ON structure (branch_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA11F59C00');
        $this->addSql('DROP INDEX UNIQ_6F0137EA11F59C00 ON structure');
        $this->addSql('ALTER TABLE structure DROP branch_id_id');
    }
}
