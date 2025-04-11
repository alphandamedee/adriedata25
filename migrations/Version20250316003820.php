<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250316003820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention ADD produit_id INT NOT NULL, ADD intervenant_id INT NOT NULL, ADD systeme_exploitation VARCHAR(50) DEFAULT NULL, ADD version_se VARCHAR(50) DEFAULT NULL, ADD date_intervention DATE NOT NULL, ADD date_update DATETIME DEFAULT CURRENT_TIMESTAMP, ADD commentaire VARCHAR(250) DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_D11814ABF347EFB ON intervention (produit_id)');
        $this->addSql('CREATE INDEX IDX_D11814ABAB9A1716 ON intervention (intervenant_id)');
        $this->addSql('ALTER TABLE produit RENAME INDEX code_barre TO UNIQ_29A5EC273DBB6887');
        $this->addSql('ALTER TABLE produit RENAME INDEX numero_serie TO UNIQ_29A5EC27565B809');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABF347EFB');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABAB9A1716');
        $this->addSql('DROP INDEX IDX_D11814ABF347EFB ON intervention');
        $this->addSql('DROP INDEX IDX_D11814ABAB9A1716 ON intervention');
        $this->addSql('ALTER TABLE intervention DROP produit_id, DROP intervenant_id, DROP systeme_exploitation, DROP version_se, DROP date_intervention, DROP date_update, DROP commentaire');
        $this->addSql('ALTER TABLE produit RENAME INDEX uniq_29a5ec273dbb6887 TO code_barre');
        $this->addSql('ALTER TABLE produit RENAME INDEX uniq_29a5ec27565b809 TO numero_serie');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA17977D60322AC');
    }
}
