<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418120200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCD5E258C5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC63379586
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC67B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_67F068BC67B3B43D ON commentaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_67F068BC63379586 ON commentaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_67F068BCD5E258C5 ON commentaire
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD post_id INT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', DROP posts_id, DROP comments_id, DROP is_reply, CHANGE users_id user_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC4B89032C FOREIGN KEY (post_id) REFERENCES post (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BCA76ED395 ON commentaire (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BC4B89032C ON commentaire (post_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC4B89032C
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_67F068BCA76ED395 ON commentaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_67F068BC4B89032C ON commentaire
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD users_id INT NOT NULL, ADD posts_id INT DEFAULT NULL, ADD comments_id INT DEFAULT NULL, ADD is_reply TINYINT(1) DEFAULT NULL, DROP user_id, DROP post_id, DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCD5E258C5 FOREIGN KEY (posts_id) REFERENCES post (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC63379586 FOREIGN KEY (comments_id) REFERENCES commentaire (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC67B3B43D FOREIGN KEY (users_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BC67B3B43D ON commentaire (users_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BC63379586 ON commentaire (comments_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BCD5E258C5 ON commentaire (posts_id)
        SQL);
    }
}
