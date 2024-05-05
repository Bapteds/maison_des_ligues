<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240505133330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE nuite_inscription (nuite_id INT NOT NULL, inscription_id INT NOT NULL, INDEX IDX_F1BAB5F2A84291E2 (nuite_id), INDEX IDX_F1BAB5F25DAC5993 (inscription_id), PRIMARY KEY(nuite_id, inscription_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nuite_inscription ADD CONSTRAINT FK_F1BAB5F2A84291E2 FOREIGN KEY (nuite_id) REFERENCES nuite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nuite_inscription ADD CONSTRAINT FK_F1BAB5F25DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6F2C56620');
        $this->addSql('DROP INDEX IDX_5E90F6D6F2C56620 ON inscription');
        $this->addSql('ALTER TABLE inscription ADD est_paye TINYINT(1) NOT NULL, CHANGE compte_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5E90F6D6A76ED395 ON inscription (user_id)');
        $this->addSql('ALTER TABLE nuite DROP FOREIGN KEY FK_8D4CB7155DAC5993');
        $this->addSql('DROP INDEX IDX_8D4CB7155DAC5993 ON nuite');
        $this->addSql('ALTER TABLE nuite DROP inscription_id');
        $this->addSql('ALTER TABLE user ADD inscription_id INT DEFAULT NULL, ADD valid_token VARCHAR(200) DEFAULT NULL, ADD nom VARCHAR(255) NOT NULL, ADD prenom VARCHAR(255) NOT NULL, ADD ville VARCHAR(255) NOT NULL, ADD adresse VARCHAR(255) NOT NULL, ADD idqualite INT NOT NULL, ADD cp VARCHAR(255) NOT NULL, ADD tel VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6495DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495DAC5993 ON user (inscription_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nuite_inscription DROP FOREIGN KEY FK_F1BAB5F2A84291E2');
        $this->addSql('ALTER TABLE nuite_inscription DROP FOREIGN KEY FK_F1BAB5F25DAC5993');
        $this->addSql('DROP TABLE nuite_inscription');
        $this->addSql('ALTER TABLE nuite ADD inscription_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nuite ADD CONSTRAINT FK_8D4CB7155DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D4CB7155DAC5993 ON nuite (inscription_id)');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6A76ED395');
        $this->addSql('DROP INDEX IDX_5E90F6D6A76ED395 ON inscription');
        $this->addSql('ALTER TABLE inscription DROP est_paye, CHANGE user_id compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5E90F6D6F2C56620 ON inscription (compte_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6495DAC5993');
        $this->addSql('DROP INDEX UNIQ_8D93D6495DAC5993 ON user');
        $this->addSql('ALTER TABLE user DROP inscription_id, DROP valid_token, DROP nom, DROP prenom, DROP ville, DROP adresse, DROP idqualite, DROP cp, DROP tel');
    }
}
