-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: drs_app_db
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `utilisateur_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `adresse_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `anciennes_valeurs` json DEFAULT NULL,
  `nouvelles_valeurs` json DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_module_created_at_index` (`module`,`created_at`),
  KEY `audit_logs_utilisateur_id_created_at_index` (`utilisateur_id`,`created_at`),
  CONSTRAINT `audit_logs_utilisateur_id_foreign` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courriers`
--

DROP TABLE IF EXISTS `courriers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courriers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_courrier_id` bigint unsigned DEFAULT NULL,
  `objet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_courrier_physique` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_emetteur_id` bigint unsigned DEFAULT NULL,
  `direction_id` bigint unsigned DEFAULT NULL,
  `date_reception` date NOT NULL,
  `date_envoi` date DEFAULT NULL,
  `date_traitement` date DEFAULT NULL,
  `montant` decimal(15,2) DEFAULT NULL,
  `devise` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'XAF',
  `beneficiaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motif` text COLLATE utf8mb4_unicode_ci,
  `urgence` enum('normal','moyenne','haute') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `confidentialite` enum('standard','confidentiel','secret') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `statut_general` enum('brouillon','enregistre','en_parapheur','traite','archive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enregistre',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courriers_numero_unique` (`numero`),
  KEY `courriers_service_emetteur_id_foreign` (`service_emetteur_id`),
  KEY `courriers_direction_id_foreign` (`direction_id`),
  KEY `courriers_created_by_foreign` (`created_by`),
  KEY `courriers_updated_by_foreign` (`updated_by`),
  CONSTRAINT `courriers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `courriers_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`),
  CONSTRAINT `courriers_service_emetteur_id_foreign` FOREIGN KEY (`service_emetteur_id`) REFERENCES `services` (`id`),
  CONSTRAINT `courriers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courriers`
--

LOCK TABLES `courriers` WRITE;
/*!40000 ALTER TABLE `courriers` DISABLE KEYS */;
/*!40000 ALTER TABLE `courriers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `directions`
--

DROP TABLE IF EXISTS `directions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `directions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `directeur_id` bigint unsigned DEFAULT NULL,
  `sigle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mission` text COLLATE utf8mb4_unicode_ci,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `directions_code_unique` (`code`),
  KEY `directions_directeur_id_foreign` (`directeur_id`),
  CONSTRAINT `directions_directeur_id_foreign` FOREIGN KEY (`directeur_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `directions`
--

LOCK TABLES `directions` WRITE;
/*!40000 ALTER TABLE `directions` DISABLE KEYS */;
/*!40000 ALTER TABLE `directions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fichier_parapheurs`
--

DROP TABLE IF EXISTS `fichier_parapheurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fichier_parapheurs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parapheur_id` bigint unsigned NOT NULL,
  `nom_original` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_stockage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_mime` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille` int NOT NULL,
  `extension` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploader_id` bigint unsigned NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `telechargements` int NOT NULL DEFAULT '0',
  `est_signature` tinyint(1) NOT NULL DEFAULT '0',
  `est_principal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fichier_parapheurs_parapheur_id_foreign` (`parapheur_id`),
  KEY `fichier_parapheurs_uploader_id_foreign` (`uploader_id`),
  CONSTRAINT `fichier_parapheurs_parapheur_id_foreign` FOREIGN KEY (`parapheur_id`) REFERENCES `parapheurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fichier_parapheurs_uploader_id_foreign` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fichier_parapheurs`
--

LOCK TABLES `fichier_parapheurs` WRITE;
/*!40000 ALTER TABLE `fichier_parapheurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `fichier_parapheurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historique_courriers`
--

DROP TABLE IF EXISTS `historique_courriers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historique_courriers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historique_courriers`
--

LOCK TABLES `historique_courriers` WRITE;
/*!40000 ALTER TABLE `historique_courriers` DISABLE KEYS */;
/*!40000 ALTER TABLE `historique_courriers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historique_parapheurs`
--

DROP TABLE IF EXISTS `historique_parapheurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historique_parapheurs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parapheur_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` enum('creation','modification','validation','rejet','transmission','ajout_fichier','suppression_fichier','changement_statut','changement_priorite','ajout_note','relance','archivage') COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadata` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historique_parapheurs_user_id_foreign` (`user_id`),
  KEY `historique_parapheurs_parapheur_id_created_at_index` (`parapheur_id`,`created_at`),
  CONSTRAINT `historique_parapheurs_parapheur_id_foreign` FOREIGN KEY (`parapheur_id`) REFERENCES `parapheurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `historique_parapheurs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historique_parapheurs`
--

LOCK TABLES `historique_parapheurs` WRITE;
/*!40000 ALTER TABLE `historique_parapheurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `historique_parapheurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_12_07_144847_create_roles_table',1),(5,'2025_12_12_145449_create_services_table',1),(6,'2025_12_12_145453_create_directions_table',1),(7,'2025_12_12_145454_create_parapheurs_table',1),(8,'2025_12_12_145457_create_fichier_parapheurs_table',1),(9,'2025_12_12_145458_create_historique_parapheurs_table',1),(10,'2025_12_12_155624_create_permission_tables',2),(11,'2025_12_12_155624_create_permission_tables',1),(12,'2025_12_17_094110_create_courriers_table',3),(13,'2025_12_17_094115_create_paraphe_workflows_table',4),(14,'2025_12_17_094116_create_paraphe_actions_table',4),(15,'2025_12_17_094118_create_pieces_jointes_table',4),(16,'2025_12_17_094119_create_observations_table',4),(17,'2025_12_17_094120_create_notifications_table',4),(18,'2025_12_17_101702_create_type_courriers_table',4),(19,'2026_01_13_104706_add_metier_fields_to_courriers_table',5),(20,'2026_01_14_104954_create_historique_courriers_table',6),(21,'2026_01_14_115026_create_validations_table',6),(22,'2026_01_14_115336_create_piece_jointes_table',6),(23,'2026_01_14_115559_create_services_table',5),(24,'2026_01_15_154106_add_numero_courrier_physique_to_courriers_table',5),(25,'2026_01_13_104706_add_metier_fields_to_courriers_table',5),(26,'2026_01_14_115559_create_services_table',5),(27,'2026_01_15_154106_add_numero_courrier_physique_to_courriers_table',5),(28,'2026_01_18_164004_create_permission_tables',5),(29,'2026_02_20_213055_create_roles_permissions_tables',5),(30,'2026_02_20_213141_create_configurations_table',5),(31,'2026_02_20_213547_add_admin_fields_to_users_table',5),(32,'2026_02_20_213622_create_roles_and_permissions_tables',5),(33,'2026_02_20_213644_create_audit_and_config_tables',5),(34,'2026_03_08_190751_create_audit_logs_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(1,'App\\Models\\User',2),(3,'App\\Models\\User',3),(4,'App\\Models\\User',4),(5,'App\\Models\\User',5),(5,'App\\Models\\User',6),(5,'App\\Models\\User',7),(6,'App\\Models\\User',8),(4,'App\\Models\\User',9),(4,'App\\Models\\User',10);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL,
  `lien` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lien_texte` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  `date_lecture` timestamp NULL DEFAULT NULL,
  `expire_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_lu_created_at_index` (`user_id`,`lu`,`created_at`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `observations`
--

DROP TABLE IF EXISTS `observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `observations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parapheur_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('observation','recommandation','alerte','question','reponse') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'observation',
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `resolu` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `observations_parapheur_id_foreign` (`parapheur_id`),
  KEY `observations_user_id_foreign` (`user_id`),
  KEY `observations_parent_id_foreign` (`parent_id`),
  CONSTRAINT `observations_parapheur_id_foreign` FOREIGN KEY (`parapheur_id`) REFERENCES `parapheurs` (`id`),
  CONSTRAINT `observations_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `observations` (`id`),
  CONSTRAINT `observations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `observations`
--

LOCK TABLES `observations` WRITE;
/*!40000 ALTER TABLE `observations` DISABLE KEYS */;
/*!40000 ALTER TABLE `observations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paraphe_actions`
--

DROP TABLE IF EXISTS `paraphe_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paraphe_actions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parapheur_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `action` enum('creation','analyse','transmission','validation','signature','rejet','observation','archivage') COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `statut_avant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut_apres` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_data` json DEFAULT NULL,
  `signature_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paraphe_actions_user_id_foreign` (`user_id`),
  KEY `paraphe_actions_parapheur_id_created_at_index` (`parapheur_id`,`created_at`),
  CONSTRAINT `paraphe_actions_parapheur_id_foreign` FOREIGN KEY (`parapheur_id`) REFERENCES `parapheurs` (`id`),
  CONSTRAINT `paraphe_actions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paraphe_actions`
--

LOCK TABLES `paraphe_actions` WRITE;
/*!40000 ALTER TABLE `paraphe_actions` DISABLE KEYS */;
/*!40000 ALTER TABLE `paraphe_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paraphe_workflows`
--

DROP TABLE IF EXISTS `paraphe_workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paraphe_workflows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parapheur_id` bigint unsigned NOT NULL,
  `etapes_config` json DEFAULT NULL,
  `etape_actuelle` int NOT NULL DEFAULT '1',
  `date_debut` datetime NOT NULL,
  `date_fin_prevue` date DEFAULT NULL,
  `date_fin_reelle` date DEFAULT NULL,
  `duree_estimee_jours` int NOT NULL DEFAULT '5',
  `duree_reelle_jours` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paraphe_workflows_parapheur_id_foreign` (`parapheur_id`),
  CONSTRAINT `paraphe_workflows_parapheur_id_foreign` FOREIGN KEY (`parapheur_id`) REFERENCES `parapheurs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paraphe_workflows`
--

LOCK TABLES `paraphe_workflows` WRITE;
/*!40000 ALTER TABLE `paraphe_workflows` DISABLE KEYS */;
/*!40000 ALTER TABLE `paraphe_workflows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parapheurs`
--

DROP TABLE IF EXISTS `parapheurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parapheurs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('brouillon','en_attente','en_cours','valide','rejete','en_retard','archive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon',
  `priorite` enum('basse','normale','haute','urgente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normale',
  `confidentialite` enum('standard','confidentiel','tres_confidentiel') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `date_creation` date NOT NULL,
  `date_echeance` date NOT NULL,
  `date_validation` datetime DEFAULT NULL,
  `date_rejet` datetime DEFAULT NULL,
  `createur_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `direction_id` bigint unsigned NOT NULL,
  `responsable_actuel_id` bigint unsigned DEFAULT NULL,
  `etape_actuelle` int NOT NULL DEFAULT '1',
  `etapes_total` int NOT NULL DEFAULT '3',
  `workflow` json DEFAULT NULL,
  `motif_rejet` text COLLATE utf8mb4_unicode_ci,
  `notes_internes` text COLLATE utf8mb4_unicode_ci,
  `notifier_createur` tinyint(1) NOT NULL DEFAULT '1',
  `notifier_responsable` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parapheurs_reference_unique` (`reference`),
  KEY `parapheurs_createur_id_foreign` (`createur_id`),
  KEY `parapheurs_responsable_actuel_id_foreign` (`responsable_actuel_id`),
  KEY `parapheurs_statut_date_echeance_index` (`statut`,`date_echeance`),
  KEY `parapheurs_service_id_created_at_index` (`service_id`,`created_at`),
  KEY `parapheurs_direction_id_statut_index` (`direction_id`,`statut`),
  CONSTRAINT `parapheurs_createur_id_foreign` FOREIGN KEY (`createur_id`) REFERENCES `users` (`id`),
  CONSTRAINT `parapheurs_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`),
  CONSTRAINT `parapheurs_responsable_actuel_id_foreign` FOREIGN KEY (`responsable_actuel_id`) REFERENCES `users` (`id`),
  CONSTRAINT `parapheurs_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parapheurs`
--

LOCK TABLES `parapheurs` WRITE;
/*!40000 ALTER TABLE `parapheurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `parapheurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'courrier.enregistrer','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(2,'courrier.consulter','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(3,'courrier.analyser','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(4,'courrier.modifier','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(5,'courrier.supprimer','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(6,'courrier.archiver','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(7,'parapheur.consulter','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(8,'parapheur.deposer','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(9,'parapheur.viser','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(10,'parapheur.signer','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(11,'parapheur.rejeter','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(12,'parapheur.annoter','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(13,'tableau.consulter','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(14,'tableau.valider_ligne','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(15,'tableau.rejeter_ligne','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(16,'admin.utilisateurs','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(17,'admin.roles','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(18,'admin.services','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(19,'admin.audit','web','2026-03-21 15:20:21','2026-03-21 15:20:21'),(20,'admin.configuration','web','2026-03-21 15:20:21','2026-03-21 15:20:21');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `piece_jointes`
--

DROP TABLE IF EXISTS `piece_jointes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `piece_jointes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `courrier_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille` int unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `categorie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `est_obligatoire` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `piece_jointes_courrier_id_index` (`courrier_id`),
  KEY `piece_jointes_user_id_index` (`user_id`),
  KEY `piece_jointes_categorie_index` (`categorie`),
  CONSTRAINT `piece_jointes_courrier_id_foreign` FOREIGN KEY (`courrier_id`) REFERENCES `courriers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `piece_jointes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `piece_jointes`
--

LOCK TABLES `piece_jointes` WRITE;
/*!40000 ALTER TABLE `piece_jointes` DISABLE KEYS */;
/*!40000 ALTER TABLE `piece_jointes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pieces_jointes`
--

DROP TABLE IF EXISTS `pieces_jointes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pieces_jointes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `courrier_id` bigint unsigned NOT NULL,
  `nom_original` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_stockage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_mime` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille` int NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categorie` enum('document','justificatif','signature','autre') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'document',
  `hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verifie` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pieces_jointes_nom_stockage_unique` (`nom_stockage`),
  KEY `pieces_jointes_courrier_id_foreign` (`courrier_id`),
  CONSTRAINT `pieces_jointes_courrier_id_foreign` FOREIGN KEY (`courrier_id`) REFERENCES `courriers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pieces_jointes`
--

LOCK TABLES `pieces_jointes` WRITE;
/*!40000 ALTER TABLE `pieces_jointes` DISABLE KEYS */;
/*!40000 ALTER TABLE `pieces_jointes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'superadmin','web','2026-01-11 15:55:25','2026-01-11 15:55:25'),(2,'admin','web','2026-01-11 15:55:25','2026-01-11 15:55:25'),(3,'secretaire','web','2026-01-11 15:55:25','2026-01-11 15:55:25'),(4,'agent','web','2026-01-11 15:55:25','2026-01-11 15:55:25'),(5,'chef_service','web','2026-01-11 15:55:25','2026-01-11 15:55:25'),(6,'directeur','web','2026-01-11 15:55:25','2026-01-11 15:55:25'),(7,'gestionnaire','web','2026-01-18 17:07:21','2026-01-18 17:07:21'),(8,'careerinns','web','2026-02-03 16:02:14','2026-02-03 16:02:14');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction_id` bigint unsigned DEFAULT NULL,
  `chef_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `services_code_unique` (`code`),
  KEY `services_chef_id_foreign` (`chef_id`),
  CONSTRAINT `services_chef_id_foreign` FOREIGN KEY (`chef_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'IFD','Service des Incitations Fiscales pour le Développement',NULL,NULL,'Gère les demandes d\'exonération fiscale pour le développement économique','ifd@dgi.ga','+241 01 23 45 67',1,NULL,NULL,NULL),(2,'GFMPF','Service Gestion Fiscales Mines, Pétrole et Forêts',NULL,NULL,'Gère les demandes pour les secteurs minier, pétrolier et forestier','gfmpf@dgi.ga','+241 01 23 45 68',1,NULL,NULL,NULL),(3,'ZES','Service des Zones Économiques Spéciales',NULL,NULL,'Gère les demandes pour les zones économiques spéciales','zes@dgi.ga','+241 01 23 45 69',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('3bAO3Co1YTgcsSIeC1Axz8AItFBwMlQhLnJRYHxt',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoicUJkVUN6WmRIdkthdzd1Uk5uTEhUUE5TVXJvWjBYOUhiSHE5SHBHRSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2NvdXJyaWVycyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjE1OiJhZG1pbi5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1773698152),('bXNQlTElrHvs1qp9iBp4xlG45dzNjByqiapHzhIS',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMGZKSGF1MGc1OFZrOHFVTGpDRE9FWkFZeDN2MkpWRnI1d2d4eXhFSiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3V0aWxpc2F0ZXVycy9jcmVhdGUiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo0MDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3V0aWxpc2F0ZXVycyI7czo1OiJyb3V0ZSI7czoxODoiYWRtaW4udXRpbGlzYXRldXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1774206598),('gfAuj1pHOYmD6CQ8HA1EmAZBGJqMfqTnj5vHSuw7',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVmxwQXNFdWR2dmllNHJRWGpsdXNYZUIweEE0Vm90SjJuS1JoelFNSCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3V0aWxpc2F0ZXVycyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vdXRpbGlzYXRldXJzL2NyZWF0ZSI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4udXRpbGlzYXRldXJzLWNyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1774130669),('gQ4T2gj1nkGzibCH4FiNOV2GDmhvGoEDXDwnddTJ',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVGFFODdsZXZwV1NEUWZKMzFiUzJ2MWw1S01DZWd4UWI3NzAzdkt2YSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi91dGlsaXNhdGV1cnMiO3M6NToicm91dGUiO3M6MTY6ImFkbWluLnVzZXJzLWxpc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1774117608);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_courriers`
--

DROP TABLE IF EXISTS `type_courriers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `type_courriers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_courriers_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_courriers`
--

LOCK TABLES `type_courriers` WRITE;
/*!40000 ALTER TABLE `type_courriers` DISABLE KEYS */;
/*!40000 ALTER TABLE `type_courriers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_id` bigint unsigned DEFAULT NULL,
  `direction_id` bigint unsigned DEFAULT NULL,
  `poste` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_matricule_unique` (`matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','superadmin@gedf.com','2026-01-11 15:33:57','$2y$12$IW0Mo0.itn4C3afLfCWHDeK.3UXXbTqDfkn7GLBD4cbLt5n..0unq','SUP001',NULL,NULL,'Super Admin','770000000',1,NULL,NULL,'2026-01-11 15:33:57','2026-01-18 16:12:07',NULL),(2,'Super Admin','admin@dgi.ga','2026-02-20 21:29:06','$2y$12$yJh3fl7G2oRdzmvMEFxw3eb6YLoS8S1sU3j9BhYIaTFdupNEwERZW','ADMIN20260220523',NULL,NULL,'Super Administrateur','00000000',1,NULL,NULL,'2026-02-20 21:29:06','2026-02-20 21:29:06',NULL),(3,'Marie Secrétaire','secretaire@gedf.com',NULL,'$2y$12$1GCuPSRqhgixU0jPcVgRxOxrjB8fi4BHpmFww6ZMfbs6qonMgPxMe','SEC20260321431',1,NULL,'Secrétaire administrative',NULL,1,NULL,NULL,'2026-03-21 15:39:29','2026-03-21 15:39:29',NULL),(4,'Paul Agent','agent@gedf.com',NULL,'$2y$12$h7yg6y5aFgG0D8m2dYsCyu74U9ThIksGHG4FoMzOc7cLui2I5xT76','AGE20260321914',1,NULL,'Agent de gestion',NULL,1,NULL,NULL,'2026-03-21 15:39:29','2026-03-21 15:39:29',NULL),(5,'Carine Chef IFD','chef.ifd@gedf.com',NULL,'$2y$12$jCtm20rky9GyZRvG/f0qVO.oF96/Ok/23UmqbaHJHCxlgUpexl1.q','CHE20260321849',1,NULL,'Chef de Service IFD',NULL,1,NULL,NULL,'2026-03-21 15:39:29','2026-03-21 15:39:29',NULL),(6,'Ida Chef GFMPF','chef.gfmpf@gedf.com',NULL,'$2y$12$C8EbJvL2PbvdHQoUcOjHGeX6Gx4TWX/qwp06QI66WTtdsWvhn4N5G','CHE20260321493',2,NULL,'Chef de Service GFMPF',NULL,1,NULL,NULL,'2026-03-21 15:39:29','2026-03-21 15:39:29',NULL),(7,'Reine Chef ZES','chef.zes@gedf.com',NULL,'$2y$12$l5b58dveOLVO.uaByoWEFu3XvU7JmRRtWlxr0XPhqQoidZ/R53k5S','CHE20260321852',3,NULL,'Chef de Service ZES',NULL,1,NULL,NULL,'2026-03-21 15:39:30','2026-03-21 15:39:30',NULL),(8,'Carl Directeur DRS','directeur.drs@gedf.com',NULL,'$2y$12$ABgQSz6PKhJ3u2/EMQx7geQKXaBku6FBw9vzG3WzlVa0WcExc5iai','DIR20260321614',NULL,NULL,'Directeur des Régimes Spécifiques',NULL,1,NULL,NULL,'2026-03-21 15:39:30','2026-03-21 15:39:30',NULL),(9,'Test Agent','test.agent@gedf.com',NULL,'$2y$12$7627gQaHTLkQOxzHlfEwrOVD8OHd7lkLlqxifcBhg5wQftCFJ6giO','AGT20260321001',1,NULL,'Agent de test',NULL,1,NULL,NULL,'2026-03-21 20:34:55','2026-03-21 20:34:55',NULL),(10,'Jean Dupont','jean.dupont@gedf.com',NULL,'$2y$12$h1cu8bGueOZltEHBbeRZAu4MOHKIlOKRr78ftK362filSsiaSDEqe','JD2026001',1,NULL,'Agent de saisie',NULL,1,NULL,NULL,'2026-03-22 17:27:58','2026-03-22 17:27:58',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `validations`
--

DROP TABLE IF EXISTS `validations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `validations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `courrier_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role_validation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `ordre` int NOT NULL DEFAULT '1',
  `date_validation` timestamp NULL DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `validations_courrier_id_statut_index` (`courrier_id`,`statut`),
  KEY `validations_user_id_statut_index` (`user_id`,`statut`),
  KEY `validations_role_validation_statut_index` (`role_validation`,`statut`),
  CONSTRAINT `validations_courrier_id_foreign` FOREIGN KEY (`courrier_id`) REFERENCES `courriers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `validations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `validations`
--

LOCK TABLES `validations` WRITE;
/*!40000 ALTER TABLE `validations` DISABLE KEYS */;
/*!40000 ALTER TABLE `validations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-23  0:09:45
