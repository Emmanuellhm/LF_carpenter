-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-03-2026 a las 15:38:14
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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `approve_carpenter` (IN `p_carpenter_id` INT)   BEGIN
    DECLARE v_user_id INT;

    -- Obtener el ID del usuario asociado al carpintero
    SELECT user_id INTO v_user_id
    FROM carpenters
    WHERE carpenter_id = p_carpenter_id;

    -- Validar que exista
    IF v_user_id IS NOT NULL THEN
        -- Actualizar estado del carpintero
        UPDATE carpenters
        SET approved = 1,
            is_verified = 1,
            last_update = NOW()
        WHERE carpenter_id = p_carpenter_id;

        -- Actualizar rol del usuario a 'carpenter'
        UPDATE users
        SET role = 'carpenter'
        WHERE user_id = v_user_id;

        -- Registrar acción en trazabilidad (si tienes tabla de logs)
        INSERT INTO traceability (performed_by, affected_user, action_type, affected_table)
        VALUES (1, v_user_id, 'APPROVE_CARPENTER', 'carpenters'); -- 1 = admin_id (puedes adaptarlo)

    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `createRequestWithMaterials` (IN `p_user_id` INT, IN `p_carpenter_id` INT, IN `p_job_type` VARCHAR(100), IN `p_material` VARCHAR(100), IN `p_budget` DECIMAL(10,2), IN `p_material_name` VARCHAR(100), IN `p_quantity` DECIMAL(10,2), IN `p_unit` VARCHAR(50), IN `p_cost` DECIMAL(10,2))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO requests (user_id, carpenter_id, job_type, material, budget, status)
    VALUES (p_user_id, p_carpenter_id, p_job_type, p_material, p_budget, 'pending');

    INSERT INTO materials (request_id, name, quantity, unit, cost)
    VALUES (LAST_INSERT_ID(), p_material_name, p_quantity, p_unit, p_cost);

    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteMaterial` (IN `p_material_id` INT)   BEGIN
    DELETE FROM materials WHERE material_id = p_material_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteRequest` (IN `p_request_id` INT)   BEGIN
    DELETE FROM requests WHERE request_id = p_request_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteReview` (IN `p_review_id` INT)   BEGIN
    DELETE FROM reviews WHERE review_id = p_review_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteUser` (IN `p_user_id` INT)   BEGIN
    DELETE FROM users WHERE user_id = p_user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getPedidosCompletadosPorCarpintero` (IN `p_carpenter_id` INT)   BEGIN
    SELECT *
    FROM requests
    WHERE carpenter_id = p_carpenter_id
      AND status = 'completed';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getPedidosPorUsuario` (IN `p_user_id` INT)   BEGIN
    SELECT *
    FROM requests
    WHERE user_id = p_user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getProductosPorCarpintero` (IN `p_carpenter_id` INT)   BEGIN
    SELECT *
    FROM portafolio
    WHERE carpenter_user_id = p_carpenter_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUsers` ()   BEGIN
    SELECT user_id, full_name, email, role, is_active FROM users;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertMaterial` (IN `p_request_id` INT, IN `p_name` VARCHAR(100), IN `p_quantity` DECIMAL(10,2), IN `p_unit` VARCHAR(50), IN `p_cost` DECIMAL(10,2))   BEGIN
    INSERT INTO materials (request_id, name, quantity, unit, cost)
    VALUES (p_request_id, p_name, p_quantity, p_unit, p_cost);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertReview` (IN `p_user_id` INT, IN `p_carpenter_id` INT, IN `p_rating` INT, IN `p_comment` TEXT)   BEGIN
    INSERT INTO reviews (user_id, carpenter_id, rating, comment)
    VALUES (p_user_id, p_carpenter_id, p_rating, p_comment);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertUser` (IN `p_full_name` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_password_hash` VARCHAR(255), IN `p_phone` VARCHAR(20), IN `p_role` ENUM('user','carpenter','admin'))   BEGIN
    INSERT INTO users (full_name, email, password_hash, phone, role)
    VALUES (p_full_name, p_email, p_password_hash, p_phone, p_role);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `reject_carpenter` (IN `p_carpenter_id` INT)   BEGIN
    DECLARE v_user_id INT;

    -- Obtener el ID del usuario asociado al carpintero
    SELECT user_id INTO v_user_id
    FROM carpenters
    WHERE carpenter_id = p_carpenter_id;

    -- Validar que exista
    IF v_user_id IS NOT NULL THEN
        -- Actualizar estado del carpintero
        UPDATE carpenters
        SET approved = 0,
            is_verified = 0,
            last_update = NOW()
        WHERE carpenter_id = p_carpenter_id;

        -- Mantener el rol del usuario como 'client'
        UPDATE users
        SET role = 'client'
        WHERE user_id = v_user_id;

        -- Registrar acción en trazabilidad (si aplica)
        INSERT INTO traceability (performed_by, affected_user, action_type, affected_table)
        VALUES (1, v_user_id, 'REJECT_CARPENTER', 'carpenters');
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateMaterial` (IN `p_material_id` INT, IN `p_quantity` DECIMAL(10,2), IN `p_cost` DECIMAL(10,2))   BEGIN
    UPDATE materials
    SET quantity = p_quantity,
        cost = p_cost
    WHERE material_id = p_material_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateRequest` (IN `p_request_id` INT, IN `p_status` ENUM('pending','accepted','rejected'), IN `p_response_message` TEXT)   BEGIN
    UPDATE requests
    SET status = p_status,
        response_message = p_response_message
    WHERE request_id = p_request_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateReview` (IN `p_review_id` INT, IN `p_rating` INT, IN `p_comment` TEXT)   BEGIN
    UPDATE reviews
    SET rating = p_rating,
        comment = p_comment
    WHERE review_id = p_review_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUser` (IN `p_user_id` INT, IN `p_full_name` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_phone` VARCHAR(20), IN `p_role` ENUM('user','carpenter','admin'), IN `p_is_active` TINYINT)   BEGIN
    UPDATE users
    SET full_name = p_full_name,
        email = p_email,
        phone = p_phone,
        role = p_role,
        is_active = p_is_active
    WHERE user_id = p_user_id;
END$$

DELIMITER ;

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

--
-- Volcado de datos para la tabla `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action_type`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'LOGIN', 'Usuario inició sesión', '192.168.0.10', 'Chrome', '2025-08-28 10:45:30'),
(2, 2, 'LOGOUT', 'Usuario cerró sesión', '192.168.0.11', 'Firefox', '2025-08-28 10:45:30'),
(3, 3, 'REQUEST_CREATED', 'Se creó una solicitud', '192.168.0.12', 'Edge', '2025-08-28 10:45:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carpenters`
--

CREATE TABLE `carpenters` (
  `carpenter_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp(),
  `budget_range` decimal(10,2) DEFAULT NULL COMMENT 'Presupuesto base del carpintero',
  `user_id` int(11) DEFAULT NULL,
  `carpenter_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `specialties` varchar(255) DEFAULT NULL,
  `rating_avg` decimal(2,1) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carpenters`
--

INSERT INTO `carpenters` (`carpenter_id`, `description`, `cv_file`, `approved`, `created_at`, `last_update`, `budget_range`, `user_id`, `carpenter_name`, `email`, `password_hash`, `experience_years`, `specialties`, `rating_avg`, `is_verified`) VALUES
(1, NULL, NULL, 1, '2025-08-28 11:32:14', '2025-11-26 14:04:18', NULL, 11, 'Carpintero 1', '', '', 5, 'Muebles de madera, sillas', 4.5, 1),
(2, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:38:51', NULL, 12, 'Carpintero 2', '', '', 8, 'Closets, cocinas integrales', 4.7, 1),
(3, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:34', NULL, 13, 'Carpintero 3', '', '', 3, 'Puertas, ventanas', 4.2, 0),
(4, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:35', NULL, 14, 'Carpintero 4', '', '', 10, 'Muebles rústicos', 4.8, 1),
(5, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:35', NULL, 15, 'Carpintero 5', '', '', 6, 'Decoración en madera', 4.6, 1),
(6, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:36', NULL, 16, 'Carpintero 6', '', '', 4, 'Reparaciones de muebles', 4.1, 0),
(7, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:36', NULL, 17, 'Carpintero 7', '', '', 7, 'Mesas de comedor', 4.4, 1),
(8, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:37', NULL, 18, 'Carpintero 8', '', '', 12, 'Diseño de interiores', 4.9, 1),
(9, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:37', NULL, 19, 'Carpintero 9', '', '', 2, 'Estanterías y repisas', 4.0, 0),
(10, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:38', NULL, 20, 'Carpintero 10', '', '', 9, 'Muebles de oficina', 4.5, 1),
(11, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:38', NULL, 21, 'Carpintero 11', '', '', 11, 'Muebles minimalistas', 4.7, 1),
(12, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:38', NULL, 22, 'Carpintero 12', '', '', 5, 'Camas y cabeceros', 4.3, 0),
(13, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:39', NULL, 23, 'Carpintero 13', '', '', 14, 'Restauración de muebles', 4.9, 1),
(14, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:39', NULL, 24, 'Carpintero 14', '', '', 6, 'Cajoneras y armarios', 4.5, 1),
(15, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:39', NULL, 25, 'Carpintero 15', '', '', 8, 'Puertas corredizas', 4.6, 1),
(16, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:40', NULL, 26, 'Carpintero 16', '', '', 4, 'Muebles infantiles', 4.2, 0),
(17, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:40', NULL, 27, 'Carpintero 17', '', '', 10, 'Cocinas modernas', 4.8, 1),
(18, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:41', NULL, 28, 'Carpintero 18', '', '', 3, 'Mesas auxiliares', 4.1, 0),
(19, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:41', NULL, 29, 'Carpintero 19', '', '', 7, 'Muebles modulares', 4.6, 1),
(20, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:41', NULL, 30, 'Carpintero 20', '', '', 15, 'Todo tipo de carpintería', 5.0, 1),
(21, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:42', NULL, 31, 'Carpintero 21', '', '', 5, 'Muebles clásicos', 4.3, 1),
(22, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:42', NULL, 32, 'Carpintero 22', '', '', 6, 'Muebles modernos', 4.5, 1),
(23, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:42', NULL, 33, 'Carpintero 23', '', '', 8, 'Diseño de estanterías', 4.7, 1),
(24, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:43', NULL, 34, 'Carpintero 24', '', '', 12, 'Decoración artesanal', 4.8, 1),
(25, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:43', NULL, 35, 'Carpintero 25', '', '', 9, 'Mesas plegables', 4.6, 0),
(26, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:44', NULL, 36, 'Carpintero 26', '', '', 11, 'Muebles de lujo', 4.9, 1),
(27, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:44', NULL, 37, 'Carpintero 27', '', '', 4, 'Reparaciones básicas', 4.1, 0),
(28, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:44', NULL, 38, 'Carpintero 28', '', '', 13, 'Diseño arquitectónico en madera', 4.8, 1),
(29, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:45', NULL, 39, 'Carpintero 29', '', '', 7, 'Muebles multifuncionales', 4.5, 1),
(30, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:46', NULL, 40, 'Carpintero 30', '', '', 6, 'Estilo rústico', 4.4, 1),
(31, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:46', NULL, 41, 'Carpintero 31', '', '', 10, 'Camas personalizadas', 4.7, 1),
(32, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:47', NULL, 42, 'Carpintero 32', '', '', 15, 'Bibliotecas grandes', 4.9, 1),
(33, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:47', NULL, 43, 'Carpintero 33', '', '', 5, 'Puertas clásicas', 4.3, 0),
(34, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:48', NULL, 44, 'Carpintero 34', '', '', 9, 'Closets modernos', 4.6, 1),
(35, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:48', NULL, 45, 'Carpintero 35', '', '', 8, 'Muebles ecológicos', 4.8, 1),
(36, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:49', NULL, 46, 'Carpintero 36', '', '', 7, 'Mesas de lujo', 4.7, 1),
(37, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:49', NULL, 47, 'Carpintero 37', '', '', 3, 'Reparaciones simples', 4.0, 0),
(38, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:49', NULL, 48, 'Carpintero 38', '', '', 12, 'Diseños exclusivos', 4.9, 1),
(39, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:50', NULL, 49, 'Carpintero 39', '', '', 14, 'Restauraciones clásicas', 5.0, 1),
(40, NULL, NULL, -1, '2025-08-28 11:32:14', '2025-11-26 14:45:51', NULL, 50, 'Carpintero 40', '', '', 6, 'Muebles minimalistas', 4.5, 1),
(41, 'Tel: 1111 | Ciudad: Ibague | Portafolio:  | CV:  | Email: miguel@gmail.com', NULL, -1, '2025-11-12 10:26:36', '2025-12-02 19:16:41', NULL, NULL, 'Miguel', '', '', 0, '', NULL, 0),
(42, 'Tel: 111111 | Ciudad: Medellin | Portafolio: Prueba de registro | CV: c2a7d670-8fa5-426c-b054-e0a8977b9d11.pdf | Email: miguel@gmail.com', NULL, 1, '2025-11-12 10:31:28', '2025-11-12 10:57:06', NULL, NULL, 'Miguel', '', '', 14, 'Carpintero', NULL, 1),
(44, 'Ciudad: Medellin | Tel: 11111 | Email: Miguel@gmail.com | Portafolio: Prueba | CV: c2a7d670-8fa5-426c-b054-e0a8977b9d11.pdf', NULL, -1, '2025-11-12 10:41:46', '2025-12-02 19:16:43', NULL, NULL, 'Miguel', '', '', 13, 'Madera', NULL, 0),
(266, 'Teléfono: 3118020103 | Ciudad: | Email: | Tel: ', 'uploads/cvs/cv_1764727197_cv_1764721668_Emmanuel_Hincapie_marin_CV.pdf', 1, '2025-12-02 20:59:57', '2025-12-03 08:24:29', NULL, NULL, 'Emmanuel Hincapie Marin', 'Emma@gmail.com', '$2y$10$6ynajB90ccAB.sNaVXFfGuHZMIlVJLPoF3pLiCO1G62.WHPyM0Qs6', 4, 'Madera', NULL, NULL);

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

--
-- Volcado de datos para la tabla `failed_logins`
--

INSERT INTO `failed_logins` (`fail_id`, `user_id`, `email_attempted`, `ip_address`, `user_agent`, `fail_reason`, `attempt_time`) VALUES
(1, 1, NULL, '192.168.0.20', NULL, NULL, '2025-08-28 10:45:38'),
(2, 2, NULL, '192.168.0.21', NULL, NULL, '2025-08-28 10:45:38'),
(3, 3, NULL, '192.168.0.22', NULL, NULL, '2025-08-28 10:45:38');

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
(1, 1, 'Madera Pino', 10.00, 'unidades', 250.00, '2025-08-28 10:46:12', '2025-08-28 10:46:12'),
(2, 2, 'Barniz', 5.00, 'litros', 100.00, '2025-08-28 10:46:12', '2025-08-28 10:46:12'),
(3, 3, 'Clavos', 500.00, 'unidades', 50.00, '2025-08-28 10:46:12', '2025-08-28 10:46:12'),
(4, 1, 'Madera Pino', 10.00, 'unidades', 250.00, '2025-08-28 10:59:02', '2025-08-28 10:59:02'),
(5, 2, 'Barniz', 5.00, 'litros', 100.00, '2025-08-28 10:59:02', '2025-08-28 10:59:02'),
(6, 3, 'Clavos', 500.00, 'unidades', 50.00, '2025-08-28 10:59:02', '2025-08-28 10:59:02'),
(7, 100, 'Bisagras', 20.00, 'unidades', 200.00, '2025-08-28 10:59:02', '2025-08-28 10:59:02'),
(8, 3, 'Clavos', 500.00, 'unidades', 125.00, '2025-09-17 11:06:01', '2025-09-17 11:06:01');

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
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'Nuevo comentario en tu proyecto \"Mesa de Comedor Moderna\"', 0, '2025-12-02 17:10:11'),
(2, 2, 'Nueva solicitud de proyecto personalizado recibida', 0, '2025-12-02 17:10:11'),
(3, 3, 'Tu solicitud de proyecto ha sido aceptada', 1, '2025-12-02 17:10:11'),
(4, 4, 'Nuevo comentario en tu proyecto \"Rec├ímara Completa\"', 0, '2025-12-02 17:10:11'),
(5, 5, 'Nueva solicitud de proyecto personalizado recibida', 0, '2025-12-02 17:10:11'),
(6, 1, 'El carpintero ha respondido a tu solicitud', 1, '2025-12-02 17:10:11'),
(7, 10, 'Tu solicitud ha sido aceptada. El carpintero te contactar├í pronto.', 0, '2025-12-02 17:10:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portafolio`
--

CREATE TABLE `portafolio` (
  `project_id` int(11) NOT NULL,
  `carpenter_user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portafolio`
--

INSERT INTO `portafolio` (`project_id`, `carpenter_user_id`, `title`, `description`, `image_path`, `price`, `created_at`) VALUES
(1, 2, 'Mesa de Comedor Moderna', 'Mesa de comedor de madera de roble con acabado natural. Capacidad para 6 personas.', 'uploads/mesa_comedor_1.jpg', 15000.00, '2025-12-02 17:10:11'),
(2, 2, 'Librer├¡a Minimalista', 'Librer├¡a de pino con dise├▒o minimalista, 5 repisas ajustables.', 'uploads/libreria_1.jpg', 8500.00, '2025-12-02 17:10:11'),
(3, 2, 'Escritorio Ejecutivo', 'Escritorio de cedro con cajones y compartimentos. Ideal para oficina.', 'uploads/escritorio_1.jpg', 12000.00, '2025-12-02 17:10:11'),
(4, 3, 'Silla de Madera Artesanal', 'Silla hecha a mano con detalles tallados. Dise├▒o tradicional.', 'uploads/silla_1.jpg', 3500.00, '2025-12-02 17:10:11'),
(5, 3, 'Cama King Size', 'Cama de madera maciza con cabecera tallada. Estilo r├║stico.', 'uploads/cama_1.jpg', 25000.00, '2025-12-02 17:10:11'),
(6, 4, 'Rec├ímara Completa', 'Juego de rec├ímara completo: cama, bur├│, tocador y ropero.', 'uploads/recamara_1.jpg', 45000.00, '2025-12-02 17:10:11'),
(7, 4, 'Cocina Integral', 'Muebles de cocina en madera de maple. Incluye alacenas y barra.', 'uploads/cocina_1.jpg', 65000.00, '2025-12-02 17:10:11'),
(8, 5, 'Puerta Principal Tallada', 'Puerta de entrada en caoba con tallados ornamentales.', 'uploads/puerta_1.jpg', 18000.00, '2025-12-02 17:10:11'),
(31, 266, 'Mesa de comedor', 'Mesa de madera, mueble funcional con una superficie lisa para comer o trabajar, sostenida por una o varias patas. Destaca por su durabilidad, calidez, y belleza natural, y puede variar en diseño, desde rústico hasta moderno, adaptándose a diferentes usos como mesa de comedor, escritorio o auxiliar.', 'uploads/projects/proj_692fb65baebee.png', 150000.00, '2025-12-02 23:02:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portafolios`
--

CREATE TABLE `portafolios` (
  `id_portafolio` int(11) NOT NULL,
  `carpenter_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `project_comments`
--

CREATE TABLE `project_comments` (
  `comment_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `project_comments`
--

INSERT INTO `project_comments` (`comment_id`, `project_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 1, '┬íHermosa mesa! ┬┐Hacen env├¡os fuera de la ciudad?', '2025-12-02 17:10:11'),
(2, 1, 10, 'Me encanta el dise├▒o. ┬┐Cu├ínto tiempo toma fabricarla?', '2025-12-02 17:10:11'),
(3, 2, 1, 'Perfecta para mi sala de lectura. ┬┐Puedo personalizarla?', '2025-12-02 17:10:11'),
(4, 3, 15, 'Excelente trabajo. ┬┐Aceptan pagos en mensualidades?', '2025-12-02 17:10:11'),
(5, 5, 20, '┬┐Incluye el colch├│n o solo la estructura?', '2025-12-02 17:10:11'),
(6, 6, 25, 'Interesado en este proyecto. ┬┐Tienen m├ís fotos?', '2025-12-02 17:10:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `project_requests`
--

CREATE TABLE `project_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `carpenter_user_id` int(11) NOT NULL,
  `project_description` text NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `status` enum('pending','accepted','rejected','completed') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `project_requests`
--

INSERT INTO `project_requests` (`request_id`, `user_id`, `carpenter_user_id`, `project_description`, `contact_info`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Necesito un closet empotrado de 3 metros con acabado en nogal. Incluir cajones y zapatero.', 'usuario1@email.com / 555-1234', 'pending', '2025-12-02 17:10:11', '2025-12-02 17:10:11'),
(2, 10, 3, 'Quisiera una mesa de centro moderna con cristal templado y base de madera.', 'usuario10@email.com / 555-5678', 'accepted', '2025-12-02 17:10:11', '2025-12-02 17:10:11'),
(3, 15, 4, 'Requiero muebles para consultorio m├®dico: escritorio, archivero y sala de espera.', 'usuario15@email.com / 555-9012', 'pending', '2025-12-02 17:10:11', '2025-12-02 17:10:11'),
(4, 20, 5, 'Barra para bar en casa, estilo industrial con madera y metal.', 'usuario20@email.com / 555-3456', 'rejected', '2025-12-02 17:10:11', '2025-12-02 17:10:11'),
(5, 25, 2, 'Reparaci├│n y restauraci├│n de muebles antiguos heredados.', 'usuario25@email.com / 555-7890', 'completed', '2025-12-02 17:10:11', '2025-12-02 17:10:11');

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
  `status` enum('pending','accepted','rejected','completed') NOT NULL DEFAULT 'pending',
  `response_message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `requests`
--

INSERT INTO `requests` (`request_id`, `user_id`, `carpenter_id`, `job_type`, `material`, `dimensions`, `budget`, `description`, `status`, `response_message`, `created_at`, `last_update`) VALUES
(1, 1, 1, 'Mueble de sala a medida', NULL, NULL, 1200.00, NULL, 'pending', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(2, 2, 2, 'Closet empotrado', NULL, NULL, 1500.00, NULL, 'accepted', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(3, 3, 3, 'Reparación de puerta', NULL, NULL, 300.00, NULL, 'completed', NULL, '2025-08-28 11:33:44', '2025-08-28 11:42:16'),
(4, 4, 4, 'Mesa de comedor moderna', NULL, NULL, 1100.00, NULL, 'pending', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(5, 5, 5, 'Restauración de armario antiguo', NULL, NULL, 900.00, NULL, 'accepted', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(6, 6, 6, 'Estantería flotante', NULL, NULL, 400.00, NULL, 'pending', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(7, 7, 7, 'Cama king size personalizada', NULL, NULL, 2000.00, NULL, 'rejected', NULL, '2025-08-28 11:33:44', '2025-08-28 11:42:16'),
(8, 8, 8, 'Puerta corrediza de madera', NULL, NULL, 1300.00, NULL, 'pending', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(9, 9, 9, 'Cocina integral', NULL, NULL, 3500.00, NULL, 'accepted', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(10, 10, 10, 'Muebles infantiles', NULL, NULL, 800.00, NULL, 'completed', NULL, '2025-08-28 11:33:44', '2025-08-28 11:42:16'),
(11, 11, 11, 'Mesa auxiliar rústica', NULL, NULL, 500.00, NULL, 'pending', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(12, 12, 12, 'Muebles de oficina', NULL, NULL, 2500.00, NULL, 'accepted', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(13, 13, 13, 'Reparación de silla antigua', NULL, NULL, 200.00, NULL, 'completed', NULL, '2025-08-28 11:33:44', '2025-08-28 11:42:16'),
(14, 14, 14, 'Mueble minimalista', NULL, NULL, 950.00, NULL, 'pending', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(15, 15, 15, 'Decoración interior en madera', NULL, NULL, 1800.00, NULL, 'accepted', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(16, 16, 16, 'Closet moderno con correderas', NULL, NULL, 1400.00, NULL, 'pending', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(17, 17, 17, 'Puerta de seguridad', NULL, NULL, 2200.00, NULL, 'accepted', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(18, 18, 18, 'Biblioteca personalizada', NULL, NULL, 2700.00, NULL, 'pending', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(19, 19, 19, 'Reparación de mesa comedor', NULL, NULL, 350.00, NULL, 'completed', NULL, '2025-08-28 11:33:44', '2025-08-28 11:42:16'),
(20, 20, 20, 'Mueble modular para sala', NULL, NULL, 1200.00, NULL, 'pending', NULL, '2025-08-28 11:33:44', '2025-08-28 11:33:44'),
(41, 21, 21, 'Escritorio de oficina', NULL, NULL, 1600.00, NULL, 'pending', NULL, '2025-08-28 11:45:56', '2025-08-28 11:45:56'),
(42, 22, 22, 'Cama doble con cabecero', NULL, NULL, 1900.00, NULL, 'accepted', NULL, '2025-08-28 11:45:56', '2025-08-28 11:45:56'),
(43, 23, 23, 'Armario empotrado', NULL, NULL, 2100.00, NULL, 'completed', NULL, '2025-08-28 11:45:56', '2025-08-28 11:45:56'),
(44, 24, 24, 'Puerta de madera maciza', NULL, NULL, 800.00, NULL, 'pending', NULL, '2025-08-28 11:45:56', '2025-08-28 11:45:56'),
(45, 25, 25, 'Juego de sillas comedor', NULL, NULL, 1200.00, NULL, 'accepted', NULL, '2025-08-28 11:45:56', '2025-08-28 11:45:56'),
(46, 26, 26, 'Reparación de cajonera', NULL, NULL, 300.00, NULL, 'completed', NULL, '2025-08-28 11:45:56', '2025-08-28 11:45:56'),
(47, 27, 27, 'Mueble para TV', NULL, NULL, 1400.00, NULL, 'pending', NULL, '2025-08-28 11:45:56', '2025-08-28 11:45:56'),
(48, 28, 28, 'Puerta corrediza doble', NULL, NULL, 1800.00, NULL, 'accepted', NULL, '2025-08-28 11:45:56', '2025-08-28 11:45:56'),
(49, 29, 29, 'Biblioteca modular', NULL, NULL, 2500.00, NULL, 'pending', NULL, '2025-08-28 11:45:56', '2025-08-28 11:45:56'),
(50, 30, 30, 'Reparación de repisa', NULL, NULL, 350.00, NULL, 'completed', NULL, '2025-08-28 11:45:56', '2025-08-28 11:45:56'),
(51, 21, 21, 'Escritorio de oficina', NULL, NULL, 1600.00, NULL, 'pending', NULL, '2025-08-28 11:46:09', '2025-08-28 11:46:09'),
(52, 22, 22, 'Cama doble con cabecero', NULL, NULL, 1900.00, NULL, 'accepted', NULL, '2025-08-28 11:46:09', '2025-08-28 11:46:09'),
(53, 23, 23, 'Armario empotrado', NULL, NULL, 2100.00, NULL, 'completed', NULL, '2025-08-28 11:46:09', '2025-08-28 11:46:09'),
(54, 24, 24, 'Puerta de madera maciza', NULL, NULL, 800.00, NULL, 'pending', NULL, '2025-08-28 11:46:09', '2025-08-28 11:46:09'),
(55, 25, 25, 'Juego de sillas comedor', NULL, NULL, 1200.00, NULL, 'accepted', NULL, '2025-08-28 11:46:09', '2025-08-28 11:46:09'),
(56, 26, 26, 'Reparación de cajonera', NULL, NULL, 300.00, NULL, 'completed', NULL, '2025-08-28 11:46:09', '2025-08-28 11:46:09'),
(57, 27, 27, 'Mueble para TV', NULL, NULL, 1400.00, NULL, 'pending', NULL, '2025-08-28 11:46:09', '2025-08-28 11:46:09'),
(58, 28, 28, 'Puerta corrediza doble', NULL, NULL, 1800.00, NULL, 'accepted', NULL, '2025-08-28 11:46:09', '2025-08-28 11:46:09'),
(59, 29, 29, 'Biblioteca modular', NULL, NULL, 2500.00, NULL, 'pending', NULL, '2025-08-28 11:46:09', '2025-08-28 11:46:09'),
(60, 30, 30, 'Reparación de repisa', NULL, NULL, 350.00, NULL, 'completed', NULL, '2025-08-28 11:46:09', '2025-08-28 11:46:09'),
(100, 1, 1, 'Test Job', 'Wood', '100x100', 500.00, NULL, 'pending', NULL, '2025-09-09 09:53:52', '2025-09-09 09:53:52');

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

--
-- Volcado de datos para la tabla `traceability`
--

INSERT INTO `traceability` (`trace_id`, `action_type`, `performed_by`, `affected_user`, `affected_table`, `affected_id`, `old_value`, `new_value`, `authority_level`, `created_at`) VALUES
(1, 'INSERT', 1, NULL, 'requests', 10, NULL, 'Nuevo pedido', 'user', '2025-08-28 10:44:42'),
(2, 'UPDATE', 5, 1, 'users', 1, '{\"is_active\":1}', '{\"is_active\":0}', 'admin', '2025-08-28 10:44:42'),
(3, 'INSERT', 1, 2, 'requests', 1, NULL, 'Nueva solicitud', 'user', '2025-08-28 10:59:11'),
(4, 'UPDATE', 5, 1, 'users', 1, '{\"is_active\":1}', '{\"is_active\":0}', 'admin', '2025-08-28 10:59:11'),
(5, 'DELETE', 6, 3, 'reviews', 10, '{\"rating\":3}', NULL, 'admin', '2025-08-28 10:59:11'),
(6, 'INSERT', 2, 5, 'materials', 50, NULL, 'Material agregado', 'carpenter', '2025-08-28 10:59:11');

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
  `last_update` datetime DEFAULT current_timestamp(),
  `city` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password_hash`, `phone`, `role`, `is_active`, `failed_attempts`, `account_locked`, `created_at`, `last_update`, `city`) VALUES
(1, 'Cliente 1', 'cliente1@example.com', 'hash1', '3000000001', 'user', 1, 0, 0, '2025-08-28 10:27:08', '2025-08-28 10:27:08', NULL),
(2, 'Cliente 2', 'cliente2@example.com', 'hash2', '3000000002', 'user', 1, 0, 0, '2025-08-28 10:27:08', '2025-08-28 10:27:08', NULL),
(3, 'Cliente 3', 'cliente3@example.com', 'hash3', '3000000003', 'user', 1, 0, 0, '2025-08-28 10:27:08', '2025-08-28 10:27:08', NULL),
(4, 'Cliente 4', 'cliente4@example.com', 'hash4', '3000000004', 'user', 1, 0, 0, '2025-08-28 10:27:08', '2025-08-28 10:27:08', NULL),
(5, 'Carpintero 1', 'carp1@example.com', 'hashc1', '3111111111', 'carpenter', 1, 0, 0, '2025-08-28 10:27:39', '2025-08-28 10:27:39', NULL),
(6, 'Carpintero 2', 'carp2@example.com', 'hashc2', '3222222222', 'carpenter', 1, 0, 0, '2025-08-28 10:27:39', '2025-08-28 10:27:39', NULL),
(7, 'Cliente 5', 'cliente5@example.com', 'hash5', '3000000005', 'user', 1, 0, 0, '2025-08-28 10:58:34', '2025-08-28 10:58:34', NULL),
(8, 'Cliente 6', 'cliente6@example.com', 'hash6', '3000000006', 'user', 1, 0, 0, '2025-08-28 10:58:34', '2025-08-28 10:58:34', NULL),
(9, 'Cliente 7', 'cliente7@example.com', 'hash7', '3000000007', 'user', 1, 0, 0, '2025-08-28 10:58:34', '2025-08-28 10:58:34', NULL),
(10, 'Cliente 100', 'cliente100@example.com', 'hash100', '3000000100', 'user', 1, 0, 0, '2025-08-28 10:58:34', '2025-08-28 10:58:34', NULL),
(11, 'Carpintero 3', 'carp3@example.com', 'hashc3', '3333333333', 'carpenter', 1, 0, 0, '2025-08-28 10:58:34', '2025-08-28 10:58:34', NULL),
(12, 'Carpintero 4', 'carp4@example.com', 'hashc4', '3444444444', 'carpenter', 1, 0, 0, '2025-08-28 10:58:34', '2025-08-28 10:58:34', NULL),
(13, 'Administrador', 'admin@example.com', 'hashadmin', '3999999999', 'admin', 1, 0, 0, '2025-08-28 10:58:34', '2025-08-28 10:58:34', NULL),
(14, 'Usuario 1', 'user1@example.com', 'hash1', '3000000001', 'user', 1, 0, 0, '2025-08-28 11:01:43', '2025-08-28 11:01:43', NULL),
(15, 'Usuario 2', 'user2@example.com', 'hash2', '3000000002', 'user', 1, 0, 0, '2025-08-28 11:01:43', '2025-08-28 11:01:43', NULL),
(16, 'Usuario 3', 'user3@example.com', 'hash3', '3000000003', 'user', 1, 0, 0, '2025-08-28 11:01:43', '2025-08-28 11:01:43', NULL),
(17, 'Usuario 100', 'user100@example.com', 'hash100', '3000000100', 'user', 1, 0, 0, '2025-08-28 11:01:43', '2025-08-28 11:01:43', NULL),
(18, 'Usuario 18', 'user18@example.com', 'hash18', '3000000018', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(19, 'Usuario 19', 'user19@example.com', 'hash19', '3000000019', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(20, 'Usuario 20', 'user20@example.com', 'hash20', '3000000020', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(21, 'Usuario 21', 'user21@example.com', 'hash21', '3000000021', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(22, 'Usuario 22', 'user22@example.com', 'hash22', '3000000022', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(23, 'Usuario 23', 'user23@example.com', 'hash23', '3000000023', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(24, 'Usuario 24', 'user24@example.com', 'hash24', '3000000024', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(25, 'Usuario 25', 'user25@example.com', 'hash25', '3000000025', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(26, 'Usuario 26', 'user26@example.com', 'hash26', '3000000026', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(27, 'Usuario 27', 'user27@example.com', 'hash27', '3000000027', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(28, 'Usuario 28', 'user28@example.com', 'hash28', '3000000028', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(29, 'Usuario 29', 'user29@example.com', 'hash29', '3000000029', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(30, 'Usuario 30', 'user30@example.com', 'hash30', '3000000030', 'user', 1, 0, 0, '2025-08-28 11:03:50', '2025-08-28 11:03:50', NULL),
(171, 'Usuario 31', 'user31@example.com', 'hash31', '3000000031', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(172, 'Usuario 32', 'user32@example.com', 'hash32', '3000000032', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(173, 'Usuario 33', 'user33@example.com', 'hash33', '3000000033', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(174, 'Usuario 34', 'user34@example.com', 'hash34', '3000000034', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(175, 'Usuario 35', 'user35@example.com', 'hash35', '3000000035', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(176, 'Usuario 36', 'user36@example.com', 'hash36', '3000000036', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(177, 'Usuario 37', 'user37@example.com', 'hash37', '3000000037', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(178, 'Usuario 38', 'user38@example.com', 'hash38', '3000000038', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(179, 'Usuario 39', 'user39@example.com', 'hash39', '3000000039', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(180, 'Usuario 40', 'user40@example.com', 'hash40', '3000000040', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(181, 'Usuario 41', 'user41@example.com', 'hash41', '3000000041', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(182, 'Usuario 42', 'user42@example.com', 'hash42', '3000000042', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(183, 'Usuario 43', 'user43@example.com', 'hash43', '3000000043', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(184, 'Usuario 44', 'user44@example.com', 'hash44', '3000000044', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(185, 'Usuario 45', 'user45@example.com', 'hash45', '3000000045', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(186, 'Usuario 46', 'user46@example.com', 'hash46', '3000000046', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(187, 'Usuario 47', 'user47@example.com', 'hash47', '3000000047', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(188, 'Usuario 48', 'user48@example.com', 'hash48', '3000000048', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(189, 'Usuario 49', 'user49@example.com', 'hash49', '3000000049', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(190, 'Usuario 50', 'user50@example.com', 'hash50', '3000000050', 'user', 1, 0, 0, '2025-08-28 11:08:43', '2025-08-28 11:08:43', NULL),
(191, 'Usuario 51', 'user51@example.com', 'hash51', '3000000051', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(192, 'Usuario 52', 'user52@example.com', 'hash52', '3000000052', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(193, 'Usuario 53', 'user53@example.com', 'hash53', '3000000053', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(194, 'Usuario 54', 'user54@example.com', 'hash54', '3000000054', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(195, 'Usuario 55', 'user55@example.com', 'hash55', '3000000055', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(196, 'Usuario 56', 'user56@example.com', 'hash56', '3000000056', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(197, 'Usuario 57', 'user57@example.com', 'hash57', '3000000057', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(198, 'Usuario 58', 'user58@example.com', 'hash58', '3000000058', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(199, 'Usuario 59', 'user59@example.com', 'hash59', '3000000059', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(200, 'Usuario 60', 'user60@example.com', 'hash60', '3000000060', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(201, 'Usuario 61', 'user61@example.com', 'hash61', '3000000061', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(202, 'Usuario 62', 'user62@example.com', 'hash62', '3000000062', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(203, 'Usuario 63', 'user63@example.com', 'hash63', '3000000063', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(204, 'Usuario 64', 'user64@example.com', 'hash64', '3000000064', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(205, 'Usuario 65', 'user65@example.com', 'hash65', '3000000065', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(206, 'Usuario 66', 'user66@example.com', 'hash66', '3000000066', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(207, 'Usuario 67', 'user67@example.com', 'hash67', '3000000067', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(208, 'Usuario 68', 'user68@example.com', 'hash68', '3000000068', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(209, 'Usuario 69', 'user69@example.com', 'hash69', '3000000069', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(210, 'Usuario 70', 'user70@example.com', 'hash70', '3000000070', 'user', 1, 0, 0, '2025-08-28 11:10:09', '2025-08-28 11:10:09', NULL),
(211, 'Usuario 71', 'user71@example.com', 'hash71', '3000000071', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(212, 'Usuario 72', 'user72@example.com', 'hash72', '3000000072', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(213, 'Usuario 73', 'user73@example.com', 'hash73', '3000000073', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(214, 'Usuario 74', 'user74@example.com', 'hash74', '3000000074', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(215, 'Usuario 75', 'user75@example.com', 'hash75', '3000000075', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(216, 'Usuario 76', 'user76@example.com', 'hash76', '3000000076', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(217, 'Usuario 77', 'user77@example.com', 'hash77', '3000000077', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(218, 'Usuario 78', 'user78@example.com', 'hash78', '3000000078', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(219, 'Usuario 79', 'user79@example.com', 'hash79', '3000000079', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(220, 'Usuario 80', 'user80@example.com', 'hash80', '3000000080', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(221, 'Usuario 81', 'user81@example.com', 'hash81', '3000000081', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(222, 'Usuario 82', 'user82@example.com', 'hash82', '3000000082', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(223, 'Usuario 83', 'user83@example.com', 'hash83', '3000000083', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(224, 'Usuario 84', 'user84@example.com', 'hash84', '3000000084', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(225, 'Usuario 85', 'user85@example.com', 'hash85', '3000000085', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(226, 'Usuario 86', 'user86@example.com', 'hash86', '3000000086', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(227, 'Usuario 87', 'user87@example.com', 'hash87', '3000000087', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(228, 'Usuario 88', 'user88@example.com', 'hash88', '3000000088', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(229, 'Usuario 89', 'user89@example.com', 'hash89', '3000000089', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(230, 'Usuario 90', 'user90@example.com', 'hash90', '3000000090', 'user', 1, 0, 0, '2025-08-28 11:10:54', '2025-08-28 11:10:54', NULL),
(241, 'Usuario 91', 'user91@example.com', 'hash91', '3000000091', 'user', 1, 0, 0, '2025-08-28 11:12:56', '2025-08-28 11:12:56', NULL),
(242, 'Usuario 92', 'user92@example.com', 'hash92', '3000000092', 'user', 1, 0, 0, '2025-08-28 11:12:56', '2025-08-28 11:12:56', NULL),
(243, 'Usuario 93', 'user93@example.com', 'hash93', '3000000093', 'user', 1, 0, 0, '2025-08-28 11:12:56', '2025-08-28 11:12:56', NULL),
(244, 'Usuario 94', 'user94@example.com', 'hash94', '3000000094', 'user', 1, 0, 0, '2025-08-28 11:12:56', '2025-08-28 11:12:56', NULL),
(245, 'Usuario 95', 'user95@example.com', 'hash95', '3000000095', 'user', 1, 0, 0, '2025-08-28 11:12:56', '2025-08-28 11:12:56', NULL),
(246, 'Usuario 96', 'user96@example.com', 'hash96', '3000000096', 'user', 1, 0, 0, '2025-08-28 11:12:56', '2025-08-28 11:12:56', NULL),
(247, 'Usuario 97', 'user97@example.com', 'hash97', '3000000097', 'user', 1, 0, 0, '2025-08-28 11:12:56', '2025-08-28 11:12:56', NULL),
(248, 'Usuario 98', 'user98@example.com', 'hash98', '3000000098', 'user', 1, 0, 0, '2025-08-28 11:12:56', '2025-08-28 11:12:56', NULL),
(249, 'Usuario 99', 'user99@example.com', 'hash99', '3000000099', 'user', 1, 0, 0, '2025-08-28 11:12:56', '2025-08-28 11:12:56', NULL),
(250, 'Usuario 101', 'user101@example.com', 'hash101', '3000000101', 'user', 1, 0, 0, '2025-08-28 11:12:56', '2025-08-28 11:12:56', NULL),
(251, 'Administrador General', 'admin@lfcarpinter.com', '$2y$10$Qit2QeMt7PCYhSHoxqgVdeJ8sMCGeTclV0G5UKzH0WucLL4oQO48K', '3000000000', 'admin', 1, 0, 0, '2025-11-12 08:07:13', '2025-11-12 08:07:13', 'Medellín'),
(253, 'Administrador LF', 'admin@lf.com', '$2y$10$yS8bH3f2gGtNd.Aekf.HoOGNhyTvvOUkXycM6mHsxAjgAgfimLQDe', '3000000000', 'admin', 1, 0, 0, '2025-11-12 09:19:14', '2025-11-12 09:19:14', 'Medellín'),
(254, 'Emmanuel Hincapie Marin', 'Emma@gmail.com', '$2y$10$pJh8QxZwnFMwYPSsfY891.mZ18EmfggwiwTKJL2UCEIBIe7m1M3JG', '3118020103', 'user', 1, 0, 0, '2025-11-12 09:33:45', '2025-11-12 09:33:45', 'Medellin'),
(255, 'Alejitauwuyt224', 'puta@gmail.com', '$2y$10$yR7Mg8VyFWRDKlpWSgpZjOZO.FXPz8DCCIcMmQejtEgJ5.ImpbfD6', '111', 'user', 1, 0, 0, '2025-11-12 11:14:21', '2025-11-12 11:14:21', 'medellin'),
(259, 'Juan Pérez (Carpintero de Prueba)', 'carpintero@test.com', '$2y$10$/0BfsEEN./pmsksgnBpENOQNmhW0YAMXZJT6miuNhe8n4k8T9ixoe', '555-1234-5678', 'carpenter', 1, 0, 0, '2025-12-02 17:20:23', '2025-12-02 17:20:23', 'Ciudad de México'),
(260, 'María González (Cliente de Prueba)', 'cliente@test.com', '$2y$10$/0BfsEEN./pmsksgnBpENOQNmhW0YAMXZJT6miuNhe8n4k8T9ixoe', '555-9876-5432', 'user', 1, 0, 0, '2025-12-02 17:20:23', '2025-12-02 17:20:23', 'Guadalajara'),
(261, 'aleja', 'baba@gmail.com', '$2y$10$A/nYMtCL3O1FjeuGro8CaOWk0w1t.NlQM12xR./jnVDKjbW7PVyeK', NULL, 'carpenter', 1, 0, 0, '2025-12-02 17:39:19', '2025-12-02 17:39:19', NULL),
(262, 'Emmanuel', 'emmanuelhincm7@gmail.com', '$2y$10$3PCx1EgWDGYGAaPTL7Uczu9HlO4N1/lCc9mjHsQSazyFca1sk4cFq', '3118020103', 'user', 1, 0, 0, '2025-12-02 20:21:08', '2025-12-02 20:21:08', 'Medellin');

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
-- Volcado de datos para la tabla `user_preferences`
--

INSERT INTO `user_preferences` (`preference_id`, `user_id`, `preferred_materials`, `preferred_styles`, `notifications_enabled`, `created_at`, `last_update`) VALUES
(1, 1, 'Madera, Pino', 'Rústico', 1, '2025-08-28 10:45:12', '2025-08-28 10:45:12'),
(2, 2, 'Metal, Vidrio', 'Moderno', 0, '2025-08-28 10:45:12', '2025-08-28 10:45:12'),
(3, 3, 'Madera', 'Minimalista', 1, '2025-08-28 10:45:12', '2025-08-28 10:45:12');

--
-- Disparadores `user_preferences`
--
DELIMITER $$
CREATE TRIGGER `update_user_preferences_last_update` BEFORE UPDATE ON `user_preferences` FOR EACH ROW BEGIN
    SET NEW.last_update = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_pedidos_completados_carpintero`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_pedidos_completados_carpintero` (
`request_id` int(11)
,`carpenter_id` int(11)
,`user_id` int(11)
,`job_type` varchar(100)
,`budget` decimal(10,2)
,`created_at` datetime
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_pedidos_personalizados_usuario`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_pedidos_personalizados_usuario` (
`request_id` int(11)
,`user_id` int(11)
,`carpenter_user_id` int(11)
,`project_description` text
,`status` enum('pending','accepted','rejected','completed')
,`created_at` datetime
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_pedidos_usuario`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_pedidos_usuario` (
`request_id` int(11)
,`user_id` int(11)
,`carpenter_id` int(11)
,`job_type` varchar(100)
,`budget` decimal(10,2)
,`status` enum('pending','accepted','rejected','completed')
,`created_at` datetime
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_por_carpintero`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_por_carpintero` (
`project_id` int(11)
,`title` varchar(255)
,`description` text
,`price` decimal(12,2)
,`created_at` datetime
,`carpenter_id` int(11)
);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_activity_logs_action` (`action_type`);

--
-- Indices de la tabla `carpenters`
--
ALTER TABLE `carpenters`
  ADD PRIMARY KEY (`carpenter_id`),
  ADD KEY `idx_carpenters_specialties` (`specialties`);

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
  ADD KEY `fk_materials_request` (`request_id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `portafolio`
--
ALTER TABLE `portafolio`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `carpenter_user_id` (`carpenter_user_id`);

--
-- Indices de la tabla `portafolios`
--
ALTER TABLE `portafolios`
  ADD PRIMARY KEY (`id_portafolio`),
  ADD KEY `carpenter_id` (`carpenter_id`);

--
-- Indices de la tabla `project_comments`
--
ALTER TABLE `project_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `project_requests`
--
ALTER TABLE `project_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `carpenter_user_id` (`carpenter_user_id`);

--
-- Indices de la tabla `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `carpenter_id` (`carpenter_id`),
  ADD KEY `idx_requests_status` (`status`),
  ADD KEY `idx_requests_created_at` (`created_at`);

--
-- Indices de la tabla `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `carpenter_id` (`carpenter_id`),
  ADD KEY `idx_reviews_rating` (`rating`);

--
-- Indices de la tabla `traceability`
--
ALTER TABLE `traceability`
  ADD PRIMARY KEY (`trace_id`),
  ADD KEY `fk_traceability_performed_by` (`performed_by`),
  ADD KEY `fk_traceability_affected_user` (`affected_user`),
  ADD KEY `idx_traceability_action` (`action_type`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `carpenters`
--
ALTER TABLE `carpenters`
  MODIFY `carpenter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268;

--
-- AUTO_INCREMENT de la tabla `certifications`
--
ALTER TABLE `certifications`
  MODIFY `certification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_logins`
--
ALTER TABLE `failed_logins`
  MODIFY `fail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `materials`
--
ALTER TABLE `materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `portafolio`
--
ALTER TABLE `portafolio`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `portafolios`
--
ALTER TABLE `portafolios`
  MODIFY `id_portafolio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `project_comments`
--
ALTER TABLE `project_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `project_requests`
--
ALTER TABLE `project_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `traceability`
--
ALTER TABLE `traceability`
  MODIFY `trace_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=265;

--
-- AUTO_INCREMENT de la tabla `user_behavior`
--
ALTER TABLE `user_behavior`
  MODIFY `behavior_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `preference_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_pedidos_completados_carpintero`
--
DROP TABLE IF EXISTS `vista_pedidos_completados_carpintero`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_pedidos_completados_carpintero`  AS SELECT `r`.`request_id` AS `request_id`, `r`.`carpenter_id` AS `carpenter_id`, `r`.`user_id` AS `user_id`, `r`.`job_type` AS `job_type`, `r`.`budget` AS `budget`, `r`.`created_at` AS `created_at` FROM `requests` AS `r` WHERE `r`.`status` = 'completed' ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_pedidos_personalizados_usuario`
--
DROP TABLE IF EXISTS `vista_pedidos_personalizados_usuario`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_pedidos_personalizados_usuario`  AS SELECT `pr`.`request_id` AS `request_id`, `pr`.`user_id` AS `user_id`, `pr`.`carpenter_user_id` AS `carpenter_user_id`, `pr`.`project_description` AS `project_description`, `pr`.`status` AS `status`, `pr`.`created_at` AS `created_at` FROM `project_requests` AS `pr` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_pedidos_usuario`
--
DROP TABLE IF EXISTS `vista_pedidos_usuario`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_pedidos_usuario`  AS SELECT `r`.`request_id` AS `request_id`, `r`.`user_id` AS `user_id`, `r`.`carpenter_id` AS `carpenter_id`, `r`.`job_type` AS `job_type`, `r`.`budget` AS `budget`, `r`.`status` AS `status`, `r`.`created_at` AS `created_at` FROM `requests` AS `r` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_por_carpintero`
--
DROP TABLE IF EXISTS `vista_productos_por_carpintero`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_por_carpintero`  AS SELECT `p`.`project_id` AS `project_id`, `p`.`title` AS `title`, `p`.`description` AS `description`, `p`.`price` AS `price`, `p`.`created_at` AS `created_at`, `p`.`carpenter_user_id` AS `carpenter_id` FROM `portafolio` AS `p` ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `certifications`
--
ALTER TABLE `certifications`
  ADD CONSTRAINT `certifications_ibfk_1` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`),
  ADD CONSTRAINT `fk_certifications_carpenter` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);

--
-- Filtros para la tabla `failed_logins`
--
ALTER TABLE `failed_logins`
  ADD CONSTRAINT `failed_logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_failed_logins_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `fk_materials_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`),
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`);

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `portafolios`
--
ALTER TABLE `portafolios`
  ADD CONSTRAINT `fk_portafolios_carpenter` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`),
  ADD CONSTRAINT `portafolios_ibfk_1` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);

--
-- Filtros para la tabla `project_comments`
--
ALTER TABLE `project_comments`
  ADD CONSTRAINT `project_comments_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `portafolio` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `project_requests`
--
ALTER TABLE `project_requests`
  ADD CONSTRAINT `project_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_requests_ibfk_2` FOREIGN KEY (`carpenter_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `fk_requests_carpenter` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`),
  ADD CONSTRAINT `fk_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);

--
-- Filtros para la tabla `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_carpenter` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`),
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`carpenter_id`) REFERENCES `carpenters` (`carpenter_id`);

--
-- Filtros para la tabla `traceability`
--
ALTER TABLE `traceability`
  ADD CONSTRAINT `fk_traceability_affected_user` FOREIGN KEY (`affected_user`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_traceability_performed_by` FOREIGN KEY (`performed_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `traceability_ibfk_1` FOREIGN KEY (`performed_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `traceability_ibfk_2` FOREIGN KEY (`affected_user`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `user_behavior`
--
ALTER TABLE `user_behavior`
  ADD CONSTRAINT `fk_user_behavior_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_behavior_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Filtros para la tabla `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `fk_user_preferences_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
