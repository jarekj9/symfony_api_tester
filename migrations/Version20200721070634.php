<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200721070634 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE api_urls_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE api_vars_values_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE api_urls (id INT NOT NULL, userkey_id INT NOT NULL, url VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B63415265EE42F7 ON api_urls (userkey_id)');
        $this->addSql('CREATE TABLE api_vars_values (id INT NOT NULL, urlkey_id INT NOT NULL, var VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8B30C5A74288D6D8 ON api_vars_values (urlkey_id)');
        $this->addSql('ALTER TABLE api_urls ADD CONSTRAINT FK_B63415265EE42F7 FOREIGN KEY (userkey_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE api_vars_values ADD CONSTRAINT FK_8B30C5A74288D6D8 FOREIGN KEY (urlkey_id) REFERENCES api_urls (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE api_vars_values DROP CONSTRAINT FK_8B30C5A74288D6D8');
        $this->addSql('DROP SEQUENCE api_urls_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE api_vars_values_id_seq CASCADE');
        $this->addSql('DROP TABLE api_urls');
        $this->addSql('DROP TABLE api_vars_values');
    }
}
