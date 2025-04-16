<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416102714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD type_ram_relation_id INT DEFAULT NULL
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
            ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABC10ECD1B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D11814ABC10ECD1B ON intervention
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention DROP type_ram_relation_id
        SQL);
    }
}
