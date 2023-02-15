<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215132233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE housing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE housing (id INT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, size INT DEFAULT NULL, surface INT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, thumbnail VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FB8142C37E3C61F9 ON housing (owner_id)');
        $this->addSql('ALTER TABLE housing ADD CONSTRAINT FK_FB8142C37E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE housing_id_seq CASCADE');
        $this->addSql('ALTER TABLE housing DROP CONSTRAINT FK_FB8142C37E3C61F9');
        $this->addSql('DROP TABLE housing');
    }
}
