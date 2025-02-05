<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250131145431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D11624F3E');
        $this->addSql('ALTER TABLE recette DROP FOREIGN KEY FK_49BB6390933FE08C');
        $this->addSql('ALTER TABLE recette DROP FOREIGN KEY FK_49BB6390D73DB560');
        $this->addSql('ALTER TABLE mouvements DROP FOREIGN KEY FK_DA34835C933FE08C');
        $this->addSql('ALTER TABLE payement_commande DROP FOREIGN KEY FK_E02FC108438F5B63');
        $this->addSql('ALTER TABLE payement_commande DROP FOREIGN KEY FK_E02FC108A76ED395');
        $this->addSql('ALTER TABLE ingredients DROP FOREIGN KEY FK_4B60114FBC91983E');
        $this->addSql('DROP TABLE recette');
        $this->addSql('DROP TABLE mouvements');
        $this->addSql('DROP TABLE mode_paiement');
        $this->addSql('DROP TABLE payement_commande');
        $this->addSql('DROP TABLE ingredients');
        $this->addSql('DROP TABLE unite');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('DROP INDEX IDX_6EEAA67D11624F3E ON commande');
        $this->addSql('DROP INDEX IDX_6EEAA67DA76ED395 ON commande');
        $this->addSql('ALTER TABLE commande ADD quantite INT NOT NULL, DROP payement_commande_id, DROP quantite_plat, CHANGE user_id user_id VARCHAR(255) NOT NULL, CHANGE statut statut INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, DROP is_admin, CHANGE email email VARCHAR(180) NOT NULL, CHANGE mot_de_passe password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recette (id INT AUTO_INCREMENT NOT NULL, plat_id INT NOT NULL, ingredient_id INT NOT NULL, quantite_ingredient INT NOT NULL, INDEX IDX_49BB6390933FE08C (ingredient_id), INDEX IDX_49BB6390D73DB560 (plat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE mouvements (id INT AUTO_INCREMENT NOT NULL, ingredient_id INT NOT NULL, entre INT DEFAULT NULL, sortie INT DEFAULT NULL, INDEX IDX_DA34835C933FE08C (ingredient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE mode_paiement (id INT AUTO_INCREMENT NOT NULL, mode_paiement VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE payement_commande (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, mode_paiement_id INT NOT NULL, prix_total NUMERIC(10, 2) NOT NULL, statut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_E02FC108438F5B63 (mode_paiement_id), INDEX IDX_E02FC108A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ingredients (id INT AUTO_INCREMENT NOT NULL, id_unite_id INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sprite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_4B60114FBC91983E (id_unite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE unite (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE recette ADD CONSTRAINT FK_49BB6390933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredients (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE recette ADD CONSTRAINT FK_49BB6390D73DB560 FOREIGN KEY (plat_id) REFERENCES plats (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE mouvements ADD CONSTRAINT FK_DA34835C933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredients (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE payement_commande ADD CONSTRAINT FK_E02FC108438F5B63 FOREIGN KEY (mode_paiement_id) REFERENCES mode_paiement (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE payement_commande ADD CONSTRAINT FK_E02FC108A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE ingredients ADD CONSTRAINT FK_4B60114FBC91983E FOREIGN KEY (id_unite_id) REFERENCES unite (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commande ADD quantite_plat INT NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE statut statut VARCHAR(255) NOT NULL, CHANGE quantite payement_commande_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D11624F3E FOREIGN KEY (payement_commande_id) REFERENCES payement_commande (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6EEAA67D11624F3E ON commande (payement_commande_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD is_admin TINYINT(1) NOT NULL, DROP roles, CHANGE email email VARCHAR(255) NOT NULL, CHANGE password mot_de_passe VARCHAR(255) NOT NULL');
    }
}
