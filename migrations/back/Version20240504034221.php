<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240504034221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nuite DROP FOREIGN KEY FK_8D4CB7155DAC5993');
        $this->addSql('DROP INDEX IDX_8D4CB7155DAC5993 ON nuite');
        $this->addSql('ALTER TABLE nuite DROP inscription_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nuite ADD inscription_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nuite ADD CONSTRAINT FK_8D4CB7155DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D4CB7155DAC5993 ON nuite (inscription_id)');
    }
}
