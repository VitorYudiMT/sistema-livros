<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261102035750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Assunto ADD Ativo TINYINT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE Autor ADD Ativo TINYINT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE Livro ADD Ativo TINYINT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Assunto DROP Ativo');
        $this->addSql('ALTER TABLE Autor DROP Ativo');
        $this->addSql('ALTER TABLE Livro DROP Ativo');
    }
}
