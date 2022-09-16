<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220916084257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_install_perm CHANGE members_read members_read TINYINT(1) NOT NULL, CHANGE members_write members_write TINYINT(1) NOT NULL, CHANGE members_add members_add TINYINT(1) NOT NULL, CHANGE members_products_add members_products_add TINYINT(1) NOT NULL, CHANGE members_payment_schedules_read members_payment_schedules_read TINYINT(1) NOT NULL, CHANGE members_statistiques_read members_statistiques_read TINYINT(1) NOT NULL, CHANGE members_subscription_read members_subscription_read TINYINT(1) NOT NULL, CHANGE payment_schedules_read payment_schedules_read TINYINT(1) NOT NULL, CHANGE payment_schedules_write payment_schedules_write TINYINT(1) NOT NULL, CHANGE payment_day_read payment_day_read TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_install_perm CHANGE members_read members_read INT NOT NULL, CHANGE members_write members_write INT NOT NULL, CHANGE members_add members_add INT NOT NULL, CHANGE members_products_add members_products_add INT NOT NULL, CHANGE members_payment_schedules_read members_payment_schedules_read INT NOT NULL, CHANGE members_statistiques_read members_statistiques_read INT NOT NULL, CHANGE members_subscription_read members_subscription_read INT NOT NULL, CHANGE payment_schedules_read payment_schedules_read INT NOT NULL, CHANGE payment_schedules_write payment_schedules_write INT NOT NULL, CHANGE payment_day_read payment_day_read INT NOT NULL');
    }
}
