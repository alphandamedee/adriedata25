<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250517091538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_2DA17977E7927C74
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user RENAME INDEX idx_8d93d649d60322ac TO IDX_2DA17977D60322AC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP lu
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F10335F61 FOREIGN KEY (expediteur_id) REFERENCES User (id_User)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES User (id_User)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F10335F61
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA4F84F6E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD lu TINYINT(1) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE User RENAME INDEX uniq_2da17977e7927c74 TO UNIQ_8D93D649E7927C74
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE User RENAME INDEX idx_2da17977d60322ac TO IDX_8D93D649D60322AC
        SQL);
    }
}
