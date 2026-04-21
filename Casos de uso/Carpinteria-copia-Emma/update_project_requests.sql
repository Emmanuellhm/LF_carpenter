-- Script SQL para actualizar la base de datos con soporte completo para solicitudes de proyectos
-- Base de datos: lf

USE lf;

-- Primero verificamos si la tabla existe y la eliminamos si es necesaria (CUIDADO: esto borrará datos existentes)
-- Comenta las siguientes líneas si quieres preservar datos existentes
-- DROP TABLE IF EXISTS project_requests;

-- Crear tabla de solicitudes de proyectos personalizados con todos los campos necesarios
CREATE TABLE IF NOT EXISTS project_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    carpenter_user_id INT NOT NULL,
    title VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    project_description TEXT DEFAULT NULL,
    contact_info VARCHAR(255) DEFAULT NULL,
    budget DECIMAL(12,2) DEFAULT NULL,
    deadline DATE DEFAULT NULL,
    dimensions VARCHAR(255) DEFAULT NULL,
    materials TEXT DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'completed') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (carpenter_user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Verificar la estructura de la tabla
DESCRIBE project_requests;

-- Verificar datos existentes (si los hay)
SELECT COUNT(*) as total_requests FROM project_requests;
