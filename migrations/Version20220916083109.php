<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220916083109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api_clients (id INT AUTO_INCREMENT NOT NULL, client_id VARCHAR(255) NOT NULL, client_secret VARCHAR(255) NOT NULL, client_name VARCHAR(100) NOT NULL, active VARCHAR(1) NOT NULL, short_description VARCHAR(50) NOT NULL, full_description LONGTEXT NOT NULL, logo_url VARCHAR(200) NOT NULL, url VARCHAR(100) NOT NULL, dpo VARCHAR(100) NOT NULL, technical_contact VARCHAR(200) NOT NULL, commercial_contact VARCHAR(200) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_clients_grants (id INT AUTO_INCREMENT NOT NULL, client_id_id INT NOT NULL, install_id VARCHAR(100) NOT NULL, active VARCHAR(1) NOT NULL, perms LONGTEXT NOT NULL, branch_id INT NOT NULL, UNIQUE INDEX UNIQ_85DCB18FDC2902E0 (client_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_install_perm (id INT AUTO_INCREMENT NOT NULL, client_id_id INT NOT NULL, branch_id VARCHAR(100) NOT NULL, install_id INT NOT NULL, members_read INT NOT NULL, members_write INT NOT NULL, members_add INT NOT NULL, members_products_add INT NOT NULL, members_payment_schedules_read INT NOT NULL, members_statistiques_read INT NOT NULL, members_subscription_read INT NOT NULL, payment_schedules_read INT NOT NULL, payment_schedules_write INT NOT NULL, payment_day_read INT NOT NULL, INDEX IDX_E8DCE66EDC2902E0 (client_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_clients_grants ADD CONSTRAINT FK_85DCB18FDC2902E0 FOREIGN KEY (client_id_id) REFERENCES api_clients (id)');
        $this->addSql('ALTER TABLE api_install_perm ADD CONSTRAINT FK_E8DCE66EDC2902E0 FOREIGN KEY (client_id_id) REFERENCES api_clients_grants (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE is_active is_active TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_clients_grants DROP FOREIGN KEY FK_85DCB18FDC2902E0');
        $this->addSql('ALTER TABLE api_install_perm DROP FOREIGN KEY FK_E8DCE66EDC2902E0');
        $this->addSql('DROP TABLE api_clients');
        $this->addSql('DROP TABLE api_clients_grants');
        $this->addSql('DROP TABLE api_install_perm');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE is_active is_active TINYINT(1) DEFAULT NULL');
    }
}
