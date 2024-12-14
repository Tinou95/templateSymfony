<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241214190545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE serie_id_seq CASCADE');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CBF2AF943');
        $this->addSql('ALTER TABLE comment ADD media_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CBF2AF943 FOREIGN KEY (parent_comment_id) REFERENCES comment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9474526CEA9FDD75 ON comment (media_id)');
        $this->addSql('ALTER TABLE episode DROP CONSTRAINT fk_ddaa1cda68756988');
        $this->addSql('DROP INDEX idx_ddaa1cda68756988');
        $this->addSql('ALTER TABLE episode ADD season_id INT NOT NULL');
        $this->addSql('ALTER TABLE episode DROP season_id_id');
        $this->addSql('ALTER TABLE episode ALTER released_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN episode.released_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDA4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DDAA1CDA4EC001D1 ON episode (season_id)');
        $this->addSql('ALTER TABLE playlist_subscription ADD playlist_id INT NOT NULL');
        $this->addSql('ALTER TABLE playlist_subscription ADD subscriber_id INT NOT NULL');
        $this->addSql('ALTER TABLE playlist_subscription ADD CONSTRAINT FK_832940C6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE playlist_subscription ADD CONSTRAINT FK_832940C7808B1AD FOREIGN KEY (subscriber_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_832940C6BBD148 ON playlist_subscription (playlist_id)');
        $this->addSql('CREATE INDEX IDX_832940C7808B1AD ON playlist_subscription (subscriber_id)');
        $this->addSql('ALTER TABLE season DROP CONSTRAINT fk_f0e45ba9b748aac3');
        $this->addSql('DROP INDEX idx_f0e45ba9b748aac3');
        $this->addSql('ALTER TABLE season ADD number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE season DROP serie_id_id');
        $this->addSql('ALTER TABLE season RENAME COLUMN season_number TO serie_id');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F0E45BA9D94388BD ON season (serie_id)');
        $this->addSql('ALTER TABLE serie ADD CONSTRAINT FK_AA3A9334BF396750 FOREIGN KEY (id) REFERENCES media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE watch_history ADD watcher_id INT NOT NULL');
        $this->addSql('ALTER TABLE watch_history ADD media_id INT NOT NULL');
        $this->addSql('ALTER TABLE watch_history ADD CONSTRAINT FK_DE44EFD8C300AB5D FOREIGN KEY (watcher_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE watch_history ADD CONSTRAINT FK_DE44EFD8EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DE44EFD8C300AB5D ON watch_history (watcher_id)');
        $this->addSql('CREATE INDEX IDX_DE44EFD8EA9FDD75 ON watch_history (media_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE serie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE playlist_subscription DROP CONSTRAINT FK_832940C6BBD148');
        $this->addSql('ALTER TABLE playlist_subscription DROP CONSTRAINT FK_832940C7808B1AD');
        $this->addSql('DROP INDEX IDX_832940C6BBD148');
        $this->addSql('DROP INDEX IDX_832940C7808B1AD');
        $this->addSql('ALTER TABLE playlist_subscription DROP playlist_id');
        $this->addSql('ALTER TABLE playlist_subscription DROP subscriber_id');
        $this->addSql('ALTER TABLE watch_history DROP CONSTRAINT FK_DE44EFD8C300AB5D');
        $this->addSql('ALTER TABLE watch_history DROP CONSTRAINT FK_DE44EFD8EA9FDD75');
        $this->addSql('DROP INDEX IDX_DE44EFD8C300AB5D');
        $this->addSql('DROP INDEX IDX_DE44EFD8EA9FDD75');
        $this->addSql('ALTER TABLE watch_history DROP watcher_id');
        $this->addSql('ALTER TABLE watch_history DROP media_id');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CEA9FDD75');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526cbf2af943');
        $this->addSql('DROP INDEX IDX_9474526CEA9FDD75');
        $this->addSql('ALTER TABLE comment DROP media_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526cbf2af943 FOREIGN KEY (parent_comment_id) REFERENCES comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE season DROP CONSTRAINT FK_F0E45BA9D94388BD');
        $this->addSql('DROP INDEX IDX_F0E45BA9D94388BD');
        $this->addSql('ALTER TABLE season ADD serie_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE season DROP number');
        $this->addSql('ALTER TABLE season RENAME COLUMN serie_id TO season_number');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT fk_f0e45ba9b748aac3 FOREIGN KEY (serie_id_id) REFERENCES serie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f0e45ba9b748aac3 ON season (serie_id_id)');
        $this->addSql('ALTER TABLE episode DROP CONSTRAINT FK_DDAA1CDA4EC001D1');
        $this->addSql('DROP INDEX IDX_DDAA1CDA4EC001D1');
        $this->addSql('ALTER TABLE episode ADD season_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE episode DROP season_id');
        $this->addSql('ALTER TABLE episode ALTER released_at TYPE DATE');
        $this->addSql('COMMENT ON COLUMN episode.released_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT fk_ddaa1cda68756988 FOREIGN KEY (season_id_id) REFERENCES season (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_ddaa1cda68756988 ON episode (season_id_id)');
        $this->addSql('ALTER TABLE serie DROP CONSTRAINT FK_AA3A9334BF396750');
    }
}
