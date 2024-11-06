<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211005131453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, task VARCHAR(255) NOT NULL, due_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_account_type CHANGE id id VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE app_city CHANGE id id VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE app_continent CHANGE code code VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE app_country CHANGE id id VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE app_language CHANGE code code VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE app_location_city CHANGE id id VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE app_location_province CHANGE id id VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE app_user_notification CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_admin CHANGE id id VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE user_chat CHANGE id id VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user_fraud_alert CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_notification CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_otp CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_person_found CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_person_missing CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_person_wanted CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_pet_found CHANGE id id VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user_pet_missing CHANGE id id VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user_property_found CHANGE id id VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user_property_missing CHANGE id id VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user_report CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_review CHANGE id id VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user_ride_alert CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_search CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_setting CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_wallet CHANGE id id VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE user_watch_list CHANGE id id VARCHAR(60) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE task');
        $this->addSql('ALTER TABLE app_account_type CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE app_city CHANGE id id VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_continent CHANGE code code VARCHAR(2) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_country CHANGE id id VARCHAR(2) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_language CHANGE code code VARCHAR(2) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_location_city CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE app_location_province CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE app_user_notification CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_admin CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE user_chat CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_fraud_alert CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_notification CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_otp CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user_person_found CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_person_missing CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_person_wanted CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_pet_found CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_pet_missing CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_property_found CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_property_missing CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_report CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user_review CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_ride_alert CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_search CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_setting CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user_wallet CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_watch_list CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
    }
}
