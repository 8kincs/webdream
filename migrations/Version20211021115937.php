<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211021115937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, quality_category INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE in_stock (id INT AUTO_INCREMENT NOT NULL, storage_id INT NOT NULL, product_id INT NOT NULL, capacity INT NOT NULL, stock INT NOT NULL, INDEX IDX_1C3481E5CC5DB90 (storage_id), INDEX IDX_1C3481E4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE keyboard (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, layout VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_837480954584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monitor (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, size INT NOT NULL, UNIQUE INDEX UNIQ_E11599854584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, class_name VARCHAR(255) NOT NULL, article_number VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, price INT DEFAULT NULL, INDEX IDX_D34A04AD44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ssd (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, capacity INT NOT NULL, UNIQUE INDEX UNIQ_E73B806F4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, postal_code VARCHAR(10) DEFAULT NULL, city VARCHAR(32) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE in_stock ADD CONSTRAINT FK_1C3481E5CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE in_stock ADD CONSTRAINT FK_1C3481E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE keyboard ADD CONSTRAINT FK_837480954584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE monitor ADD CONSTRAINT FK_E11599854584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE ssd ADD CONSTRAINT FK_E73B806F4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('ALTER TABLE in_stock DROP FOREIGN KEY FK_1C3481E4584665A');
        $this->addSql('ALTER TABLE keyboard DROP FOREIGN KEY FK_837480954584665A');
        $this->addSql('ALTER TABLE monitor DROP FOREIGN KEY FK_E11599854584665A');
        $this->addSql('ALTER TABLE ssd DROP FOREIGN KEY FK_E73B806F4584665A');
        $this->addSql('ALTER TABLE in_stock DROP FOREIGN KEY FK_1C3481E5CC5DB90');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE in_stock');
        $this->addSql('DROP TABLE keyboard');
        $this->addSql('DROP TABLE monitor');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE ssd');
        $this->addSql('DROP TABLE storage');
    }
}
