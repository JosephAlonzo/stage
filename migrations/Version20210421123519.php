<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210421123519 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orientation_sheet_plannings ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE orientation_sheet_plannings ADD CONSTRAINT FK_6B517CEB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orientation_sheet_plannings ADD CONSTRAINT FK_6B517CE896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6B517CEB03A8386 ON orientation_sheet_plannings (created_by_id)');
        $this->addSql('CREATE INDEX IDX_6B517CE896DBBDE ON orientation_sheet_plannings (updated_by_id)');
        $this->addSql('ALTER TABLE planning CHANGE max_places max_places INT NOT NULL, CHANGE educator_id educator_id INT NOT NULL');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6887E9271 FOREIGN KEY (educator_id) REFERENCES educator (id)');
        $this->addSql('CREATE INDEX IDX_D499BFF6887E9271 ON planning (educator_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orientation_sheet_plannings DROP FOREIGN KEY FK_6B517CEB03A8386');
        $this->addSql('ALTER TABLE orientation_sheet_plannings DROP FOREIGN KEY FK_6B517CE896DBBDE');
        $this->addSql('DROP INDEX IDX_6B517CEB03A8386 ON orientation_sheet_plannings');
        $this->addSql('DROP INDEX IDX_6B517CE896DBBDE ON orientation_sheet_plannings');
        $this->addSql('ALTER TABLE orientation_sheet_plannings DROP created_by_id, DROP updated_by_id, DROP deleted_at, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6887E9271');
        $this->addSql('DROP INDEX IDX_D499BFF6887E9271 ON planning');
        $this->addSql('ALTER TABLE planning CHANGE educator_id educator_id INT DEFAULT NULL, CHANGE max_places max_places INT DEFAULT NULL');
    }
}
