<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507090626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD type_stockage_id INT DEFAULT NULL, DROP type_stockage
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB91B1EAEE FOREIGN KEY (type_stockage_id) REFERENCES type_stockage (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D11814AB91B1EAEE ON intervention (type_stockage_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP type_stockage
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit RENAME INDEX idx_29a5ec27b37ee6bc TO IDX_29A5EC2791B1EAEE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB91B1EAEE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D11814AB91B1EAEE ON intervention
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD type_stockage VARCHAR(255) DEFAULT NULL, DROP type_stockage_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD type_stockage VARCHAR(50) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit RENAME INDEX idx_29a5ec2791b1eaee TO IDX_29A5EC27B37EE6BC
        SQL);
    }
}
