<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221018195003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE characteristic (id INT AUTO_INCREMENT NOT NULL, updated_by_id INT DEFAULT NULL, min_age INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_522FA950896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristic_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_BE6D18FD2C2AC5D3 (translatable_id), UNIQUE INDEX characteristic_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE child (id INT AUTO_INCREMENT NOT NULL, family_id INT NOT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, height INT NOT NULL, birthday DATETIME NOT NULL, gender VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_22B35429C35E566A (family_id), UNIQUE INDEX UNIQ_22B35429896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE child_characteristic (child_id INT NOT NULL, characteristic_id INT NOT NULL, INDEX IDX_A0957839DD62C21B (child_id), INDEX IDX_A0957839DEE9D12B (characteristic_id), PRIMARY KEY(child_id, characteristic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE family (id INT AUTO_INCREMENT NOT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A5E6215B896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, locale VARCHAR(4) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64964D218E (location_id), UNIQUE INDEX UNIQ_8D93D649896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_family (user_id INT NOT NULL, family_id INT NOT NULL, INDEX IDX_C0B43A66A76ED395 (user_id), INDEX IDX_C0B43A66C35E566A (family_id), PRIMARY KEY(user_id, family_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE characteristic ADD CONSTRAINT FK_522FA950896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE characteristic_translation ADD CONSTRAINT FK_BE6D18FD2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES characteristic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B35429C35E566A FOREIGN KEY (family_id) REFERENCES family (id)');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B35429896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE child_characteristic ADD CONSTRAINT FK_A0957839DD62C21B FOREIGN KEY (child_id) REFERENCES child (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE child_characteristic ADD CONSTRAINT FK_A0957839DEE9D12B FOREIGN KEY (characteristic_id) REFERENCES characteristic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE family ADD CONSTRAINT FK_A5E6215B896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64964D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_family ADD CONSTRAINT FK_C0B43A66A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_family ADD CONSTRAINT FK_C0B43A66C35E566A FOREIGN KEY (family_id) REFERENCES family (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE characteristic DROP FOREIGN KEY FK_522FA950896DBBDE');
        $this->addSql('ALTER TABLE characteristic_translation DROP FOREIGN KEY FK_BE6D18FD2C2AC5D3');
        $this->addSql('ALTER TABLE child DROP FOREIGN KEY FK_22B35429C35E566A');
        $this->addSql('ALTER TABLE child DROP FOREIGN KEY FK_22B35429896DBBDE');
        $this->addSql('ALTER TABLE child_characteristic DROP FOREIGN KEY FK_A0957839DD62C21B');
        $this->addSql('ALTER TABLE child_characteristic DROP FOREIGN KEY FK_A0957839DEE9D12B');
        $this->addSql('ALTER TABLE family DROP FOREIGN KEY FK_A5E6215B896DBBDE');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64964D218E');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649896DBBDE');
        $this->addSql('ALTER TABLE user_family DROP FOREIGN KEY FK_C0B43A66A76ED395');
        $this->addSql('ALTER TABLE user_family DROP FOREIGN KEY FK_C0B43A66C35E566A');
        $this->addSql('DROP TABLE characteristic');
        $this->addSql('DROP TABLE characteristic_translation');
        $this->addSql('DROP TABLE child');
        $this->addSql('DROP TABLE child_characteristic');
        $this->addSql('DROP TABLE family');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_family');
    }
}
