<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317060358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_29A5EC273DBB6887 ON produit');
        $this->addSql('DROP INDEX UNIQ_29A5EC27565B809 ON produit');
        $this->addSql('ALTER TABLE produit ADD code_etagere VARCHAR(10) DEFAULT NULL, DROP taille, DROP numero_serie, DROP cpu, DROP frequence_cpu, DROP ram, DROP type_ram, DROP carte_graphique, DROP memoire_video, DROP date_ajout, DROP date_update, DROP num_etagere, CHANGE code_barre code_barre VARCHAR(100) DEFAULT NULL, CHANGE categorie categorie VARCHAR(100) DEFAULT NULL, CHANGE marque marque VARCHAR(100) DEFAULT NULL, CHANGE modele modele VARCHAR(100) DEFAULT NULL, CHANGE stockage stockage INT DEFAULT NULL, CHANGE status status VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABF347EFB');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABAB9A1716');
        $this->addSql('ALTER TABLE produit ADD numero_serie VARCHAR(50) DEFAULT NULL, ADD cpu VARCHAR(50) DEFAULT NULL, ADD frequence_cpu VARCHAR(10) DEFAULT NULL, ADD ram VARCHAR(10) DEFAULT NULL, ADD type_ram VARCHAR(50) DEFAULT NULL, ADD carte_graphique VARCHAR(50) DEFAULT NULL, ADD memoire_video VARCHAR(50) DEFAULT NULL, ADD date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP, ADD date_update DATETIME DEFAULT CURRENT_TIMESTAMP, ADD num_etagere VARCHAR(20) DEFAULT NULL, CHANGE code_barre code_barre BIGINT NOT NULL, CHANGE categorie categorie VARCHAR(50) NOT NULL, CHANGE marque marque VARCHAR(50) DEFAULT NULL, CHANGE modele modele VARCHAR(50) DEFAULT NULL, CHANGE stockage stockage VARCHAR(50) DEFAULT NULL, CHANGE status status VARCHAR(50) NOT NULL, CHANGE code_etagere taille VARCHAR(10) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_29A5EC273DBB6887 ON produit (code_barre)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_29A5EC27565B809 ON produit (numero_serie)');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA17977D60322AC');
    }
}
