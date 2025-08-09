

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `chatify_convo` (
  `dataid` int(10) NOT NULL,
  `project_refid` varchar(26) DEFAULT NULL,
  `convo_refid` varchar(26) DEFAULT NULL,
  `convo_theme` text DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `created_by` varchar(26) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_message` varchar(60) DEFAULT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `chatify_member` (
  `dataid` int(10) NOT NULL,
  `project_refid` varchar(26) DEFAULT NULL,
  `convo_refid` varchar(26) DEFAULT NULL,
  `user_refid` varchar(26) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `image` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `member_type` varchar(60) DEFAULT NULL,
  `blocked` enum('0','1') DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `chatify_member_type` (
  `dataid` int(10) NOT NULL,
  `project_refid` varchar(26) DEFAULT NULL,
  `member_name` varchar(60) DEFAULT NULL,
  `public` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `chatify_messages` (
  `dataid` int(10) NOT NULL,
  `project_refid` varchar(26) DEFAULT NULL,
  `convo_refid` varchar(26) DEFAULT NULL,
  `content_type` enum('REG_TXT','SGL_IMG','MTL_IMG','REG_LINK','WEB_LINK','FORWARDED','REPLY') DEFAULT NULL,
  `content_json` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(26) DEFAULT NULL,
  `pinned` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `chatify_convo`
  ADD PRIMARY KEY (`dataid`),
  ADD UNIQUE KEY `convo_refid` (`convo_refid`);

ALTER TABLE `chatify_member`
  ADD PRIMARY KEY (`dataid`);

ALTER TABLE `chatify_member_type`
  ADD PRIMARY KEY (`dataid`);

ALTER TABLE `chatify_messages`
  ADD PRIMARY KEY (`dataid`);

ALTER TABLE `chatify_convo`
  MODIFY `dataid` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chatify_member`
  MODIFY `dataid` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chatify_member_type`
  MODIFY `dataid` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chatify_messages`
  MODIFY `dataid` int(10) NOT NULL AUTO_INCREMENT;

