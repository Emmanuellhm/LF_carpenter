-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-08-2025 a las 13:57:18
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `lf`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carpenters`
--

CREATE TABLE `carpenters` (
  `carpenter_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp(),
  `budget_range` decimal(10,2) DEFAULT NULL COMMENT 'Presupuesto base del carpintero'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carpenters`
--

INSERT INTO `carpenters` (`carpenter_id`, `description`, `approved`, `created_at`, `last_update`, `budget_range`) VALUES
(3, 'Especialista en muebles de madera', 1, '2025-08-14 10:54:02', '2025-08-14 10:54:02', 500.00),
(4, 'Experta en carpintería metálica', 1, '2025-08-14 10:54:02', '2025-08-14 10:54:02', 800.00);

--
-- Disparadores `carpenters`
--
DELIMITER $$
CREATE TRIGGER `update_carpenters_last_update` BEFORE UPDATE ON `carpenters` FOR EACH ROW BEGIN
    -- Siempre actualiza la fecha de última modificación
    SET NEW.last_update = CURRENT_TIMESTAMP();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certifications`
--

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

--
-- Volcado de datos para la tabla `certifications`
--

INSERT INTO `certifications` (`certification_id`, `carpenter_id`, `name`, `issuer`, `issued_date`, `verified`, `created_at`, `last_update`) VALUES
(1, 3, 'Certificación en carpintería avanzada', 'SENA', '2024-05-10', 1, '2025-08-14 10:54:40', '2025-08-14 10:54:40'),
(2, 4, 'Certificación en estructuras metálicas', 'SENA', '2023-11-15', 1, '2025-08-14 10:54:40', '2025-08-14 10:54:40');

--
-- Disparadores `certifications`
--
DELIMITER $$
CREATE TRIGGER `update_certifications_last_update` BEFORE UPDATE ON `certifications` FOR EACH ROW BEGIN
    SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_logins`
--

CREATE TABLE `failed_logins` (
  `fail_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email_attempted` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `fail_reason` varchar(100) DEFAULT NULL,
  `attempt_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materials`
--

CREATE TABLE `materials` (
  `material_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materials`
--

INSERT INTO `materials` (`material_id`, `request_id`, `name`, `quantity`, `unit`, `cost`, `created_at`, `last_update`) VALUES
(1, 101, 'Madera', 5.00, 'Tablas', 200.00, '2025-08-14 10:54:19', '2025-08-14 10:54:19'),
(2, 101, 'Tornillos', 50.00, 'Unidades', 20.00, '2025-08-14 10:54:19', '2025-08-14 10:54:19'),
(3, 102, 'Vidrio', 2.00, 'Planchas', 300.00, '2025-08-14 10:54:19', '2025-08-14 10:54:19');

--
-- Disparadores `materials`
--
DELIMITER $$
CREATE TRIGGER `update_materials_last_update` BEFORE UPDATE ON `materials` FOR EACH ROW BEGIN
    SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portafolios`
--

CREATE TABLE `portafolios` (
  `portfolio_id` int(11) NOT NULL,
  `carpenter_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portafolios`
--

INSERT INTO `portafolios` (`portfolio_id`, `carpenter_id`, `title`, `description`, `image_url`, `created_at`, `last_update`) VALUES
(1, 3, 'Mesa rústica', 'Mesa de comedor estilo rústico en madera maciza', 'mesa.jpg', '2025-08-14 10:54:30', '2025-08-14 10:54:30'),
(2, 4, 'Ventana moderna', 'Ventana de aluminio con vidrio templado', 'ventana.jpg', '2025-08-14 10:54:30', '2025-08-14 10:54:30');

--
-- Disparadores `portafolios`
--
DELIMITER $$
CREATE TRIGGER `update_portafolios_last_update` BEFORE UPDATE ON `portafolios` FOR EACH ROW BEGIN
    SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requests`
--

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

--
-- Volcado de datos para la tabla `requests`
--

INSERT INTO `requests` (`request_id`, `user_id`, `carpenter_id`, `job_type`, `material`, `dimensions`, `budget`, `description`, `status`, `response_message`, `created_at`, `last_update`) VALUES
(101, 1, 3, 'Mesa de comedor', 'Madera', '120x80 cm', 450.00, NULL, 'accepted', NULL, '2025-08-14 10:54:09', '2025-08-14 10:54:09'),
(102, 2, 4, 'Ventana de vidrio', 'Vidrio', '100x100 cm', 600.00, NULL, 'pending', NULL, '2025-08-14 10:54:09', '2025-08-14 10:54:09');

--
-- Disparadores `requests`
--
DELIMITER $$
CREATE TRIGGER `update_requests_last_update` BEFORE UPDATE ON `requests` FOR EACH ROW BEGIN
    SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `carpenter_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `reviews`
--
DELIMITER $$
CREATE TRIGGER `update_reviews_last_update` BEFORE UPDATE ON `reviews` FOR EACH ROW BEGIN
    SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traceability`
--

CREATE TABLE `traceability` (
  `trace_id` int(11) NOT NULL,
  `action_type` varchar(100) NOT NULL,
  `performed_by` int(11) NOT NULL,
  `affected_user` int(11) DEFAULT NULL,
  `affected_table` varchar(50) DEFAULT NULL,
  `affected_id` int(11) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `authority_level` enum('user','carpenter','admin') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

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

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password_hash`, `phone`, `role`, `is_active`, `failed_attempts`, `account_locked`, `created_at`, `last_update`) VALUES
(1, 'Juan Pérez', 'juan@example.com', 'hash1', '3001112233', 'user', 1, 0, 0, '2025-08-14 10:53:52', '2025-08-14 10:53:52'),
(2, 'María López', 'maria@example.com', 'hash2', '3004445566', 'user', 1, 0, 0, '2025-08-14 10:53:52', '2025-08-14 10:53:52'),
(3, 'Carlos Ramírez', 'carlos@example.com', 'hash3', '3007778899', 'carpenter', 1, 0, 0, '2025-08-14 10:53:52', '2025-08-14 10:53:52'),
(4, 'Laura Gómez', 'laura@example.com', 'hash4', '3010001122', 'carpenter', 1, 0, 0, '2025-08-14 10:53:52', '2025-08-14 10:53:52');

--
-- Disparadores `users`
--
DELIMITER $$
CREATE TRIGGER `update_users_last_update` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_behavior`
--

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

--
-- Disparadores `user_behavior`
--
DELIMITER $$
CREATE TRIGGER `update_user_behavior_last_update` BEFORE UPDATE ON `user_behavior` FOR EACH ROW BEGIN
    SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_preferences`
--

CREATE TABLE `user_preferences` (
  `preference_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `preferred_materials` varchar(255) DEFAULT NULL,
  `preferred_styles` varchar(255) DEFAULT NULL,
  `notifications_enabled` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `user_preferences`
--
DELIMITER $$
CREATE TRIGGER `update_user_preferences_last_update` BEFORE UPDATE ON `user_preferences` FOR EACH ROW BEGIN
    SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `carpenters`
--
ALTER TABLE `carpenters`
  ADD PRIMARY KEY (`carpenter_id`);

--
-- Indices de la tabla `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`certification_id`),
  ADD KEY `carpenter_id` (`carpenter_id`);

--
-- Indices de la tabla `failed_logins`
--
ALTER TABLE `failed_logins`
  ADD PRIMARY KEY (`fail_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indices de la tabla `portafolios`
--
ALTER TABLE `portafolios`
  ADD PRIMARY KEY (`portfolio_id`),
  ADD KEY `carpenter_id` (`carpenter_id`);

--
-- Indices de la tabla `requests`
--
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

--
-- Indices de la tabla `traceability`
--
ALTER TABLE `traceability`
  ADD PRIMARY KEY (`trace_id`),
  ADD KEY `performed_by` (`performed_by`),
  ADD KEY `affected_user` (`affected_user`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `user_behavior`
--
ALTER TABLE `user_behavior`
  ADD PRIMARY KEY (`behavior_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`preference_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carpenters`
--
ALTER TABLE `carpenters`
  MODIFY `carpenter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `certifications`
--
ALTER TABLE `certifications`
  MODIFY `certification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `failed_logins`
--
ALTER TABLE `failed_logins`
  MODIFY `fail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materials`
--
ALTER TABLE `materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `portafolios`
--
ALTER TABLE `portafolios`
  MODIFY `portfolio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT de la tabla `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `traceability`
--
ALTER TABLE `traceability`
  MODIFY `trace_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `user_behavior`
--
ALTER TABLE `user_behavior`
  MODIFY `behavior_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `preference_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `certifications`
--
ALTER TABLE `certifications`
  ADD CONSTRAINT `certifications_ibfk_1` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);

--
-- Filtros para la tabla `failed_logins`
--
ALTER TABLE `failed_logins`
  ADD CONSTRAINT `failed_logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`);

--
-- Filtros para la tabla `portafolios`
--
ALTER TABLE `portafolios`
  ADD CONSTRAINT `portafolios_ibfk_1` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);

--
-- Filtros para la tabla `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);

--
-- Filtros para la tabla `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);

--
-- Filtros para la tabla `traceability`
--
ALTER TABLE `traceability`
  ADD CONSTRAINT `traceability_ibfk_1` FOREIGN KEY (`performed_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `traceability_ibfk_2` FOREIGN KEY (`affected_user`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `user_behavior`
--
ALTER TABLE `user_behavior`
  ADD CONSTRAINT `user_behavior_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
