<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220141751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE invoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE invoice (id INT NOT NULL, booking_id INT NOT NULL, client_id INT DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_906517443301C60 ON invoice (booking_id)');
        $this->addSql('CREATE INDEX IDX_9065174419EB6921 ON invoice (client_id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517443301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174419EB6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE invoice_id_seq CASCADE');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_906517443301C60');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_9065174419EB6921');
        $this->addSql('DROP TABLE invoice');
    }
}
