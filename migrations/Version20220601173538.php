<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20220601173538
 * @package DoctrineMigrations
 */
final class Version20220601173538 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(2) NOT NULL, created_at INT NOT NULL, is_active TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, updated_at INT NOT NULL, UNIQUE INDEX UNIQ_D4DB71B577153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE language');
    }
}
