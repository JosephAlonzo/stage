<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210416055647 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning CHANGE max_places max_places INT NOT NULL, CHANGE educator_id educator_id INT NOT NULL');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6887E9271 FOREIGN KEY (educator_id) REFERENCES educator (id)');
        $this->addSql('CREATE INDEX IDX_D499BFF6887E9271 ON planning (educator_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6887E9271');
        $this->addSql('DROP INDEX IDX_D499BFF6887E9271 ON planning');
        $this->addSql('ALTER TABLE planning CHANGE educator_id educator_id INT DEFAULT NULL, CHANGE max_places max_places INT DEFAULT NULL');
    }
}
