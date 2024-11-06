<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211005063625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE app_offense');
        $this->addSql('DROP TABLE app_package');
        $this->addSql('DROP TABLE app_prom_setting');
        $this->addSql('DROP TABLE app_subscription');
        $this->addSql('DROP TABLE user_availablility');
        $this->addSql('DROP TABLE user_connection');
        $this->addSql('DROP TABLE user_photo');
        $this->addSql('DROP TABLE user_rate');
        $this->addSql('DROP TABLE user_request');
        $this->addSql('DROP TABLE user_save');
        $this->addSql('ALTER TABLE app_account_type CHANGE id id VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE app_city CHANGE id id VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE app_continent CHANGE code code VARCHAR(2) NOT NULL, CHANGE name name VARCHAR(15) NOT NULL');
        $this->addSql('ALTER TABLE app_country CHANGE id id VARCHAR(2) NOT NULL, CHANGE name name VARCHAR(50) NOT NULL, CHANGE native native VARCHAR(50) NOT NULL, CHANGE phone phone VARCHAR(15) NOT NULL, CHANGE continent continent VARCHAR(2) NOT NULL, CHANGE capital capital VARCHAR(50) NOT NULL, CHANGE currency currency VARCHAR(30) NOT NULL, CHANGE languages languages VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE app_language CHANGE code code VARCHAR(2) NOT NULL, CHANGE name name VARCHAR(50) NOT NULL, CHANGE native native VARCHAR(50) NOT NULL, CHANGE rtl rtl TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE app_location_city CHANGE id id VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE app_location_province CHANGE id id VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE app_settings CHANGE name name VARCHAR(150) NOT NULL, CHANGE default_value default_value INT UNSIGNED DEFAULT NULL, CHANGE custom_value custom_value CHAR(32) NOT NULL');
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
        $this->addSql('CREATE TABLE app_offense (id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, oaction INT NOT NULL, date_modified DATETIME NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE app_package (ID INT AUTO_INCREMENT NOT NULL, title MEDIUMTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, credits INT NOT NULL, color MEDIUMTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE app_prom_setting (id INT AUTO_INCREMENT NOT NULL, app_api_key VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, publisher_id VARCHAR(500) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, interstital_ad VARCHAR(500) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, interstital_ad_id VARCHAR(500) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, interstital_ad_click VARCHAR(500) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, banner_ad VARCHAR(500) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, banner_ad_id VARCHAR(500) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, facebook_interstital_ad VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, facebook_interstital_ad_id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, facebook_interstital_ad_click VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, facebook_banner_ad VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, facebook_banner_ad_id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, facebook_native_ad VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, facebook_native_ad_id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, facebook_native_ad_click VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, admob_native_ad VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, admob_native_ad_id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, admob_native_ad_click VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, banner_size VARCHAR(255) CHARACTER SET utf8 DEFAULT \'SMART_BANNER\' NOT NULL COLLATE `utf8_general_ci`, banner_size_fb VARCHAR(255) CHARACTER SET utf8 DEFAULT \'BANNER_HEIGHT_90\' NOT NULL COLLATE `utf8_general_ci`, google_login VARCHAR(255) CHARACTER SET utf8 DEFAULT \'true\' NOT NULL COLLATE `utf8_general_ci`, facebook_login VARCHAR(255) CHARACTER SET utf8 DEFAULT \'true\' NOT NULL COLLATE `utf8_general_ci`, currency_code VARCHAR(2) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, currency_position VARCHAR(2) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, ad_promote VARCHAR(2) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, auto_post VARCHAR(10) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, isRTL VARCHAR(2) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, paypal_payment_on_off VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, paypal_mode VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, paypal_client_id VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, paypal_secret VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, company VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, email VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, website VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, contact VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, google_sender_id VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, google_api_key TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, bulk_sms_username VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, bulk_sms_token VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, app_description TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, app_link VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, new_app_version_name VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, new_app_version_code VARCHAR(60) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, payfast_merchant_id VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, payfast_merchant_key VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, payfast_merchant_mode VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, payfast_merchant_url_sandbox VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, payfast_merchant_url VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, date_modified DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, date_created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE app_subscription (id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, discount DOUBLE PRECISION NOT NULL, amount INT NOT NULL, state INT NOT NULL, date_modified DATETIME NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_availablility (ID INT AUTO_INCREMENT NOT NULL, accountId INT NOT NULL, day MEDIUMTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, start_time MEDIUMTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, end_time MEDIUMTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_connection (id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, addedby VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, connections TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, date_created DATETIME NOT NULL, date_modified DATETIME NOT NULL, UNIQUE INDEX addedby (addedby), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_photo (id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, addedby VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, photos TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date_created DATE NOT NULL, date_modified DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_rate (id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, addedby VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, rates TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, date_modified DATETIME NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_request (id VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, requestor VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, artisan VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, data TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, state INT NOT NULL, date_modified DATETIME NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_save (id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, addedby VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, saves TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, date_created DATETIME NOT NULL, date_modified DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('ALTER TABLE app_account_type CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE app_city CHANGE id id VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_continent CHANGE code code VARCHAR(2) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE name name VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_country CHANGE id id VARCHAR(2) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE name name VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE native native VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE phone phone VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE continent continent VARCHAR(2) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE capital capital VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE currency currency VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE languages languages VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_language CHANGE code code VARCHAR(2) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE name name VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE native native VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE rtl rtl TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE app_location_city CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE app_location_province CHANGE id id VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE app_settings CHANGE name name VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`, CHANGE default_value default_value INT UNSIGNED DEFAULT 0, CHANGE custom_value custom_value CHAR(32) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE app_user_notification CHANGE id id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user_admin CHANGE id id VARCHAR(50) CHARACTER SET latin1 DEFAULT \'\' NOT NULL COLLATE `latin1_swedish_ci`');
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
