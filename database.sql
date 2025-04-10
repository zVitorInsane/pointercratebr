CREATE DATABASE s7677_pointercreatebr;
USE s7677_pointercreatebr;

-- Tabela de usuários
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    gender ENUM('Masculino', 'Feminino', 'Outro'),
    bio TEXT,
    profile_pic VARCHAR(255),
    state VARCHAR(50),
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de conquistas
CREATE TABLE achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    level_name VARCHAR(100),
    level_id INT, -- ID da aredl.net
    points INT,
    hardest_rank INT, -- posição do hardest
    video VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);