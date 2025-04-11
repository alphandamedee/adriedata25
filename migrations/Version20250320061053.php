<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250320061053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention ADD mise_ajour_pilotes TINYINT(1) DEFAULT NULL, ADD autres_logiciels TINYINT(1) DEFAULT NULL, ADD numero_serie VARCHAR(255) DEFAULT NULL, ADD frequence_cpu VARCHAR(255) DEFAULT NULL, ADD type_ram VARCHAR(255) DEFAULT NULL, ADD type_stockage VARCHAR(255) DEFAULT NULL, ADD carte_graphique VARCHAR(255) DEFAULT NULL, ADD memoire_video VARCHAR(255) DEFAULT NULL, ADD intervenant VARCHAR(255) DEFAULT NULL, ADD pdf_file_path VARCHAR(255) DEFAULT NULL, DROP numeroSerie, DROP frequenceCpu, DROP typeRam, DROP typeStockage, DROP carteGraphique, DROP memoireViveo, DROP miseAJourPilotes, DROP autresLogiciels, CHANGE categorie categorie VARCHAR(255) DEFAULT NULL, CHANGE marque marque VARCHAR(255) DEFAULT NULL, CHANGE modele modele VARCHAR(255) DEFAULT NULL, CHANGE cpu cpu VARCHAR(255) DEFAULT NULL, CHANGE ram ram VARCHAR(255) DEFAULT NULL, CHANGE stockage stockage VARCHAR(255) DEFAULT NULL, CHANGE mise_ajour_windows mise_ajour_windows TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE num_serie num_serie VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABF347EFB');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABAB9A1716');
        $this->addSql('ALTER TABLE intervention ADD numeroSerie VARCHAR(25) NOT NULL, ADD frequenceCpu VARCHAR(50) DEFAULT NULL, ADD typeRam VARCHAR(10) DEFAULT NULL, ADD typeStockage VARCHAR(10) DEFAULT NULL, ADD carteGraphique VARCHAR(50) DEFAULT NULL, ADD memoireViveo VARCHAR(10) DEFAULT NULL, ADD miseAJourPilotes VARCHAR(10) DEFAULT NULL, ADD autresLogiciels VARCHAR(10) DEFAULT NULL, DROP mise_ajour_pilotes, DROP autres_logiciels, DROP numero_serie, DROP frequence_cpu, DROP type_ram, DROP type_stockage, DROP carte_graphique, DROP memoire_video, DROP intervenant, DROP pdf_file_path, CHANGE mise_ajour_windows mise_ajour_windows VARCHAR(10) DEFAULT NULL, CHANGE categorie categorie VARCHAR(50) NOT NULL, CHANGE marque marque VARCHAR(50) NOT NULL, CHANGE modele modele VARCHAR(50) NOT NULL, CHANGE cpu cpu VARCHAR(50) DEFAULT NULL, CHANGE ram ram VARCHAR(10) DEFAULT NULL, CHANGE stockage stockage VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE num_serie num_serie VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA17977D60322AC');
    }
}
