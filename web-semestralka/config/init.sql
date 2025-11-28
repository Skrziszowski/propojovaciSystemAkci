CREATE DATABASE IF NOT EXISTS propojovaci_system_akci
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_czech_ci;

USE propojovaci_system_akci;

CREATE TABLE roles(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);


CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role INT(1) NOT NULL,
    information TEXT,
    photoPath VARCHAR(255),
    FOREIGN KEY (role) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE category(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE message(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT
);

CREATE TABLE events(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    capacity INT,
    description TEXT,
    date DATETIME NOT NULL,
    category_id INT,
    photoPath VARCHAR(255),
    -- schvlaneni majitelem prostoru
    place_id INT,
    -- schvaleni administratorem - pokud False
    approved ENUM('čeká se na schválení','schváleno','zamítnuto') DEFAULT 'čeká se na schválení',
    message TEXT,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category(id),
    FOREIGN KEY (place_id) REFERENCES users(id)
);


-- =========================
-- Vložení dat pro chod
-- =========================
INSERT INTO roles(name) VALUES ('Superadmin'), ('Admin'), ('Tvůrce'), ('Majitel prostoru')  ;
INSERT INTO category (name) VALUES  ('Koncert'), ('Workshop'), ('Výstava'), ('Přednáška'), ('Festival'), ('Jiné');
INSERT INTO users (id, username, email, password, role, information, photoPath)
VALUES (1,'Administrátor','admin','$2y$10$1bgNZrOT7LfuoxaWfl7/nOXgjhj21riPM9aYDSeIpq/TK.mF0pI7m',1,NULL,NULL);
-- =========================
-- Vložení demodat
-- =========================
