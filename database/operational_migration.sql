USE saas_asset_manager;

-- Ensure organizations exist
CREATE TABLE IF NOT EXISTS organizations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ensure users table is correct
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'org_admin', 'member') NOT NULL DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
);

-- Refine Assets Table
CREATE TABLE IF NOT EXISTS assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    category VARCHAR(100) NULL,
    description TEXT,
    location VARCHAR(150) NULL,
    quantity INT NOT NULL DEFAULT 0,
    condition_note TEXT,
    status ENUM('active', 'maintenance', 'lost', 'in_use') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
);

-- Refine Borrow Requests with 'returning' status
CREATE TABLE IF NOT EXISTS borrow_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    user_id INT NOT NULL,
    asset_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'returning', 'returned') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
);

-- Ensure Return Logs
CREATE TABLE IF NOT EXISTS return_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    borrow_request_id INT NOT NULL,
    return_date DATE NOT NULL,
    condition_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (borrow_request_id) REFERENCES borrow_requests(id) ON DELETE CASCADE
);

-- Ensure Activity Logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Seed Initial Data if empty
INSERT IGNORE INTO organizations (id, name) VALUES (1, 'Main University Campus');

INSERT IGNORE INTO users (organization_id, name, email, password, role) VALUES 
(1, 'Admin User', 'admin@ukm.com', '$2y$10$p84DAX.ublfADSFvZsKbzOx0s2Rqvfqd.gh2F8OpsqL8ZYQSvR1Em', 'org_admin'),
(1, 'Member User', 'member@ukm.com', '$2y$10$p84DAX.ublfADSFvZsKbzOx0s2Rqvfqd.gh2F8OpsqL8ZYQSvR1Em', 'member');
