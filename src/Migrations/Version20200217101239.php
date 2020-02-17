<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200217101239 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE teacher (id INT AUTO_INCREMENT NOT NULL, fullname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, accessnumber INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE criteria (id INT AUTO_INCREMENT NOT NULL, quiz_id INT DEFAULT NULL, candidate_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, oralreview INT DEFAULT NULL, moodlereview INT NOT NULL, INDEX IDX_B61F9B81853CD175 (quiz_id), INDEX IDX_B61F9B8191BD8781 (candidate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, teacher_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_A412FA9241807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidate (id INT AUTO_INCREMENT NOT NULL, teacher_id INT DEFAULT NULL, fullname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_C8B28E4441807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE criteria ADD CONSTRAINT FK_B61F9B81853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE criteria ADD CONSTRAINT FK_B61F9B8191BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9241807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E4441807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9241807E1D');
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E4441807E1D');
        $this->addSql('ALTER TABLE criteria DROP FOREIGN KEY FK_B61F9B81853CD175');
        $this->addSql('ALTER TABLE criteria DROP FOREIGN KEY FK_B61F9B8191BD8781');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('DROP TABLE criteria');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE candidate');
    }
}
