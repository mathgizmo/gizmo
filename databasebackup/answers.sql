-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 15, 2017 at 09:35 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gizmo`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `answer_order` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `answers`
--
INSERT into answers (`question_id`, `value`, `is_correct`, `answer_order`, `created_at`, `updated_at`)
SELECT id, answer, 1, 0, NOW(), NOW() FROM question where reply_mode IN ('TF', 'FB');

INSERT into answers (`question_id`, `value`, `is_correct`, `answer_order`, `created_at`, `updated_at`)
SELECT id, mcq1, IF(mcq1=answer, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq1<> ''
UNION ALL
SELECT id, mcq2, IF(mcq2=answer, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq2<> ''
UNION ALL
SELECT id, mcq3, IF(mcq3=answer, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq3<> ''
UNION ALL
SELECT id, mcq4, IF(mcq4=answer, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq4<> ''
UNION ALL
SELECT id, mcq5, IF(mcq5=answer, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq5<> ''
UNION ALL
SELECT id, mcq6, IF(mcq6=answer, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq6<> ''
;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
