<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221014193003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_85DCB18F19EB6921 ON api_clients_grants');
        $this->addSql('ALTER TABLE api_clients_grants CHANGE client_id client_id VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_clients_grants CHANGE client_id client_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_85DCB18F19EB6921 ON api_clients_grants (client_id)');
    }
}
