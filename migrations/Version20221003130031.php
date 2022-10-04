<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221003130031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bouquet (id INT AUTO_INCREMENT NOT NULL, price DOUBLE PRECISION DEFAULT NULL, isactive TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, flag VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, compte_id INT DEFAULT NULL, datecreation DATETIME DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_81398E09F2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE souscription (id INT AUTO_INCREMENT NOT NULL, bouquet_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, created DATETIME DEFAULT NULL, expired_at DATETIME DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, INDEX IDX_2AED620D6C8DF983 (bouquet_id), INDEX IDX_2AED620D9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, name VARCHAR(250) NOT NULL, phone VARCHAR(250) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, isactivate TINYINT(1) DEFAULT NULL, facebook_id VARCHAR(250) DEFAULT NULL, google_id VARCHAR(250) DEFAULT NULL, avatar VARCHAR(250) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09F2C56620 FOREIGN KEY (compte_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE souscription ADD CONSTRAINT FK_2AED620D6C8DF983 FOREIGN KEY (bouquet_id) REFERENCES bouquet (id)');
        $this->addSql('ALTER TABLE souscription ADD CONSTRAINT FK_2AED620D9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09F2C56620');
        $this->addSql('ALTER TABLE souscription DROP FOREIGN KEY FK_2AED620D6C8DF983');
        $this->addSql('ALTER TABLE souscription DROP FOREIGN KEY FK_2AED620D9395C3F3');
        $this->addSql('DROP TABLE bouquet');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE souscription');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
