-- Create the database
CREATE DATABASE IF NOT EXISTS bank_wifi_system;
USE bank_wifi_system;

-- Table: users (Bank Employees)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    id_number VARCHAR(20) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    department VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: admins
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    employee_id VARCHAR(50) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: wifi_requests
-- Fix ENUM to include 'Replied'
CREATE TABLE IF NOT EXISTS wifi_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    request_message TEXT NOT NULL,
    reply_message TEXT DEFAULT NULL,
    status ENUM('Pending', 'Approved', 'Rejected', 'Replied') DEFAULT 'Pending',
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reply_date TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Table: wifi_replies
CREATE TABLE IF NOT EXISTS wifi_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    admin_id INT,
    ssid VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    reply_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES wifi_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
);

-- Table: tickets (support tickets)
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    reply TEXT DEFAULT NULL,
    status ENUM('Open', 'Replied', 'Closed') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    replied_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: ticket_replies
CREATE TABLE IF NOT EXISTS ticket_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    admin_id INT,
    reply_message TEXT NOT NULL,
    reply_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
);

-- Sample data for users
INSERT INTO users (full_name, email, id_number, phone, department, position, password)
VALUES
('Alice Wanjiku', 'alice@example.com', '12345678', '0712345678', 'Finance', 'Accountant', SHA2('password123', 256)),
('Brian Otieno', 'brian@example.com', '87654321', '0798765432', 'IT', 'Support Engineer', SHA2('securepass', 256));

-- Sample data for admins
INSERT INTO admins (full_name, email, employee_id, phone, password)
VALUES
('Admin One', 'admin1@bank.com', 'EMP001', '0700111222', SHA2('adminpass1', 256)),
('Admin Two', 'admin2@bank.com', 'EMP002', '0700333444', SHA2('adminpass2', 256));

-- Sample data for wifi_requests
INSERT INTO wifi_requests (user_id, request_message, status)
VALUES
(1, 'Requesting WiFi access for Finance office.', 'Pending'),
(2, 'Need guest WiFi credentials for IT visitors.', 'Approved');

-- Sample data for wifi_replies
INSERT INTO wifi_replies (request_id, admin_id, ssid, password)
VALUES
(2, 1, 'BankGuestWiFi', 'guest@2024');

-- Sample data for tickets
INSERT INTO tickets (user_id, subject, message, status)
VALUES
(1, 'WiFi Not Working', 'Unable to connect to WiFi since morning.', 'Replied'),
(2, 'Need Permanent Credentials', 'Can I have permanent access credentials?', 'Open');

-- Sample data for ticket_replies
INSERT INTO ticket_replies (ticket_id, admin_id, reply_message)
VALUES
(1, 2, 'Please restart your router and try again.');
