<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210415180828 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, color VARCHAR(25) DEFAULT NULL, max_places INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_AC74095AB03A8386 (created_by_id), INDEX IDX_AC74095A896DBBDE (updated_by_id), INDEX IDX_AC74095A9033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_educator (activity_id INT NOT NULL, educator_id INT NOT NULL, INDEX IDX_2BAB0F3381C06096 (activity_id), INDEX IDX_2BAB0F33887E9271 (educator_id), PRIMARY KEY(activity_id, educator_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendance_sheet (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, orientation_sheet_planning_id INT NOT NULL, cycle VARCHAR(50) NOT NULL, attendances LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A76BCA8CB03A8386 (created_by_id), INDEX IDX_A76BCA8C896DBBDE (updated_by_id), INDEX IDX_A76BCA8C9033212A (tenant_id), UNIQUE INDEX UNIQ_A76BCA8C58C4A166 (orientation_sheet_planning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE beneficiary (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, gender VARCHAR(20) NOT NULL, family_situation VARCHAR(50) NOT NULL, number_children INT NOT NULL, date_birth DATE NOT NULL, address VARCHAR(255) NOT NULL, lodging VARCHAR(50) NOT NULL, medical_cover VARCHAR(50) NOT NULL, resources_received VARCHAR(50) NOT NULL, phone_number VARCHAR(13) NOT NULL, email VARCHAR(255) DEFAULT NULL, autre_resources_received VARCHAR(255) DEFAULT NULL, autre_lodging VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_7ABF446A8BAC62AF (city_id), INDEX IDX_7ABF446AB03A8386 (created_by_id), INDEX IDX_7ABF446A896DBBDE (updated_by_id), INDEX IDX_7ABF446A9033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, postal_code VARCHAR(5) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2D5B0234B03A8386 (created_by_id), INDEX IDX_2D5B0234896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE educator (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, user_id INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8BA1BF3DB03A8386 (created_by_id), INDEX IDX_8BA1BF3D896DBBDE (updated_by_id), INDEX IDX_8BA1BF3D9033212A (tenant_id), UNIQUE INDEX UNIQ_8BA1BF3DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(191) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE holiday (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, name VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DC9AB234B03A8386 (created_by_id), INDEX IDX_DC9AB234896DBBDE (updated_by_id), INDEX IDX_DC9AB2349033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orientation_sheet (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, beneficiary_id INT DEFAULT NULL, social_worker_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, sending_date DATE NOT NULL, situation VARCHAR(300) NOT NULL, axes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', photo_authorization TINYINT(1) DEFAULT NULL, start_date DATE DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3B99DE26B03A8386 (created_by_id), INDEX IDX_3B99DE26896DBBDE (updated_by_id), INDEX IDX_3B99DE26ECCAAFA0 (beneficiary_id), INDEX IDX_3B99DE265976E741 (social_worker_id), INDEX IDX_3B99DE269033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orientation_sheet_plannings (id INT AUTO_INCREMENT NOT NULL, orientation_sheet_id INT NOT NULL, planning_id INT NOT NULL, confirmed TINYINT(1) NOT NULL, INDEX IDX_6B517CECCE93C28 (orientation_sheet_id), INDEX IDX_6B517CE3D865311 (planning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_741D53CD8BAC62AF (city_id), INDEX IDX_741D53CDB03A8386 (created_by_id), INDEX IDX_741D53CD896DBBDE (updated_by_id), INDEX IDX_741D53CD9033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, activity_id INT NOT NULL, place_id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, beginning_time TIME NOT NULL, ending_time TIME NOT NULL, number_sessions INT NOT NULL, day VARCHAR(20) NOT NULL, status TINYINT(1) NOT NULL, max_places INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D499BFF681C06096 (activity_id), INDEX IDX_D499BFF6DA6A219 (place_id), INDEX IDX_D499BFF6B03A8386 (created_by_id), INDEX IDX_D499BFF6896DBBDE (updated_by_id), INDEX IDX_D499BFF69033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_worker (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, user_id INT DEFAULT NULL, structure_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, origin VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_81F9DDBE8BAC62AF (city_id), INDEX IDX_81F9DDBEB03A8386 (created_by_id), INDEX IDX_81F9DDBE896DBBDE (updated_by_id), UNIQUE INDEX UNIQ_81F9DDBEA76ED395 (user_id), INDEX IDX_81F9DDBE2534008B (structure_id), INDEX IDX_81F9DDBE9033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE structure (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, city_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(15) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6F0137EAB03A8386 (created_by_id), INDEX IDX_6F0137EA896DBBDE (updated_by_id), INDEX IDX_6F0137EA8BAC62AF (city_id), INDEX IDX_6F0137EA9033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, city_id INT DEFAULT NULL, site_internet VARCHAR(255) DEFAULT NULL, siret VARCHAR(30) DEFAULT NULL, code_ape VARCHAR(30) DEFAULT NULL, cdos_name VARCHAR(255) NOT NULL, cdos_number VARCHAR(10) NOT NULL, address VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(15) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_4E59C462B03A8386 (created_by_id), INDEX IDX_4E59C462896DBBDE (updated_by_id), INDEX IDX_4E59C4628BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, tenant_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone_number VARCHAR(15) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8D93D6499033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE activity_educator ADD CONSTRAINT FK_2BAB0F3381C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_educator ADD CONSTRAINT FK_2BAB0F33887E9271 FOREIGN KEY (educator_id) REFERENCES educator (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendance_sheet ADD CONSTRAINT FK_A76BCA8CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE attendance_sheet ADD CONSTRAINT FK_A76BCA8C896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE attendance_sheet ADD CONSTRAINT FK_A76BCA8C9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE attendance_sheet ADD CONSTRAINT FK_A76BCA8C58C4A166 FOREIGN KEY (orientation_sheet_planning_id) REFERENCES orientation_sheet_plannings (id)');
        $this->addSql('ALTER TABLE beneficiary ADD CONSTRAINT FK_7ABF446A8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE beneficiary ADD CONSTRAINT FK_7ABF446AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE beneficiary ADD CONSTRAINT FK_7ABF446A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE beneficiary ADD CONSTRAINT FK_7ABF446A9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE educator ADD CONSTRAINT FK_8BA1BF3DB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE educator ADD CONSTRAINT FK_8BA1BF3D896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE educator ADD CONSTRAINT FK_8BA1BF3D9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE educator ADD CONSTRAINT FK_8BA1BF3DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_DC9AB234B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_DC9AB234896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_DC9AB2349033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE orientation_sheet ADD CONSTRAINT FK_3B99DE26B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orientation_sheet ADD CONSTRAINT FK_3B99DE26896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orientation_sheet ADD CONSTRAINT FK_3B99DE26ECCAAFA0 FOREIGN KEY (beneficiary_id) REFERENCES beneficiary (id)');
        $this->addSql('ALTER TABLE orientation_sheet ADD CONSTRAINT FK_3B99DE265976E741 FOREIGN KEY (social_worker_id) REFERENCES social_worker (id)');
        $this->addSql('ALTER TABLE orientation_sheet ADD CONSTRAINT FK_3B99DE269033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE orientation_sheet_plannings ADD CONSTRAINT FK_6B517CECCE93C28 FOREIGN KEY (orientation_sheet_id) REFERENCES orientation_sheet (id)');
        $this->addSql('ALTER TABLE orientation_sheet_plannings ADD CONSTRAINT FK_6B517CE3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CDB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF681C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF69033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE social_worker ADD CONSTRAINT FK_81F9DDBE8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE social_worker ADD CONSTRAINT FK_81F9DDBEB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE social_worker ADD CONSTRAINT FK_81F9DDBE896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE social_worker ADD CONSTRAINT FK_81F9DDBEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE social_worker ADD CONSTRAINT FK_81F9DDBE2534008B FOREIGN KEY (structure_id) REFERENCES structure (id)');
        $this->addSql('ALTER TABLE social_worker ADD CONSTRAINT FK_81F9DDBE9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C462B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C462896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C4628BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_educator DROP FOREIGN KEY FK_2BAB0F3381C06096');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF681C06096');
        $this->addSql('ALTER TABLE orientation_sheet DROP FOREIGN KEY FK_3B99DE26ECCAAFA0');
        $this->addSql('ALTER TABLE beneficiary DROP FOREIGN KEY FK_7ABF446A8BAC62AF');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD8BAC62AF');
        $this->addSql('ALTER TABLE social_worker DROP FOREIGN KEY FK_81F9DDBE8BAC62AF');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA8BAC62AF');
        $this->addSql('ALTER TABLE tenant DROP FOREIGN KEY FK_4E59C4628BAC62AF');
        $this->addSql('ALTER TABLE activity_educator DROP FOREIGN KEY FK_2BAB0F33887E9271');
        $this->addSql('ALTER TABLE orientation_sheet_plannings DROP FOREIGN KEY FK_6B517CECCE93C28');
        $this->addSql('ALTER TABLE attendance_sheet DROP FOREIGN KEY FK_A76BCA8C58C4A166');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6DA6A219');
        $this->addSql('ALTER TABLE orientation_sheet_plannings DROP FOREIGN KEY FK_6B517CE3D865311');
        $this->addSql('ALTER TABLE orientation_sheet DROP FOREIGN KEY FK_3B99DE265976E741');
        $this->addSql('ALTER TABLE social_worker DROP FOREIGN KEY FK_81F9DDBE2534008B');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A9033212A');
        $this->addSql('ALTER TABLE attendance_sheet DROP FOREIGN KEY FK_A76BCA8C9033212A');
        $this->addSql('ALTER TABLE beneficiary DROP FOREIGN KEY FK_7ABF446A9033212A');
        $this->addSql('ALTER TABLE educator DROP FOREIGN KEY FK_8BA1BF3D9033212A');
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_DC9AB2349033212A');
        $this->addSql('ALTER TABLE orientation_sheet DROP FOREIGN KEY FK_3B99DE269033212A');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD9033212A');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF69033212A');
        $this->addSql('ALTER TABLE social_worker DROP FOREIGN KEY FK_81F9DDBE9033212A');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA9033212A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499033212A');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AB03A8386');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A896DBBDE');
        $this->addSql('ALTER TABLE attendance_sheet DROP FOREIGN KEY FK_A76BCA8CB03A8386');
        $this->addSql('ALTER TABLE attendance_sheet DROP FOREIGN KEY FK_A76BCA8C896DBBDE');
        $this->addSql('ALTER TABLE beneficiary DROP FOREIGN KEY FK_7ABF446AB03A8386');
        $this->addSql('ALTER TABLE beneficiary DROP FOREIGN KEY FK_7ABF446A896DBBDE');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234B03A8386');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234896DBBDE');
        $this->addSql('ALTER TABLE educator DROP FOREIGN KEY FK_8BA1BF3DB03A8386');
        $this->addSql('ALTER TABLE educator DROP FOREIGN KEY FK_8BA1BF3D896DBBDE');
        $this->addSql('ALTER TABLE educator DROP FOREIGN KEY FK_8BA1BF3DA76ED395');
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_DC9AB234B03A8386');
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_DC9AB234896DBBDE');
        $this->addSql('ALTER TABLE orientation_sheet DROP FOREIGN KEY FK_3B99DE26B03A8386');
        $this->addSql('ALTER TABLE orientation_sheet DROP FOREIGN KEY FK_3B99DE26896DBBDE');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CDB03A8386');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD896DBBDE');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6B03A8386');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6896DBBDE');
        $this->addSql('ALTER TABLE social_worker DROP FOREIGN KEY FK_81F9DDBEB03A8386');
        $this->addSql('ALTER TABLE social_worker DROP FOREIGN KEY FK_81F9DDBE896DBBDE');
        $this->addSql('ALTER TABLE social_worker DROP FOREIGN KEY FK_81F9DDBEA76ED395');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EAB03A8386');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA896DBBDE');
        $this->addSql('ALTER TABLE tenant DROP FOREIGN KEY FK_4E59C462B03A8386');
        $this->addSql('ALTER TABLE tenant DROP FOREIGN KEY FK_4E59C462896DBBDE');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_educator');
        $this->addSql('DROP TABLE attendance_sheet');
        $this->addSql('DROP TABLE beneficiary');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE educator');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE holiday');
        $this->addSql('DROP TABLE orientation_sheet');
        $this->addSql('DROP TABLE orientation_sheet_plannings');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE social_worker');
        $this->addSql('DROP TABLE structure');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('DROP TABLE user');
    }
}
