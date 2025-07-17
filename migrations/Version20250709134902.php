<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250709134902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD users_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D67B3B43D FOREIGN KEY (users_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D67B3B43D ON commande (users_id)');
        $this->addSql('ALTER TABLE user ADD commande_id INT DEFAULT NULL, ADD nom VARCHAR(50) NOT NULL, ADD prenom VARCHAR(50) NOT NULL, ADD telephone VARCHAR(20) NOT NULL, ADD adresse VARCHAR(50) NOT NULL, ADD cp VARCHAR(20) NOT NULL, ADD ville VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64982EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64982EA2E54 ON user (commande_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64982EA2E54');
        $this->addSql('DROP INDEX IDX_8D93D64982EA2E54 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP commande_id, DROP nom, DROP prenom, DROP telephone, DROP adresse, DROP cp, DROP ville');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D67B3B43D');
        $this->addSql('DROP INDEX IDX_6EEAA67D67B3B43D ON commande');
        $this->addSql('ALTER TABLE commande DROP users_id');
    }
}
