<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220111130417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE todo_list (id INT AUTO_INCREMENT NOT NULL, parent_task_id INT DEFAULT NULL, user_key VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, priority INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, done_at DATE DEFAULT NULL, created_at DATE NOT NULL, INDEX IDX_1B199E07FFFE75C0 (parent_task_id), FULLTEXT INDEX IDX_1B199E072B36786B6DE44026 (title, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE todo_list ADD CONSTRAINT FK_1B199E07FFFE75C0 FOREIGN KEY (parent_task_id) REFERENCES todo_list (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todo_list DROP FOREIGN KEY FK_1B199E07FFFE75C0');
        $this->addSql('DROP TABLE todo_list');
    }
}
