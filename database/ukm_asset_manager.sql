CREATE DATABASE IF NOT EXISTS ukm_asset_manager;
USE ukm_asset_manager;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'member') NOT NULL DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Assets Table
CREATE TABLE IF NOT EXISTS assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    quantity INT NOT NULL DEFAULT 0,
    condition_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Borrow Requests Table
CREATE TABLE IF NOT EXISTS borrow_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    asset_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'returned') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
);

-- Return Logs Table
CREATE TABLE IF NOT EXISTS return_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    borrow_request_id INT NOT NULL,
    return_date DATE NOT NULL,
    condition_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (borrow_request_id) REFERENCES borrow_requests(id) ON DELETE CASCADE
);

-- Seed Data (Password: 123456)
-- Hash for '123456': $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@ukm.com', '$2y$10$p84DAX.ublfADSFvZsKbzOx0s2Rqvfqd.gh2F8OpsqL8ZYQSvR1Em', 'admin'),
('Member User', 'member@ukm.com', '$2y$10$p84DAX.ublfADSFvZsKbzOx0s2Rqvfqd.gh2F8OpsqL8ZYQSvR1Em', 'member');

INSERT INTO assets (name, quantity, condition_note) VALUES 
('Projector Sony', 2, 'Good condition'),
('Speaker JBL', 1, 'Minor scratch on side'),
('Camera Canon DSLR', 3, 'Lens cap missing on one');
