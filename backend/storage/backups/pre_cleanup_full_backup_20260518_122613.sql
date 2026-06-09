/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: alogoto_backend
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-5 from Debian

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Current Database: `alogoto_backend`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `alogoto_backend` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;

USE `alogoto_backend`;

--
-- Table structure for table `analysis_histories`
--

DROP TABLE IF EXISTS `analysis_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `analysis_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `analysis_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `analysis_histories_analysis_id_foreign` (`analysis_id`),
  CONSTRAINT `analysis_histories_analysis_id_foreign` FOREIGN KEY (`analysis_id`) REFERENCES `institution_analyses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analysis_histories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `analysis_histories` WRITE;
/*!40000 ALTER TABLE `analysis_histories` DISABLE KEYS */;
INSERT INTO `analysis_histories` VALUES
(1,9,'creation','Banque Atlantique Benin','Analyse du projet initiée.','2026-05-16 21:39:03','2026-05-16 21:39:03'),
(2,9,'status_updated','Banque Atlantique Benin','Statut changé de en_analyse à approuve. ','2026-05-16 21:40:09','2026-05-16 21:40:09'),
(3,9,'entretien_planifie','Banque Atlantique Benin','Entretien programmé pour le 2026-05-19','2026-05-16 22:11:00','2026-05-16 22:11:00');
/*!40000 ALTER TABLE `analysis_histories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'info',
  `ip` varchar(255) DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `idx_audit_user_date` (`user_id`,`created_at`),
  KEY `idx_audit_action` (`action`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES
(1,1,'Sequi dolorem aut modi unde.',NULL,NULL,'info','206.175.231.253','2026-01-24 06:00:27','2026-03-18 21:09:46','2026-03-18 21:09:46'),
(2,1,'Est est qui magni quasi id.',NULL,NULL,'info','128.4.61.227','2026-01-29 13:28:22','2026-03-18 21:09:46','2026-03-18 21:09:46'),
(3,1,'Optio consequatur omnis nihil commodi cumque quo dolorem.',NULL,NULL,'info','242.124.251.156','2026-03-05 07:01:27','2026-03-18 21:09:46','2026-03-18 21:09:46'),
(4,1,'Pariatur optio aut dolore delectus enim soluta.',NULL,NULL,'info','141.172.177.183','2026-02-28 00:48:42','2026-03-18 21:09:47','2026-03-18 21:09:47'),
(5,1,'Voluptatibus libero quibusdam ipsa quasi in.',NULL,NULL,'info','234.32.135.96','2026-02-19 10:33:34','2026-03-18 21:09:47','2026-03-18 21:09:47'),
(6,1,'Dolores sunt voluptas quas.',NULL,NULL,'info','16.244.126.162','2026-03-05 08:54:32','2026-03-18 21:09:47','2026-03-18 21:09:47'),
(7,1,'Validation du projet 1',NULL,NULL,'info','102.54.18.12','2026-03-18 21:09:47','2026-03-18 21:09:47','2026-03-18 21:09:47'),
(8,2,'photo_change',NULL,NULL,'info','127.0.0.1','2026-05-07 13:51:48','2026-05-07 13:51:48','2026-05-07 13:51:48'),
(9,2,'photo_change',NULL,NULL,'info','127.0.0.1','2026-05-07 13:52:21','2026-05-07 13:52:21','2026-05-07 13:52:21'),
(10,2,'profile_update',NULL,NULL,'info','127.0.0.1','2026-05-07 14:09:13','2026-05-07 14:09:13','2026-05-07 14:09:13'),
(11,2,'profile_update',NULL,NULL,'info','127.0.0.1','2026-05-07 14:52:30','2026-05-07 14:52:30','2026-05-07 14:52:30');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba','i:1;',1779092597),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1779092597;',1779092597),
('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb','i:1;',1778768802),
('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb:timer','i:1778768802;',1778768802),
('laravel-cache-boost.roster.scan','a:2:{s:6:\"roster\";O:21:\"Laravel\\Roster\\Roster\":3:{s:13:\"\0*\0approaches\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:10:{i:0;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^12.0\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:LARAVEL\";s:14:\"\0*\0packageName\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"12.53.0\";s:6:\"\0*\0dev\";b:0;}i:1;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:7:\"v0.3.13\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:PROMPTS\";s:14:\"\0*\0packageName\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.13\";s:6:\"\0*\0dev\";b:0;}i:2;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:1:\"*\";s:10:\"\0*\0package\";E:36:\"Laravel\\Roster\\Enums\\Packages:REVERB\";s:14:\"\0*\0packageName\";s:14:\"laravel/reverb\";s:10:\"\0*\0version\";s:6:\"1.10.2\";s:6:\"\0*\0dev\";b:0;}i:3;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:1:\"*\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:SANCTUM\";s:14:\"\0*\0packageName\";s:15:\"laravel/sanctum\";s:10:\"\0*\0version\";s:5:\"4.3.1\";s:6:\"\0*\0dev\";b:0;}i:4;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^5.27\";s:10:\"\0*\0package\";E:39:\"Laravel\\Roster\\Enums\\Packages:SOCIALITE\";s:14:\"\0*\0packageName\";s:17:\"laravel/socialite\";s:10:\"\0*\0version\";s:6:\"5.27.0\";s:6:\"\0*\0dev\";b:0;}i:5;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:6:\"v0.5.9\";s:10:\"\0*\0package\";E:33:\"Laravel\\Roster\\Enums\\Packages:MCP\";s:14:\"\0*\0packageName\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"0.5.9\";s:6:\"\0*\0dev\";b:1;}i:6;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.24\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:PINT\";s:14:\"\0*\0packageName\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.27.1\";s:6:\"\0*\0dev\";b:1;}i:7;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.41\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:SAIL\";s:14:\"\0*\0packageName\";s:12:\"laravel/sail\";s:10:\"\0*\0version\";s:6:\"1.53.0\";s:6:\"\0*\0dev\";b:1;}i:8;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^11.5.3\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:PHPUNIT\";s:14:\"\0*\0packageName\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"11.5.55\";s:6:\"\0*\0dev\";b:1;}i:9;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:10:\"\0*\0package\";E:41:\"Laravel\\Roster\\Enums\\Packages:TAILWINDCSS\";s:14:\"\0*\0packageName\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:6:\"\0*\0dev\";b:1;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:21:\"\0*\0nodePackageManager\";E:43:\"Laravel\\Roster\\Enums\\NodePackageManager:NPM\";}s:9:\"timestamp\";i:1779089883;}',1779176283);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `conversation_participants`
--

DROP TABLE IF EXISTS `conversation_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversation_participants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversation_participants_conversation_id_foreign` (`conversation_id`),
  KEY `conversation_participants_user_id_foreign` (`user_id`),
  CONSTRAINT `conversation_participants_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversation_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversation_participants`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `conversation_participants` WRITE;
/*!40000 ALTER TABLE `conversation_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversation_participants` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'porteur_institution',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `institution_id` bigint(20) unsigned DEFAULT NULL,
  `porteur_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `last_message_at` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_project_id_foreign` (`project_id`),
  KEY `conversations_institution_id_foreign` (`institution_id`),
  KEY `conversations_porteur_id_foreign` (`porteur_id`),
  KEY `conversations_created_by_foreign` (`created_by`),
  CONSTRAINT `conversations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `conversations_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `conversations_porteur_id_foreign` FOREIGN KEY (`porteur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `conversations_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
INSERT INTO `conversations` VALUES
(1,NULL,'porteur_institution','2026-03-18 21:09:44','2026-05-06 21:03:31',NULL,NULL,'active','2026-05-06 23:03:31',NULL),
(2,NULL,'porteur_institution','2026-03-18 21:09:46','2026-03-18 21:09:46',NULL,NULL,'active',NULL,NULL),
(3,2,'porteur_institution','2026-05-06 19:57:32','2026-05-18 05:56:23',1,2,'active','2026-05-18 07:56:23',NULL),
(4,2,'porteur_institution','2026-05-06 21:05:44','2026-05-18 05:56:00',2,2,'active','2026-05-18 07:56:00',NULL),
(5,2,'porteur_institution','2026-05-06 21:06:19','2026-05-15 16:16:26',2,2,'active','2026-05-15 18:16:26',NULL),
(6,15,'porteur_institution','2026-05-06 21:06:19','2026-05-06 21:06:19',3,2,'active','2026-05-01 23:06:19',NULL);
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `disputes`
--

DROP TABLE IF EXISTS `disputes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `disputes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `statut` varchar(255) NOT NULL DEFAULT 'ouvert',
  `description` text DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `disputes_project_id_foreign` (`project_id`),
  KEY `disputes_user_id_foreign` (`user_id`),
  CONSTRAINT `disputes_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `disputes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disputes`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `disputes` WRITE;
/*!40000 ALTER TABLE `disputes` DISABLE KEYS */;
/*!40000 ALTER TABLE `disputes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `financement_documents`
--

DROP TABLE IF EXISTS `financement_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `financement_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `financement_id` bigint(20) unsigned NOT NULL,
  `type_document` varchar(255) NOT NULL,
  `fichier` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financement_documents_financement_id_foreign` (`financement_id`),
  CONSTRAINT `financement_documents_financement_id_foreign` FOREIGN KEY (`financement_id`) REFERENCES `financements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financement_documents`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `financement_documents` WRITE;
/*!40000 ALTER TABLE `financement_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `financement_documents` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `financement_histories`
--

DROP TABLE IF EXISTS `financement_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `financement_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `financement_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financement_histories_financement_id_foreign` (`financement_id`),
  CONSTRAINT `financement_histories_financement_id_foreign` FOREIGN KEY (`financement_id`) REFERENCES `financements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financement_histories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `financement_histories` WRITE;
/*!40000 ALTER TABLE `financement_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `financement_histories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `financements`
--

DROP TABLE IF EXISTS `financements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `financements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `institution_id` bigint(20) unsigned NOT NULL,
  `porteur_id` bigint(20) unsigned DEFAULT NULL,
  `montant_demande` decimal(15,2) NOT NULL DEFAULT 0.00,
  `montant_propose` decimal(15,2) NOT NULL DEFAULT 0.00,
  `montant_valide` decimal(15,2) NOT NULL DEFAULT 0.00,
  `montant_decaisse` decimal(15,2) NOT NULL DEFAULT 0.00,
  `taux_interet` decimal(5,2) NOT NULL DEFAULT 0.00,
  `duree` int(11) NOT NULL DEFAULT 12,
  `montant` decimal(15,2) NOT NULL,
  `date_financement` date DEFAULT NULL,
  `statut` varchar(255) NOT NULL DEFAULT 'en_analyse',
  `date_validation` timestamp NULL DEFAULT NULL,
  `date_decaissement` timestamp NULL DEFAULT NULL,
  `commentaires` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financements_project_id_foreign` (`project_id`),
  KEY `financements_institution_id_foreign` (`institution_id`),
  KEY `idx_financements_project_id` (`project_id`),
  KEY `idx_financements_institution_id` (`institution_id`),
  KEY `idx_financements_statut` (`statut`),
  KEY `financements_porteur_id_foreign` (`porteur_id`),
  CONSTRAINT `financements_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `financements_porteur_id_foreign` FOREIGN KEY (`porteur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `financements_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financements`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `financements` WRITE;
/*!40000 ALTER TABLE `financements` DISABLE KEYS */;
INSERT INTO `financements` VALUES
(5,2,1,NULL,0.00,0.00,0.00,0.00,0.00,12,5000000.00,'2026-01-07','decaisse',NULL,NULL,NULL,'2026-05-07 10:59:29','2026-05-07 10:59:29');
/*!40000 ALTER TABLE `financements` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `institution_analyses`
--

DROP TABLE IF EXISTS `institution_analyses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `institution_analyses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `institution_id` bigint(20) unsigned NOT NULL,
  `analyste_id` bigint(20) unsigned DEFAULT NULL,
  `statut` varchar(255) NOT NULL DEFAULT 'en_analyse',
  `risk_score` int(10) unsigned NOT NULL DEFAULT 0,
  `score_credibilite` int(10) unsigned NOT NULL DEFAULT 0,
  `score_solvabilite` int(10) unsigned NOT NULL DEFAULT 0,
  `note_globale` int(10) unsigned NOT NULL DEFAULT 0,
  `recommandation` text DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `entretien_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institution_analyses_project_id_foreign` (`project_id`),
  KEY `institution_analyses_institution_id_foreign` (`institution_id`),
  KEY `institution_analyses_analyste_id_foreign` (`analyste_id`),
  CONSTRAINT `institution_analyses_analyste_id_foreign` FOREIGN KEY (`analyste_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `institution_analyses_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `institution_analyses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institution_analyses`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `institution_analyses` WRITE;
/*!40000 ALTER TABLE `institution_analyses` DISABLE KEYS */;
INSERT INTO `institution_analyses` VALUES
(9,2,1,3,'entretien_planifie',40,85,40,60,'Analyse complémentaire requise. Risque modéré.','Analyse initiée par l\'institution.','2026-05-18 22:00:00','2026-05-16 21:39:03','2026-05-16 22:11:00');
/*!40000 ALTER TABLE `institution_analyses` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `institutions`
--

DROP TABLE IF EXISTS `institutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `institutions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `statut` varchar(255) NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institutions_user_id_foreign` (`user_id`),
  CONSTRAINT `institutions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institutions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `institutions` WRITE;
/*!40000 ALTER TABLE `institutions` DISABLE KEYS */;
INSERT INTO `institutions` VALUES
(1,3,'Banque Atlantique Benin','institution1@alogoto.bj','+229 11 52 23 64','Natitingou, Benin','verifie','2026-03-18 21:09:31','2026-03-18 21:09:31'),
(2,13,'Rosine Ahouansou','rosine.ahouansou199@alogoto.bj','+229 32 02 49 28','Natitingou, Benin','verifie','2026-03-18 21:09:32','2026-05-06 21:06:10'),
(3,14,'Armand Houssou','armand.houssou451@alogoto.bj','+229 39 06 60 61','Ouidah, Benin','actif','2026-03-18 21:09:32','2026-05-06 21:06:10'),
(4,15,'Wilfried Sossou','wilfried.sossou943@alogoto.bj','+229 56 98 56 73','Parakou, Benin','actif','2026-03-18 21:09:32','2026-05-06 21:06:10'),
(5,NULL,'Institution Test','inst@test.com',NULL,NULL,'actif','2026-05-07 08:38:22','2026-05-07 08:38:22');
/*!40000 ALTER TABLE `institutions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `interview_histories`
--

DROP TABLE IF EXISTS `interview_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `interview_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `interview_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_histories_interview_id_foreign` (`interview_id`),
  CONSTRAINT `interview_histories_interview_id_foreign` FOREIGN KEY (`interview_id`) REFERENCES `interviews` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interview_histories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `interview_histories` WRITE;
/*!40000 ALTER TABLE `interview_histories` DISABLE KEYS */;
INSERT INTO `interview_histories` VALUES
(1,1,'creation','Banque Atlantique Benin','Entretien programmé le 2026-05-20 00:00:00 à 14:30.','2026-05-16 22:20:23','2026-05-16 22:20:23'),
(2,1,'status_change','Banque Atlantique Benin','Statut changé de programme à annule. RAISON PERSONNELLE','2026-05-16 22:28:11','2026-05-16 22:28:11'),
(3,2,'creation','Banque Atlantique Benin','Entretien programmé le 2026-05-18 00:00:00 à 22:37.','2026-05-17 15:34:30','2026-05-17 15:34:30'),
(4,2,'status_change','Banque Atlantique Benin','Statut changé de programme à confirme. ','2026-05-18 03:12:38','2026-05-18 03:12:38');
/*!40000 ALTER TABLE `interview_histories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `interviews`
--

DROP TABLE IF EXISTS `interviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `interviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `institution_id` bigint(20) unsigned NOT NULL,
  `porteur_id` bigint(20) unsigned NOT NULL,
  `analyste_id` bigint(20) unsigned DEFAULT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type_entretien` varchar(255) NOT NULL,
  `date_entretien` date NOT NULL,
  `heure_entretien` time NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `statut` varchar(255) NOT NULL DEFAULT 'programme',
  `convocation_pdf` varchar(255) DEFAULT NULL,
  `compte_rendu` text DEFAULT NULL,
  `decision_preliminaire` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interviews_project_id_foreign` (`project_id`),
  KEY `interviews_institution_id_foreign` (`institution_id`),
  KEY `interviews_porteur_id_foreign` (`porteur_id`),
  KEY `interviews_analyste_id_foreign` (`analyste_id`),
  CONSTRAINT `interviews_analyste_id_foreign` FOREIGN KEY (`analyste_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `interviews_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `interviews_porteur_id_foreign` FOREIGN KEY (`porteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `interviews_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interviews`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `interviews` WRITE;
/*!40000 ALTER TABLE `interviews` DISABLE KEYS */;
INSERT INTO `interviews` VALUES
(1,2,1,2,3,'Test interview',NULL,'physique','2026-05-20','14:30:00','Office','annule',NULL,NULL,NULL,'2026-05-16 22:20:23','2026-05-16 22:28:11'),
(2,2,1,2,3,'lkjhgfdfghjk','mlkjhgfdxdfghjk','physique','2026-05-18','22:37:00','lxxghjk','confirme',NULL,NULL,NULL,'2026-05-17 15:34:30','2026-05-18 03:12:38');
/*!40000 ALTER TABLE `interviews` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `message_attachments`
--

DROP TABLE IF EXISTS `message_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` bigint(20) unsigned NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_attachments_message_id_foreign` (`message_id`),
  CONSTRAINT `message_attachments_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_attachments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `message_attachments` WRITE;
/*!40000 ALTER TABLE `message_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_attachments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `message_audits`
--

DROP TABLE IF EXISTS `message_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `performed_by` bigint(20) unsigned NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_audits_message_id_foreign` (`message_id`),
  KEY `message_audits_performed_by_foreign` (`performed_by`),
  CONSTRAINT `message_audits_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `message_audits_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_audits`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `message_audits` WRITE;
/*!40000 ALTER TABLE `message_audits` DISABLE KEYS */;
INSERT INTO `message_audits` VALUES
(1,25,'sent',1,'Admin replied to conversation #4','2026-05-18 05:54:47','2026-05-18 05:54:47'),
(2,26,'sent',2,'Message sent by GANDAHO Ines Brunelle (porteur)','2026-05-18 05:56:00','2026-05-18 05:56:00'),
(3,27,'sent',2,'Message sent by GANDAHO Ines Brunelle (porteur)','2026-05-18 05:56:23','2026-05-18 05:56:23'),
(4,27,'edited',2,'Message edited. Old: \"bonjour monsieur\"','2026-05-18 05:56:34','2026-05-18 05:56:34');
/*!40000 ALTER TABLE `message_audits` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned DEFAULT NULL,
  `sender_id` bigint(20) unsigned NOT NULL,
  `receiver_id` bigint(20) unsigned DEFAULT NULL,
  `message` text NOT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sender_role` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'texte',
  `file_path` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `mime_type` varchar(255) DEFAULT NULL,
  `audio_duration` int(11) DEFAULT NULL,
  `is_edited` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `reply_to` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_conversation_id_foreign` (`conversation_id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  KEY `idx_messages_conversation` (`conversation_id`),
  KEY `idx_messages_created` (`created_at`),
  KEY `messages_reply_to_foreign` (`reply_to`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `messages_reply_to_foreign` FOREIGN KEY (`reply_to`) REFERENCES `messages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES
(7,3,3,NULL,'Bonjour, nous avons analysé votre projet \"Mini-centrale solaire rurale\". Il est très prometteur.',NULL,'2026-05-06 19:57:32','2026-05-18 05:56:40','institution','texte',NULL,1,NULL,NULL,0,0,NULL),
(8,3,2,NULL,'Merci pour votre retour. Pouvons-nous planifier un entretien ?',NULL,'2026-05-06 19:57:32','2026-05-06 19:57:32','porteur','texte',NULL,1,NULL,NULL,0,0,NULL),
(9,3,3,NULL,'Oui, je suis disponible demain à 14h. Que pensez-vous du financement à 85% ?',NULL,'2026-05-06 19:57:32','2026-05-18 05:56:40','institution','texte',NULL,1,NULL,NULL,0,0,NULL),
(10,1,3,NULL,'Bonjour, nous avons examiné votre projet  . (\\$project ? \\$project->titre : ) . . Il est très intéressant.',NULL,'2026-05-06 21:03:31','2026-05-06 21:03:31','institution','texte',NULL,0,NULL,NULL,0,0,NULL),
(11,1,2,NULL,'Bonjour, merci pour votre retour positif. Quelles sont les prochaines étapes ?',NULL,'2026-05-06 21:03:31','2026-05-06 21:03:31','porteur','texte',NULL,0,NULL,NULL,0,0,NULL),
(12,1,3,NULL,'Nous aimerions planifier un entretien la semaine prochaine. Êtes-vous disponible mardi à 10h ?',NULL,'2026-05-06 21:03:31','2026-05-06 21:03:31','institution','texte',NULL,0,NULL,NULL,0,0,NULL),
(13,1,2,NULL,'Oui, mardi à 10h me convient parfaitement. Où souhaitez-vous que nous nous rencontrions ?',NULL,'2026-05-06 21:03:31','2026-05-06 21:03:31','porteur','texte',NULL,0,NULL,NULL,0,0,NULL),
(14,1,3,NULL,'Nous pouvons nous voir à notre agence principale, ou en visio. Que préférez-vous ?',NULL,'2026-05-06 21:03:31','2026-05-06 21:03:31','institution','texte',NULL,0,NULL,NULL,0,0,NULL),
(15,1,2,NULL,'La visio me conviendrait mieux. Pouvez-vous m\'envoyer le lien de connexion ?',NULL,'2026-05-06 21:03:31','2026-05-06 21:03:31','porteur','texte',NULL,0,NULL,NULL,0,0,NULL),
(16,5,13,NULL,'Bonjour, nous avons validé votre projet \"Mini-centrale solaire rurale\". Pouvons-nous échanger ?',NULL,'2026-05-06 21:06:19','2026-05-15 17:54:52','institution','texte',NULL,1,NULL,NULL,0,0,NULL),
(17,5,2,NULL,'Bonjour, oui bien sûr. Qu\'aimeriez-vous savoir ?',NULL,'2026-05-06 21:06:19','2026-05-06 21:06:19','porteur','texte',NULL,0,NULL,NULL,0,0,NULL),
(18,5,13,NULL,'Nous souhaiterions des précisions sur votre modèle financier.',NULL,'2026-05-06 21:06:19','2026-05-15 17:54:52','institution','texte',NULL,1,NULL,NULL,0,0,NULL),
(19,5,2,NULL,'Bien sûr, le modèle repose sur 3 piliers : croissance, rentabilité et impact social.',NULL,'2026-05-06 21:06:19','2026-05-06 21:06:19','porteur','texte',NULL,0,NULL,NULL,0,0,NULL),
(20,6,14,NULL,'Votre projet \"MPP\" présente un fort potentiel.',NULL,'2026-05-06 21:06:19','2026-05-07 12:01:27','institution','texte',NULL,1,NULL,NULL,0,0,NULL),
(21,6,2,NULL,'Merci beaucoup ! Nous avons hâte de collaborer avec vous.',NULL,'2026-05-06 21:06:19','2026-05-06 21:06:19','porteur','texte',NULL,0,NULL,NULL,0,0,NULL),
(22,6,14,NULL,'Pourriez-vous nous envoyer le business plan complet ?',NULL,'2026-05-06 21:06:19','2026-05-07 12:01:27','institution','texte',NULL,1,NULL,NULL,0,0,NULL),
(23,5,1,NULL,'cc',NULL,'2026-05-15 16:16:26','2026-05-15 17:54:52','admin','text',NULL,1,NULL,NULL,0,0,NULL),
(24,3,3,2,'r\'ee',NULL,'2026-05-15 18:04:18','2026-05-18 05:56:40','institution','image','messages/3/iuNANGaaUHzE7j2nL1V6jJq88KOVipcn2hhOoYjG.png',1,'image/png',NULL,0,0,NULL),
(25,4,1,2,'bonjour',NULL,'2026-05-18 05:54:47','2026-05-18 05:56:10','admin','texte',NULL,1,NULL,NULL,0,0,NULL),
(26,4,2,13,'comment vous allez',NULL,'2026-05-18 05:56:00','2026-05-18 05:56:00','porteur','texte',NULL,0,NULL,NULL,0,0,NULL),
(27,3,2,3,'bonjour monsieur',NULL,'2026-05-18 05:56:22','2026-05-18 05:56:56','porteur','texte',NULL,1,NULL,NULL,1,0,NULL);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_03_16_120000_create_personal_access_tokens_table',1),
(5,'2026_03_16_121000_add_role_fields_to_users_table',1),
(6,'2026_03_16_121100_create_institutions_table',1),
(7,'2026_03_16_121200_create_projects_table',1),
(8,'2026_03_16_121300_create_project_documents_table',1),
(9,'2026_03_16_121400_create_project_validations_table',1),
(10,'2026_03_16_121500_create_institution_analyses_table',1),
(11,'2026_03_16_121600_create_financements_table',1),
(12,'2026_03_16_121700_create_remboursements_table',1),
(13,'2026_03_16_121800_create_remboursement_confirmations_table',1),
(14,'2026_03_16_121900_create_conversations_table',1),
(15,'2026_03_16_122000_create_conversation_participants_table',1),
(16,'2026_03_16_122100_create_messages_table',1),
(17,'2026_03_16_122200_create_notifications_table',1),
(18,'2026_03_16_122300_create_audit_logs_table',1),
(19,'2026_03_16_122400_create_disputes_table',1),
(20,'2026_03_16_122500_create_settings_table',1),
(21,'2026_03_16_122600_create_project_status_histories_table',1),
(22,'2026_03_16_122700_create_project_comments_table',1),
(23,'2026_04_22_000001_update_project_status_histories_table',2),
(24,'2026_04_22_000002_create_transactions_table',2),
(25,'2026_04_22_000003_convert_amounts_to_decimal',3),
(26,'2026_04_22_000004_add_foreign_key_constraints',4),
(27,'2026_04_22_000005_remove_redundant_status_columns',4),
(28,'2026_04_22_000006_add_performance_indexes',5),
(29,'2026_05_05_000007_normalize_project_status_values',6),
(30,'2026_05_06_151856_create_rendez_vous_table',7),
(31,'2026_05_06_164145_add_raison_rejet_to_project_documents',8),
(32,'2026_05_06_000001_update_messaging_tables',9),
(33,'2026_05_07_094650_create_repayment_histories_table',10),
(34,'2026_05_07_094757_update_remboursements_table_add_missing_fields',10),
(35,'2026_05_07_141345_add_archived_at_to_notifications_table',11),
(36,'2026_05_07_153953_add_profile_fields_to_users_table',12),
(37,'2026_05_12_012045_add_scores_to_institution_analyses_table',13),
(38,'2026_05_12_012045_create_analysis_histories_table',13),
(39,'2026_05_12_013353_create_interviews_table',14),
(40,'2026_05_12_013354_create_interview_histories_table',15),
(41,'2026_05_12_014210_add_monitoring_fields_to_remboursements_table',16),
(42,'2026_05_12_014211_create_repayment_disputes_table',16),
(43,'2026_05_12_014211_create_repayment_events_table',16),
(44,'2026_05_12_014945_add_business_fields_to_financements_table',17),
(45,'2026_05_12_014945_create_financement_histories_table',17),
(46,'2026_05_12_014946_create_financement_documents_table',17),
(47,'2026_05_12_015904_add_ui_fields_to_audit_logs_table',18),
(48,'2026_05_12_073500_enhance_messaging_system',19),
(49,'2026_05_12_115703_create_project_monitoring_tables',20),
(50,'2026_05_13_000001_cleanup_legacy_project_columns',21),
(51,'2026_05_13_143110_add_unique_constraint_to_telephone_in_users_table',21),
(52,'2026_05_14_152628_add_provider_fields_to_users_table',22),
(53,'2026_05_18_074641_add_branche_to_projects_table',23);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  KEY `idx_notifications_user_read` (`user_id`,`is_read`),
  KEY `idx_notifications_created` (`created_at`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES
(1,1,'validation_projet',NULL,'Reprehenderit omnis architecto ea molestias placeat et.',0,NULL,'2026-03-18 21:09:41','2026-03-18 21:09:41'),
(2,1,'litige_cree',NULL,'Nam dignissimos id ullam perspiciatis voluptas nesciunt voluptas.',1,NULL,'2026-03-18 21:09:42','2026-03-18 21:09:42'),
(3,1,'remboursement_confirme',NULL,'Autem quo omnis voluptas eum aut magnam.',0,NULL,'2026-03-18 21:09:42','2026-03-18 21:09:42'),
(5,2,'message',NULL,'Molestiae et et sapiente in sed est rerum quia mollitia et omnis quae qui.',1,NULL,'2026-03-18 21:09:42','2026-03-18 21:09:42'),
(6,2,'litige_cree',NULL,'Architecto beatae odit sed odit laboriosam odio et voluptas enim dolorem voluptates suscipit dignissimos.',1,NULL,'2026-03-18 21:09:42','2026-05-07 12:48:59'),
(7,3,'litige_cree',NULL,'Necessitatibus omnis nulla quidem rerum est nulla ut in labore molestias quasi.',1,NULL,'2026-03-18 21:09:42','2026-05-15 18:05:13'),
(8,3,'remboursement_confirme',NULL,'Delectus rerum doloremque dignissimos accusantium omnis occaecati illum at veniam fugit quia in.',1,NULL,'2026-03-18 21:09:42','2026-05-15 18:05:13'),
(9,3,'financement_recu',NULL,'Quis et excepturi vitae excepturi dolores placeat unde.',1,NULL,'2026-03-18 21:09:42','2026-05-15 18:05:13'),
(34,1,'validation_projet',NULL,'Le projet Mini-centrale solaire rurale a ete valide.',0,NULL,'2026-03-18 21:09:44','2026-03-18 21:09:44'),
(38,2,'entretien_planifie','Entretien planifié','Un entretien a été planifié pour votre projet \"Mini-centrale solaire rurale\" le 2026-05-18 à 22:37.',0,NULL,'2026-05-17 15:34:30','2026-05-17 15:34:30'),
(39,3,'entretien_planifie','Entretien créé','Vous avez planifié un entretien pour le projet \"Mini-centrale solaire rurale\" le 2026-05-18 à 22:37.',0,NULL,'2026-05-17 15:34:30','2026-05-17 15:34:30'),
(40,2,'entretien_confirme','Entretien confirmé','L\'entretien pour le projet \"Mini-centrale solaire rurale\" du 2026-05-18 00:00:00 a été confirmé.',0,NULL,'2026-05-18 03:12:38','2026-05-18 03:12:38'),
(41,3,'entretien_confirme','Entretien confirmé','Vous avez confirmé l\'entretien pour le projet \"Mini-centrale solaire rurale\".',0,NULL,'2026-05-18 03:12:38','2026-05-18 03:12:38'),
(42,2,'message','Nouveau message de l\'administration','Vous avez reçu un message de l\'administration concernant votre projet.',0,NULL,'2026-05-18 05:54:47','2026-05-18 05:54:47'),
(43,13,'message','Nouveau message','Vous avez un nouveau message de GANDAHO Ines Brunelle',0,NULL,'2026-05-18 05:56:00','2026-05-18 05:56:00'),
(44,3,'message','Nouveau message','Vous avez un nouveau message de GANDAHO Ines Brunelle',0,NULL,'2026-05-18 05:56:23','2026-05-18 05:56:23');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES
(1,'App\\Models\\User',1,'api-token','08e24c266f33225415db32fc16fafab90eb15e73728639422f96ef97e6cd6cb8','[\"*\"]',NULL,NULL,'2026-04-28 13:54:50','2026-04-28 13:54:50'),
(2,'App\\Models\\User',1,'api-token','6d8dc5fb1ae0e22dd1a014b5ae36b71805cbf9ef6163dbc8ece092c82f234703','[\"*\"]',NULL,NULL,'2026-04-28 13:55:29','2026-04-28 13:55:29'),
(3,'App\\Models\\User',1,'api-token','444af0ef487ef135ad3e4b5c284d8099a3fd94ae2fa780694cbf7d0ae8ad5141','[\"*\"]',NULL,NULL,'2026-04-28 14:20:12','2026-04-28 14:20:12'),
(4,'App\\Models\\User',1,'api-token','c445bf310fbac09fa1f439e97d94466d4fc973fe517fd1c80f4a051d9b065183','[\"*\"]',NULL,NULL,'2026-04-28 14:20:17','2026-04-28 14:20:17'),
(5,'App\\Models\\User',2,'test','05fb54386c17b43f0bb23eaf23fe8715c95fbd140a82512e71478e9a252125cc','[\"*\"]','2026-05-06 20:44:38',NULL,'2026-05-06 20:44:07','2026-05-06 20:44:38'),
(6,'App\\Models\\User',2,'test','f5108cc079a9da65a5bcd25227916e1898c5f6481a07210c2293040c27f5d98f','[\"*\"]','2026-05-06 21:06:28',NULL,'2026-05-06 21:04:20','2026-05-06 21:06:28');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `project_audits`
--

DROP TABLE IF EXISTS `project_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `performed_by` bigint(20) unsigned NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_audits_project_id_foreign` (`project_id`),
  KEY `project_audits_performed_by_foreign` (`performed_by`),
  CONSTRAINT `project_audits_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `project_audits_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_audits`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `project_audits` WRITE;
/*!40000 ALTER TABLE `project_audits` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_audits` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `project_comments`
--

DROP TABLE IF EXISTS `project_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `commentaire` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_comments_project_id_foreign` (`project_id`),
  KEY `project_comments_user_id_foreign` (`user_id`),
  CONSTRAINT `project_comments_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_comments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `project_comments` WRITE;
/*!40000 ALTER TABLE `project_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_comments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `project_documents`
--

DROP TABLE IF EXISTS `project_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `fichier` varchar(255) NOT NULL,
  `statut_validation` varchar(255) NOT NULL DEFAULT 'en_attente',
  `raison_rejet` text DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_documents_project_id_foreign` (`project_id`),
  CONSTRAINT `project_documents_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_documents`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `project_documents` WRITE;
/*!40000 ALTER TABLE `project_documents` DISABLE KEYS */;
INSERT INTO `project_documents` VALUES
(4,2,'business_plan','documents/2/business_plan_836afea8-e22d-3d15-98a2-927fec741baf.pdf','en_attente',NULL,'2026-02-22 21:39:19','2026-03-18 21:09:36','2026-05-06 14:58:43'),
(5,2,'statuts','documents/2/statuts_68e74faa-3b39-30a9-9deb-8eca57d7bf3c.pdf','en_attente',NULL,'2025-12-31 07:32:59','2026-03-18 21:09:36','2026-05-06 14:58:43'),
(6,2,'budget','documents/2/budget_aa45ef99-5cbb-3275-ac6e-0202e8edc5c8.pdf','en_attente',NULL,'2026-01-31 06:15:15','2026-03-18 21:09:36','2026-05-06 14:58:43'),
(7,2,'budget','documents/2/budget_e2d919ae-c421-3e65-8676-04fd04370b44.pdf','en_attente',NULL,'2026-03-16 10:16:28','2026-03-18 21:09:36','2026-05-06 14:58:43'),
(40,2,'piece_identite','documents/2/test_document.pdf','en_attente',NULL,'2026-05-06 14:15:25','2026-05-06 14:15:25','2026-05-06 14:15:25'),
(41,2,'business_plan','documents/2/test_document.pdf','en_attente',NULL,'2026-05-06 14:57:01','2026-05-06 14:57:01','2026-05-06 14:57:01'),
(42,2,'business_plan','documents/2/business_plan_test.pdf','valide',NULL,'2026-04-26 15:15:26','2026-05-06 15:15:26','2026-05-06 15:15:26'),
(43,2,'piece_identite','documents/2/identite_scan.jpg','en_attente',NULL,'2026-05-03 15:15:26','2026-05-06 15:15:26','2026-05-06 15:15:26'),
(44,2,'justificatif_residence','documents/2/residence_justificatif.png','en_attente',NULL,'2026-04-21 15:15:26','2026-05-06 15:15:26','2026-05-06 15:15:26'),
(45,2,'plan_utilisation_fonds','documents/2/budget_previsionnel.xls','rejete',NULL,'2026-05-01 15:15:26','2026-05-06 15:15:26','2026-05-06 15:15:26'),
(46,2,'preuve_activite','documents/2/rapport_activite.doc','rejete',NULL,'2026-05-04 15:15:26','2026-05-06 15:15:26','2026-05-06 15:15:26'),
(47,2,'garanties','documents/2/garantie_document.pdf','valide',NULL,'2026-04-16 15:15:26','2026-05-06 15:15:26','2026-05-06 15:15:26'),
(48,2,'historique_financier','documents/2/releve_bancaire.pdf','en_attente',NULL,'2026-05-05 15:15:26','2026-05-06 15:15:26','2026-05-06 15:15:26'),
(49,2,'photo_identite','documents/2/photo_identite.jpg','valide',NULL,'2026-04-06 15:15:26','2026-05-06 15:15:26','2026-05-06 15:15:26'),
(50,23,'photo_identite','documents/23/1778376130_logo-MC.png','en_attente',NULL,'2026-05-09 23:22:10','2026-05-09 23:22:10','2026-05-09 23:22:10'),
(51,23,'description_projet','documents/23/1778376130_test_document.pdf','en_attente',NULL,'2026-05-09 23:22:10','2026-05-09 23:22:10','2026-05-09 23:22:10'),
(52,23,'piece_identite','documents/23/1778376130_1.jpeg','en_attente',NULL,'2026-05-09 23:22:10','2026-05-09 23:22:10','2026-05-09 23:22:10'),
(53,23,'plan_utilisation_fonds','documents/23/1778376130_ABP MEMOIRE.pdf','en_attente',NULL,'2026-05-09 23:22:10','2026-05-09 23:22:10','2026-05-09 23:22:10'),
(54,23,'plan_remboursement','documents/23/1778376130_releve_bancaire.pdf','en_attente',NULL,'2026-05-09 23:22:10','2026-05-09 23:22:10','2026-05-09 23:22:10'),
(55,23,'preuve_activite','documents/23/1778376130_WhatsApp Image 2026-04-17 at 21.39.20 (4).jpeg','en_attente',NULL,'2026-05-09 23:22:10','2026-05-09 23:22:10','2026-05-09 23:22:10'),
(56,23,'objectif_financement','documents/23/1778376130_PROJET MISS KETOU LA REINE.pdf','en_attente',NULL,'2026-05-09 23:22:10','2026-05-09 23:22:10','2026-05-09 23:22:10'),
(57,23,'garanties','documents/23/1778376130_logo-MA2.png','en_attente',NULL,'2026-05-09 23:22:10','2026-05-09 23:22:10','2026-05-09 23:22:10');
/*!40000 ALTER TABLE `project_documents` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `project_risk_scores`
--

DROP TABLE IF EXISTS `project_risk_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_risk_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `risk_level` varchar(255) NOT NULL,
  `score` int(11) NOT NULL,
  `analysis` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_risk_scores_project_id_foreign` (`project_id`),
  CONSTRAINT `project_risk_scores_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_risk_scores`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `project_risk_scores` WRITE;
/*!40000 ALTER TABLE `project_risk_scores` DISABLE KEYS */;
INSERT INTO `project_risk_scores` VALUES
(1,23,'faible',0,'Aucune anomalie détectée.','2026-05-15 15:37:52','2026-05-15 15:37:52'),
(2,22,'faible',0,'Aucune anomalie détectée.','2026-05-15 15:37:52','2026-05-15 15:37:52'),
(3,21,'faible',0,'Aucune anomalie détectée.','2026-05-15 15:37:52','2026-05-15 15:37:52'),
(4,20,'faible',0,'Aucune anomalie détectée.','2026-05-15 15:37:52','2026-05-15 15:37:52'),
(5,19,'faible',0,'Aucune anomalie détectée.','2026-05-15 15:37:52','2026-05-15 15:37:52'),
(6,18,'faible',0,'Aucune anomalie détectée.','2026-05-15 15:37:52','2026-05-15 15:37:52'),
(7,17,'faible',0,'Aucune anomalie détectée.','2026-05-15 15:37:52','2026-05-15 15:37:52'),
(8,16,'faible',0,'Aucune anomalie détectée.','2026-05-15 15:37:52','2026-05-15 15:37:52'),
(9,15,'faible',0,'Aucune anomalie détectée.','2026-05-15 15:37:52','2026-05-15 15:37:52'),
(10,14,'faible',0,'Aucune anomalie détectée.','2026-05-15 15:37:52','2026-05-15 15:37:52');
/*!40000 ALTER TABLE `project_risk_scores` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `project_status_histories`
--

DROP TABLE IF EXISTS `project_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_status_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `old_status` varchar(255) NOT NULL,
  `new_status` varchar(255) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_status_histories_project_id_foreign` (`project_id`),
  KEY `project_status_histories_actor_id_foreign` (`actor_id`),
  CONSTRAINT `project_status_histories_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_status_histories_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_status_histories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `project_status_histories` WRITE;
/*!40000 ALTER TABLE `project_status_histories` DISABLE KEYS */;
INSERT INTO `project_status_histories` VALUES
(2,2,'valide','','system',NULL,NULL,'2026-03-18 21:09:32','2026-03-18 21:09:32'),
(13,16,'soumis','','porteur',NULL,NULL,'2026-03-24 20:23:59','2026-03-24 20:23:59'),
(14,14,'soumis','','porteur',NULL,NULL,'2026-03-24 20:27:54','2026-03-24 20:27:54'),
(15,15,'soumis','','porteur',NULL,NULL,'2026-03-24 20:45:35','2026-03-24 20:45:35'),
(16,23,'draft','draft','Projet créé (brouillon)',2,'{\"source\":\"web.projects.store\"}','2026-05-09 23:22:10','2026-05-09 23:22:10'),
(17,2,'admin_validated','under_institution_review','En analyse par l\'institution',3,'{\"project_title\":\"Mini-centrale solaire rurale\",\"project_sector\":\"Fintech\",\"amount\":\"19181557.00\",\"transition_timestamp\":\"2026-05-16T23:39:03+00:00\"}','2026-05-16 21:39:03','2026-05-16 21:39:03');
/*!40000 ALTER TABLE `project_status_histories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `project_validations`
--

DROP TABLE IF EXISTS `project_validations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_validations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `admin_id` bigint(20) unsigned NOT NULL,
  `decision` varchar(255) NOT NULL,
  `commentaire` text DEFAULT NULL,
  `date_decision` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_validations_project_id_foreign` (`project_id`),
  KEY `project_validations_admin_id_foreign` (`admin_id`),
  CONSTRAINT `project_validations_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_validations_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_validations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `project_validations` WRITE;
/*!40000 ALTER TABLE `project_validations` DISABLE KEYS */;
INSERT INTO `project_validations` VALUES
(1,2,1,'valide','Validation initiale du projet.','2026-02-27 09:32:19','2026-03-18 21:09:32','2026-03-18 21:09:32');
/*!40000 ALTER TABLE `project_validations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `secteur` varchar(255) DEFAULT NULL,
  `branche` varchar(255) DEFAULT NULL,
  `montant_demande` decimal(15,2) NOT NULL DEFAULT 0.00,
  `montant_finance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `duree` varchar(255) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `statut` varchar(255) NOT NULL DEFAULT 'soumis',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_user_id_foreign` (`user_id`),
  KEY `idx_projects_user_id` (`user_id`),
  KEY `idx_projects_statut` (`statut`),
  KEY `idx_projects_secteur` (`secteur`),
  KEY `idx_projects_created_at` (`created_at`),
  CONSTRAINT `projects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES
(2,2,'Mini-centrale solaire rurale','Quisquam quo quo voluptatem reprehenderit voluptatum rem exercitationem. Ducimus quia nostrum sed. In enim dolores alias cupiditate. Ut iste nemo dolor nihil neque porro.','Fintech',NULL,19181557.00,0.00,'24 mois','Natitingou','under_institution_review','2026-03-18 21:09:32','2026-05-16 21:39:03'),
(14,2,'Cooperative Cacao Premium','lkjhgfdfghjklmùmlkjhgfdfghjklmlkjhgfghjklmlkjhg','Agriculture',NULL,123000000000.00,0.00,'moyen_terme','Abidjan, CI','submitted','2026-03-23 12:02:39','2026-03-24 20:27:54'),
(15,2,'MPP','KJHGFDSDFGHJKLKJHGFDFGHJKJHGFD','Technologie',NULL,1888800000000.00,0.00,'court_terme','Abidjan, CI','submitted','2026-03-23 12:15:19','2026-03-24 20:45:35'),
(16,2,'Eau potable','kjhgfdghjklmkjhgffhjklmlkjhgfwfghjklmlkjhgfd','Santé',NULL,119000.00,0.00,'long_terme','Ketou','draft','2026-03-24 20:23:45','2026-03-24 20:24:54'),
(17,2,'Application Mobile E-commerce','Description de test pour Application Mobile E-commerce','Technologie',NULL,5000000.00,0.00,'12 mois','Abidjan','draft','2026-05-06 18:06:12','2026-05-06 18:06:12'),
(18,2,'Ferme Bio Durable','Description de test pour Ferme Bio Durable','Agriculture',NULL,3000000.00,0.00,'12 mois','Abidjan','submitted','2026-05-06 18:06:12','2026-05-06 18:06:12'),
(19,2,'Restaurant Gastronomique','Description de test pour Restaurant Gastronomique','Restauration',NULL,7500000.00,0.00,'12 mois','Abidjan','under_admin_review','2026-05-06 18:06:12','2026-05-06 18:06:12'),
(20,2,'Boutique en Ligne','Description de test pour Boutique en Ligne','Commerce',NULL,2000000.00,2000000.00,'12 mois','Abidjan','funded','2026-05-06 18:06:12','2026-05-06 18:06:12'),
(21,2,'Centre de Formation','Description de test pour Centre de Formation','Education',NULL,4000000.00,0.00,'12 mois','Abidjan','admin_rejected','2026-05-06 18:06:12','2026-05-06 18:06:12'),
(22,16,'Projet Test Remboursement','Projet de test','Agriculture',NULL,1000000.00,0.00,NULL,NULL,'active','2026-05-07 08:38:22','2026-05-07 08:38:22'),
(23,2,'ALOGOTO','<p>Alogoto est une plate de misse en relation entre porteur de projet et&nbsp;</p>','informatique',NULL,500000.00,0.00,'03','Porto-Novo , Benin','draft','2026-05-09 23:22:10','2026-05-09 23:22:10');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `realtime_notifications`
--

DROP TABLE IF EXISTS `realtime_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `realtime_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `realtime_notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `realtime_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `realtime_notifications`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `realtime_notifications` WRITE;
/*!40000 ALTER TABLE `realtime_notifications` DISABLE KEYS */;
INSERT INTO `realtime_notifications` VALUES
(1,2,'new_message','Nouveau message','Vous avez reçu un nouveau message concernant le projet Mini-centrale solaire rurale','{\"conversation_id\":3,\"message_id\":24,\"sender_name\":\"Banque Atlantique Benin\"}',0,'2026-05-15 18:04:18','2026-05-15 18:04:18');
/*!40000 ALTER TABLE `realtime_notifications` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `remboursement_confirmations`
--

DROP TABLE IF EXISTS `remboursement_confirmations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `remboursement_confirmations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `remboursement_id` bigint(20) unsigned NOT NULL,
  `institution_id` bigint(20) unsigned NOT NULL,
  `date_confirmation` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `remboursement_confirmations_remboursement_id_foreign` (`remboursement_id`),
  KEY `remboursement_confirmations_institution_id_foreign` (`institution_id`),
  CONSTRAINT `remboursement_confirmations_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `remboursement_confirmations_remboursement_id_foreign` FOREIGN KEY (`remboursement_id`) REFERENCES `remboursements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remboursement_confirmations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `remboursement_confirmations` WRITE;
/*!40000 ALTER TABLE `remboursement_confirmations` DISABLE KEYS */;
/*!40000 ALTER TABLE `remboursement_confirmations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `remboursements`
--

DROP TABLE IF EXISTS `remboursements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `remboursements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `institution_id` bigint(20) unsigned DEFAULT NULL,
  `financement_id` bigint(20) unsigned DEFAULT NULL,
  `montant_total` decimal(15,2) NOT NULL,
  `montant_rembourse` decimal(15,2) NOT NULL DEFAULT 0.00,
  `montant_restant` decimal(15,2) NOT NULL DEFAULT 0.00,
  `date_echeance` date DEFAULT NULL,
  `date_paiement` date DEFAULT NULL,
  `statut` varchar(255) NOT NULL DEFAULT 'en_attente',
  `niveau_risque` varchar(255) NOT NULL DEFAULT 'faible',
  `penalites` decimal(15,2) NOT NULL DEFAULT 0.00,
  `methode_paiement` varchar(255) DEFAULT NULL,
  `transaction_reference` varchar(255) DEFAULT NULL,
  `preuve_path` varchar(255) DEFAULT NULL,
  `commentaires` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `remboursements_project_id_foreign` (`project_id`),
  KEY `idx_remboursements_project_id` (`project_id`),
  KEY `idx_remboursements_statut` (`statut`),
  KEY `idx_remboursements_date_echeance` (`date_echeance`),
  KEY `remboursements_institution_id_foreign` (`institution_id`),
  KEY `remboursements_statut_index` (`statut`),
  KEY `remboursements_date_echeance_index` (`date_echeance`),
  KEY `remboursements_financement_id_foreign` (`financement_id`),
  CONSTRAINT `remboursements_financement_id_foreign` FOREIGN KEY (`financement_id`) REFERENCES `financements` (`id`) ON DELETE SET NULL,
  CONSTRAINT `remboursements_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `remboursements_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remboursements`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `remboursements` WRITE;
/*!40000 ALTER TABLE `remboursements` DISABLE KEYS */;
INSERT INTO `remboursements` VALUES
(1,2,1,NULL,500000.00,500000.00,0.00,'2026-02-07','2026-05-15','paye','faible',0.00,'mobile_money',NULL,NULL,NULL,'2026-05-07 11:03:57','2026-05-15 16:01:54'),
(2,2,1,NULL,500000.00,0.00,500000.00,'2026-05-22',NULL,'en_attente','faible',0.00,NULL,NULL,NULL,NULL,'2026-05-07 11:03:57','2026-05-07 11:03:57'),
(3,2,1,NULL,500000.00,0.00,500000.00,'2026-04-27',NULL,'en_retard','faible',0.00,NULL,NULL,NULL,NULL,'2026-05-07 11:03:57','2026-05-07 11:03:57'),
(4,2,1,NULL,750000.00,0.00,0.00,'2026-04-07','2026-04-06','paye','faible',0.00,'virement',NULL,NULL,NULL,'2026-05-07 11:03:57','2026-05-07 11:03:57'),
(5,2,1,NULL,300000.00,0.00,300000.00,'2026-06-06',NULL,'en_attente','faible',0.00,NULL,NULL,NULL,NULL,'2026-05-07 11:03:57','2026-05-07 11:03:57');
/*!40000 ALTER TABLE `remboursements` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `rendez_vous`
--

DROP TABLE IF EXISTS `rendez_vous`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rendez_vous` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `institution_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `date_heure` datetime NOT NULL,
  `objet` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `statut` enum('planifie','accepte','rejete','termine','annule') NOT NULL DEFAULT 'planifie',
  `notes_porteur` text DEFAULT NULL,
  `notes_institution` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rendez_vous_user_id_foreign` (`user_id`),
  KEY `rendez_vous_institution_id_foreign` (`institution_id`),
  KEY `rendez_vous_project_id_foreign` (`project_id`),
  CONSTRAINT `rendez_vous_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rendez_vous_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rendez_vous_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rendez_vous`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `rendez_vous` WRITE;
/*!40000 ALTER TABLE `rendez_vous` DISABLE KEYS */;
INSERT INTO `rendez_vous` VALUES
(1,2,1,2,'2026-05-12 13:03:57','Discussion financement projet',NULL,'Agence Abidjan','planifie','Apporter dossiers',NULL,'2026-05-07 11:03:57','2026-05-07 11:03:57'),
(2,2,1,2,'2026-05-05 13:03:57','Signature convention',NULL,'Visio','accepte','Signé par les deux parties',NULL,'2026-05-07 11:03:57','2026-05-07 11:03:57'),
(3,2,1,2,'2026-04-27 13:03:57','Revue technique',NULL,'Bureau Yamoussoukro','rejete','Indisponible',NULL,'2026-05-07 11:03:57','2026-05-07 11:03:57'),
(4,2,1,2,'2026-04-17 13:03:57','Premier paiement',NULL,'Banque ATL','termine','Paiement effectué',NULL,'2026-05-07 11:03:57','2026-05-07 11:03:57'),
(5,2,1,2,'2026-05-02 13:03:57','Rencontre imprevue',NULL,'Cocody','annule','Annulé par l\'institution',NULL,'2026-05-07 11:03:57','2026-05-07 11:03:57');
/*!40000 ALTER TABLE `rendez_vous` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `repayment_disputes`
--

DROP TABLE IF EXISTS `repayment_disputes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `repayment_disputes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `repayment_id` bigint(20) unsigned NOT NULL,
  `institution_id` bigint(20) unsigned NOT NULL,
  `porteur_id` bigint(20) unsigned NOT NULL,
  `motif` text NOT NULL,
  `statut` varchar(255) NOT NULL DEFAULT 'ouvert',
  `preuves` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repayment_disputes_repayment_id_foreign` (`repayment_id`),
  KEY `repayment_disputes_institution_id_foreign` (`institution_id`),
  KEY `repayment_disputes_porteur_id_foreign` (`porteur_id`),
  CONSTRAINT `repayment_disputes_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `repayment_disputes_porteur_id_foreign` FOREIGN KEY (`porteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `repayment_disputes_repayment_id_foreign` FOREIGN KEY (`repayment_id`) REFERENCES `remboursements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repayment_disputes`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `repayment_disputes` WRITE;
/*!40000 ALTER TABLE `repayment_disputes` DISABLE KEYS */;
/*!40000 ALTER TABLE `repayment_disputes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `repayment_events`
--

DROP TABLE IF EXISTS `repayment_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `repayment_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `repayment_id` bigint(20) unsigned NOT NULL,
  `type_evenement` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `auteur` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repayment_events_repayment_id_foreign` (`repayment_id`),
  CONSTRAINT `repayment_events_repayment_id_foreign` FOREIGN KEY (`repayment_id`) REFERENCES `remboursements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repayment_events`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `repayment_events` WRITE;
/*!40000 ALTER TABLE `repayment_events` DISABLE KEYS */;
INSERT INTO `repayment_events` VALUES
(1,1,'validation','Remboursement validé par l\'institution.','Banque Atlantique Benin','2026-05-15 16:01:55','2026-05-15 16:01:55');
/*!40000 ALTER TABLE `repayment_events` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `repayment_histories`
--

DROP TABLE IF EXISTS `repayment_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `repayment_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `repayment_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `acteur_type` varchar(255) NOT NULL,
  `acteur_id` bigint(20) unsigned NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repayment_histories_repayment_id_foreign` (`repayment_id`),
  KEY `repayment_histories_acteur_type_acteur_id_index` (`acteur_type`,`acteur_id`),
  CONSTRAINT `repayment_histories_repayment_id_foreign` FOREIGN KEY (`repayment_id`) REFERENCES `remboursements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repayment_histories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `repayment_histories` WRITE;
/*!40000 ALTER TABLE `repayment_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `repayment_histories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('uGjFuS1d2R4MvpiI8mb8w6qW4UOAo6iFBSqn7a5F',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoid3N6dzloWVVQNUM1RGpRM0VxSlJFR1JzbXc1Q1hwcDdNcFZKajJzVSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Njk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9mcm9udGVuZC9kYXNoYm9hcmQwMi9zaGFyZWQvanMvcmVhbHRpbWUtaW5pdC5qcyI7czo1OiJyb3V0ZSI7czoxNjoiZGFzaGJvYXJkMDIuZmlsZSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6IjlmY2EyNjNjMTQzNWMyZmZhNTc3MjBhMzZlMjViMzM0ZjQwYTJmYTMxODM5ODk3YTUxZjVlNjk2ODNiYzIzNmYiO30=',1779098340);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `commission` decimal(5,2) NOT NULL DEFAULT 0.00,
  `min_financement` bigint(20) unsigned NOT NULL DEFAULT 0,
  `max_financement` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `institution_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'XOF',
  `status` varchar(255) NOT NULL,
  `fedapay_transaction_id` varchar(255) DEFAULT NULL,
  `fedapay_payment_method` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_fedapay_transaction_id_unique` (`fedapay_transaction_id`),
  KEY `transactions_institution_id_foreign` (`institution_id`),
  KEY `transactions_type_status_index` (`type`,`status`),
  KEY `transactions_fedapay_transaction_id_index` (`fedapay_transaction_id`),
  KEY `idx_transactions_project_id` (`project_id`),
  KEY `idx_transactions_user_id` (`user_id`),
  KEY `idx_transactions_status` (`status`),
  KEY `idx_transactions_type` (`type`),
  CONSTRAINT `transactions_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `telephone_verified_at` timestamp NULL DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'porteur',
  `telephone` varchar(255) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `activite` varchar(255) DEFAULT NULL,
  `entreprise_nom` varchar(255) DEFAULT NULL,
  `entreprise_secteur` varchar(255) DEFAULT NULL,
  `statut` varchar(255) NOT NULL DEFAULT 'actif',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_telephone_unique` (`telephone`),
  KEY `users_provider_provider_id_index` (`provider`,`provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'ADECHINA Gaston',NULL,'admin@alogoto.bj','2026-03-18 21:09:28',NULL,NULL,NULL,NULL,'$2y$12$1DOnvv7LtM5I3Lfw/G2qAuszLbh.RyG89Uya9x7AOIUUn6x9jC9PW','admin','+229 90 00 00 00',NULL,NULL,NULL,NULL,'actif','QW8bAtzZhqI3MWLhXfrqmvrMZOjg7CDxZHUdSqNeiJmhwpdB1peh0tVV11Ub','2026-03-18 21:09:29','2026-05-15 15:37:07'),
(2,'GANDAHO Ines Brunelle',NULL,'porteur1@alogoto.bj','2026-03-18 21:09:30',NULL,NULL,NULL,'avatars/uBegwjeweQWg9SvIxv67EYCnsmv4snbGI6Eo8mGb.jpg','$2y$12$yr8/v/ljLTLDtAM68R5aXO3MhTyq9ReRnMjid9oJuNdbDRvZJuhP6','porteur','+229 90109023',NULL,NULL,NULL,NULL,'actif','ziOH7Xtroglaex0Oh4JK5gETX7aXRNDFTOd1qGP7Zv0deec3ncKRW2W9erZR','2026-03-18 21:09:30','2026-05-07 14:52:30'),
(3,'Banque Atlantique Benin',NULL,'institution1@alogoto.bj','2026-03-18 21:09:31',NULL,NULL,NULL,NULL,'$2y$12$rwL5d2D6D6.4VDkRkoMFv.xEO2LHiRt/e2WcI.YpnOl/dAEg5oPJ6','institution','+229 90 22 22 22',NULL,NULL,NULL,NULL,'verifie','fj1jthgignT5fs9x5KweCfOzcu69qSoOrYTP0v4vx4ESkXP857DMBXSWJ604','2026-03-18 21:09:31','2026-03-18 21:09:31'),
(13,'Rosine Ahouansou',NULL,'contact@rosineahouansou.bj',NULL,NULL,NULL,NULL,NULL,'$2y$12$gd2JmKaThHCsbd69n2KHYOFlC6d8JYgS3gzwHYh9/D6SfpNhUpcRq','institution','+229 71930679',NULL,NULL,NULL,NULL,'verifie',NULL,'2026-05-06 21:06:10','2026-05-06 21:06:10'),
(14,'Armand Houssou',NULL,'contact@armandhoussou.bj',NULL,NULL,NULL,NULL,NULL,'$2y$12$Vs4wmYyxDJksZ6Ni3MUw/uWOQXnUKSGEIuE1QVOkAraOVzAAnyl76','institution','+229 57241273',NULL,NULL,NULL,NULL,'verifie',NULL,'2026-05-06 21:06:10','2026-05-06 21:06:10'),
(15,'Wilfried Sossou',NULL,'contact@wilfriedsossou.bj',NULL,NULL,NULL,NULL,NULL,'$2y$12$Khb6xo61aLAnECgJgQC/7emSVbey2O8lQKEblhU6lOR/ac5d52Ttm','institution','+229 18239903',NULL,NULL,NULL,NULL,'verifie',NULL,'2026-05-06 21:06:10','2026-05-06 21:06:10'),
(16,'Porteur Test',NULL,'porteur@test.com',NULL,NULL,NULL,NULL,NULL,'$2y$12$O7Mr8KzOUyN0CaZlLP5YK.cNRrTm9wZgS.BprG6VhssLuIaKq.al.','porteur',NULL,NULL,NULL,NULL,NULL,'actif',NULL,'2026-05-07 08:38:22','2026-05-07 08:38:22');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Dumping events for database 'alogoto_backend'
--

--
-- Dumping routines for database 'alogoto_backend'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-05-18 12:26:13
