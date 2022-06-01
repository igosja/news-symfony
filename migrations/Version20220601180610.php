<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220601180610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, created_by_id INT NOT NULL, image_id INT DEFAULT NULL, updated_by_id INT NOT NULL, created_at INT NOT NULL, is_active TINYINT(1) DEFAULT 0 NOT NULL, name VARCHAR(255) NOT NULL, translation_text JSON NOT NULL, translation_title JSON NOT NULL, updated_at INT NOT NULL, url VARCHAR(255) NOT NULL, views INT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_5A8A6C8DF47645AE (url), INDEX IDX_5A8A6C8D12469DE2 (category_id), INDEX IDX_5A8A6C8DB03A8386 (created_by_id), UNIQUE INDEX UNIQ_5A8A6C8D3DA5256D (image_id), INDEX IDX_5A8A6C8D896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE category CHANGE is_active is_active TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE post');
        $this->addSql('ALTER TABLE category CHANGE is_active is_active TINYINT(1) NOT NULL');
    }
}
