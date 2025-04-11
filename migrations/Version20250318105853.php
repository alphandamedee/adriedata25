<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318105853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention ADD mise_ajour_windows TINYINT(1) DEFAULT NULL, ADD mise_ajour_pilotes TINYINT(1) DEFAULT NULL, ADD autres_logiciels TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABF347EFB');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABAB9A1716');
        $this->addSql('ALTER TABLE intervention DROP mise_ajour_windows, DROP mise_ajour_pilotes, DROP autres_logiciels');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA17977D60322AC');
    }
}
