-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: casemix_audit
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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
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
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `claim_ai_results`
--

DROP TABLE IF EXISTS `claim_ai_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claim_ai_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `claim_episode_id` bigint unsigned NOT NULL,
  `model_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prompt_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_diagnosis_text` text COLLATE utf8mb4_unicode_ci,
  `primary_icd10_json` json DEFAULT NULL,
  `secondary_icd10_json` json DEFAULT NULL,
  `procedure_json` json DEFAULT NULL,
  `confidence_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `missing_data_json` json DEFAULT NULL,
  `ai_notes` text COLLATE utf8mb4_unicode_ci,
  `raw_response_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claim_ai_results_claim_episode_id_confidence_score_index` (`claim_episode_id`,`confidence_score`),
  CONSTRAINT `claim_ai_results_claim_episode_id_foreign` FOREIGN KEY (`claim_episode_id`) REFERENCES `claim_episodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claim_ai_results`
--

LOCK TABLES `claim_ai_results` WRITE;
/*!40000 ALTER TABLE `claim_ai_results` DISABLE KEYS */;
INSERT INTO `claim_ai_results` VALUES (1,1,'mock-gpt','v1','Sepsis ec pneumonia berat','[{\"code\": \"A41.9\", \"label\": \"Sepsis, unspecified organism\", \"confidence\": 0.88}]','[{\"code\": \"J18.9\", \"label\": \"Pneumonia, unspecified organism\", \"confidence\": 0.84}]','[{\"code\": \"96.72\", \"label\": \"Continuous mechanical ventilation\", \"confidence\": 0.82}]',88.50,'[]','Mock AI result berhasil disimpan.','{\"source\": \"test-route\"}','2026-04-14 22:43:07','2026-04-14 22:43:07'),(2,1,'mock-gpt','v1','Sepsis ec pneumonia berat','[{\"code\": \"A41.9\", \"label\": \"Sepsis, unspecified organism\", \"confidence\": 0.88}]','[{\"code\": \"J18.9\", \"label\": \"Pneumonia, unspecified organism\", \"confidence\": 0.84}]','[{\"code\": \"96.72\", \"label\": \"Continuous mechanical ventilation\", \"confidence\": 0.82}]',88.50,'[]','Mock AI result berhasil disimpan dari dashboard.','{\"source\": \"dashboard\"}','2026-04-15 23:19:45','2026-04-15 23:19:45');
/*!40000 ALTER TABLE `claim_ai_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `claim_audit_flags`
--

DROP TABLE IF EXISTS `claim_audit_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claim_audit_flags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `claim_episode_id` bigint unsigned NOT NULL,
  `flag_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag_description` text COLLATE utf8mb4_unicode_ci,
  `evidence_json` json DEFAULT NULL,
  `source_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rule',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claim_audit_flags_claim_episode_id_status_index` (`claim_episode_id`,`status`),
  KEY `claim_audit_flags_flag_code_severity_index` (`flag_code`,`severity`),
  KEY `claim_audit_flags_flag_type_index` (`flag_type`),
  KEY `claim_audit_flags_severity_index` (`severity`),
  KEY `claim_audit_flags_flag_code_index` (`flag_code`),
  KEY `claim_audit_flags_source_type_index` (`source_type`),
  KEY `claim_audit_flags_status_index` (`status`),
  CONSTRAINT `claim_audit_flags_claim_episode_id_foreign` FOREIGN KEY (`claim_episode_id`) REFERENCES `claim_episodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claim_audit_flags`
--

LOCK TABLES `claim_audit_flags` WRITE;
/*!40000 ALTER TABLE `claim_audit_flags` DISABLE KEYS */;
INSERT INTO `claim_audit_flags` VALUES (1,1,'clinical_mismatch','high','DX_PROC_MISMATCH','Diagnosis dan prosedur perlu review','Perlu verifikasi kesesuaian diagnosis dengan tindakan.','{\"resume\": \"Pasien sepsis pneumonia\", \"procedure\": \"Ventilator mekanik\"}','rule','open',NULL,NULL,NULL,'2026-04-14 22:43:27','2026-04-14 22:43:27'),(2,1,'document_missing','medium','DOC_MISSING_OPREPORT','Laporan operasi belum tersedia','Dokumen operasi belum terlampir.','{\"document_type\": \"laporan_operasi\"}','rule','open',NULL,NULL,NULL,'2026-04-14 22:43:27','2026-04-14 22:43:27');
/*!40000 ALTER TABLE `claim_audit_flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `claim_documents`
--

DROP TABLE IF EXISTS `claim_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claim_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `claim_episode_id` bigint unsigned NOT NULL,
  `document_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_url` text COLLATE utf8mb4_unicode_ci,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_available` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claim_documents_claim_episode_id_document_type_index` (`claim_episode_id`,`document_type`),
  KEY `claim_documents_document_type_index` (`document_type`),
  KEY `claim_documents_is_required_index` (`is_required`),
  KEY `claim_documents_is_available_index` (`is_available`),
  CONSTRAINT `claim_documents_claim_episode_id_foreign` FOREIGN KEY (`claim_episode_id`) REFERENCES `claim_episodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claim_documents`
--

LOCK TABLES `claim_documents` WRITE;
/*!40000 ALTER TABLE `claim_documents` DISABLE KEYS */;
INSERT INTO `claim_documents` VALUES (1,1,'resume_medis','http://127.0.0.1:8000/storage/mock/resume_rj458899.pdf','resume_rj458899.pdf',0,1,'{\"type\":\"resume_medis\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/resume_rj458899.pdf\",\"file_name\":\"resume_rj458899.pdf\"}','2026-04-14 22:28:30','2026-04-14 22:28:30'),(2,1,'hasil_lab','http://127.0.0.1:8000/storage/mock/lab_rj458899.pdf','lab_rj458899.pdf',0,1,'{\"type\":\"hasil_lab\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/lab_rj458899.pdf\",\"file_name\":\"lab_rj458899.pdf\"}','2026-04-14 22:28:30','2026-04-14 22:28:30'),(3,1,'laporan_operasi',NULL,NULL,0,0,'{\"type\":\"laporan_operasi\",\"available\":false,\"file_url\":null,\"file_name\":null}','2026-04-14 22:28:30','2026-04-14 22:28:30'),(4,2,'resume_medis','http://127.0.0.1:8000/storage/mock/resume_ri458900.pdf','resume_ri458900.pdf',0,1,'{\"type\":\"resume_medis\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/resume_ri458900.pdf\",\"file_name\":\"resume_ri458900.pdf\"}','2026-04-14 22:28:34','2026-04-14 22:28:34'),(5,2,'laporan_operasi','http://127.0.0.1:8000/storage/mock/op_ri458900.pdf','op_ri458900.pdf',0,1,'{\"type\":\"laporan_operasi\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/op_ri458900.pdf\",\"file_name\":\"op_ri458900.pdf\"}','2026-04-14 22:28:34','2026-04-14 22:28:34'),(6,2,'hasil_lab','http://127.0.0.1:8000/storage/mock/lab_ri458900.pdf','lab_ri458900.pdf',0,1,'{\"type\":\"hasil_lab\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/lab_ri458900.pdf\",\"file_name\":\"lab_ri458900.pdf\"}','2026-04-14 22:28:34','2026-04-14 22:28:34');
/*!40000 ALTER TABLE `claim_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `claim_episodes`
--

DROP TABLE IF EXISTS `claim_episodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claim_episodes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `episode_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `simrs_encounter_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sep_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mrn` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `care_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_unit` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admission_at` datetime DEFAULT NULL,
  `discharge_at` datetime DEFAULT NULL,
  `payer_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `claim_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `audit_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `processing_stage` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `risk_level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unknown',
  `risk_score` int unsigned NOT NULL DEFAULT '0',
  `snapshot_json` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `claim_episodes_episode_no_unique` (`episode_no`),
  KEY `claim_episodes_claim_status_audit_status_index` (`claim_status`,`audit_status`),
  KEY `claim_episodes_processing_stage_risk_level_index` (`processing_stage`,`risk_level`),
  KEY `claim_episodes_simrs_encounter_id_index` (`simrs_encounter_id`),
  KEY `claim_episodes_sep_no_index` (`sep_no`),
  KEY `claim_episodes_mrn_index` (`mrn`),
  KEY `claim_episodes_care_type_index` (`care_type`),
  KEY `claim_episodes_service_unit_index` (`service_unit`),
  KEY `claim_episodes_admission_at_index` (`admission_at`),
  KEY `claim_episodes_discharge_at_index` (`discharge_at`),
  KEY `claim_episodes_payer_name_index` (`payer_name`),
  KEY `claim_episodes_claim_status_index` (`claim_status`),
  KEY `claim_episodes_audit_status_index` (`audit_status`),
  KEY `claim_episodes_processing_stage_index` (`processing_stage`),
  KEY `claim_episodes_risk_level_index` (`risk_level`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claim_episodes`
--

LOCK TABLES `claim_episodes` WRITE;
/*!40000 ALTER TABLE `claim_episodes` DISABLE KEYS */;
INSERT INTO `claim_episodes` VALUES (1,'EP-RJ-458899','RJ-458899','0301R0010426V000123','RM001122','I Made Sudarma','rawat_inap','ICU','dr. Ketut Arimbawa, Sp.PD','2026-04-12 09:20:00','2026-04-15 14:35:00','BPJS','ready_review','reviewed','review','medium',40,'{\"sep_no\": \"0301R0010426V000123\", \"patient\": {\"dob\": \"1978-01-10\", \"mrn\": \"RM001122\", \"name\": \"I Made Sudarma\", \"gender\": \"L\"}, \"service\": {\"unit\": \"ICU\", \"doctor\": \"dr. Ketut Arimbawa, Sp.PD\", \"care_type\": \"rawat_inap\", \"admission_date\": \"2026-04-12 09:20:00\", \"discharge_date\": \"2026-04-15 14:35:00\", \"length_of_stay_days\": 3}, \"documents\": [{\"raw\": {\"type\": \"resume_medis\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/resume_rj458899.pdf\", \"available\": true, \"file_name\": \"resume_rj458899.pdf\"}, \"type\": \"resume_medis\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/resume_rj458899.pdf\", \"available\": true, \"file_name\": \"resume_rj458899.pdf\"}, {\"raw\": {\"type\": \"hasil_lab\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/lab_rj458899.pdf\", \"available\": true, \"file_name\": \"lab_rj458899.pdf\"}, \"type\": \"hasil_lab\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/lab_rj458899.pdf\", \"available\": true, \"file_name\": \"lab_rj458899.pdf\"}, {\"raw\": {\"type\": \"laporan_operasi\", \"file_url\": null, \"available\": false, \"file_name\": null}, \"type\": \"laporan_operasi\", \"file_url\": null, \"available\": false, \"file_name\": null}], \"episode_id\": \"EP-RJ-458899\", \"raw_sources\": {\"sep\": {\"data\": {\"no_sep\": \"0301R0010426V000123\"}, \"success\": true}, \"cppt\": {\"data\": {\"cppt_text\": \"Hari 1: pasien sesak berat, leukosit meningkat. Hari 2: terpasang ventilator. Hari 3: kondisi membaik, ventilator dilepas bertahap.\"}, \"success\": true}, \"labs\": {\"data\": [{\"result\": \"Leukosit 18000\"}, {\"result\": \"CRP meningkat\"}, {\"result\": \"Hb 12.4\"}], \"success\": true}, \"detail\": {\"data\": {\"unit\": \"ICU\", \"class\": \"Kelas 3\", \"payer\": \"BPJS\", \"patient\": {\"nama\": \"I Made Sudarma\", \"no_rm\": \"RM001122\", \"jenis_kelamin\": \"L\", \"tanggal_lahir\": \"1978-01-10\"}, \"episode_no\": \"EP-RJ-458899\", \"doctor_name\": \"dr. Ketut Arimbawa, Sp.PD\", \"jenis_rawat\": \"rawat_inap\", \"encounter_id\": \"RJ-458899\", \"admission_date\": \"2026-04-12 09:20:00\", \"discharge_date\": \"2026-04-15 14:35:00\"}, \"success\": true}, \"resume\": {\"data\": {\"resume_text\": \"Pasien dirawat dengan sepsis ec pneumonia berat. Selama perawatan pasien mendapat ventilator mekanik, antibiotik injeksi, monitoring intensif, dan terapi suportif. Kondisi membaik saat pulang.\", \"keluhan_utama\": \"Sesak napas dan demam tinggi\"}, \"success\": true}, \"billing\": {\"data\": [{\"item_name\": \"ICU visit\"}, {\"item_name\": \"Ventilator\"}, {\"item_name\": \"Antibiotik injeksi\"}], \"success\": true}, \"diagnoses\": {\"data\": [{\"diagnosis\": \"Sepsis\"}, {\"diagnosis\": \"Pneumonia berat\"}, {\"diagnosis\": \"Hipertensi\"}], \"success\": true}, \"documents\": {\"data\": [{\"type\": \"resume_medis\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/resume_rj458899.pdf\", \"available\": true, \"file_name\": \"resume_rj458899.pdf\"}, {\"type\": \"hasil_lab\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/lab_rj458899.pdf\", \"available\": true, \"file_name\": \"lab_rj458899.pdf\"}, {\"type\": \"laporan_operasi\", \"file_url\": null, \"available\": false, \"file_name\": null}], \"success\": true}, \"radiology\": {\"data\": [{\"result\": \"Thorax: infiltrat bilateral\"}], \"success\": true}, \"procedures\": {\"data\": [{\"procedure\": \"Ventilator mekanik\"}, {\"procedure\": \"Pemasangan central line\"}], \"success\": true}, \"operation_report\": {\"data\": {\"report_text\": \"\"}, \"success\": true}}, \"encounter_id\": \"RJ-458899\", \"clinical_data\": {\"cppt_text\": \"Hari 1: pasien sesak berat, leukosit meningkat. Hari 2: terpasang ventilator. Hari 3: kondisi membaik, ventilator dilepas bertahap.\", \"resume_text\": \"Pasien dirawat dengan sepsis ec pneumonia berat. Selama perawatan pasien mendapat ventilator mekanik, antibiotik injeksi, monitoring intensif, dan terapi suportif. Kondisi membaik saat pulang.\", \"diagnoses_text\": [\"Sepsis\", \"Pneumonia berat\", \"Hipertensi\"], \"chief_complaint\": \"Sesak napas dan demam tinggi\", \"procedures_text\": [\"Ventilator mekanik\", \"Pemasangan central line\"], \"operation_report_text\": \"\"}, \"supporting_results\": {\"labs\": [\"Leukosit 18000\", \"CRP meningkat\", \"Hb 12.4\"], \"radiology\": [\"Thorax: infiltrat bilateral\"]}, \"administrative_data\": {\"class\": \"Kelas 3\", \"payer\": \"BPJS\", \"billing_items\": [\"ICU visit\", \"Ventilator\", \"Antibiotik injeksi\"]}}','Episode siap direview coder.','2026-04-14 22:28:30','2026-04-15 23:19:54'),(2,'EP-RI-458900','RI-458900','0301R0010426V000124','RM001123','Ni Luh Sari Dewi','rawat_inap','Rawat Inap Bedah','dr. Komang Yasa, Sp.B','2026-04-13 08:10:00','2026-04-15 11:20:00','BPJS','draft','pending','new','unknown',0,'{\"sep_no\": \"0301R0010426V000124\", \"patient\": {\"dob\": \"1986-09-25\", \"mrn\": \"RM001123\", \"name\": \"Ni Luh Sari Dewi\", \"gender\": \"P\"}, \"service\": {\"unit\": \"Rawat Inap Bedah\", \"doctor\": \"dr. Komang Yasa, Sp.B\", \"care_type\": \"rawat_inap\", \"admission_date\": \"2026-04-13 08:10:00\", \"discharge_date\": \"2026-04-15 11:20:00\", \"length_of_stay_days\": 2}, \"documents\": [{\"raw\": {\"type\": \"resume_medis\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/resume_ri458900.pdf\", \"available\": true, \"file_name\": \"resume_ri458900.pdf\"}, \"type\": \"resume_medis\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/resume_ri458900.pdf\", \"available\": true, \"file_name\": \"resume_ri458900.pdf\"}, {\"raw\": {\"type\": \"laporan_operasi\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/op_ri458900.pdf\", \"available\": true, \"file_name\": \"op_ri458900.pdf\"}, \"type\": \"laporan_operasi\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/op_ri458900.pdf\", \"available\": true, \"file_name\": \"op_ri458900.pdf\"}, {\"raw\": {\"type\": \"hasil_lab\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/lab_ri458900.pdf\", \"available\": true, \"file_name\": \"lab_ri458900.pdf\"}, \"type\": \"hasil_lab\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/lab_ri458900.pdf\", \"available\": true, \"file_name\": \"lab_ri458900.pdf\"}], \"episode_id\": \"EP-RI-458900\", \"raw_sources\": {\"sep\": {\"data\": {\"no_sep\": \"0301R0010426V000124\"}, \"success\": true}, \"cppt\": {\"data\": {\"cppt_text\": \"Hari 1: nyeri abdomen kanan bawah. Hari 2: operasi appendektomi. Hari 3: nyeri berkurang dan pasien stabil.\"}, \"success\": true}, \"labs\": {\"data\": [{\"result\": \"Leukosit 14500\"}, {\"result\": \"Hb 10.9\"}], \"success\": true}, \"detail\": {\"data\": {\"unit\": \"Rawat Inap Bedah\", \"class\": \"Kelas 2\", \"payer\": \"BPJS\", \"patient\": {\"nama\": \"Ni Luh Sari Dewi\", \"no_rm\": \"RM001123\", \"jenis_kelamin\": \"P\", \"tanggal_lahir\": \"1986-09-25\"}, \"episode_no\": \"EP-RI-458900\", \"doctor_name\": \"dr. Komang Yasa, Sp.B\", \"jenis_rawat\": \"rawat_inap\", \"encounter_id\": \"RI-458900\", \"admission_date\": \"2026-04-13 08:10:00\", \"discharge_date\": \"2026-04-15 11:20:00\"}, \"success\": true}, \"resume\": {\"data\": {\"resume_text\": \"Pasien dirawat dengan appendicitis akut dan dilakukan appendektomi. Pasca operasi kondisi stabil dan diperbolehkan pulang.\", \"keluhan_utama\": \"Nyeri perut kanan bawah\"}, \"success\": true}, \"billing\": {\"data\": [{\"item_name\": \"Rawat inap bedah\"}, {\"item_name\": \"Appendektomi\"}, {\"item_name\": \"Obat pasca operasi\"}], \"success\": true}, \"diagnoses\": {\"data\": [{\"diagnosis\": \"Appendicitis akut\"}, {\"diagnosis\": \"Anemia ringan\"}], \"success\": true}, \"documents\": {\"data\": [{\"type\": \"resume_medis\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/resume_ri458900.pdf\", \"available\": true, \"file_name\": \"resume_ri458900.pdf\"}, {\"type\": \"laporan_operasi\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/op_ri458900.pdf\", \"available\": true, \"file_name\": \"op_ri458900.pdf\"}, {\"type\": \"hasil_lab\", \"file_url\": \"http://127.0.0.1:8000/storage/mock/lab_ri458900.pdf\", \"available\": true, \"file_name\": \"lab_ri458900.pdf\"}], \"success\": true}, \"radiology\": {\"data\": [{\"result\": \"USG abdomen: appendicitis akut\"}], \"success\": true}, \"procedures\": {\"data\": [{\"procedure\": \"Appendektomi\"}], \"success\": true}, \"operation_report\": {\"data\": {\"report_text\": \"Dilakukan appendektomi terbuka. Appendix tampak inflamed. Tidak ditemukan komplikasi intraoperatif.\"}, \"success\": true}}, \"encounter_id\": \"RI-458900\", \"clinical_data\": {\"cppt_text\": \"Hari 1: nyeri abdomen kanan bawah. Hari 2: operasi appendektomi. Hari 3: nyeri berkurang dan pasien stabil.\", \"resume_text\": \"Pasien dirawat dengan appendicitis akut dan dilakukan appendektomi. Pasca operasi kondisi stabil dan diperbolehkan pulang.\", \"diagnoses_text\": [\"Appendicitis akut\", \"Anemia ringan\"], \"chief_complaint\": \"Nyeri perut kanan bawah\", \"procedures_text\": [\"Appendektomi\"], \"operation_report_text\": \"Dilakukan appendektomi terbuka. Appendix tampak inflamed. Tidak ditemukan komplikasi intraoperatif.\"}, \"supporting_results\": {\"labs\": [\"Leukosit 14500\", \"Hb 10.9\"], \"radiology\": [\"USG abdomen: appendicitis akut\"]}, \"administrative_data\": {\"class\": \"Kelas 2\", \"payer\": \"BPJS\", \"billing_items\": [\"Rawat inap bedah\", \"Appendektomi\", \"Obat pasca operasi\"]}}',NULL,'2026-04-14 22:28:34','2026-04-14 22:29:19');
/*!40000 ALTER TABLE `claim_episodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `claim_follow_ups`
--

DROP TABLE IF EXISTS `claim_follow_ups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claim_follow_ups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `claim_episode_id` bigint unsigned NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_unit` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `issue_summary` text COLLATE utf8mb4_unicode_ci,
  `action_needed` text COLLATE utf8mb4_unicode_ci,
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `created_by_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_to_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_at` datetime DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claim_follow_ups_claim_episode_id_foreign` (`claim_episode_id`),
  KEY `claim_follow_ups_category_index` (`category`),
  KEY `claim_follow_ups_target_unit_index` (`target_unit`),
  KEY `claim_follow_ups_priority_index` (`priority`),
  KEY `claim_follow_ups_status_index` (`status`),
  CONSTRAINT `claim_follow_ups_claim_episode_id_foreign` FOREIGN KEY (`claim_episode_id`) REFERENCES `claim_episodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claim_follow_ups`
--

LOCK TABLES `claim_follow_ups` WRITE;
/*!40000 ALTER TABLE `claim_follow_ups` DISABLE KEYS */;
/*!40000 ALTER TABLE `claim_follow_ups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `claim_reviews`
--

DROP TABLE IF EXISTS `claim_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claim_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `claim_episode_id` bigint unsigned NOT NULL,
  `reviewer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reviewer_role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `old_data_json` json DEFAULT NULL,
  `new_data_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claim_reviews_claim_episode_id_action_type_index` (`claim_episode_id`,`action_type`),
  KEY `claim_reviews_reviewer_role_index` (`reviewer_role`),
  KEY `claim_reviews_action_type_index` (`action_type`),
  CONSTRAINT `claim_reviews_claim_episode_id_foreign` FOREIGN KEY (`claim_episode_id`) REFERENCES `claim_episodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claim_reviews`
--

LOCK TABLES `claim_reviews` WRITE;
/*!40000 ALTER TABLE `claim_reviews` DISABLE KEYS */;
INSERT INTO `claim_reviews` VALUES (1,1,'SYSTEM','system','ai_result_saved','Hasil AI coding disimpan.',NULL,'{\"confidence_score\": \"88.50\", \"processing_stage\": \"auditing\", \"claim_ai_result_id\": 1}','2026-04-14 22:43:07','2026-04-14 22:43:07'),(2,1,'SYSTEM','system','audit_flags_saved','Audit flags disimpan.',NULL,'{\"risk_level\": \"medium\", \"risk_score\": 40, \"flags_count\": 2, \"processing_stage\": \"document_check\"}','2026-04-14 22:43:27','2026-04-14 22:43:27'),(3,1,'SYSTEM TEST','system','test_status_update','Status diperbarui lewat route test.','{\"notes\": null, \"risk_level\": \"medium\", \"risk_score\": 40, \"audit_status\": \"flagged\", \"claim_status\": \"draft\", \"processing_stage\": \"document_check\"}','{\"notes\": \"Episode siap direview coder.\", \"risk_level\": \"medium\", \"risk_score\": 40, \"audit_status\": \"reviewed\", \"claim_status\": \"ready_review\", \"processing_stage\": \"review\"}','2026-04-14 22:43:46','2026-04-14 22:43:46'),(4,1,'SYSTEM','system','ai_result_saved','Hasil AI coding disimpan.',NULL,'{\"confidence_score\": \"88.50\", \"processing_stage\": \"auditing\", \"claim_ai_result_id\": 2}','2026-04-15 23:19:46','2026-04-15 23:19:46'),(5,1,'DASHBOARD SYSTEM','system','dashboard_status_update','Status diperbarui dari dashboard.','{\"notes\": \"Episode siap direview coder.\", \"risk_level\": \"medium\", \"risk_score\": 40, \"audit_status\": \"ai_completed\", \"claim_status\": \"ready_review\", \"processing_stage\": \"auditing\"}','{\"notes\": \"Episode siap direview coder.\", \"risk_level\": \"medium\", \"risk_score\": 40, \"audit_status\": \"reviewed\", \"claim_status\": \"ready_review\", \"processing_stage\": \"review\"}','2026-04-15 23:19:54','2026-04-15 23:19:54'),(6,1,'DASHBOARD SYSTEM','system','dashboard_status_update','Status diperbarui dari dashboard.','{\"notes\": \"Episode siap direview coder.\", \"risk_level\": \"medium\", \"risk_score\": 40, \"audit_status\": \"reviewed\", \"claim_status\": \"ready_review\", \"processing_stage\": \"review\"}','{\"notes\": \"Episode siap direview coder.\", \"risk_level\": \"medium\", \"risk_score\": 40, \"audit_status\": \"reviewed\", \"claim_status\": \"ready_review\", \"processing_stage\": \"review\"}','2026-04-16 00:06:37','2026-04-16 00:06:37');
/*!40000 ALTER TABLE `claim_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `claim_verification_items`
--

DROP TABLE IF EXISTS `claim_verification_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claim_verification_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `claim_episode_id` bigint unsigned NOT NULL,
  `verification_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_checked',
  `finding_notes` text COLLATE utf8mb4_unicode_ci,
  `follow_up_notes` text COLLATE utf8mb4_unicode_ci,
  `source_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewer_role` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checked_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_episode_verification_key` (`claim_episode_id`,`verification_key`),
  KEY `claim_verification_items_verification_key_index` (`verification_key`),
  KEY `claim_verification_items_status_index` (`status`),
  CONSTRAINT `claim_verification_items_claim_episode_id_foreign` FOREIGN KEY (`claim_episode_id`) REFERENCES `claim_episodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claim_verification_items`
--

LOCK TABLES `claim_verification_items` WRITE;
/*!40000 ALTER TABLE `claim_verification_items` DISABLE KEYS */;
INSERT INTO `claim_verification_items` VALUES (1,1,'billing_vs_cppt','Kelengkapan Billing vs CPPT','not_checked',NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-15 23:20:32','2026-04-15 23:20:32'),(2,1,'chronology_vs_cppt','Kesesuaian Form Kronologis vs CPPT','not_checked',NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-15 23:20:32','2026-04-15 23:20:32'),(3,1,'documents_vs_rm','Kelengkapan Berkas vs Rekam Medis','not_checked',NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-15 23:20:32','2026-04-15 23:20:32');
/*!40000 ALTER TABLE `claim_verification_items` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_04_15_051503_create_claim_episodes_table',1),(5,'2026_04_15_051504_create_claim_ai_results_table',1),(6,'2026_04_15_051504_create_claim_documents_table',1),(7,'2026_04_15_051505_create_claim_audit_flags_table',1),(8,'2026_04_15_051506_create_claim_reviews_table',1),(9,'2026_04_15_051508_create_simrs_api_logs_table',1),(10,'2026_04_16_070457_create_claim_verification_items_table',2),(11,'2026_04_16_072352_create_claim_follow_ups_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `sessions` VALUES ('dCCcxEfaxCznGDlAH2hrAKIdrBPAhnCEQXwMxNwr',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidDJTSXlLUXVlMWdyYVVZcjh3ZnlrQzVHSmdvWmYya2hSUFR6NzFqcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly9sb2NhbGhvc3QvY2FzZW1peC1hdWRpdC9wdWJsaWMvY2FzZW1peC8xIjtzOjU6InJvdXRlIjtzOjEyOiJjYXNlbWl4LnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1776326801),('NslH8sQARbmtqgUyWKeLtzVr3InBjZX5zueCOPmz',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUlpURk9BUXBPVHNzSWQ0bGk0am4welMzU0ZIemFBRWVhMHFUSDZUayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3QvY2FzZW1peC1hdWRpdC9wdWJsaWMvY2FzZW1peCI7czo1OiJyb3V0ZSI7czoxMzoiY2FzZW1peC5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1776238739),('WCqDe91QSMA6MTGV0tX26d67lq4pUnknZXOTspXN',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUTA4MHJEbzFNT1l0WUM1ZXBicXlldFZvc2ZWOUVlZ1ZPZ3R2N1BDcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1776231912);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simrs_api_logs`
--

DROP TABLE IF EXISTS `simrs_api_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simrs_api_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `endpoint` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_payload` longtext COLLATE utf8mb4_unicode_ci,
  `response_payload` longtext COLLATE utf8mb4_unicode_ci,
  `http_status` int DEFAULT NULL,
  `is_success` tinyint(1) NOT NULL DEFAULT '1',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `simrs_api_logs_endpoint_index` (`endpoint`),
  KEY `simrs_api_logs_method_index` (`method`),
  KEY `simrs_api_logs_http_status_index` (`http_status`),
  KEY `simrs_api_logs_is_success_index` (`is_success`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simrs_api_logs`
--

LOCK TABLES `simrs_api_logs` WRITE;
/*!40000 ALTER TABLE `simrs_api_logs` DISABLE KEYS */;
INSERT INTO `simrs_api_logs` VALUES (1,'/discharge','GET','{\"date\":\"2026-04-15\"}','{\"success\":true,\"message\":\"Mock discharge data.\",\"data\":[{\"encounter_id\":\"RJ-458899\",\"no_rm\":\"RM001122\",\"nama_pasien\":\"I Made Sudarma\",\"tanggal_pulang\":\"2026-04-15 14:35:00\",\"unit\":\"ICU\",\"dokter\":\"dr. Ketut Arimbawa, Sp.PD\"},{\"encounter_id\":\"RI-458900\",\"no_rm\":\"RM001123\",\"nama_pasien\":\"Ni Luh Sari Dewi\",\"tanggal_pulang\":\"2026-04-15 11:20:00\",\"unit\":\"Rawat Inap Bedah\",\"dokter\":\"dr. Komang Yasa, Sp.B\"}]}',200,1,NULL,'2026-04-14 22:28:26','2026-04-14 22:28:26'),(2,'/encounters/RJ-458899','GET',NULL,'{\"success\":true,\"data\":{\"episode_no\":\"EP-RJ-458899\",\"encounter_id\":\"RJ-458899\",\"patient\":{\"no_rm\":\"RM001122\",\"nama\":\"I Made Sudarma\",\"jenis_kelamin\":\"L\",\"tanggal_lahir\":\"1978-01-10\"},\"jenis_rawat\":\"rawat_inap\",\"unit\":\"ICU\",\"doctor_name\":\"dr. Ketut Arimbawa, Sp.PD\",\"admission_date\":\"2026-04-12 09:20:00\",\"discharge_date\":\"2026-04-15 14:35:00\",\"payer\":\"BPJS\",\"class\":\"Kelas 3\"}}',200,1,NULL,'2026-04-14 22:28:27','2026-04-14 22:28:27'),(3,'/encounters/RJ-458899/resume','GET',NULL,'{\"success\":true,\"data\":{\"keluhan_utama\":\"Sesak napas dan demam tinggi\",\"resume_text\":\"Pasien dirawat dengan sepsis ec pneumonia berat. Selama perawatan pasien mendapat ventilator mekanik, antibiotik injeksi, monitoring intensif, dan terapi suportif. Kondisi membaik saat pulang.\"}}',200,1,NULL,'2026-04-14 22:28:27','2026-04-14 22:28:27'),(4,'/encounters/RJ-458899/diagnoses','GET',NULL,'{\"success\":true,\"data\":[{\"diagnosis\":\"Sepsis\"},{\"diagnosis\":\"Pneumonia berat\"},{\"diagnosis\":\"Hipertensi\"}]}',200,1,NULL,'2026-04-14 22:28:28','2026-04-14 22:28:28'),(5,'/encounters/RJ-458899/procedures','GET',NULL,'{\"success\":true,\"data\":[{\"procedure\":\"Ventilator mekanik\"},{\"procedure\":\"Pemasangan central line\"}]}',200,1,NULL,'2026-04-14 22:28:28','2026-04-14 22:28:28'),(6,'/discharge','GET','{\"date\":\"2026-04-15\"}','{\"success\":true,\"message\":\"Mock discharge data.\",\"data\":[{\"encounter_id\":\"RJ-458899\",\"no_rm\":\"RM001122\",\"nama_pasien\":\"I Made Sudarma\",\"tanggal_pulang\":\"2026-04-15 14:35:00\",\"unit\":\"ICU\",\"dokter\":\"dr. Ketut Arimbawa, Sp.PD\"},{\"encounter_id\":\"RI-458900\",\"no_rm\":\"RM001123\",\"nama_pasien\":\"Ni Luh Sari Dewi\",\"tanggal_pulang\":\"2026-04-15 11:20:00\",\"unit\":\"Rawat Inap Bedah\",\"dokter\":\"dr. Komang Yasa, Sp.B\"}]}',200,1,NULL,'2026-04-14 22:28:28','2026-04-14 22:28:28'),(7,'/encounters/RJ-458899/billing','GET',NULL,'{\"success\":true,\"data\":[{\"item_name\":\"ICU visit\"},{\"item_name\":\"Ventilator\"},{\"item_name\":\"Antibiotik injeksi\"}]}',200,1,NULL,'2026-04-14 22:28:28','2026-04-14 22:28:28'),(8,'/encounters/RJ-458899','GET',NULL,'{\"success\":true,\"data\":{\"episode_no\":\"EP-RJ-458899\",\"encounter_id\":\"RJ-458899\",\"patient\":{\"no_rm\":\"RM001122\",\"nama\":\"I Made Sudarma\",\"jenis_kelamin\":\"L\",\"tanggal_lahir\":\"1978-01-10\"},\"jenis_rawat\":\"rawat_inap\",\"unit\":\"ICU\",\"doctor_name\":\"dr. Ketut Arimbawa, Sp.PD\",\"admission_date\":\"2026-04-12 09:20:00\",\"discharge_date\":\"2026-04-15 14:35:00\",\"payer\":\"BPJS\",\"class\":\"Kelas 3\"}}',200,1,NULL,'2026-04-14 22:28:29','2026-04-14 22:28:29'),(9,'/encounters/RJ-458899/sep','GET',NULL,'{\"success\":true,\"data\":{\"no_sep\":\"0301R0010426V000123\"}}',200,1,NULL,'2026-04-14 22:28:29','2026-04-14 22:28:29'),(10,'/encounters/RJ-458899/resume','GET',NULL,'{\"success\":true,\"data\":{\"keluhan_utama\":\"Sesak napas dan demam tinggi\",\"resume_text\":\"Pasien dirawat dengan sepsis ec pneumonia berat. Selama perawatan pasien mendapat ventilator mekanik, antibiotik injeksi, monitoring intensif, dan terapi suportif. Kondisi membaik saat pulang.\"}}',200,1,NULL,'2026-04-14 22:28:29','2026-04-14 22:28:29'),(11,'/encounters/RJ-458899/documents','GET',NULL,'{\"success\":true,\"data\":[{\"type\":\"resume_medis\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/resume_rj458899.pdf\",\"file_name\":\"resume_rj458899.pdf\"},{\"type\":\"hasil_lab\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/lab_rj458899.pdf\",\"file_name\":\"lab_rj458899.pdf\"},{\"type\":\"laporan_operasi\",\"available\":false,\"file_url\":null,\"file_name\":null}]}',200,1,NULL,'2026-04-14 22:28:29','2026-04-14 22:28:29'),(12,'/encounters/RJ-458899/diagnoses','GET',NULL,'{\"success\":true,\"data\":[{\"diagnosis\":\"Sepsis\"},{\"diagnosis\":\"Pneumonia berat\"},{\"diagnosis\":\"Hipertensi\"}]}',200,1,NULL,'2026-04-14 22:28:29','2026-04-14 22:28:29'),(13,'/encounters/RJ-458899/labs','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"Leukosit 18000\"},{\"result\":\"CRP meningkat\"},{\"result\":\"Hb 12.4\"}]}',200,1,NULL,'2026-04-14 22:28:29','2026-04-14 22:28:29'),(14,'/encounters/RJ-458899/procedures','GET',NULL,'{\"success\":true,\"data\":[{\"procedure\":\"Ventilator mekanik\"},{\"procedure\":\"Pemasangan central line\"}]}',200,1,NULL,'2026-04-14 22:28:29','2026-04-14 22:28:29'),(15,'/encounters/RJ-458899/radiology','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"Thorax: infiltrat bilateral\"}]}',200,1,NULL,'2026-04-14 22:28:30','2026-04-14 22:28:30'),(16,'/encounters/RJ-458899/billing','GET',NULL,'{\"success\":true,\"data\":[{\"item_name\":\"ICU visit\"},{\"item_name\":\"Ventilator\"},{\"item_name\":\"Antibiotik injeksi\"}]}',200,1,NULL,'2026-04-14 22:28:30','2026-04-14 22:28:30'),(17,'/encounters/RJ-458899/operation-report','GET',NULL,'{\"success\":true,\"data\":{\"report_text\":\"\"}}',200,1,NULL,'2026-04-14 22:28:30','2026-04-14 22:28:30'),(18,'/encounters/RJ-458899/sep','GET',NULL,'{\"success\":true,\"data\":{\"no_sep\":\"0301R0010426V000123\"}}',200,1,NULL,'2026-04-14 22:28:30','2026-04-14 22:28:30'),(19,'/encounters/RJ-458899/cppt','GET',NULL,'{\"success\":true,\"data\":{\"cppt_text\":\"Hari 1: pasien sesak berat, leukosit meningkat. Hari 2: terpasang ventilator. Hari 3: kondisi membaik, ventilator dilepas bertahap.\"}}',200,1,NULL,'2026-04-14 22:28:30','2026-04-14 22:28:30'),(20,'/encounters/RJ-458899/documents','GET',NULL,'{\"success\":true,\"data\":[{\"type\":\"resume_medis\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/resume_rj458899.pdf\",\"file_name\":\"resume_rj458899.pdf\"},{\"type\":\"hasil_lab\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/lab_rj458899.pdf\",\"file_name\":\"lab_rj458899.pdf\"},{\"type\":\"laporan_operasi\",\"available\":false,\"file_url\":null,\"file_name\":null}]}',200,1,NULL,'2026-04-14 22:28:30','2026-04-14 22:28:30'),(21,'/encounters/RI-458900','GET',NULL,'{\"success\":true,\"data\":{\"episode_no\":\"EP-RI-458900\",\"encounter_id\":\"RI-458900\",\"patient\":{\"no_rm\":\"RM001123\",\"nama\":\"Ni Luh Sari Dewi\",\"jenis_kelamin\":\"P\",\"tanggal_lahir\":\"1986-09-25\"},\"jenis_rawat\":\"rawat_inap\",\"unit\":\"Rawat Inap Bedah\",\"doctor_name\":\"dr. Komang Yasa, Sp.B\",\"admission_date\":\"2026-04-13 08:10:00\",\"discharge_date\":\"2026-04-15 11:20:00\",\"payer\":\"BPJS\",\"class\":\"Kelas 2\"}}',200,1,NULL,'2026-04-14 22:28:31','2026-04-14 22:28:31'),(22,'/encounters/RJ-458899/labs','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"Leukosit 18000\"},{\"result\":\"CRP meningkat\"},{\"result\":\"Hb 12.4\"}]}',200,1,NULL,'2026-04-14 22:28:31','2026-04-14 22:28:31'),(23,'/encounters/RI-458900/resume','GET',NULL,'{\"success\":true,\"data\":{\"keluhan_utama\":\"Nyeri perut kanan bawah\",\"resume_text\":\"Pasien dirawat dengan appendicitis akut dan dilakukan appendektomi. Pasca operasi kondisi stabil dan diperbolehkan pulang.\"}}',200,1,NULL,'2026-04-14 22:28:31','2026-04-14 22:28:31'),(24,'/encounters/RJ-458899/radiology','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"Thorax: infiltrat bilateral\"}]}',200,1,NULL,'2026-04-14 22:28:31','2026-04-14 22:28:31'),(25,'/encounters/RI-458900/diagnoses','GET',NULL,'{\"success\":true,\"data\":[{\"diagnosis\":\"Appendicitis akut\"},{\"diagnosis\":\"Anemia ringan\"}]}',200,1,NULL,'2026-04-14 22:28:31','2026-04-14 22:28:31'),(26,'/encounters/RJ-458899/operation-report','GET',NULL,'{\"success\":true,\"data\":{\"report_text\":\"\"}}',200,1,NULL,'2026-04-14 22:28:31','2026-04-14 22:28:31'),(27,'/encounters/RI-458900/procedures','GET',NULL,'{\"success\":true,\"data\":[{\"procedure\":\"Appendektomi\"}]}',200,1,NULL,'2026-04-14 22:28:31','2026-04-14 22:28:31'),(28,'/encounters/RJ-458899/cppt','GET',NULL,'{\"success\":true,\"data\":{\"cppt_text\":\"Hari 1: pasien sesak berat, leukosit meningkat. Hari 2: terpasang ventilator. Hari 3: kondisi membaik, ventilator dilepas bertahap.\"}}',200,1,NULL,'2026-04-14 22:28:32','2026-04-14 22:28:32'),(29,'/encounters/RI-458900/billing','GET',NULL,'{\"success\":true,\"data\":[{\"item_name\":\"Rawat inap bedah\"},{\"item_name\":\"Appendektomi\"},{\"item_name\":\"Obat pasca operasi\"}]}',200,1,NULL,'2026-04-14 22:28:32','2026-04-14 22:28:32'),(30,'/encounters/RI-458900','GET',NULL,'{\"success\":true,\"data\":{\"episode_no\":\"EP-RI-458900\",\"encounter_id\":\"RI-458900\",\"patient\":{\"no_rm\":\"RM001123\",\"nama\":\"Ni Luh Sari Dewi\",\"jenis_kelamin\":\"P\",\"tanggal_lahir\":\"1986-09-25\"},\"jenis_rawat\":\"rawat_inap\",\"unit\":\"Rawat Inap Bedah\",\"doctor_name\":\"dr. Komang Yasa, Sp.B\",\"admission_date\":\"2026-04-13 08:10:00\",\"discharge_date\":\"2026-04-15 11:20:00\",\"payer\":\"BPJS\",\"class\":\"Kelas 2\"}}',200,1,NULL,'2026-04-14 22:28:32','2026-04-14 22:28:32'),(31,'/encounters/RI-458900/sep','GET',NULL,'{\"success\":true,\"data\":{\"no_sep\":\"0301R0010426V000124\"}}',200,1,NULL,'2026-04-14 22:28:32','2026-04-14 22:28:32'),(32,'/encounters/RI-458900/resume','GET',NULL,'{\"success\":true,\"data\":{\"keluhan_utama\":\"Nyeri perut kanan bawah\",\"resume_text\":\"Pasien dirawat dengan appendicitis akut dan dilakukan appendektomi. Pasca operasi kondisi stabil dan diperbolehkan pulang.\"}}',200,1,NULL,'2026-04-14 22:28:32','2026-04-14 22:28:32'),(33,'/encounters/RI-458900/documents','GET',NULL,'{\"success\":true,\"data\":[{\"type\":\"resume_medis\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/resume_ri458900.pdf\",\"file_name\":\"resume_ri458900.pdf\"},{\"type\":\"laporan_operasi\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/op_ri458900.pdf\",\"file_name\":\"op_ri458900.pdf\"},{\"type\":\"hasil_lab\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/lab_ri458900.pdf\",\"file_name\":\"lab_ri458900.pdf\"}]}',200,1,NULL,'2026-04-14 22:28:32','2026-04-14 22:28:32'),(34,'/encounters/RI-458900/diagnoses','GET',NULL,'{\"success\":true,\"data\":[{\"diagnosis\":\"Appendicitis akut\"},{\"diagnosis\":\"Anemia ringan\"}]}',200,1,NULL,'2026-04-14 22:28:33','2026-04-14 22:28:33'),(35,'/encounters/RI-458900/labs','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"Leukosit 14500\"},{\"result\":\"Hb 10.9\"}]}',200,1,NULL,'2026-04-14 22:28:33','2026-04-14 22:28:33'),(36,'/encounters/RI-458900/procedures','GET',NULL,'{\"success\":true,\"data\":[{\"procedure\":\"Appendektomi\"}]}',200,1,NULL,'2026-04-14 22:28:33','2026-04-14 22:28:33'),(37,'/encounters/RI-458900/radiology','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"USG abdomen: appendicitis akut\"}]}',200,1,NULL,'2026-04-14 22:28:33','2026-04-14 22:28:33'),(38,'/encounters/RI-458900/billing','GET',NULL,'{\"success\":true,\"data\":[{\"item_name\":\"Rawat inap bedah\"},{\"item_name\":\"Appendektomi\"},{\"item_name\":\"Obat pasca operasi\"}]}',200,1,NULL,'2026-04-14 22:28:33','2026-04-14 22:28:33'),(39,'/encounters/RI-458900/operation-report','GET',NULL,'{\"success\":true,\"data\":{\"report_text\":\"Dilakukan appendektomi terbuka. Appendix tampak inflamed. Tidak ditemukan komplikasi intraoperatif.\"}}',200,1,NULL,'2026-04-14 22:28:33','2026-04-14 22:28:33'),(40,'/encounters/RI-458900/sep','GET',NULL,'{\"success\":true,\"data\":{\"no_sep\":\"0301R0010426V000124\"}}',200,1,NULL,'2026-04-14 22:28:33','2026-04-14 22:28:33'),(41,'/encounters/RI-458900/cppt','GET',NULL,'{\"success\":true,\"data\":{\"cppt_text\":\"Hari 1: nyeri abdomen kanan bawah. Hari 2: operasi appendektomi. Hari 3: nyeri berkurang dan pasien stabil.\"}}',200,1,NULL,'2026-04-14 22:28:34','2026-04-14 22:28:34'),(42,'/encounters/RI-458900/documents','GET',NULL,'{\"success\":true,\"data\":[{\"type\":\"resume_medis\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/resume_ri458900.pdf\",\"file_name\":\"resume_ri458900.pdf\"},{\"type\":\"laporan_operasi\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/op_ri458900.pdf\",\"file_name\":\"op_ri458900.pdf\"},{\"type\":\"hasil_lab\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/lab_ri458900.pdf\",\"file_name\":\"lab_ri458900.pdf\"}]}',200,1,NULL,'2026-04-14 22:28:34','2026-04-14 22:28:34'),(43,'/encounters/RI-458900/labs','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"Leukosit 14500\"},{\"result\":\"Hb 10.9\"}]}',200,1,NULL,'2026-04-14 22:28:34','2026-04-14 22:28:34'),(44,'/encounters/RI-458900/radiology','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"USG abdomen: appendicitis akut\"}]}',200,1,NULL,'2026-04-14 22:28:34','2026-04-14 22:28:34'),(45,'/encounters/RI-458900/operation-report','GET',NULL,'{\"success\":true,\"data\":{\"report_text\":\"Dilakukan appendektomi terbuka. Appendix tampak inflamed. Tidak ditemukan komplikasi intraoperatif.\"}}',200,1,NULL,'2026-04-14 22:28:35','2026-04-14 22:28:35'),(46,'/encounters/RI-458900/cppt','GET',NULL,'{\"success\":true,\"data\":{\"cppt_text\":\"Hari 1: nyeri abdomen kanan bawah. Hari 2: operasi appendektomi. Hari 3: nyeri berkurang dan pasien stabil.\"}}',200,1,NULL,'2026-04-14 22:28:35','2026-04-14 22:28:35'),(47,'/discharge','GET','{\"date\":\"2026-04-15\"}','{\"success\":true,\"message\":\"Mock discharge data.\",\"data\":[{\"encounter_id\":\"RJ-458899\",\"no_rm\":\"RM001122\",\"nama_pasien\":\"I Made Sudarma\",\"tanggal_pulang\":\"2026-04-15 14:35:00\",\"unit\":\"ICU\",\"dokter\":\"dr. Ketut Arimbawa, Sp.PD\"},{\"encounter_id\":\"RI-458900\",\"no_rm\":\"RM001123\",\"nama_pasien\":\"Ni Luh Sari Dewi\",\"tanggal_pulang\":\"2026-04-15 11:20:00\",\"unit\":\"Rawat Inap Bedah\",\"dokter\":\"dr. Komang Yasa, Sp.B\"}]}',200,1,NULL,'2026-04-14 22:29:14','2026-04-14 22:29:14'),(48,'/encounters/RJ-458899','GET',NULL,'{\"success\":true,\"data\":{\"episode_no\":\"EP-RJ-458899\",\"encounter_id\":\"RJ-458899\",\"patient\":{\"no_rm\":\"RM001122\",\"nama\":\"I Made Sudarma\",\"jenis_kelamin\":\"L\",\"tanggal_lahir\":\"1978-01-10\"},\"jenis_rawat\":\"rawat_inap\",\"unit\":\"ICU\",\"doctor_name\":\"dr. Ketut Arimbawa, Sp.PD\",\"admission_date\":\"2026-04-12 09:20:00\",\"discharge_date\":\"2026-04-15 14:35:00\",\"payer\":\"BPJS\",\"class\":\"Kelas 3\"}}',200,1,NULL,'2026-04-14 22:29:14','2026-04-14 22:29:14'),(49,'/encounters/RJ-458899/resume','GET',NULL,'{\"success\":true,\"data\":{\"keluhan_utama\":\"Sesak napas dan demam tinggi\",\"resume_text\":\"Pasien dirawat dengan sepsis ec pneumonia berat. Selama perawatan pasien mendapat ventilator mekanik, antibiotik injeksi, monitoring intensif, dan terapi suportif. Kondisi membaik saat pulang.\"}}',200,1,NULL,'2026-04-14 22:29:14','2026-04-14 22:29:14'),(50,'/encounters/RJ-458899/diagnoses','GET',NULL,'{\"success\":true,\"data\":[{\"diagnosis\":\"Sepsis\"},{\"diagnosis\":\"Pneumonia berat\"},{\"diagnosis\":\"Hipertensi\"}]}',200,1,NULL,'2026-04-14 22:29:14','2026-04-14 22:29:14'),(51,'/encounters/RJ-458899/procedures','GET',NULL,'{\"success\":true,\"data\":[{\"procedure\":\"Ventilator mekanik\"},{\"procedure\":\"Pemasangan central line\"}]}',200,1,NULL,'2026-04-14 22:29:15','2026-04-14 22:29:15'),(52,'/encounters/RJ-458899/billing','GET',NULL,'{\"success\":true,\"data\":[{\"item_name\":\"ICU visit\"},{\"item_name\":\"Ventilator\"},{\"item_name\":\"Antibiotik injeksi\"}]}',200,1,NULL,'2026-04-14 22:29:15','2026-04-14 22:29:15'),(53,'/encounters/RJ-458899/sep','GET',NULL,'{\"success\":true,\"data\":{\"no_sep\":\"0301R0010426V000123\"}}',200,1,NULL,'2026-04-14 22:29:15','2026-04-14 22:29:15'),(54,'/encounters/RJ-458899/documents','GET',NULL,'{\"success\":true,\"data\":[{\"type\":\"resume_medis\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/resume_rj458899.pdf\",\"file_name\":\"resume_rj458899.pdf\"},{\"type\":\"hasil_lab\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/lab_rj458899.pdf\",\"file_name\":\"lab_rj458899.pdf\"},{\"type\":\"laporan_operasi\",\"available\":false,\"file_url\":null,\"file_name\":null}]}',200,1,NULL,'2026-04-14 22:29:15','2026-04-14 22:29:15'),(55,'/encounters/RJ-458899/labs','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"Leukosit 18000\"},{\"result\":\"CRP meningkat\"},{\"result\":\"Hb 12.4\"}]}',200,1,NULL,'2026-04-14 22:29:16','2026-04-14 22:29:16'),(56,'/encounters/RJ-458899/radiology','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"Thorax: infiltrat bilateral\"}]}',200,1,NULL,'2026-04-14 22:29:16','2026-04-14 22:29:16'),(57,'/encounters/RJ-458899/operation-report','GET',NULL,'{\"success\":true,\"data\":{\"report_text\":\"\"}}',200,1,NULL,'2026-04-14 22:29:16','2026-04-14 22:29:16'),(58,'/encounters/RJ-458899/cppt','GET',NULL,'{\"success\":true,\"data\":{\"cppt_text\":\"Hari 1: pasien sesak berat, leukosit meningkat. Hari 2: terpasang ventilator. Hari 3: kondisi membaik, ventilator dilepas bertahap.\"}}',200,1,NULL,'2026-04-14 22:29:17','2026-04-14 22:29:17'),(59,'/encounters/RI-458900','GET',NULL,'{\"success\":true,\"data\":{\"episode_no\":\"EP-RI-458900\",\"encounter_id\":\"RI-458900\",\"patient\":{\"no_rm\":\"RM001123\",\"nama\":\"Ni Luh Sari Dewi\",\"jenis_kelamin\":\"P\",\"tanggal_lahir\":\"1986-09-25\"},\"jenis_rawat\":\"rawat_inap\",\"unit\":\"Rawat Inap Bedah\",\"doctor_name\":\"dr. Komang Yasa, Sp.B\",\"admission_date\":\"2026-04-13 08:10:00\",\"discharge_date\":\"2026-04-15 11:20:00\",\"payer\":\"BPJS\",\"class\":\"Kelas 2\"}}',200,1,NULL,'2026-04-14 22:29:17','2026-04-14 22:29:17'),(60,'/encounters/RI-458900/resume','GET',NULL,'{\"success\":true,\"data\":{\"keluhan_utama\":\"Nyeri perut kanan bawah\",\"resume_text\":\"Pasien dirawat dengan appendicitis akut dan dilakukan appendektomi. Pasca operasi kondisi stabil dan diperbolehkan pulang.\"}}',200,1,NULL,'2026-04-14 22:29:17','2026-04-14 22:29:17'),(61,'/encounters/RI-458900/diagnoses','GET',NULL,'{\"success\":true,\"data\":[{\"diagnosis\":\"Appendicitis akut\"},{\"diagnosis\":\"Anemia ringan\"}]}',200,1,NULL,'2026-04-14 22:29:17','2026-04-14 22:29:17'),(62,'/encounters/RI-458900/procedures','GET',NULL,'{\"success\":true,\"data\":[{\"procedure\":\"Appendektomi\"}]}',200,1,NULL,'2026-04-14 22:29:18','2026-04-14 22:29:18'),(63,'/encounters/RI-458900/billing','GET',NULL,'{\"success\":true,\"data\":[{\"item_name\":\"Rawat inap bedah\"},{\"item_name\":\"Appendektomi\"},{\"item_name\":\"Obat pasca operasi\"}]}',200,1,NULL,'2026-04-14 22:29:18','2026-04-14 22:29:18'),(64,'/encounters/RI-458900/sep','GET',NULL,'{\"success\":true,\"data\":{\"no_sep\":\"0301R0010426V000124\"}}',200,1,NULL,'2026-04-14 22:29:18','2026-04-14 22:29:18'),(65,'/encounters/RI-458900/documents','GET',NULL,'{\"success\":true,\"data\":[{\"type\":\"resume_medis\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/resume_ri458900.pdf\",\"file_name\":\"resume_ri458900.pdf\"},{\"type\":\"laporan_operasi\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/op_ri458900.pdf\",\"file_name\":\"op_ri458900.pdf\"},{\"type\":\"hasil_lab\",\"available\":true,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/mock\\/lab_ri458900.pdf\",\"file_name\":\"lab_ri458900.pdf\"}]}',200,1,NULL,'2026-04-14 22:29:18','2026-04-14 22:29:18'),(66,'/encounters/RI-458900/labs','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"Leukosit 14500\"},{\"result\":\"Hb 10.9\"}]}',200,1,NULL,'2026-04-14 22:29:19','2026-04-14 22:29:19'),(67,'/encounters/RI-458900/radiology','GET',NULL,'{\"success\":true,\"data\":[{\"result\":\"USG abdomen: appendicitis akut\"}]}',200,1,NULL,'2026-04-14 22:29:19','2026-04-14 22:29:19'),(68,'/encounters/RI-458900/operation-report','GET',NULL,'{\"success\":true,\"data\":{\"report_text\":\"Dilakukan appendektomi terbuka. Appendix tampak inflamed. Tidak ditemukan komplikasi intraoperatif.\"}}',200,1,NULL,'2026-04-14 22:29:19','2026-04-14 22:29:19'),(69,'/encounters/RI-458900/cppt','GET',NULL,'{\"success\":true,\"data\":{\"cppt_text\":\"Hari 1: nyeri abdomen kanan bawah. Hari 2: operasi appendektomi. Hari 3: nyeri berkurang dan pasien stabil.\"}}',200,1,NULL,'2026-04-14 22:29:19','2026-04-14 22:29:19');
/*!40000 ALTER TABLE `simrs_api_logs` ENABLE KEYS */;
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
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-16 16:09:01
