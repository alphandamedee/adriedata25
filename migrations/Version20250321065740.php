<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321065740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_USER_ROLE');
        $this->addSql('ALTER TABLE user CHANGE role_id role_id INT NOT NULL, CHANGE date_creation date_Creation DATETIME NOT NULL, CHANGE date_update date_Update DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_2DA17977D60322AC FOREIGN KEY (role_id) REFERENCES role (role_id)');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_user_email TO UNIQ_2DA17977E7927C74');
        $this->addSql('ALTER TABLE user RENAME INDEX idx_user_role TO IDX_2DA17977D60322AC');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_INTERVENTION_INTERVENANT');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_INTERVENTION_PRODUIT');
        $this->addSql('ALTER TABLE intervention ADD intervenant_nom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABAB9A1716 FOREIGN KEY (intervenant_id) REFERENCES User (id_User)');
        $this->addSql('ALTER TABLE intervention RENAME INDEX idx_intervention_produit TO IDX_D11814ABF347EFB');
        $this->addSql('ALTER TABLE intervention RENAME INDEX idx_intervention_intervenant TO IDX_D11814ABAB9A1716');
        $this->addSql('ALTER TABLE role RENAME INDEX uniq_role_name TO UNIQ_57698A6AE09C0C92');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABF347EFB');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABAB9A1716');
        $this->addSql('ALTER TABLE intervention DROP intervenant_nom');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_INTERVENTION_INTERVENANT FOREIGN KEY (intervenant_id) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_INTERVENTION_PRODUIT FOREIGN KEY (produit_id) REFERENCES produit (id_produit) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intervention RENAME INDEX idx_d11814abf347efb TO IDX_INTERVENTION_PRODUIT');
        $this->addSql('ALTER TABLE intervention RENAME INDEX idx_d11814abab9a1716 TO IDX_INTERVENTION_INTERVENANT');
        $this->addSql('ALTER TABLE role RENAME INDEX uniq_57698a6ae09c0c92 TO UNIQ_ROLE_NAME');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA17977D60322AC');
        $this->addSql('ALTER TABLE User CHANGE role_id role_id INT DEFAULT NULL, CHANGE date_Creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE date_Update date_update DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE User ADD CONSTRAINT FK_USER_ROLE FOREIGN KEY (role_id) REFERENCES role (role_id) ON UPDATE CASCADE ON DELETE SET NULL');
        $this->addSql('ALTER TABLE User RENAME INDEX uniq_2da17977e7927c74 TO UNIQ_USER_EMAIL');
        $this->addSql('ALTER TABLE User RENAME INDEX idx_2da17977d60322ac TO IDX_USER_ROLE');
    }
}
