-- Agregar columna is_active a la tabla users si no existe
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1;

-- Agregar columna is_active a la tabla carpenters si no existe
ALTER TABLE carpenters ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1;
