<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200217105112 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, candidate_id INT DEFAULT NULL, oralreview SMALLINT DEFAULT NULL, testreview SMALLINT NOT NULL, INDEX IDX_136AC11391BD8781 (candidate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC11391BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('ALTER TABLE criteria DROP FOREIGN KEY FK_B61F9B8191BD8781');
        $this->addSql('DROP INDEX IDX_B61F9B8191BD8781 ON criteria');
        $this->addSql('ALTER TABLE criteria DROP candidate_id, DROP oralreview, DROP moodlereview');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE result');
        $this->addSql('ALTER TABLE criteria ADD candidate_id INT DEFAULT NULL, ADD oralreview INT DEFAULT NULL, ADD moodlereview INT NOT NULL');
        $this->addSql('ALTER TABLE criteria ADD CONSTRAINT FK_B61F9B8191BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('CREATE INDEX IDX_B61F9B8191BD8781 ON criteria (candidate_id)');
    }
}
