-- Script para actualizar la tabla project_requests con las columnas necesarias
-- Este script agrega columnas solo si no existen para evitar errores

USE lf;

-- Agregar columna title si no existe
SET @exist = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'lf' AND TABLE_NAME = 'project_requests' AND COLUMN_NAME = 'title');
SET @sql = IF(@exist = 0, 
              'ALTER TABLE project_requests ADD COLUMN title VARCHAR(255) NOT NULL AFTER carpenter_user_id',
              'SELECT ''Column title already exists'' AS log');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Renombrar project_description a description si es necesario
SET @exist = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'lf' AND TABLE_NAME = 'project_requests' AND COLUMN_NAME = 'description');
SET @sql = IF(@exist = 0, 
              'ALTER TABLE project_requests CHANGE project_description description TEXT NOT NULL',
              'SELECT ''Column description already exists'' AS log');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna budget
SET @exist = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'lf' AND TABLE_NAME = 'project_requests' AND COLUMN_NAME = 'budget');
SET @sql = IF(@exist = 0, 
              'ALTER TABLE project_requests ADD COLUMN budget DECIMAL(10,2) NULL AFTER description',
              'SELECT ''Column budget already exists'' AS log');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna deadline
SET @exist = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'lf' AND TABLE_NAME = 'project_requests' AND COLUMN_NAME = 'deadline');
SET @sql = IF(@exist = 0, 
              'ALTER TABLE project_requests ADD COLUMN deadline DATE NULL AFTER budget',
              'SELECT ''Column deadline already exists'' AS log');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna dimensions
SET @exist = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'lf' AND TABLE_NAME = 'project_requests' AND COLUMN_NAME = 'dimensions');
SET @sql = IF(@exist = 0, 
              'ALTER TABLE project_requests ADD COLUMN dimensions VARCHAR(255) NULL AFTER deadline',
              'SELECT ''Column dimensions already exists'' AS log');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna materials
SET @exist = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'lf' AND TABLE_NAME = 'project_requests' AND COLUMN_NAME = 'materials');
SET @sql = IF(@exist = 0, 
              'ALTER TABLE project_requests ADD COLUMN materials TEXT NULL AFTER dimensions',
              'SELECT ''Column materials already exists'' AS log');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna image_path
SET @exist = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'lf' AND TABLE_NAME = 'project_requests' AND COLUMN_NAME = 'image_path');
SET @sql = IF(@exist = 0, 
              'ALTER TABLE project_requests ADD COLUMN image_path VARCHAR(500) NULL AFTER materials',
              'SELECT ''Column image_path already exists'' AS log');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eliminar columna contact_info si existe (ya no es necesaria)
SET @exist = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_SCHEMA = 'lf' AND TABLE_NAME = 'project_requests' AND COLUMN_NAME = 'contact_info');
SET @sql = IF(@exist > 0, 
              'ALTER TABLE project_requests DROP COLUMN contact_info',
              'SELECT ''Column contact_info does not exist'' AS log');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Tabla project_requests actualizada correctamente' AS resultado;

-- Mostrar estructura final
DESCRIBE project_requests;
