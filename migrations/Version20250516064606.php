<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516064606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB91B1EAEE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27B37EE6BC
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE type_stockage
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D11814AB91B1EAEE ON intervention
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD status VARCHAR(255) DEFAULT NULL, CHANGE type_stockage_id type_ram_relation_id INT DEFAULT NULL, CHANGE status type_stockage VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABC10ECD1B FOREIGN KEY (type_ram_relation_id) REFERENCES type_ram (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D11814ABC10ECD1B ON intervention (type_ram_relation_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_29A5EC2791B1EAEE ON produit
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD status VARCHAR(50) DEFAULT NULL, DROP type_stockage_id, CHANGE status type_stockage VARCHAR(50) DEFAULT NULL
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
            ALTER TABLE intervention ADD status VARCHAR(255) DEFAULT NULL, DROP type_stockage, DROP status, CHANGE type_ram_relation_id type_stockage_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB91B1EAEE FOREIGN KEY (type_stockage_id) REFERENCES type_stockage (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D11814AB91B1EAEE ON intervention (type_stockage_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD type_stockage_id INT DEFAULT NULL, ADD status VARCHAR(50) DEFAULT NULL, DROP type_stockage, DROP status
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27B37EE6BC FOREIGN KEY (type_stockage_id) REFERENCES type_stockage (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_29A5EC2791B1EAEE ON produit (type_stockage_id)
        SQL);
    }
}
