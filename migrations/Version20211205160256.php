<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211205160256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE locoe (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, date_of_birth DATE NOT NULL, gender VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
       // $this->addSql('ALTER TABLE app_account_type CHANGE id id VARCHAR(50) NOT NULL');
       // $this->addSql('ALTER TABLE app_city CHANGE id id VARCHAR(32) NOT NULL');
    //     $this->addSql('ALTER TABLE app_continent CHANGE code code VARCHAR(2) NOT NULL');
    //     $this->addSql('ALTER TABLE app_country CHANGE id id VARCHAR(2) NOT NULL');
    //     $this->addSql('ALTER TABLE app_language CHANGE code code VARCHAR(2) NOT NULL');
    //     $this->addSql('ALTER TABLE app_location_city DROP province_id, DROP country_id, CHANGE id id VARCHAR(50) NOT NULL');
    //   //  $this->addSql('ALTER TABLE app_location_province CHANGE id id VARCHAR(50) NOT NULL, CHANGE country_id country_id VARCHAR(2) DEFAULT NULL');
    //    // $this->addSql('ALTER TABLE app_location_province ADD CONSTRAINT FK_3ACA5B76F92F3E70 FOREIGN KEY (country_id) REFERENCES app_country (id)');
    //     $this->addSql('CREATE INDEX IDX_3ACA5B76F92F3E70 ON app_location_province (country_id)');
    //     $this->addSql('ALTER TABLE app_user_notification CHANGE id id VARCHAR(60) NOT NULL');
    //     $this->addSql('ALTER TABLE user_admin CHANGE id id VARCHAR(50) NOT NULL');
    //     $this->addSql('ALTER TABLE user_chat CHANGE id id VARCHAR(100) NOT NULL');
    //     $this->addSql('ALTER TABLE user_fraud_alert ADD addedby VARCHAR(100) NOT NULL, ADD last_seen_location VARCHAR(100) NOT NULL, DROP addedby_id, DROP location_id, CHANGE id id VARCHAR(60) NOT NULL, CHANGE gender gender INT NOT NULL, CHANGE state state INT NOT NULL');
    //     $this->addSql('ALTER TABLE user_notification CHANGE id id VARCHAR(60) NOT NULL');
    //     $this->addSql('ALTER TABLE user_otp CHANGE id id VARCHAR(60) NOT NULL');
    //     $this->addSql('ALTER TABLE user_person_found ADD addedby VARCHAR(100) NOT NULL, ADD found_location VARCHAR(100) NOT NULL, DROP addedby_id, DROP location_id, CHANGE id id VARCHAR(60) NOT NULL, CHANGE gender gender INT NOT NULL, CHANGE state state INT NOT NULL');
    //     $this->addSql('ALTER TABLE user_person_missing ADD last_seen_location VARCHAR(100) NOT NULL, CHANGE id id VARCHAR(60) NOT NULL, CHANGE gender gender INT NOT NULL, CHANGE location_id location_id VARCHAR(50) DEFAULT NULL, CHANGE state state INT NOT NULL, CHANGE addedby_id addedby VARCHAR(100) NOT NULL');
    //     $this->addSql('ALTER TABLE user_person_missing ADD CONSTRAINT FK_B9BB170964D218E FOREIGN KEY (location_id) REFERENCES app_location_city (id)');
    //     $this->addSql('CREATE INDEX IDX_B9BB170964D218E ON user_person_missing (location_id)');
    //     $this->addSql('ALTER TABLE user_person_wanted CHANGE id id VARCHAR(60) NOT NULL, CHANGE addedby_id addedby_id VARCHAR(50) DEFAULT NULL, CHANGE gender gender VARCHAR(255) NOT NULL, CHANGE race race VARCHAR(10) DEFAULT NULL, CHANGE location_id location_id VARCHAR(50) NOT NULL, CHANGE state state INT NOT NULL');
    //     $this->addSql('ALTER TABLE user_person_wanted ADD CONSTRAINT FK_ABC825DE64D218E FOREIGN KEY (location_id) REFERENCES app_location_city (id)');
    //     $this->addSql('ALTER TABLE user_person_wanted ADD CONSTRAINT FK_ABC825DEAEAF80C6 FOREIGN KEY (addedby_id) REFERENCES user_admin (id)');
    //     $this->addSql('CREATE INDEX IDX_ABC825DE64D218E ON user_person_wanted (location_id)');
    //     $this->addSql('CREATE INDEX IDX_ABC825DEAEAF80C6 ON user_person_wanted (addedby_id)');
    //     $this->addSql('ALTER TABLE user_pet_found ADD addedby VARCHAR(100) NOT NULL, ADD found_location VARCHAR(100) NOT NULL, DROP addedby_id, DROP location_id, CHANGE id id VARCHAR(100) NOT NULL, CHANGE state state INT NOT NULL');
    //     $this->addSql('ALTER TABLE user_pet_missing ADD last_seen_location VARCHAR(100) NOT NULL, CHANGE id id VARCHAR(100) NOT NULL, CHANGE location_id location_id VARCHAR(50) DEFAULT NULL, CHANGE state state INT NOT NULL, CHANGE addedby_id addedby VARCHAR(100) NOT NULL');
    //     $this->addSql('ALTER TABLE user_pet_missing ADD CONSTRAINT FK_47F532AA64D218E FOREIGN KEY (location_id) REFERENCES app_location_city (id)');
    //     $this->addSql('CREATE INDEX IDX_47F532AA64D218E ON user_pet_missing (location_id)');
    //     $this->addSql('ALTER TABLE user_property_found ADD addedby VARCHAR(100) NOT NULL, ADD found_location VARCHAR(100) NOT NULL, DROP addedby_id, DROP location_id, CHANGE id id VARCHAR(100) NOT NULL, CHANGE state state INT NOT NULL');
    //     $this->addSql('ALTER TABLE user_property_missing ADD addedby VARCHAR(100) NOT NULL, ADD last_seen_location VARCHAR(100) NOT NULL, DROP addedby_id, DROP location_id, CHANGE id id VARCHAR(100) NOT NULL, CHANGE state state INT NOT NULL');
    //     $this->addSql('ALTER TABLE user_report CHANGE id id VARCHAR(60) NOT NULL');
    //     $this->addSql('ALTER TABLE user_review CHANGE id id VARCHAR(100) NOT NULL');
    //     $this->addSql('ALTER TABLE user_ride_alert CHANGE id id VARCHAR(60) NOT NULL');
    //     $this->addSql('ALTER TABLE user_search CHANGE id id VARCHAR(60) NOT NULL');
    //     $this->addSql('ALTER TABLE user_setting CHANGE id id VARCHAR(60) NOT NULL');
    //     $this->addSql('ALTER TABLE user_wallet CHANGE id id VARCHAR(60) NOT NULL');
    //     $this->addSql('ALTER TABLE user_watch_list CHANGE id id VARCHAR(60) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE locoe');
        $this->addSql('ALTER TABLE app_account_type CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE app_city CHANGE id id VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_continent CHANGE code code VARCHAR(2) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_country CHANGE id id VARCHAR(2) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_language CHANGE code code VARCHAR(2) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_location_city ADD province_id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, ADD country_id VARCHAR(60) CHARACTER SET latin1 DEFAULT \'ZA\' NOT NULL COLLATE `latin1_swedish_ci`, CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE app_location_province DROP FOREIGN KEY FK_3ACA5B76F92F3E70');
        $this->addSql('DROP INDEX IDX_3ACA5B76F92F3E70 ON app_location_province');
        $this->addSql('ALTER TABLE app_location_province CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, CHANGE country_id country_id VARCHAR(60) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE app_user_notification CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_admin CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE user_chat CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_fraud_alert ADD addedby_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ADD location_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, DROP addedby, DROP last_seen_location, CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE gender gender VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE state state VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_notification CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_otp CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user_person_found ADD addedby_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ADD location_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, DROP addedby, DROP found_location, CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE gender gender VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE state state VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_person_missing DROP FOREIGN KEY FK_B9BB170964D218E');
        $this->addSql('DROP INDEX IDX_B9BB170964D218E ON user_person_missing');
        $this->addSql('ALTER TABLE user_person_missing ADD addedby_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, DROP addedby, DROP last_seen_location, CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE location_id location_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE gender gender VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE state state VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_person_wanted DROP FOREIGN KEY FK_ABC825DE64D218E');
        $this->addSql('ALTER TABLE user_person_wanted DROP FOREIGN KEY FK_ABC825DEAEAF80C6');
        $this->addSql('DROP INDEX IDX_ABC825DE64D218E ON user_person_wanted');
        $this->addSql('DROP INDEX IDX_ABC825DEAEAF80C6 ON user_person_wanted');
        $this->addSql('ALTER TABLE user_person_wanted CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE location_id location_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE addedby_id addedby_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE gender gender VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE state state VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE race race VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_pet_found ADD addedby_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ADD location_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, DROP addedby, DROP found_location, CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE state state VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_pet_missing DROP FOREIGN KEY FK_47F532AA64D218E');
        $this->addSql('DROP INDEX IDX_47F532AA64D218E ON user_pet_missing');
        $this->addSql('ALTER TABLE user_pet_missing ADD addedby_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, DROP addedby, DROP last_seen_location, CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE location_id location_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE state state VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_property_found ADD addedby_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ADD location_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, DROP addedby, DROP found_location, CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE state state VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_property_missing ADD addedby_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ADD location_id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, DROP addedby, DROP last_seen_location, CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE state state VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_report CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user_review CHANGE id id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_ride_alert CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_search CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_setting CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user_wallet CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_watch_list CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
    }
}
