<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201010233143 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE quote ADD author_id INT DEFAULT NULL, DROP author');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4F675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('CREATE INDEX IDX_6B71CBF4F675F31B ON quote (author_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4F675F31B');
        $this->addSql('DROP INDEX IDX_6B71CBF4F675F31B ON quote');
        $this->addSql('ALTER TABLE quote ADD author VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP author_id');
    }
}
