-- MySQL dump 10.13  Distrib 8.0.30, for macos12 (x86_64)
--
-- Host: localhost    Database: gizmoDB
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `answer`
--

LOCK TABLES `answer` WRITE;
/*!40000 ALTER TABLE `answer` DISABLE KEYS */;
INSERT INTO `answer` VALUES (48592,4987,'42',1,0,'2025-05-04 20:50:35','2025-05-04 20:50:35'),(48593,4987,'12',0,1,'2025-05-04 20:50:35','2025-05-04 20:50:35'),(48594,4987,'36',0,2,'2025-05-04 20:50:35','2025-05-04 20:50:35'),(48595,4987,'no',0,3,'2025-05-04 20:50:35','2025-05-04 20:50:35');
/*!40000 ALTER TABLE `answer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `application_has_models`
--

LOCK TABLES `application_has_models` WRITE;
/*!40000 ALTER TABLE `application_has_models` DISABLE KEYS */;
/*!40000 ALTER TABLE `application_has_models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `class_detailed_reports`
--

LOCK TABLES `class_detailed_reports` WRITE;
/*!40000 ALTER TABLE `class_detailed_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_detailed_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `class_thread_replies`
--

LOCK TABLES `class_thread_replies` WRITE;
/*!40000 ALTER TABLE `class_thread_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_thread_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `class_threads`
--

LOCK TABLES `class_threads` WRITE;
/*!40000 ALTER TABLE `class_threads` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_threads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `classes_applications`
--

LOCK TABLES `classes_applications` WRITE;
/*!40000 ALTER TABLE `classes_applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `classes_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `classes_applications_students`
--

LOCK TABLES `classes_applications_students` WRITE;
/*!40000 ALTER TABLE `classes_applications_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `classes_applications_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `classes_students`
--

LOCK TABLES `classes_students` WRITE;
/*!40000 ALTER TABLE `classes_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `classes_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `classes_teachers`
--

LOCK TABLES `classes_teachers` WRITE;
/*!40000 ALTER TABLE `classes_teachers` DISABLE KEYS */;
/*!40000 ALTER TABLE `classes_teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (1,'CA','Canada'),(2,'AF','Afghanistan'),(3,'AL','Albania'),(4,'DZ','Algeria'),(5,'DS','American Samoa'),(6,'AD','Andorra'),(7,'AO','Angola'),(8,'AI','Anguilla'),(9,'AQ','Antarctica'),(10,'AG','Antigua and Barbuda'),(11,'AR','Argentina'),(12,'AM','Armenia'),(13,'AW','Aruba'),(14,'AU','Australia'),(15,'AT','Austria'),(16,'AZ','Azerbaijan'),(17,'BS','Bahamas'),(18,'BH','Bahrain'),(19,'BD','Bangladesh'),(20,'BB','Barbados'),(21,'BY','Belarus'),(22,'BE','Belgium'),(23,'BZ','Belize'),(24,'BJ','Benin'),(25,'BM','Bermuda'),(26,'BT','Bhutan'),(27,'BO','Bolivia'),(28,'BA','Bosnia and Herzegovina'),(29,'BW','Botswana'),(30,'BV','Bouvet Island'),(31,'BR','Brazil'),(32,'IO','British Indian Ocean Territory'),(33,'BN','Brunei Darussalam'),(34,'BG','Bulgaria'),(35,'BF','Burkina Faso'),(36,'BI','Burundi'),(37,'KH','Cambodia'),(38,'CM','Cameroon'),(39,'CV','Cape Verde'),(40,'KY','Cayman Islands'),(41,'CF','Central African Republic'),(42,'TD','Chad'),(43,'CL','Chile'),(44,'CN','China'),(45,'CX','Christmas Island'),(46,'CC','Cocos (Keeling) Islands'),(47,'CO','Colombia'),(48,'KM','Comoros'),(49,'CD','Democratic Republic of the Congo'),(50,'CG','Republic of Congo'),(51,'CK','Cook Islands'),(52,'CR','Costa Rica'),(53,'HR','Croatia (Hrvatska)'),(54,'CU','Cuba'),(55,'CY','Cyprus'),(56,'CZ','Czech Republic'),(57,'DK','Denmark'),(58,'DJ','Djibouti'),(59,'DM','Dominica'),(60,'DO','Dominican Republic'),(61,'TP','East Timor'),(62,'EC','Ecuador'),(63,'EG','Egypt'),(64,'SV','El Salvador'),(65,'GQ','Equatorial Guinea'),(66,'ER','Eritrea'),(67,'EE','Estonia'),(68,'ET','Ethiopia'),(69,'FK','Falkland Islands (Malvinas)'),(70,'FO','Faroe Islands'),(71,'FJ','Fiji'),(72,'FI','Finland'),(73,'FR','France'),(74,'FX','France, Metropolitan'),(75,'GF','French Guiana'),(76,'PF','French Polynesia'),(77,'TF','French Southern Territories'),(78,'GA','Gabon'),(79,'GM','Gambia'),(80,'GE','Georgia'),(81,'DE','Germany'),(82,'GH','Ghana'),(83,'GI','Gibraltar'),(84,'GK','Guernsey'),(85,'GR','Greece'),(86,'GL','Greenland'),(87,'GD','Grenada'),(88,'GP','Guadeloupe'),(89,'GU','Guam'),(90,'GT','Guatemala'),(91,'GN','Guinea'),(92,'GW','Guinea-Bissau'),(93,'GY','Guyana'),(94,'HT','Haiti'),(95,'HM','Heard and Mc Donald Islands'),(96,'HN','Honduras'),(97,'HK','Hong Kong'),(98,'HU','Hungary'),(99,'IS','Iceland'),(100,'IN','India'),(101,'IM','Isle of Man'),(102,'ID','Indonesia'),(103,'IR','Iran (Islamic Republic of)'),(104,'IQ','Iraq'),(105,'IE','Ireland'),(106,'IL','Israel'),(107,'IT','Italy'),(108,'CI','Ivory Coast'),(109,'JE','Jersey'),(110,'JM','Jamaica'),(111,'JP','Japan'),(112,'JO','Jordan'),(113,'KZ','Kazakhstan'),(114,'KE','Kenya'),(115,'KI','Kiribati'),(116,'KP','Korea, Democratic People\'s Republic of'),(117,'KR','Korea, Republic of'),(118,'XK','Kosovo'),(119,'KW','Kuwait'),(120,'KG','Kyrgyzstan'),(121,'LA','Lao People\'s Democratic Republic'),(122,'LV','Latvia'),(123,'LB','Lebanon'),(124,'LS','Lesotho'),(125,'LR','Liberia'),(126,'LY','Libyan Arab Jamahiriya'),(127,'LI','Liechtenstein'),(128,'LT','Lithuania'),(129,'LU','Luxembourg'),(130,'MO','Macau'),(131,'MK','North Macedonia'),(132,'MG','Madagascar'),(133,'MW','Malawi'),(134,'MY','Malaysia'),(135,'MV','Maldives'),(136,'ML','Mali'),(137,'MT','Malta'),(138,'MH','Marshall Islands'),(139,'MQ','Martinique'),(140,'MR','Mauritania'),(141,'MU','Mauritius'),(142,'TY','Mayotte'),(143,'MX','Mexico'),(144,'FM','Micronesia, Federated States of'),(145,'MD','Moldova, Republic of'),(146,'MC','Monaco'),(147,'MN','Mongolia'),(148,'ME','Montenegro'),(149,'MS','Montserrat'),(150,'MA','Morocco'),(151,'MZ','Mozambique'),(152,'MM','Myanmar'),(153,'NA','Namibia'),(154,'NR','Nauru'),(155,'NP','Nepal'),(156,'NL','Netherlands'),(157,'AN','Netherlands Antilles'),(158,'NC','New Caledonia'),(159,'NZ','New Zealand'),(160,'NI','Nicaragua'),(161,'NE','Niger'),(162,'NG','Nigeria'),(163,'NU','Niue'),(164,'NF','Norfolk Island'),(165,'MP','Northern Mariana Islands'),(166,'NO','Norway'),(167,'OM','Oman'),(168,'PK','Pakistan'),(169,'PW','Palau'),(170,'PS','Palestine'),(171,'PA','Panama'),(172,'PG','Papua New Guinea'),(173,'PY','Paraguay'),(174,'PE','Peru'),(175,'PH','Philippines'),(176,'PN','Pitcairn'),(177,'PL','Poland'),(178,'PT','Portugal'),(179,'PR','Puerto Rico'),(180,'QA','Qatar'),(181,'RE','Reunion'),(182,'RO','Romania'),(183,'RU','Russian Federation'),(184,'RW','Rwanda'),(185,'KN','Saint Kitts and Nevis'),(186,'LC','Saint Lucia'),(187,'VC','Saint Vincent and the Grenadines'),(188,'WS','Samoa'),(189,'SM','San Marino'),(190,'ST','Sao Tome and Principe'),(191,'SA','Saudi Arabia'),(192,'SN','Senegal'),(193,'RS','Serbia'),(194,'SC','Seychelles'),(195,'SL','Sierra Leone'),(196,'SG','Singapore'),(197,'SK','Slovakia'),(198,'SI','Slovenia'),(199,'SB','Solomon Islands'),(200,'SO','Somalia'),(201,'ZA','South Africa'),(202,'GS','South Georgia South Sandwich Islands'),(203,'SS','South Sudan'),(204,'ES','Spain'),(205,'LK','Sri Lanka'),(206,'SH','St. Helena'),(207,'PM','St. Pierre and Miquelon'),(208,'SD','Sudan'),(209,'SR','Suriname'),(210,'SJ','Svalbard and Jan Mayen Islands'),(211,'SZ','Swaziland'),(212,'SE','Sweden'),(213,'CH','Switzerland'),(214,'SY','Syrian Arab Republic'),(215,'TW','Taiwan'),(216,'TJ','Tajikistan'),(217,'TZ','Tanzania, United Republic of'),(218,'TH','Thailand'),(219,'TG','Togo'),(220,'TK','Tokelau'),(221,'TO','Tonga'),(222,'TT','Trinidad and Tobago'),(223,'TN','Tunisia'),(224,'TR','Turkey'),(225,'TM','Turkmenistan'),(226,'TC','Turks and Caicos Islands'),(227,'TV','Tuvalu'),(228,'UG','Uganda'),(229,'UA','Ukraine'),(230,'AE','United Arab Emirates'),(231,'GB','United Kingdom'),(232,'US','United States'),(233,'UM','United States minor outlying islands'),(234,'UY','Uruguay'),(235,'UZ','Uzbekistan'),(236,'VU','Vanuatu'),(237,'VA','Vatican City State'),(238,'VE','Venezuela'),(239,'VN','Vietnam'),(240,'VG','Virgin Islands (British)'),(241,'VI','Virgin Islands (U.S.)'),(242,'WF','Wallis and Futuna Islands'),(243,'EH','Western Sahara'),(244,'YE','Yemen'),(245,'ZM','Zambia'),(246,'ZW','Zimbabwe');
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `dashboards`
--

LOCK TABLES `dashboards` WRITE;
/*!40000 ALTER TABLE `dashboards` DISABLE KEYS */;
/*!40000 ALTER TABLE `dashboards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `faqs`
--

LOCK TABLES `faqs` WRITE;
/*!40000 ALTER TABLE `faqs` DISABLE KEYS */;
INSERT INTO `faqs` VALUES (1,1,'Can I still practice after the course is finished?','<p>Only if your teacher has left assignments/tests open</p><p>&nbsp;</p><p>You can also register yourself as &#39;self-study&#39; with another email address.&nbsp; Unfortunately there is no way to change your status without accessing the admin side of the app.</p><p>&nbsp;</p><p>Cheers,</p><p>&nbsp;</p><p>Taras</p>',1,1,NULL,NULL);
/*!40000 ALTER TABLE `faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `lesson`
--

LOCK TABLES `lesson` WRITE;
/*!40000 ALTER TABLE `lesson` DISABLE KEYS */;
INSERT INTO `lesson` VALUES (998,1,'Lesson 1 title',1,203,'2025-05-04 20:49:34','2025-05-04 20:49:34',1,1,0);
/*!40000 ALTER TABLE `lesson` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `level`
--

LOCK TABLES `level` WRITE;
/*!40000 ALTER TABLE `level` DISABLE KEYS */;
INSERT INTO `level` VALUES (16,1,'Module 1','<p>Description module 1</p>','2025-05-04 20:41:52','2025-05-04 20:41:52',1,1),(17,1,'Module 2','<p>Description module 2</p>','2025-05-04 20:42:20','2025-05-04 20:42:20',1,0),(18,2,'Module3','<p>Description module 3</p>','2025-05-04 20:42:43','2025-05-04 20:42:43',0,0);
/*!40000 ALTER TABLE `level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `mail_templates`
--

LOCK TABLES `mail_templates` WRITE;
/*!40000 ALTER TABLE `mail_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `mails_history`
--

LOCK TABLES `mails_history` WRITE;
/*!40000 ALTER TABLE `mails_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `mails_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES ('2017_10_17_000000_create_initial_structure',1),('2017_10_17_000001_create_students',2),('2017_10_17_000002_create_answer',3),('2017_10_22_000001_add_field_users_table',3),('2017_10_22_000002_update_reply_mode',4),('2017_10_23_000002_update_question_table',5),('2017_10_23_175729_create_students_tracking_table',6),('2017_11_04_001_update_order',7),('2017_11_16_001_update_student_tracking',8),('2018_01_02_001_update_tf_answers',9),('2018_01_18_141050_create_settings_table',10),('2018_01_18_175729_add_field_students_tracking_table',10),('2018_01_18_195853_create_report_errors_table',10),('2018_01_22_222600_alter_question_table',11),('2018_01_30_195853_update_report_errors_table',12),('2018_01_30_222601_update_students_table',12),('2018_01_30_222602_update_dependency_fields',12),('2018_01_31_171703_create_progresses_table',13),('2018_01_31_2226001_update_progresses_table',13),('2018_01_31_222600_update_question_table',13),('2018_02_03_195853_update_report_errors_table2',14),('2018_02_04_195853_update_lesson_table',15),('2018_02_04_195854_update_students_table',15),('2018_02_04_195855_update_students_table3',15),('2018_03_22_195856_update_question_table2',16),('2018_04_01_195857_update_question_table3',17),('2018_04_27_195858_create_placement_questions_table',18),('2018_07_10_195859_update_lesson_table2',19),('2018_07_10_195860_update_question_table4',19),('2018_08_13_195862_update_topic_table2',20),('2018_08_13_195863_update_unit_table1',20),('2018_10_05_195861_update_topic_table1',20),('2018_09_20_195862_update_students_table4',21),('2018_12_03_195864_update_report_errors_table3',21),('2019_01_28_195865_fix_units_progress',22),('2019_04_11_195870_update_question_table5',23),('2019_04_10_195866_update_settings_table',24),('2018_01_31_222600_update_question_table1',25),('2018_02_04_195854_update_students_table2',25),('2019_06_18_082909_update_lesson_table3',26),('2019_10_28_195853_update_level_table',27),('2020_02_28_064142_create_applications_table',28),('2020_02_28_064735_update_students_table5',28),('2020_03_02_062119_create_application_has_models_table',28),('2020_03_05_062120_update_settings_table2',28),('2020_03_06_062121_update_settings_table3',28),('2020_04_05_084022_update_users_table',29),('2020_05_30_164851_update_students_table6',30),('2020_06_01_124225_update_progresses_table2',30),('2020_06_01_175253_create_classes_table',30),('2020_06_01_175712_create_classes_students_table',30),('2020_06_01_175925_create_classes_applications_table',30),('2020_06_01_181724_update_applications_table',30),('2020_06_29_105407_update_progresses_table3',31),('2020_06_29_121905_create_mail_templates_table',31),('2020_07_01_143159_create_mails_history_table',32),('2020_07_12_103124_update_classes_applications_table1',33),('2020_07_14_124511_update_classes_applications_table2',33),('2020_07_14_131016_update_progresses_table4',33),('2020_07_15_123730_update_classes_applications_table3',33),('2020_07_21_144406_update_students_table7',34),('2020_07_24_154141_update_students_tracking_table1',35),('2020_07_24_211410_update_applications_table2',35),('2020_08_14_094422_update_students_tracking_table2',36),('2020_08_17_113940_update_applications_table3',36),('2020_08_18_134357_create_dashboards_table',36),('2020_08_19_133603_create_students_testout_attempts_table',36),('2020_08_19_185936_create_countries_table',36),('2020_08_19_192604_update_students_table8',36),('2020_08_20_171424_update_level_table2',36),('2020_08_20_171636_update_unit_table2',36),('2020_08_20_171735_update_topic_table3',36),('2020_08_24_102453_update_classes_table1',37),('2020_08_24_153509_create_students_tracking_questions_table',37),('2020_08_26_162648_update_classes_applications_table4',38),('2020_09_01_105209_update_dashboards_table1',39),('2020_09_01_120023_update_students_table9',39),('2020_09_02_160939_update_emails_in_students_table',40),('2020_09_03_101912_create_class_detailed_reports_table',41),('2020_09_04_202250_update_applications_table4',42),('2020_09_11_060359_update_students_table10',43),('2020_09_15_160812_update_classes_applications_table5',44),('2020_09_15_161129_create_classes_applications_students_table',44),('2020_12_21_010810_update_applications_table5',45),('2020_12_21_011253_update_classes_applications_students_table1',45),('2020_12_21_012420_update_classes_students_table1',45),('2020_12_21_033438_update_classes_applications_table6',45),('2020_12_24_094110_update_students_tracking_questions_table1',45),('2020_12_27_112109_update_students_table11',45),('2020_12_28_040159_create_students_test_questions_table',45),('2021_01_19_164941_update_students_test_questions_table1',46),('2021_01_27_130932_fix_uncompleted_test_that_shows_as_completed',47),('2021_02_03_161449_update_applications_table6',48),('2021_02_03_164144_update_students_test_questions_table2',48),('2021_02_16_164440_fix_removed_marks_from_tests_with_selected_students',49),('2021_02_18_082112_create_students_test_attempts_table',50),('2021_02_18_083646_update_classes_applications_table7',50),('2021_02_23_105728_update_students_table12',51),('2021_02_26_102226_create_class_threads_table',52),('2021_02_26_102445_create_class_thread_replies_table',52),('2021_03_03_144332_update_students_test_attempts_table1',53),('2021_03_03_145435_create_students_test_attempt_health_trackers_table',53),('2021_03_09_102456_create_classes_teachers_table',54),('2021_06_28_154553_create_tutorials_table',55),('2021_06_28_154608_create_faqs_table',55),('2021_07_08_094555_update_classes_table2',56),('2021_07_27_151845_update_classes_students_table2',57),('2021_07_28_081847_update_students_table13',57),('2021_07_28_091801_update_classes_table3',57),('2021_07_28_101807_update_classes_teachers_table1',57),('2021_07_30_095608_update_classes_students_table3',58),('2021_08_05_105148_update_settings_table4',59),('2021_08_07_101328_update_students_test_questions_table3',60),('2021_08_14_122721_update_settings_table5',61),('2021_08_31_124738_fix_question_charts_colors',62),('2021_08_31_135036_update_students_table14',62),('2021_09_02_170341_fix_broken_questions_after_prev_questions_migration',63),('2022_06_08_053926_create_shares_table',64);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `placement_questions`
--

LOCK TABLES `placement_questions` WRITE;
/*!40000 ALTER TABLE `placement_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `placement_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `progresses`
--

LOCK TABLES `progresses` WRITE;
/*!40000 ALTER TABLE `progresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `progresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (4987,998,'','mcq','<p>Some question</p>',NULL,NULL,NULL,NULL,NULL,NULL,'','2025-05-04 20:50:35','2025-05-04 20:50:35',0,0,0,2);
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `question_type`
--

LOCK TABLES `question_type` WRITE;
/*!40000 ALTER TABLE `question_type` DISABLE KEYS */;
INSERT INTO `question_type` VALUES (1,'text','Plain Text Question Only',NULL,NULL),(2,'draw','Dynamic Drawing',NULL,NULL),(3,'image','Image Type',NULL,NULL);
/*!40000 ALTER TABLE `question_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `reply_mode`
--

LOCK TABLES `reply_mode` WRITE;
/*!40000 ALTER TABLE `reply_mode` DISABLE KEYS */;
INSERT INTO `reply_mode` VALUES (1,'general','Numeric/text response','2017-07-16 17:38:06.940708',NULL),(2,'FB','Fill In The Blank','2017-07-16 17:26:22.000368','2017-07-16 17:26:22.000368'),(3,'TF','True or False','2017-07-16 17:28:23.190161','2017-07-16 17:28:23.190161'),(10,'mcq','Multiple Choice',NULL,NULL),(11,'order','Correct Order',NULL,NULL),(12,'mcqms','Multiple Choice/Multiple Answers',NULL,NULL);
/*!40000 ALTER TABLE `reply_mode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `report_errors`
--

LOCK TABLES `report_errors` WRITE;
/*!40000 ALTER TABLE `report_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_errors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `shares`
--

LOCK TABLES `shares` WRITE;
/*!40000 ALTER TABLE `shares` DISABLE KEYS */;
/*!40000 ALTER TABLE `shares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `students_test_attempt_health_trackers`
--

LOCK TABLES `students_test_attempt_health_trackers` WRITE;
/*!40000 ALTER TABLE `students_test_attempt_health_trackers` DISABLE KEYS */;
/*!40000 ALTER TABLE `students_test_attempt_health_trackers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `students_test_attempts`
--

LOCK TABLES `students_test_attempts` WRITE;
/*!40000 ALTER TABLE `students_test_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `students_test_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `students_test_questions`
--

LOCK TABLES `students_test_questions` WRITE;
/*!40000 ALTER TABLE `students_test_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `students_test_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `students_testout_attempts`
--

LOCK TABLES `students_testout_attempts` WRITE;
/*!40000 ALTER TABLE `students_testout_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `students_testout_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `students_tracking`
--

LOCK TABLES `students_tracking` WRITE;
/*!40000 ALTER TABLE `students_tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `students_tracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `students_tracking_questions`
--

LOCK TABLES `students_tracking_questions` WRITE;
/*!40000 ALTER TABLE `students_tracking_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `students_tracking_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `topic`
--

LOCK TABLES `topic` WRITE;
/*!40000 ALTER TABLE `topic` DISABLE KEYS */;
INSERT INTO `topic` VALUES (203,1,'images/default-icon.svg','t1','t1','<p>Description</p>',1,86,'2025-05-04 20:47:59','2025-05-04 20:47:59',1);
/*!40000 ALTER TABLE `topic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `tutorials`
--

LOCK TABLES `tutorials` WRITE;
/*!40000 ALTER TABLE `tutorials` DISABLE KEYS */;
INSERT INTO `tutorials` VALUES (1,1,'draft of student instructions pdf is available','<p>A pdf of a set of instructions for students can be downloaded from <a href=\"https://healthnumeracyproject.com/admin/uploads/1627414213_HNP webapp registration and use instructions for students.pdf\">here</a>.</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p><img alt=\"\" src=\"https://healthnumeracyproject.com/admin/uploads/1627414213_HNP webapp registration and use instructions for students.pdf\" /></p>',1,0,NULL,NULL),(2,2,'Draft of teacher instructions','<p>A draft of instructions for registration and use can be found <a href=\"http://healthnumeracyproject.com/wp-content/uploads/2021/08/HNP-webapp-teacherreseracher-registration-and-use-instructions.pdf\">here</a></p>',0,1,NULL,NULL);
/*!40000 ALTER TABLE `tutorials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `unit`
--

LOCK TABLES `unit` WRITE;
/*!40000 ALTER TABLE `unit` DISABLE KEYS */;
INSERT INTO `unit` VALUES (86,1,' Unit m1_u1','<p>Description m1_u1</p>',1,16,'2025-05-04 20:43:39','2025-05-04 20:43:39',0),(87,1,' Unit m1_u2','<p>Description m1_u1</p>',1,16,'2025-05-04 20:43:39','2025-05-04 20:43:39',0),(88,1,' Unit m1_u3','<p>Description m1_u1</p>',1,16,'2025-05-04 20:43:39','2025-05-04 20:43:39',0),(89,1,' Unit m1_u4','<p>Description m1_u1</p>',1,16,'2025-05-04 20:43:39','2025-05-04 20:43:39',0),(90,1,' Unit m2_u1','<p>Description m1_u1</p>',1,17,'2025-05-04 20:43:39','2025-05-04 20:43:39',0),(91,1,' Unit m2_u2','<p>Description m1_u1</p>',1,17,'2025-05-04 20:43:39','2025-05-04 20:43:39',0),(92,1,' Unit m2_u3','<p>Description m1_u1</p>',1,17,'2025-05-04 20:43:39','2025-05-04 20:43:39',0),(93,1,' Unit m2_u4','<p>Description m1_u1</p>',1,17,'2025-05-04 20:43:39','2025-05-04 20:43:39',0),(94,1,' Unit m3_u1','<p>Description m1_u1</p>',1,18,'2025-05-04 20:43:39','2025-05-04 20:43:39',0),(95,1,' Unit m3_u2','<p>Description m1_u1</p>',1,18,'2025-05-04 20:43:39','2025-05-04 20:43:39',0),(96,1,' Unit m3_u3','<p>Description m1_u1</p>',1,18,'2025-05-04 20:43:39','2025-05-04 20:43:39',0);
/*!40000 ALTER TABLE `unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (58,'superadmin','admin','admin@gizmo.com','$2y$10$78rknEGQ.I6Ai3.BmTedduJoh.6wOySn44SPnPwiFQ8muJFstiPBW',NULL,'2025-05-04 20:38:58','2025-05-04 20:38:58');
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

-- Dump completed on 2025-05-04 20:52:17
