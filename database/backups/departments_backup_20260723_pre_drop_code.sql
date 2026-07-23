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
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `code` varchar(20) NOT NULL COMMENT 'รหัสแผนก',
  `name` varchar(255) NOT NULL COMMENT 'ชื่อแผนก ต้องไม่ซ้ำ',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายแผนก',
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active' COMMENT 'สถานะการใช้งานของแผนก',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'ลำดับการแสดงผล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Soft Delete Timestamp',
  `code_active` varchar(20) GENERATED ALWAYS AS (if(`deleted_at` is null,`code`,NULL)) STORED COMMENT 'ใช้สำหรับ Unique Index เท่านั้น เป็น NULL อัตโนมัติเมื่อถูก Soft Delete',
  `name_active` varchar(255) GENERATED ALWAYS AS (if(`deleted_at` is null,`name`,NULL)) STORED COMMENT 'ใช้สำหรับ Unique Index เท่านั้น เป็น NULL อัตโนมัติเมื่อถูก Soft Delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_active_unique` (`code_active`),
  UNIQUE KEY `departments_name_active_unique` (`name_active`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ตารางแผนก/หน่วยงานภายในองค์กร';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'DEPT-01','ฝ่ายบริหารงานทั่วไป','ดูแลงานสารบรรณและธุรการทั่วไปของหน่วยงาน','Active',10,'2026-07-15 17:47:19','2026-07-17 18:12:11','2026-07-18 01:12:11',NULL,NULL),(2,'DEPT-02','ฝ่ายเทคโนโลยีสารสนเทศ','ดูแลระบบเทคโนโลยีสารสนเทศและการสื่อสาร','Active',20,'2026-07-15 17:47:19','2026-07-17 18:12:13','2026-07-18 01:12:13',NULL,NULL),(3,'DEPT-03','ฝ่ายสื่อสารและประชาสัมพันธ์','ดูแลงานประชาสัมพันธ์และสื่อสารองค์กร','Active',30,'2026-07-15 17:47:19','2026-07-17 18:12:15','2026-07-18 01:12:15',NULL,NULL),(4,'DEPT-04','ฝ่ายแผนงานและงบประมาณ','ดูแลการวางแผนและงบประมาณของหน่วยงาน','Active',40,'2026-07-15 17:47:19','2026-07-22 16:45:55','2026-07-22 23:45:55',NULL,NULL),(5,'DEPT-05','ฝ่ายบุคคล','ดูแลงานบุคลากรและสวัสดิการ','Active',50,'2026-07-15 17:47:19','2026-07-22 16:45:57','2026-07-22 23:45:57',NULL,NULL),(6,'DEPT-06','ฝ่ายกฎหมาย','ดูแลงานด้านกฎหมายและระเบียบข้อบังคับ','Active',60,'2026-07-15 17:47:19','2026-07-22 16:46:03','2026-07-22 23:46:03',NULL,NULL),(7,'DEPT-07','ฝ่ายพัฒนาระบบดิจิทัล','ดูแลการพัฒนาระบบดิจิทัลขององค์กร','Active',70,'2026-07-15 17:47:19','2026-07-22 16:46:05','2026-07-22 23:46:05',NULL,NULL),(8,'DEPT-08','ฝ่ายตรวจสอบภายใน','ดูแลการตรวจสอบภายในและควบคุมคุณภาพ','Active',80,'2026-07-15 17:47:19','2026-07-22 16:46:07','2026-07-22 23:46:07',NULL,NULL),(9,'DEPT-09','ฝ่ายวิเทศสัมพันธ์','ดูแลความร่วมมือระหว่างประเทศ','Inactive',90,'2026-07-15 17:47:19','2026-07-22 16:46:09','2026-07-22 23:46:09',NULL,NULL),(10,'DEPT-10','ฝ่ายบริการประชาชน','ดูแลศูนย์บริการประชาชนแบบเบ็ดเสร็จ','Active',100,'2026-07-15 17:47:19','2026-07-15 17:47:19',NULL,'DEPT-10','ฝ่ายบริการประชาชน'),(11,'DEPT-11','ฝ่ายจัดซื้อจัดจ้าง','ดูแลกระบวนการจัดซื้อจัดจ้างของหน่วยงาน','Active',110,'2026-07-15 17:47:19','2026-07-15 17:47:19',NULL,'DEPT-11','ฝ่ายจัดซื้อจัดจ้าง'),(12,'DEPT-12','ฝ่ายความมั่นคงปลอดภัยไซเบอร์','ดูแลความมั่นคงปลอดภัยด้านไซเบอร์','Active',120,'2026-07-15 17:47:19','2026-07-15 17:47:19',NULL,'DEPT-12','ฝ่ายความมั่นคงปลอดภัยไซเบอร์'),(27,'QA1784577661','QA Test Dept 1784577661','UI-6 regression test','Active',0,'2026-07-20 20:01:01','2026-07-20 20:01:02','2026-07-21 03:01:02',NULL,NULL);
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-23 16:50:34
