<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516145417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE type_stockage
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD statut VARCHAR(255) DEFAULT NULL, CHANGE type_stockage_id type_ram_relation_id INT DEFAULT NULL, CHANGE status type_stockage VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABC10ECD1B FOREIGN KEY (type_ram_relation_id) REFERENCES type_ram (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D11814ABC10ECD1B ON intervention (type_ram_relation_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE type_stockage (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABC10ECD1B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D11814ABC10ECD1B ON intervention
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD status VARCHAR(255) DEFAULT NULL, DROP type_stockage, DROP statut, CHANGE type_ram_relation_id type_stockage_id INT DEFAULT NULL
        SQL);
    }
}
