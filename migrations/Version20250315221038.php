<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250315221038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role RENAME INDEX nom_role TO UNIQ_F75B255457698A6A');
        $this->addSql('ALTER TABLE user CHANGE role_id role_id INT DEFAULT NULL, CHANGE date_creation date_Creation DATETIME NOT NULL, CHANGE date_update date_Update DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user RENAME INDEX email TO UNIQ_2DA17977E7927C74');
        $this->addSql('ALTER TABLE user RENAME INDEX role_id TO IDX_2DA17977D60322AC');
        $this->addSql('ALTER TABLE intervention ADD id INT AUTO_INCREMENT NOT NULL, DROP id_intervention, DROP produit_id, DROP intervenant_id, DROP systeme_exploitation, DROP version_se, DROP date_intervention, DROP date_update, DROP commentaire, ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX code_barre ON produit');
        $this->addSql('DROP INDEX numero_serie ON produit');
        $this->addSql('ALTER TABLE produit DROP taille, DROP numero_serie, DROP cpu, DROP frequence_cpu, DROP ram, DROP type_ram, DROP carte_graphique, DROP memoire_video, DROP date_ajout, CHANGE code_barre code_barre VARCHAR(100) DEFAULT NULL, CHANGE categorie categorie VARCHAR(100) DEFAULT NULL, CHANGE marque marque VARCHAR(100) DEFAULT NULL, CHANGE modele modele VARCHAR(100) DEFAULT NULL, CHANGE stockage stockage INT DEFAULT NULL, CHANGE status status VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON intervention');
        $this->addSql('ALTER TABLE intervention ADD id_intervention INT NOT NULL, ADD produit_id INT NOT NULL, ADD intervenant_id INT NOT NULL, ADD systeme_exploitation VARCHAR(50) DEFAULT NULL, ADD version_se VARCHAR(50) DEFAULT NULL, ADD date_intervention DATE NOT NULL, ADD date_update DATETIME DEFAULT NULL, ADD commentaire VARCHAR(250) DEFAULT NULL, DROP id');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA17977D60322AC');
        $this->addSql('ALTER TABLE User CHANGE role_id role_id INT NOT NULL, CHANGE date_Creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE date_Update date_update DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE User RENAME INDEX uniq_2da17977e7927c74 TO email');
        $this->addSql('ALTER TABLE User RENAME INDEX idx_2da17977d60322ac TO role_id');
        $this->addSql('ALTER TABLE produit ADD taille VARCHAR(10) DEFAULT NULL, ADD numero_serie VARCHAR(50) DEFAULT NULL, ADD cpu VARCHAR(50) DEFAULT NULL, ADD frequence_cpu VARCHAR(10) DEFAULT NULL, ADD ram VARCHAR(10) DEFAULT NULL, ADD type_ram VARCHAR(50) DEFAULT NULL, ADD carte_graphique VARCHAR(50) DEFAULT NULL, ADD memoire_video VARCHAR(50) DEFAULT NULL, ADD date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE code_barre code_barre BIGINT NOT NULL, CHANGE categorie categorie VARCHAR(50) NOT NULL, CHANGE marque marque VARCHAR(50) DEFAULT NULL, CHANGE modele modele VARCHAR(50) DEFAULT NULL, CHANGE stockage stockage VARCHAR(50) DEFAULT NULL, CHANGE status status VARCHAR(50) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX code_barre ON produit (code_barre)');
        $this->addSql('CREATE UNIQUE INDEX numero_serie ON produit (numero_serie)');
        $this->addSql('ALTER TABLE Role RENAME INDEX uniq_f75b255457698a6a TO nom_role');
    }
}
