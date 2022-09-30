<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220929145246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_clients_grants DROP FOREIGN KEY FK_85DCB18FDC2902E0');
        $this->addSql('DROP INDEX UNIQ_85DCB18FDC2902E0 ON api_clients_grants');
        $this->addSql('ALTER TABLE api_clients_grants CHANGE client_id_id client_id INT NOT NULL');
        $this->addSql('ALTER TABLE api_clients_grants ADD CONSTRAINT FK_85DCB18F19EB6921 FOREIGN KEY (client_id) REFERENCES api_clients (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_85DCB18F19EB6921 ON api_clients_grants (client_id)');
        $this->addSql('ALTER TABLE api_install_perm DROP FOREIGN KEY FK_E8DCE66EDC2902E0');
        $this->addSql('DROP INDEX IDX_E8DCE66EDC2902E0 ON api_install_perm');
        $this->addSql('ALTER TABLE api_install_perm CHANGE client_id_id client_id INT NOT NULL');
        $this->addSql('ALTER TABLE api_install_perm ADD CONSTRAINT FK_E8DCE66E19EB6921 FOREIGN KEY (client_id) REFERENCES api_clients_grants (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E8DCE66E19EB6921 ON api_install_perm (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_clients_grants DROP FOREIGN KEY FK_85DCB18F19EB6921');
        $this->addSql('DROP INDEX UNIQ_85DCB18F19EB6921 ON api_clients_grants');
        $this->addSql('ALTER TABLE api_clients_grants CHANGE client_id client_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE api_clients_grants ADD CONSTRAINT FK_85DCB18FDC2902E0 FOREIGN KEY (client_id_id) REFERENCES api_clients (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_85DCB18FDC2902E0 ON api_clients_grants (client_id_id)');
        $this->addSql('ALTER TABLE api_install_perm DROP FOREIGN KEY FK_E8DCE66E19EB6921');
        $this->addSql('DROP INDEX IDX_E8DCE66E19EB6921 ON api_install_perm');
        $this->addSql('ALTER TABLE api_install_perm CHANGE client_id client_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE api_install_perm ADD CONSTRAINT FK_E8DCE66EDC2902E0 FOREIGN KEY (client_id_id) REFERENCES api_clients_grants (id)');
        $this->addSql('CREATE INDEX IDX_E8DCE66EDC2902E0 ON api_install_perm (client_id_id)');
    }
}
