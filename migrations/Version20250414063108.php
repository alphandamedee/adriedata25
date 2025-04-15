<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250414063108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD type_ram_id INT DEFAULT NULL, DROP type_ram
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2720E10B50 FOREIGN KEY (type_ram_id) REFERENCES type_ram (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_29A5EC2720E10B50 ON produit (type_ram_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2720E10B50
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_29A5EC2720E10B50 ON produit
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD type_ram VARCHAR(50) DEFAULT NULL, DROP type_ram_id
        SQL);
    }
}
