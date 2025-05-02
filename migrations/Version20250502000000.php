<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour ajouter la table des types de stockage
 */
final class Version20250502000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crée la table type_stockage';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE type_stockage (
            id INT AUTO_INCREMENT NOT NULL,
            nom VARCHAR(255) NOT NULL,
            description VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Ajout des contraintes de clé étrangère pour la table produit
        $this->addSql('ALTER TABLE produit ADD type_stockage_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27B37EE6BC FOREIGN KEY (type_stockage_id) REFERENCES type_stockage (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27B37EE6BC ON produit (type_stockage_id)');

        // Insertion des types de stockage par défaut
        $this->addSql("INSERT INTO type_stockage (nom, description) VALUES
            ('SSD', 'Solid State Drive - Stockage à état solide'),
            ('HDD', 'Hard Disk Drive - Disque dur mécanique'),
            ('NVMe', 'Non-Volatile Memory Express'),
            ('eMMC', 'embedded Multi-Media Controller'),
            ('M.2', 'Format M.2 (SATA ou NVMe)'),
            ('SATA', 'Serial ATA')");
    }

    public function down(Schema $schema): void
    {
        // Suppression des contraintes de clé étrangère
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27B37EE6BC');
        $this->addSql('DROP INDEX IDX_29A5EC27B37EE6BC ON produit');
        $this->addSql('ALTER TABLE produit DROP type_stockage_id');
        
        // Suppression de la table
        $this->addSql('DROP TABLE type_stockage');
    }
}