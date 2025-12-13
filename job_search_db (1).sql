-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 13 ديسمبر 2025 الساعة 20:26
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_search_db`
--

-- --------------------------------------------------------

--
-- بنية الجدول `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'تكنولوجيا المعلومات', NULL),
(2, 'المحاسبة والمالية', NULL),
(3, 'التسويق والمبيعات', NULL),
(4, 'الموارد البشرية', NULL),
(5, 'التعليم والتدريس', NULL),
(6, 'الصحة والطب', NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `salary` varchar(50) DEFAULT NULL,
  `job_type` enum('دوام كامل','دوام جزئي','عن بعد','عقد') DEFAULT 'دوام كامل',
  `category_id` int(11) DEFAULT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive','filled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `description`, `requirements`, `location`, `salary`, `job_type`, `category_id`, `employer_id`, `status`, `created_at`, `deadline`) VALUES
(1, 'مبرمجة', 'مبرمجة لغة php', 'دبلوم برمجة على الأقل', 'صنعاء', '70000', 'دوام جزئي', 1, 3, 'active', '2025-12-12 05:27:53', '2025-12-30');

-- --------------------------------------------------------

--
-- بنية الجدول `job_applications`
--

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','reviewed','accepted','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `job_applications`
--

INSERT INTO `job_applications` (`id`, `job_id`, `user_id`, `cover_letter`, `resume_path`, `status`, `applied_at`) VALUES
(3, 1, 4, '', '', 'reviewed', '2025-12-12 06:08:19'),
(4, 1, 4, '', '', 'rejected', '2025-12-12 06:11:56'),
(5, 1, 4, '', '', 'accepted', '2025-12-12 06:13:10'),
(6, 1, 4, '', '', 'accepted', '2025-12-12 14:44:52');

-- --------------------------------------------------------

--
-- بنية الجدول `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 4, 'تحديث حالة طلب التوظيف', 'تم تحديث حالة طلبك للوظيفة \"مبرمجة\" إلى: reviewed', 'application_update', 0, '2025-12-12 06:10:59'),
(2, 4, 'تحديث حالة طلب التوظيف', 'تم تحديث حالة طلبك للوظيفة \"مبرمجة\" إلى: rejected', 'application_update', 0, '2025-12-12 06:12:17'),
(3, 4, 'تحديث حالة طلب التوظيف', 'تم تحديث حالة طلبك للوظيفة \"مبرمجة\" إلى: accepted', 'application_update', 0, '2025-12-12 06:13:28'),
(4, 4, 'تحديث حالة طلب التوظيف', 'تم تحديث حالة طلبك للوظيفة \"مبرمجة\" إلى: accepted', 'application_update', 0, '2025-12-12 14:46:12');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_type` enum('job_seeker','employer') DEFAULT 'job_seeker',
  `company_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `user_type`, `company_name`, `created_at`) VALUES
(1, 'ahmed', 'ahmed@example.com', '123', 'أحمد محمد', NULL, 'job_seeker', NULL, '2025-12-12 03:11:18'),
(2, 'company1', 'company@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'شركة التقنية', NULL, 'employer', 'شركة التقنية المتطورة', '2025-12-12 03:11:18'),
(3, 'roba', 'robaomer2007.3.3@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'ربا عمر', '', 'employer', 'roba', '2025-12-12 05:07:31'),
(4, 'dalal', 'dal@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'دلال صالح', '', 'job_seeker', '', '2025-12-12 05:25:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`employer_id`) REFERENCES `users` (`id`);

--
-- قيود الجداول `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`),
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- قيود الجداول `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
