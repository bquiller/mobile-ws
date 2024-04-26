<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230618170106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "GRHUM"."NOTIFICATION" 
        (	"ID" NUMBER(10,0) NOT NULL ENABLE, 
         "TITLE" VARCHAR2(100 BYTE) NOT NULL ENABLE, 
         "CHANNEL" VARCHAR2(100 BYTE) NOT NULL ENABLE, 
         "MESSAGE" CLOB NOT NULL ENABLE, 
         "DATE_CREATION" DATE, 
         "LIEN" VARCHAR2(100 BYTE), 
         "AUTHOR" VARCHAR2(100 BYTE), 
          PRIMARY KEY ("ID")
       USING INDEX PCTFREE 10 INITRANS 2 MAXTRANS 255 
       STORAGE(INITIAL 65536 NEXT 1048576 MINEXTENTS 1 MAXEXTENTS 2147483645
       PCTINCREASE 0 FREELISTS 1 FREELIST GROUPS 1
       BUFFER_POOL DEFAULT FLASH_CACHE DEFAULT CELL_FLASH_CACHE DEFAULT)
       TABLESPACE "DATA_GRHUM"  ENABLE
        )');
        $this->addSql('CREATE TABLE "GRHUM"."NOTIF_UTILISATEUR" 
          (	"ID" VARCHAR2(20 BYTE), 
           "ID_NOTIFICATION" VARCHAR2(20 BYTE), 
           "USERNAME" VARCHAR2(20 BYTE), 
           "STATE" VARCHAR2(20 BYTE)
           )');
        $this->addSql('CREATE SEQUENCE notification_id_seq 
        MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 161 CACHE 20 NOORDER  NOCYCLE');
        $this->addSql('CREATE SEQUENCE notifutilisateur_id_seq 
        MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 161 CACHE 20 NOORDER  NOCYCLE');


    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notification');
    }
}
