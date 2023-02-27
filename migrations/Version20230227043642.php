<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230227043642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT fk_906517443301c60');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT fk_9065174419eb6921');
        $this->addSql('DROP INDEX idx_9065174419eb6921');
        $this->addSql('DROP INDEX idx_906517443301c60');
        $this->addSql('ALTER TABLE invoice ADD reference VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD adressed_to VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD adressed_mail VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD adressed_phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD housing_identifier VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD housing_total DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD adults_stay_tax DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD children_stay_tax DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD adults_pool_tax DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD children_pool_tax DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD total_pretax DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD total_aftertax DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE invoice DROP booking_id');
        $this->addSql('ALTER TABLE invoice DROP client_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invoice ADD booking_id INT NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice DROP reference');
        $this->addSql('ALTER TABLE invoice DROP adressed_to');
        $this->addSql('ALTER TABLE invoice DROP adressed_mail');
        $this->addSql('ALTER TABLE invoice DROP adressed_phone');
        $this->addSql('ALTER TABLE invoice DROP housing_identifier');
        $this->addSql('ALTER TABLE invoice DROP housing_total');
        $this->addSql('ALTER TABLE invoice DROP adults_stay_tax');
        $this->addSql('ALTER TABLE invoice DROP children_stay_tax');
        $this->addSql('ALTER TABLE invoice DROP adults_pool_tax');
        $this->addSql('ALTER TABLE invoice DROP children_pool_tax');
        $this->addSql('ALTER TABLE invoice DROP total_pretax');
        $this->addSql('ALTER TABLE invoice DROP total_aftertax');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT fk_906517443301c60 FOREIGN KEY (booking_id) REFERENCES booking (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT fk_9065174419eb6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9065174419eb6921 ON invoice (client_id)');
        $this->addSql('CREATE INDEX idx_906517443301c60 ON invoice (booking_id)');
    }
}
