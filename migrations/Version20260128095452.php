<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260128095452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE erabiltzaileak DROP FOREIGN KEY FK_816579526B932554');
        $this->addSql('DROP TABLE erabiltzaileak');
        $this->addSql('ALTER TABLE argazkiak CHANGE imageName imageName VARCHAR(255) NOT NULL, CHANGE imageThumbnail imageThumbnail VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE egoerak CHANGE deskripzioa_es deskripzioa_es VARCHAR(255) NOT NULL, CHANGE deskripzioa_eu deskripzioa_eu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE enpresak CHANGE izena izena VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE eranskinak CHANGE eranskinaName eranskinaName VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE erantzunak CHANGE erantzuna erantzuna LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE eskakizunMotak CHANGE deskripzioa_es deskripzioa_es VARCHAR(255) NOT NULL, CHANGE deskripzioa_eu deskripzioa_eu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE eskakizunak CHANGE lep lep VARCHAR(255) DEFAULT NULL, CHANGE mamia mamia LONGTEXT NOT NULL, CHANGE kalea kalea VARCHAR(255) DEFAULT NULL, CHANGE argazkia argazkia VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE eskatzaileak CHANGE izena izena VARCHAR(255) NOT NULL, CHANGE telefonoa telefonoa VARCHAR(255) DEFAULT NULL, CHANGE nan nan VARCHAR(255) DEFAULT NULL, CHANGE helbidea helbidea VARCHAR(255) DEFAULT NULL, CHANGE emaila emaila VARCHAR(255) DEFAULT NULL, CHANGE herria herria VARCHAR(255) DEFAULT NULL, CHANGE postaKodea postaKodea VARCHAR(255) DEFAULT NULL, CHANGE faxa faxa VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE georeferentziak CHANGE longitudea longitudea VARCHAR(255) DEFAULT NULL, CHANGE latitudea latitudea VARCHAR(255) DEFAULT NULL, CHANGE googleAddress googleAddress VARCHAR(255) DEFAULT NULL, CHANGE mapaLongitudea mapaLongitudea VARCHAR(255) DEFAULT NULL, CHANGE mapaLatitudea mapaLatitudea VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE jatorriak CHANGE deskripzioa_es deskripzioa_es VARCHAR(255) NOT NULL, CHANGE deskripzioa_eu deskripzioa_eu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(180) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE telefonoa telefonoa VARCHAR(255) DEFAULT NULL, CHANGE telefonoa2 telefonoa2 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE zerbitzuak CHANGE izena_es izena_es VARCHAR(255) NOT NULL, CHANGE izena_eu izena_eu VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE erabiltzaileak (id INT AUTO_INCREMENT NOT NULL, enpresa_id INT DEFAULT NULL, username VARCHAR(180) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, username_canonical VARCHAR(180) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, email VARCHAR(180) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, email_canonical VARCHAR(180) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, password VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci` COMMENT \'(DC2Type:array)\', izena VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, telefonoa VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, telefonoa2 VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, ordena INT DEFAULT NULL, UNIQUE INDEX UNIQ_81657952C05FB297 (confirmation_token), INDEX IDX_816579526B932554 (enpresa_id), UNIQUE INDEX UNIQ_8165795292FC23A8 (username_canonical), UNIQUE INDEX UNIQ_81657952A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE erabiltzaileak ADD CONSTRAINT FK_816579526B932554 FOREIGN KEY (enpresa_id) REFERENCES enpresak (id)');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE argazkiak CHANGE imageName imageName VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE imageThumbnail imageThumbnail VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE egoerak CHANGE deskripzioa_es deskripzioa_es VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE deskripzioa_eu deskripzioa_eu VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE enpresak CHANGE izena izena VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE eranskinak CHANGE eranskinaName eranskinaName VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE erantzunak CHANGE erantzuna erantzuna LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE eskakizunMotak CHANGE deskripzioa_es deskripzioa_es VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE deskripzioa_eu deskripzioa_eu VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE eskakizunak CHANGE lep lep VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE mamia mamia LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE kalea kalea VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE argazkia argazkia VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE eskatzaileak CHANGE izena izena VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE telefonoa telefonoa VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE nan nan VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE helbidea helbidea VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE emaila emaila VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE herria herria VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE postaKodea postaKodea VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE faxa faxa VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE georeferentziak CHANGE longitudea longitudea VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE latitudea latitudea VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE googleAddress googleAddress VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE mapaLongitudea mapaLongitudea VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE mapaLatitudea mapaLatitudea VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE jatorriak CHANGE deskripzioa_es deskripzioa_es VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE deskripzioa_eu deskripzioa_eu VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(180) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE password password VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE telefonoa telefonoa VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE telefonoa2 telefonoa2 VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE zerbitzuak CHANGE izena_es izena_es VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE izena_eu izena_eu VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`');
    }
}
