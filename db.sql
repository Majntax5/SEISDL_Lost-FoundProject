-- RPSU Lost And Found database schema
CREATE DATABASE IF NOT EXISTS rpsu_lostfound DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rpsu_lostfound;

-- users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  phone VARCHAR(50) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- posts table
CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  image_path VARCHAR(255),
  location VARCHAR(255),
  found_date DATE DEFAULT NULL,
  status ENUM('lost','found','claimed') DEFAULT 'lost',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- post messages between finder and owner (simple messaging tied to a post)
CREATE TABLE IF NOT EXISTS post_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  sender_id INT NOT NULL,
  receiver_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- chat rooms
CREATE TABLE IF NOT EXISTS chat_rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- chat messages
CREATE TABLE IF NOT EXISTS chat_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  is_bot TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed: admin user (email admin@rpsu.edu.bd, password 'admin123' — change after install)
INSERT INTO users (name, email, phone, password_hash, is_admin) VALUES
('admin', 'admin@rpsu.edu.bd', '01800000000', SHA2('admin123', 256), 1)
ON DUPLICATE KEY UPDATE email=email;

-- Seed: default chat rooms
INSERT INTO chat_rooms (name) VALUES ('General'), ('Found Items'), ('Lost Items') 
ON DUPLICATE KEY UPDATE name=name;

UPDATE posts
SET image_path = CONCAT('/' , image_path)
WHERE image_path IS NOT NULL
  AND image_path != ''
  AND image_path NOT LIKE '/%';