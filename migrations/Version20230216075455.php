<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216075455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE booking_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE booking (id INT NOT NULL, housing_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, nbr_adults INT NOT NULL, nbr_children INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E00CEDDEAD5873E3 ON booking (housing_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649444F97DD ON "user" (phone)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEAD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE housing ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE housing ADD surface INT DEFAULT NULL');
        $this->addSql('ALTER TABLE housing ADD thumbnail VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE housing ADD updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE housing ALTER type TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE housing ALTER size DROP NOT NULL');
        $this->addSql('ALTER TABLE housing ADD CONSTRAINT FK_FB8142C37E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FB8142C37E3C61F9 ON housing (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE housing DROP CONSTRAINT FK_FB8142C37E3C61F9');
        $this->addSql('DROP SEQUENCE booking_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDEAD5873E3');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP INDEX IDX_FB8142C37E3C61F9');
        $this->addSql('ALTER TABLE housing DROP owner_id');
        $this->addSql('ALTER TABLE housing DROP surface');
        $this->addSql('ALTER TABLE housing DROP thumbnail');
        $this->addSql('ALTER TABLE housing DROP updated');
        $this->addSql('ALTER TABLE housing ALTER type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE housing ALTER size SET NOT NULL');
    }
}
