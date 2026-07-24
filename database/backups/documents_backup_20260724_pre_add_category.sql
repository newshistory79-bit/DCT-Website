-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: tcsp
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `title` varchar(255) NOT NULL COMMENT 'ชื่อเอกสาร',
  `description` text DEFAULT NULL COMMENT 'รายละเอียดเอกสาร',
  `file_name` varchar(255) NOT NULL COMMENT 'ชื่อไฟล์ที่จัดเก็บจริง (สุ่มด้วย UploadHelper)',
  `original_file_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อไฟล์ต้นฉบับตอนอัปโหลด (แสดงตอนดาวน์โหลด)',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'นามสกุลไฟล์ เช่น pdf, docx, xlsx',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์ (ไบต์)',
  `status` enum('Draft','Published') NOT NULL DEFAULT 'Draft' COMMENT 'สถานะการเผยแพร่',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Soft Delete Timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_documents_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ตารางเอกสารดาวน์โหลด';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (21,'ำดดำเเฌ็้เ','โฌเฟฆฏฆฤหเด','14aba6a5b7796f2dec4c2424dadcc08a.docx','ບົດໂຄງຮ່າງເກືອບແລ້ວ2 (3).docx','docx',1280450,'Published','2026-07-20 14:39:11','2026-07-21 19:31:53','2026-07-22 02:31:53'),(22,'ກົດໝາຍ ວ່າດ້ວຍການປົກປ້ອງຂໍ້ມູນເອເລັກໂຕຣນິກ',NULL,'6565aa6868662d136fdc6f5cd6dc7d67.pdf','366af6a72b2ff21b613c4fcbedd94c03.pdf','pdf',1225224,'Published','2026-07-21 19:31:47','2026-07-21 19:31:47',NULL);
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-24 12:21:55
