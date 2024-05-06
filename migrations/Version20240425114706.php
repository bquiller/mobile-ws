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
        $this->addSql('CREATE TABLE "GRHUM"."NOTIF_GROUPE" 
          (	"ID" VARCHAR2(20 BYTE), 
           "ID_NOTIFICATION" VARCHAR2(20 BYTE), 
           "GROUPNAME" VARCHAR2(50 BYTE), 
           "STATE" VARCHAR2(20 BYTE)
           )');
        $this->addSql('CREATE TABLE "GRHUM"."NOTIF_UTILISATEUR" 
          (	"ID" VARCHAR2(20 BYTE), 
           "ID_NOTIFICATION" VARCHAR2(20 BYTE), 
           "USERNAME" VARCHAR2(20 BYTE), 
           "STATE" VARCHAR2(20 BYTE)
           )');
        $this->addSql('CREATE TABLE "GRHUM"."NOTIF_ENREGISTREMENT" 
          (	"ID" VARCHAR2(20 BYTE), 
           "USERNAME" VARCHAR2(20 BYTE), 
           "TOKEN" VARCHAR2(200 BYTE), 
           "PLATFORM" VARCHAR2(200 BYTE), 
           "IP" VARCHAR2(50 BYTE)
           )');
        $this->addSql('CREATE OR REPLACE FORCE EDITIONABLE VIEW "GRHUM"."NOTIF_GROUPES" ("LL_GROUPE") AS 
             SELECT sie.FGRA_CODE||IDIPL_ANNEE_SUIVIE ||\' \'||trim(sie.FDIP_ABREVIATION)
           from scolarite.scol_inscription_etudiant sie, scolarite.scol_formation_domaine dom, 
           scolarite.scol_formation_diplome dip, garnuche.historique his
           where sie.fann_key IN (SELECT param_value FROM garnuche.GARNUCHE_PARAMETRES WHERE param_ordre=8)
           and sie.hist_numero = his.hist_numero and (sie.res_code is null or sie.res_code <> \'Z\') and sie.idipl_type_inscription <> 3
           and dom.fdom_code = sie.FDOM_CODE and sie.fdip_code = dip.fdip_code');
        $this->addSql('CREATE OR REPLACE FORCE EDITIONABLE VIEW "GRHUM"."NOTIF_GROUPES_UTILISATEURS" ("ID", "LL_GROUPE", "CPT_LOGIN") AS 
             SELECT sie.idipl_numero, sie.FGRA_CODE||IDIPL_ANNEE_SUIVIE ||\' \'||trim(sie.FDIP_ABREVIATION),cpt.cpt_login
           from scolarite.scol_inscription_etudiant sie, individu_ulr ind, compte cpt, scolarite.scol_formation_domaine dom, 
           scolarite.scol_formation_diplome dip, garnuche.historique his
           where sie.fann_key IN (SELECT param_value FROM garnuche.GARNUCHE_PARAMETRES WHERE param_ordre=8)
           and sie.hist_numero = his.hist_numero and (sie.res_code is null or sie.res_code <> \'Z\') and sie.idipl_type_inscription <> 3
           and sie.NO_INDIVIDU = ind.NO_INDIVIDU and cpt.pers_id = ind.pers_id and cpt.cpt_vlan = \'E\'
           and dom.fdom_code = sie.FDOM_CODE and sie.fdip_code = dip.fdip_code');
           
        $this->addSql('CREATE SEQUENCE notification_id_seq 
        MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 1 CACHE 20 NOORDER  NOCYCLE');
        $this->addSql('CREATE SEQUENCE notif_groupe_id_seq 
        MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 1 CACHE 20 NOORDER  NOCYCLE');
        $this->addSql('CREATE SEQUENCE notifutilisateur_id_seq 
        MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 1 CACHE 20 NOORDER  NOCYCLE');
        $this->addSql('CREATE SEQUENCE notif_enregistrement_id_seq 
        MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 1 CACHE 20 NOORDER  NOCYCLE');


    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notification');
    }
}
