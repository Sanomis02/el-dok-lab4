DROP DATABASE IF EXISTS `animals`;
CREATE DATABASE  IF NOT EXISTS `animals`
    /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `animals`;
-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: localhost    Database: animals
-- ------------------------------------------------------
-- Server version	5.7.19

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Database: `animals`
--

DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER NOT NULL PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    role VARCHAR(50) NOT NULL,
    password VARCHAR(255) NULL
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
     (1,'Useris 1','userio1@gmail.com','admin','passwordas1'),
     (2,'Useris 2','userio2@gmail.com','member','passwordas2'),
     (3,'Useris 3','userio3@gmail.com','guest','passwordas3');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Sukurta startiniai duomenys lentelei `users`
--




DROP TABLE IF EXISTS slim_tasks;

CREATE TABLE `slim_tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `details` varchar(45) DEFAULT NULL,
  `author` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sukurta duomenų kopija lentelei `data_types`
--
LOCK TABLES `slim_tasks` WRITE;
/*!40000 ALTER TABLE `slim_tasks` DISABLE KEYS */;
INSERT INTO `slim_tasks` VALUES
     (1,'vardas 1','detalės 1','autorius 1'),
     (2,'vardas 2','detalės 2','autorius 2');
/*!40000 ALTER TABLE `slim_tasks` ENABLE KEYS */;
UNLOCK TABLES;



DROP TABLE IF EXISTS Rusys;
DROP TABLE IF EXISTS Zinutes;
DROP TABLE IF EXISTS Skelbimai;
DROP TABLE IF EXISTS Anketos;
DROP TABLE IF EXISTS gyvunu_nuotraukos;
DROP TABLE IF EXISTS Vartotojai;

CREATE TABLE Vartotojai
(
    id int (10) NOT NULL AUTO_INCREMENT,
    ar_gali_skelbt boolean NOT NULL,
    vardas varchar (30) NULL,
    pastas varchar (40) NULL,
    slaptazodis varchar (256) NULL,
    prieiga char (18) NOT NULL,
    PRIMARY KEY(id),
    CHECK(prieiga in ('Neprisiregistraves', 'Kontrolierius', 'Prisiregistraves', 'Administratorius'))
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sukurta duomenų kopija lentelei `Vartotojai`
--
LOCK TABLES `Vartotojai` WRITE;
/*!40000 ALTER TABLE `Vartotojai` DISABLE KEYS */;
INSERT INTO `Vartotojai` VALUES
     (1,1,'vartotojo vardas_1','n1@com','123', 'Neprisiregistraves'),
     (2,1,'vartotojo vardas_2','k2@com','123', 'Kontrolierius'),
     (3,1,'vartotojo vardas_3','p3@com','123', 'Prisiregistraves'),
     (4,1,'vartotojo vardas_4','a4@com','123', 'Administratorius');
/*!40000 ALTER TABLE `Vartotojai` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE Rusys
(
    id int (10) NOT NULL AUTO_INCREMENT,
    pavadinimas varchar (256) NOT NULL,
    santraupa varchar (12) NOT NULL COMMENT 'santraupa mažosioms angl raidėms iki 12 simb, naudosim nuotraukos pavadinime',
    PRIMARY KEY(id)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sukurta duomenų kopija lentelei `Rusys`
--
LOCK TABLES `Rusys` WRITE;
/*!40000 ALTER TABLE `Rusys` DISABLE KEYS */;
INSERT INTO `Rusys` VALUES
     (1,'katė','kate'),
     (2,'šuo','suo'),
     (3,'Žiurkėnas','ziurkenas'),
     (4,'Vėžlys','vezlys'),
     (5,'Ožka','ozka'),
     (6,'Panda','panda'),
     (7,'Tigras','tigras'),
     (8,'Leopardas','Leopardas');
/*!40000 ALTER TABLE `Rusys` ENABLE KEYS */;
UNLOCK TABLES;



CREATE TABLE Anketos
(
    id int (10) NOT NULL AUTO_INCREMENT,
    gyvuno_amzius int (3) NOT NULL,
    ar_Rastas boolean NOT NULL default 0,
    autorius_id int (10) NOT NULL,
    pagr_nuotraukos_id int(10) null,
    miestas varchar (30) NOT NULL,
    rajonas varchar (30) NULL,
    gatve varchar (30) NULL,
    gyvuno_vardas varchar (255) NOT NULL,
    lytis char (11) NOT NULL,
    apskritis char (11) NOT NULL,
    fk_Naudotojasid int (10) NOT NULL,
    fk_rusies_id int (10) NOT NULL,
    aprasymas text NULL,
    dingimo_data datetime NULL,
    PRIMARY KEY(id),
    KEY (pagr_nuotraukos_id),
    CHECK(lytis in ('vyras', 'moteris', 'neapibrezta')),
    CHECK(apskritis in ('Kaunas', 'Alytus', 'Klaipeda', 'Marijampole', 'Panevezys', 'Siauliai', 'Taurage', 'Telsiai', 'Utena', 'Vilnius')),
    CONSTRAINT uzpildo FOREIGN KEY(fk_Naudotojasid) REFERENCES Vartotojai (id),
    CONSTRAINT pagrindine_nuotrauka FOREIGN KEY(pagr_nuotraukos_id) REFERENCES gyvunu_nuotraukos (id),
    CONSTRAINT gyvuno_rusis FOREIGN KEY(fk_rusies_id) REFERENCES Rusys (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sukurta duomenų kopija lentelei `Anketos`
--
LOCK TABLES `Anketos` WRITE;
/*!40000 ALTER TABLE `Anketos` DISABLE KEYS */;
INSERT INTO `Anketos` VALUES
     (1,4,0,2,1,'Kaunas',null,'Basanaviciaus 2','Micka','vyras','Kaunas',3,1,'dėmė po kaklu',DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i')),
     (2,3,0,1,3,'Kaunas',null,'Basanaviciaus 6','Pilkis','vyras','Kaunas',2,2,'kanda atsargiai',DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i')),
     (3,8,0,3,6,'Kaunas',null,'Basanaviciaus 10','Riteris','vyras','Marijampole',2,4,'dėmė po krūtine',DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i')),
     (4,10,0,2,7,'Kaunas',null,'Basanaviciaus 11','Smigius','vyras','Siauliai', 1,3,'ilgai miega',DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i')),
     (5,6,0,2,null,'Kaunas',null,'Basanaviciaus 11','Inkognito','vyras','Siauliai', 1,3,'mėgsta slėptis',DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i')),
     (6,7,0,2,8,'Kaunas',null,'Basanaviciaus 17','Tešlė','moteris','klaipėda',3,6,'Mėgsta valgyti',DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i')),
     (7,2,0,3,11,'Kaunas',null,'Basanaviciaus 77','Pūkė','moteris','Panevezys',2,6,'Labai smalsi mėgsta bendrauti',DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i'));
/*!40000 ALTER TABLE `Anketos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Sukurta duomenų struktūra lentelei `gyvunu_nuotraukos`
--
CREATE TABLE gyvunu_nuotraukos (
                                    id int(10) NOT NULL AUTO_INCREMENT,
                                    anketos_id int(10) NOT NULL,
                                    nuotraukos_pav varchar(255) NOT NULL COMMENT 'kate_1_2023-11-08_18-52-36.jpg iš rūšies santraupos anketos id ir kėlimo datos bei laiko',
                                    ar_rodyti tinyint(1) NOT NULL DEFAULT 1,
                                    PRIMARY KEY (id),
                                    UNIQUE KEY nuotraukos_pavadinimas (nuotraukos_pav),
                                    CONSTRAINT susiejimas_anketos FOREIGN KEY (anketos_id) REFERENCES Anketos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `gyvunu_nuotraukos`
--
LOCK TABLES `gyvunu_nuotraukos` WRITE;
/*!40000 ALTER TABLE `gyvunu_nuotraukos` DISABLE KEYS */;
INSERT INTO `gyvunu_nuotraukos` VALUES
                                   (1,1,'kate_1_2023-11-08_18-52-36.jpg',1),
                                   (2,1,'kate_1_2023-11-08_18-53-38.jpg',1),
                                   (3,2,'suo_2_2023-11-08_20-12-05.jpg',1),
                                   (4,2,'suo_2_2023-11-08_20-18-15.jpg',1),
                                   (5,2,'suo_2_2023-11-09_07-55-37.png',1),
                                   (6,3,'vezlys_3_2023-10-05_05-22-06.jpg',1),
                                   (7,4,'ziurkenas_4_2023-06-29_09-02-45.png',1),
                                   (8,6,'panda_6_2023-09-15_09-02-45.jpg',1),
                                   (9,6,'panda_6_2023-10-15_09-02-45.jpg',1),
                                   (10,6,'panda_6_2023-11-15_09-02-45.jpg',1),
                                   (11,7,'suo_7_2023-05-15_09-02-45.jpg',1),
                                   (12,7,'suo_7_2023-06-15_09-02-45.jpg',1),
                                   (13,7,'suo_7_2023-07-15_09-02-45.png',1),
                                   (14,7,'suo_7_2023-08-15_09-02-45.jpg',1),
                                   (15,7,'suo_7_2023-09-15_09-02-45.png',1);
/*!40000 ALTER TABLE `gyvunu_nuotraukos` ENABLE KEYS */;
UNLOCK TABLES;





CREATE TABLE Skelbimai
(
    id int (16) NOT NULL AUTO_INCREMENT,
    anketa_id int (10) NOT NULL,
    autorius_id int (10) NOT NULL COMMENT 'pradubliuojam autorių iš anketos - jo daug kur reikės - rašant žinutes, kad būt greitesnis atrinkimas',
    rusies_id int (10) NOT NULL COMMENT 'pradubliuojam gyvuno rusi iš anketos - jos reiks skelbimu filtre - greitesnis atrinkimas',
    ar_aktyvus boolean NOT NULL default 1,
    skelbimo_data datetime NULL comment 'skelbimo data',
    galiojimo_laikas datetime NULL comment 'data iki kurios galioja skelbimas',
    perziuros_kiekis int (24) NOT NULL,
    aprasymas text NULL,
    PRIMARY KEY(id),
    CONSTRAINT sudaro FOREIGN KEY(anketa_id) REFERENCES Anketos (id),
    CONSTRAINT uzpildo_autorius FOREIGN KEY(autorius_id) REFERENCES Vartotojai (id),
    CONSTRAINT gyvuno_rusis_filtrui FOREIGN KEY(rusies_id) REFERENCES Rusys (id)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sukurta duomenų kopija lentelei `Skelbimai`
--
LOCK TABLES `Skelbimai` WRITE;
/*!40000 ALTER TABLE `Skelbimai` DISABLE KEYS */;
INSERT INTO `Skelbimai` VALUES
      (1,2,1,2,1,DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i'),'2024-11-20 17:39:00',15,'aprašymas 1-o skelbimo dingo neaiškionis aplinkybėmis atkelia iš anketos aprašymo - paskui galima papildyti skelbimą'),
      (2,3,3,4,1,DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i'),'2024-11-20 17:39:00',1,'aprašymas 2-o skelbimo dingo neaiškionis aplinkybėmis atkelia iš anketos aprašymo - paskui galima papildyti skelbimą'),
      (3,5,2,3,1,DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i'),'2024-11-20 17:39:00',0,'aprašymas 3-o skelbimo dingo neaiškionis aplinkybėmis atkelia iš anketos aprašymo - paskui galima papildyti skelbimą'),
      (4,7,3,6,1,DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i'),'2024-11-20 17:39:00',155,'aprašymas 4-o skelbimo dingo neaiškionis aplinkybėmis atkelia iš anketos aprašymo - paskui galima papildyti skelbimą');
/*!40000 ALTER TABLE `Skelbimai` ENABLE KEYS */;
UNLOCK TABLES;






CREATE TABLE Zinutes
(
    id int (16) NOT NULL AUTO_INCREMENT,
    autoriaus_id int (10) NOT NULL,
    skelbimo_id int (16) NOT NULL,
    zinutes_data datetime NOT NULL comment 'zinutes rasymo data ir laikas',
    ar_uzblokuota boolean NOT NULL default 0,
    turinys text NOT NULL,
    PRIMARY KEY(id),
    CONSTRAINT paraso FOREIGN KEY(autoriaus_id) REFERENCES Vartotojai (id),
    CONSTRAINT skelbiama FOREIGN KEY(skelbimo_id) REFERENCES Skelbimai (id) ON DELETE CASCADE
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Sukurta duomenų kopija lentelei `Zinutes`
--
LOCK TABLES `Zinutes` WRITE;
/*!40000 ALTER TABLE `Zinutes` DISABLE KEYS */;
INSERT INTO `Zinutes` VALUES
        (1,2,2,DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i'),0,'žinutė 1 radome šitą šunį  kaune Rusmeni7 32 aharer eerert dolores wetWETWETWE dolores SGDG dggrrggre tgres ghfdfh 5522 '),
        (2,3,2,DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i'),0,'žinutė 2 radome šitą šunį  kaune Rusmeni7 32 aharer eerert dolores wetWETWETWE dolores SGDG dggrrggre tgres ghfdfh 5522 '),
        (3,4,2,DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i'),0,'žinutė 3 radome šitą šunį  kaune Rusmeni7 32 aharer eerert dolores wetWETWETWE dolores SGDG dggrrggre tgres ghfdfh 5522 '),
        (4,3,2,DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %H:%i'),0,'žinutė 4 radome šitą šunį  kaune Rusmeni7 32 aharer eerert dolores wetWETWETWE dolores SGDG dggrrggre tgres ghfdfh 5522 ');
/*!40000 ALTER TABLE `Zinutes` ENABLE KEYS */;
UNLOCK TABLES;


COMMIT;








/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;