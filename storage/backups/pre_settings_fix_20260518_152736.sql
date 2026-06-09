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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analysis_histories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `analysis_histories` WRITE;
/*!40000 ALTER TABLE `analysis_histories` DISABLE KEYS */;
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
  KEY `idx_audit_user_date` (`user_id`,`created_at`),
  KEY `idx_audit_action` (`action`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
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
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba','i:2;',1779110301),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1779110301;',1779110301);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `last_message_at` timestamp NULL DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  KEY `idx_financements_project_id` (`project_id`),
  KEY `idx_financements_institution_id` (`institution_id`),
  KEY `idx_financements_statut` (`statut`),
  KEY `financements_porteur_id_foreign` (`porteur_id`),
  CONSTRAINT `financements_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `financements_porteur_id_foreign` FOREIGN KEY (`porteur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `financements_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financements`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `financements` WRITE;
/*!40000 ALTER TABLE `financements` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institution_analyses`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `institution_analyses` WRITE;
/*!40000 ALTER TABLE `institution_analyses` DISABLE KEYS */;
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
(2,6,'Banque Atlantique Benin','institution1@alogoto.bj','+229 09 23 29 13','Parakou, Benin','actif','2026-05-18 10:15:57','2026-05-18 10:15:57');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interview_histories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `interview_histories` WRITE;
/*!40000 ALTER TABLE `interview_histories` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interviews`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `interviews` WRITE;
/*!40000 ALTER TABLE `interviews` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_audits`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `message_audits` WRITE;
/*!40000 ALTER TABLE `message_audits` DISABLE KEYS */;
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
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  KEY `idx_messages_conversation` (`conversation_id`),
  KEY `idx_messages_created` (`created_at`),
  KEY `messages_reply_to_foreign` (`reply_to`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `messages_reply_to_foreign` FOREIGN KEY (`reply_to`) REFERENCES `messages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(23,'2026_04_22_000001_update_project_status_histories_table',1),
(24,'2026_04_22_000002_create_transactions_table',1),
(25,'2026_04_22_000003_convert_amounts_to_decimal',1),
(26,'2026_04_22_000004_add_foreign_key_constraints',1),
(27,'2026_04_22_000005_remove_redundant_status_columns',1),
(28,'2026_04_22_000006_add_performance_indexes',1),
(29,'2026_05_05_000007_normalize_project_status_values',1),
(30,'2026_05_06_000001_update_messaging_tables',1),
(31,'2026_05_06_151856_create_rendez_vous_table',1),
(32,'2026_05_06_164145_add_raison_rejet_to_project_documents',1),
(33,'2026_05_07_094650_create_repayment_histories_table',1),
(34,'2026_05_07_094757_update_remboursements_table_add_missing_fields',1),
(35,'2026_05_07_141345_add_archived_at_to_notifications_table',1),
(36,'2026_05_07_153953_add_profile_fields_to_users_table',1),
(37,'2026_05_12_012045_add_scores_to_institution_analyses_table',1),
(38,'2026_05_12_012045_create_analysis_histories_table',1),
(39,'2026_05_12_013353_create_interviews_table',1),
(40,'2026_05_12_013354_create_interview_histories_table',1),
(41,'2026_05_12_014210_add_monitoring_fields_to_remboursements_table',1),
(42,'2026_05_12_014211_create_repayment_disputes_table',1),
(43,'2026_05_12_014211_create_repayment_events_table',1),
(44,'2026_05_12_014945_add_business_fields_to_financements_table',1),
(45,'2026_05_12_014945_create_financement_histories_table',1),
(46,'2026_05_12_014946_create_financement_documents_table',1),
(47,'2026_05_12_015904_add_ui_fields_to_audit_logs_table',1),
(48,'2026_05_12_073500_enhance_messaging_system',1),
(49,'2026_05_12_115703_create_project_monitoring_tables',1),
(50,'2026_05_13_000001_cleanup_legacy_project_columns',1),
(51,'2026_05_13_143110_add_unique_constraint_to_telephone_in_users_table',1),
(52,'2026_05_14_152628_add_provider_fields_to_users_table',1),
(53,'2026_05_18_074641_add_branche_to_projects_table',1),
(54,'2026_05_18_114256_fix_last_message_at_column_type',1);
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
  KEY `idx_notifications_user_read` (`user_id`,`is_read`),
  KEY `idx_notifications_created` (`created_at`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES
(2,4,'nouveau_projet','Nouveau projet soumis','Le projet \"ALOGOTO\" a été soumis pour validation.',0,NULL,'2026-05-18 11:13:57','2026-05-18 11:13:57'),
(3,5,'projet_soumis','Projet soumis','Votre projet \"ALOGOTO\" a été soumis pour validation auprès de l\'administration.',0,NULL,'2026-05-18 11:13:57','2026-05-18 11:13:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES
(1,'App\\Models\\User',4,'api-token','355846eaec6a717b1939e9f32fb0e7da1fe3505b9024c282b4ecc9f14576277b','[\"*\"]','2026-05-18 10:48:00',NULL,'2026-05-18 10:47:52','2026-05-18 10:48:00'),
(2,'App\\Models\\User',5,'api-token','bd6b77f0fe0d92b9d7fbc370d065dd7486aa290bfde63238c0c38961c5462638','[\"*\"]','2026-05-18 10:48:08',NULL,'2026-05-18 10:47:53','2026-05-18 10:48:08'),
(3,'App\\Models\\User',6,'api-token','49499fada1429b9359da42bc387372a4e5b97677d1adf35df2691a4a71c482d9','[\"*\"]','2026-05-18 10:48:01',NULL,'2026-05-18 10:47:54','2026-05-18 10:48:01');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_documents`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `project_documents` WRITE;
/*!40000 ALTER TABLE `project_documents` DISABLE KEYS */;
INSERT INTO `project_documents` VALUES
(1,14,'piece_identite','documents/14/1779109529_WhatsApp Image 2026-04-17 at 17.14.50 (1).jpeg','en_attente',NULL,'2026-05-18 11:05:29','2026-05-18 11:05:29','2026-05-18 11:05:29'),
(2,14,'description_projet','documents/14/1779109529_Cahier_des_charges_MISS_KETOU_LA_REINE.pdf','en_attente',NULL,'2026-05-18 11:05:29','2026-05-18 11:05:29','2026-05-18 11:05:29'),
(3,14,'objectif_financement','documents/14/1779109529_Formulaires_Enquete_Alogoto (1).docx','en_attente',NULL,'2026-05-18 11:05:29','2026-05-18 11:05:29','2026-05-18 11:05:29'),
(4,14,'plan_utilisation_fonds','documents/14/1779109529_ABP MEMOIRE.pdf','en_attente',NULL,'2026-05-18 11:05:29','2026-05-18 11:05:29','2026-05-18 11:05:29'),
(5,14,'garanties','documents/14/1779109529_ChatGPT_Image_27_avr._2026__13_00_30-removebg-preview.png','en_attente',NULL,'2026-05-18 11:05:29','2026-05-18 11:05:29','2026-05-18 11:05:29'),
(6,14,'plan_remboursement','documents/14/1779109529_PROJET MISS KETOU LA REINE.pdf','en_attente',NULL,'2026-05-18 11:05:29','2026-05-18 11:05:29','2026-05-18 11:05:29'),
(7,14,'justificatif_residence','documents/14/1779109529_Guide PROJET Jud.pdf','en_attente',NULL,'2026-05-18 11:05:29','2026-05-18 11:05:29','2026-05-18 11:05:29'),
(8,14,'historique_financier','documents/14/1779109529_WhatsApp Image 2026-04-17 at 21.39.21 (1).jpeg','en_attente',NULL,'2026-05-18 11:05:29','2026-05-18 11:05:29','2026-05-18 11:05:29'),
(9,14,'photo_identite','documents/14/1779109529_totine.png','en_attente',NULL,'2026-05-18 11:05:29','2026-05-18 11:05:29','2026-05-18 11:05:29'),
(10,14,'preuve_activite','documents/14/1779109529_ChatGPT Image 13 mai 2026, 19_59_39.png','en_attente',NULL,'2026-05-18 11:05:29','2026-05-18 11:05:29','2026-05-18 11:05:29');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_risk_scores`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `project_risk_scores` WRITE;
/*!40000 ALTER TABLE `project_risk_scores` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_status_histories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `project_status_histories` WRITE;
/*!40000 ALTER TABLE `project_status_histories` DISABLE KEYS */;
INSERT INTO `project_status_histories` VALUES
(20,14,'draft','draft','Projet créé (brouillon)',5,'{\"source\":\"web.projects.store\"}','2026-05-18 11:05:29','2026-05-18 11:05:29'),
(21,14,'draft','submitted','Projet soumis par le porteur',5,'{\"project_title\":\"ALOGOTO\",\"project_sector\":\"informatique\",\"amount\":\"1500000.00\",\"transition_timestamp\":\"2026-05-18T13:13:57+00:00\"}','2026-05-18 11:13:57','2026-05-18 11:13:57');
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
  KEY `idx_projects_user_id` (`user_id`),
  KEY `idx_projects_statut` (`statut`),
  KEY `idx_projects_secteur` (`secteur`),
  KEY `idx_projects_created_at` (`created_at`),
  CONSTRAINT `projects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES
(14,5,'ALOGOTO','<p>ALOGOTO est une plateforme numérique intelligente de mise en relation entre porteurs de projets et institutions de microfinance, conçue pour faciliter le financement, le suivi et la gestion complète des projets entrepreneuriaux au Bénin.</p>','informatique','Plateforme',1500000.00,0.00,'09','Porto-Novo , Benin','submitted','2026-05-18 11:05:29','2026-05-18 11:13:57');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `realtime_notifications`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `realtime_notifications` WRITE;
/*!40000 ALTER TABLE `realtime_notifications` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remboursements`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `remboursements` WRITE;
/*!40000 ALTER TABLE `remboursements` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rendez_vous`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `rendez_vous` WRITE;
/*!40000 ALTER TABLE `rendez_vous` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repayment_events`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `repayment_events` WRITE;
/*!40000 ALTER TABLE `repayment_events` DISABLE KEYS */;
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
('QiobHeRCcVudxz1QKWioxDR8K8HRXEZKxgDbOpPd',4,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTlFVMXdVRlFSM1lZTHpESERZV2xjQTdKc3BxS2x1UVJtam1qaEhaZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Njk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9mcm9udGVuZC9kYXNoYm9hcmQwMi9zaGFyZWQvanMvcmVhbHRpbWUtaW5pdC5qcyI7czo1OiJyb3V0ZSI7czoxNjoiZGFzaGJvYXJkMDIuZmlsZSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6IjNjZjMzMWUzMzU4ZWY5NzYwOGQ5MGNmNDA4ZDAxOWIxYTkyYzgwZjEwMWMxYTYxMTI1YjNlMzcxOTVkMzZlYzMiO30=',1779110653);
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(4,'ADECHINA Gaston',NULL,'admin@alogoto.bj','2026-05-18 10:15:56',NULL,NULL,NULL,NULL,'$2y$12$ExNtsMER49iWQic1wGbbIOzrk0G.R5gsoyGdWKRn4E96nAN0KVgFy','admin','+229 90 00 00 00',NULL,NULL,NULL,NULL,'actif','epcou2j0UBV4Zb8W13jcgShSaXbuNBUMIpLScCDUqgcjhIKLnB6ogRR1qAnY','2026-05-18 10:15:56','2026-05-18 10:15:56'),
(5,'Nadine Dossou',NULL,'porteur1@alogoto.bj','2026-05-18 10:15:56',NULL,NULL,NULL,NULL,'$2y$12$Gq3XPUpfLF3qEyP0B/9sVuIz0sNssJHYeOSv4y7Ftv5KHI/Cv1wGq','porteur','+229 90 11 11 11',NULL,NULL,NULL,NULL,'actif','j29ZGzjiW7hGrWM8n90KMMrYk0RCRpzzQqwDRgS2ZAolOKZyTcnsL69o4Dpy','2026-05-18 10:15:56','2026-05-18 10:15:56'),
(6,'Banque Atlantique Benin',NULL,'institution1@alogoto.bj','2026-05-18 10:15:56',NULL,NULL,NULL,NULL,'$2y$12$.dWCMw4QUAgzC7cPlzLnEuGNMe1LzipqrOAXtJKODW2Ob7dQ7EdJy','institution','+229 90 22 22 22',NULL,NULL,NULL,NULL,'verifie','mRBiqB2d2Z8tdUNT5FrhyQgHxon69Xe4gnv5621AplzAruBNtbLPSZSHKCe3','2026-05-18 10:15:56','2026-05-18 10:15:56');
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

-- Dump completed on 2026-05-18 15:27:36
