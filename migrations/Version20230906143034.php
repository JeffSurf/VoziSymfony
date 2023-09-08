<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230906143034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD application_id INT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF03E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF03E030ACD ON avis (application_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF03E030ACD');
        $this->addSql('DROP INDEX IDX_8F91ABF03E030ACD ON avis');
        $this->addSql('ALTER TABLE avis DROP application_id');
    }
}
