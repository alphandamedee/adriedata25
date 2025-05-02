<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\DriverManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion de la base de données
 * Ce contrôleur permet de créer et initialiser la structure de la base de données
 */
class DatabaseController extends AbstractController
{
    /**
     * Point d'entrée pour la configuration de la base de données
     * 
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine
     * @return Response Réponse HTTP avec le résultat de l'opération
     */
    #[Route('/setup-database', name: 'setup_database')]
    public function setupDatabase(EntityManagerInterface $entityManager): Response
    {
        $databaseName = 'adriedata';

        // Vérifier et créer la base de données
        if (!$this->checkAndCreateDatabase($entityManager, $databaseName)) {
            return new Response("Base de données existante.");
        }

        // Vérifier et créer les tables
        $this->createTablesIfNotExists($entityManager);

        return new Response("Base de données et tables vérifiées !");
    }

    /**
     * Vérifie l'existence de la base de données et la crée si elle n'existe pas
     * 
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine
     * @param string $databaseName Nom de la base de données
     * @return bool True si la base a été créée, False si elle existait déjà
     */
    private function checkAndCreateDatabase(EntityManagerInterface $entityManager, string $databaseName): bool
    {
        $conn = $entityManager->getConnection();
        $params = $conn->getParams();
        $params['dbname'] = null; // Retirer temporairement le nom de la base

        $tmpConnection = DriverManager::getConnection($params);
        $schemaManager = $tmpConnection->createSchemaManager();

        if (!in_array($databaseName, $schemaManager->listDatabases())) {
            $tmpConnection->executeStatement("CREATE DATABASE `$databaseName`");
            return true;
        }
        return false;
    }

    /**
     * Vérifie et crée les tables nécessaires si elles n'existent pas
     * 
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine
     */
    private function createTablesIfNotExists(EntityManagerInterface $entityManager)
    {
        $conn = $entityManager->getConnection();
        $schemaManager = $conn->createSchemaManager();

        // Vérifier et créer la table Role
        if (!$schemaManager->tablesExist(['role'])) {
            $conn->executeStatement("
                CREATE TABLE Role (
                    idRole INT AUTO_INCREMENT PRIMARY KEY,
                    role VARCHAR(50) NOT NULL UNIQUE
                )
            ");
        }

        // Vérifier et créer la table User
        if (!$schemaManager->tablesExist(['user'])) {
            $conn->executeStatement("
                CREATE TABLE User (
                    idUser INT AUTO_INCREMENT PRIMARY KEY,
                    nomUser VARCHAR(50) NOT NULL,
                    prenomUser VARCHAR(50) NOT NULL,
                    mailUser VARCHAR(50) NOT NULL UNIQUE CHECK (mailUser LIKE '%@%.%'),
                    password VARCHAR(50) NOT NULL CHECK (LENGTH(password) >= 8),
                    attrUser VARCHAR(50),
                    role INT NOT NULL,
                    dateCreation DATE DEFAULT CURRENT_DATE,
                    dateUpdate DATE,
                    FOREIGN KEY (role) REFERENCES Role(idRole)
                )
            ");
        }

        // Vérifier et créer la table Produit
        if (!$schemaManager->tablesExist(['produit'])) {
            $conn->executeStatement("
                CREATE TABLE Produit (
                    idProduit INT AUTO_INCREMENT PRIMARY KEY,
                    codeBarre BIGINT NOT NULL UNIQUE,
                    categorie VARCHAR(50) NOT NULL,
                    marque VARCHAR(50),
                    modele VARCHAR(50),
                    taille VARCHAR(4),
                    numSerie VARCHAR(50) UNIQUE,
                    CPU VARCHAR(50),
                    freqCpu VARCHAR(10),
                    ram VARCHAR(10),
                    typeRam VARCHAR(50),
                    stockage VARCHAR(50),
                    typsStockage VARCHAR(50),
                    carteGraphique VARCHAR(50),
                    memVideo VARCHAR(50),
                    dateAjout DATE DEFAULT CURRENT_DATE,
                    statut VARCHAR(50) NOT NULL
                )
            ");
        }

        // Vérifier et créer la table Intervention
        if (!$schemaManager->tablesExist(['intervention'])) {
            $conn->executeStatement("
                CREATE TABLE Intervention (
                    idIntervention INT AUTO_INCREMENT PRIMARY KEY,
                    idProduit INT NOT NULL,
                    intervenant INT NOT NULL,
                    systemExploitation VARCHAR(50),
                    versionSE VARCHAR(50),
                    dateIntervention DATE NOT NULL,
                    dateUpdate DATE,
                    comment VARCHAR(250),
                    FOREIGN KEY (idProduit) REFERENCES Produit(idProduit),
                    FOREIGN KEY (intervenant) REFERENCES User(idUser)
                )
            ");
        }

        // Insérer des rôles si non existants
        $conn->executeStatement("
            INSERT IGNORE INTO Role (role) VALUES ('Administrateur'), ('Technicien'), ('Bénévole')
        ");

        // Insérer un utilisateur de test si non existant
        $conn->executeStatement("
            INSERT INTO User (nomUser, prenomUser, mailUser, password, attrUser, role)
            SELECT * FROM (SELECT 'Dupont', 'Jean', 'jean.dupont@example.com', 'motdepasse123', 'Gestion', 1) AS tmp
            WHERE NOT EXISTS (
                SELECT mailUser FROM User WHERE mailUser = 'jean.dupont@example.com'
            ) LIMIT 1;
        ");
    }
}
?>