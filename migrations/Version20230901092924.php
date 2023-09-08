<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230901092924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1FB88E14F ON application (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1FB88E14F');
        $this->addSql('DROP INDEX IDX_A45BDDC1FB88E14F ON application');
        $this->addSql('ALTER TABLE application DROP utilisateur_id');
    }
}
