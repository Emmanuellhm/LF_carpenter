CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `carpenters` (
  `carpenter_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELIMITER $$
CREATE TRIGGER `update_carpenters_last_update` BEFORE UPDATE ON `carpenters` FOR EACH ROW BEGIN
   SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;


CREATE TABLE `certifications` (
  `certification_id` int(11) NOT NULL,
  `carpenter_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `issuer` varchar(100) DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELIMITER $$
CREATE TRIGGER `update_certifications_last_update` BEFORE UPDATE ON `certifications` FOR EACH ROW BEGIN
   SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;


CREATE TABLE `failed_logins` (
  `fail_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email_attempted` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `fail_reason` varchar(100) DEFAULT NULL,
  `attempt_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `portfolios` (
  `portfolio_id` int(11) NOT NULL,
  `carpenter_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELIMITER $$
CREATE TRIGGER `update_portfolios_last_update` BEFORE UPDATE ON `portfolios` FOR EACH ROW BEGIN
   SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;


CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `carpenter_id` int(11) NOT NULL,
  `job_type` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `response_message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELIMITER $$
CREATE TRIGGER `update_requests_last_update` BEFORE UPDATE ON `requests` FOR EACH ROW BEGIN
   SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;



CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `carpenter_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELIMITER $$
CREATE TRIGGER `update_reviews_last_update` BEFORE UPDATE ON `reviews` FOR EACH ROW BEGIN
   SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;


CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','carpenter','admin') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `failed_attempts` int(11) DEFAULT 0,
  `account_locked` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELIMITER $$
CREATE TRIGGER `update_users_last_update` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
   SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;


CREATE TABLE `user_behavior` (
  `behavior_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `behavior_type` enum('view_profile','view_portfolio','send_request','leave_review','search','filter','click_whatsapp','visit_certified') NOT NULL,
  `target_type` enum('carpenter','portfolio','request','review','search') NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `action_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`action_data`)),
  `duration_seconds` int(11) DEFAULT NULL,
  `occurred_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELIMITER $$
CREATE TRIGGER `update_user_behavior_last_update` BEFORE UPDATE ON `user_behavior` FOR EACH ROW BEGIN
   SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;


CREATE TABLE `user_preferences` (
  `preference_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `preferred_materials` varchar(255) DEFAULT NULL,
  `preferred_styles` varchar(255) DEFAULT NULL,
  `notifications_enabled` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELIMITER $$
CREATE TRIGGER `update_user_preferences_last_update` BEFORE UPDATE ON `user_preferences` FOR EACH ROW BEGIN
   SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;


ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);


ALTER TABLE `carpenters`
  ADD PRIMARY KEY (`carpenter_id`);


ALTER TABLE `certifications`
  ADD PRIMARY KEY (`certification_id`),
  ADD KEY `carpenter_id` (`carpenter_id`);


ALTER TABLE `failed_logins`
  ADD PRIMARY KEY (`fail_id`),
  ADD KEY `user_id` (`user_id`);


ALTER TABLE `portfolios`
  ADD PRIMARY KEY (`portfolio_id`),
  ADD KEY `carpenter_id` (`carpenter_id`);


ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `carpenter_id` (`carpenter_id`);

--
-- Indices de la tabla `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `carpenter_id` (`carpenter_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `user_behavior`
  ADD PRIMARY KEY (`behavior_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`preference_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `certifications`
  MODIFY `certification_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `failed_logins`
  MODIFY `fail_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `portfolios`
  MODIFY `portfolio_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `user_behavior`
  MODIFY `behavior_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `user_preferences`
  MODIFY `preference_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);


ALTER TABLE `certifications`
  ADD CONSTRAINT `certifications_ibfk_1` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);


ALTER TABLE `failed_logins`
  ADD CONSTRAINT `failed_logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);


ALTER TABLE `portfolios`
  ADD CONSTRAINT `portfolios_ibfk_1` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);


ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);


ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);


ALTER TABLE `user_behavior`
  ADD CONSTRAINT `user_behavior_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);


ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

