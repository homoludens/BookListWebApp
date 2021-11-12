<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112123850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__blog AS SELECT id, title, short_description, body, image, author, published_year, isbn FROM blog');
        $this->addSql('DROP TABLE blog');
        $this->addSql('CREATE TABLE blog (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, image VARCHAR(255) DEFAULT NULL COLLATE BINARY, author VARCHAR(255) DEFAULT NULL COLLATE BINARY, published_year VARCHAR(255) DEFAULT NULL COLLATE BINARY, isbn VARCHAR(255) DEFAULT NULL COLLATE BINARY, short_description VARCHAR(255) DEFAULT NULL, body CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO blog (id, title, short_description, body, image, author, published_year, isbn) SELECT id, title, short_description, body, image, author, published_year, isbn FROM __temp__blog');
        $this->addSql('DROP TABLE __temp__blog');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__blog AS SELECT id, title, author, published_year, isbn, short_description, body, image FROM blog');
        $this->addSql('DROP TABLE blog');
        $this->addSql('CREATE TABLE blog (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) DEFAULT NULL, published_year VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, short_description VARCHAR(255) NOT NULL COLLATE BINARY, body CLOB NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO blog (id, title, author, published_year, isbn, short_description, body, image) SELECT id, title, author, published_year, isbn, short_description, body, image FROM __temp__blog');
        $this->addSql('DROP TABLE __temp__blog');
    }
}
