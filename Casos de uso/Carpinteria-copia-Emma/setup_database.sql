-- Script de verificación y creación de tablas para el sistema de carpintería
-- Base de datos: lf

USE lf;

-- Tabla de usuarios (debe existir ya)
-- CREATE TABLE IF NOT EXISTS users (
--     user_id INT AUTO_INCREMENT PRIMARY KEY,
--     full_name VARCHAR(255) NOT NULL,
--     email VARCHAR(255) UNIQUE NOT NULL,
--     password_hash VARCHAR(255) NOT NULL,
--     phone VARCHAR(20),
--     city VARCHAR(100),
--     role ENUM('user', 'carpenter', 'admin') DEFAULT 'user',
--     created_at DATETIME DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de carpinteros (debe existir ya)
-- CREATE TABLE IF NOT EXISTS carpenters (
--     carpenter_id INT AUTO_INCREMENT PRIMARY KEY,
--     carpenter_name VARCHAR(255) NOT NULL,
--     specialties TEXT,
--     experience_years INT DEFAULT 0,
--     description TEXT,
--     is_verified TINYINT(1) DEFAULT 0,
--     approved TINYINT(1) DEFAULT 0,
--     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
--     cv_file VARCHAR(255)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de portafolio (proyectos de carpinteros)
CREATE TABLE IF NOT EXISTS portafolio (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    carpenter_user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    price DECIMAL(12,2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (carpenter_user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de comentarios en proyectos
CREATE TABLE IF NOT EXISTS project_comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    comment TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES portafolio(project_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de solicitudes de proyectos personalizados
CREATE TABLE IF NOT EXISTS project_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    carpenter_user_id INT NOT NULL,
    project_description TEXT NOT NULL,
    contact_info VARCHAR(255),
    status ENUM('pending', 'accepted', 'rejected', 'completed') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (carpenter_user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Verificar tablas creadas
SHOW TABLES;

-- Verificar estructura de portafolio
DESCRIBE portafolio;

-- Verificar estructura de project_comments
DESCRIBE project_comments;

-- Verificar estructura de notifications
DESCRIBE notifications;

-- Tabla de reseteo de contraseñas
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SHOW TABLES;
