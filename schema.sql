-- Crear base de datos
CREATE DATABASE IF NOT EXISTS biblioteca_db;
USE biblioteca_db;

-- Crear tabla books
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    year INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar algunos datos de ejemplo (opcional)
INSERT INTO books (title, author, isbn, year, quantity) VALUES 
('Cien años de soledad', 'Gabriel García Márquez', '978-84-376-0494-7', 1967, 5),
('Don Quijote de la Mancha', 'Miguel de Cervantes', '978-84-376-0495-4', 1605, 3),
('La Casa de los Espíritus', 'Isabel Allende', '978-84-376-0496-1', 1982, 2);