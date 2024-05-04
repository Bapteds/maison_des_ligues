<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240504040529 extends AbstractMigration
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nuite_inscription DROP FOREIGN KEY FK_F1BAB5F2A84291E2');
        $this->addSql('ALTER TABLE nuite_inscription DROP FOREIGN KEY FK_F1BAB5F25DAC5993');
        $this->addSql('DROP TABLE nuite_inscription');
    }
}
