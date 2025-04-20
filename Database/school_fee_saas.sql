CREATE DATABASE school_fee_saas;
USE school_fee_saas;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'parent'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, role)
VALUES (
    'Admin',
    'admin@gmail.com',
    'admin123',
    'admin'
);
INSERT INTO users (name, email, password, role)
VALUES (
    'Parent Dummy',
    'parent@gmail.com',
    '123',
    'parent'
);
CREATE TABLE fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    reference_no VARCHAR(100) UNIQUE,
    due_date DATE,
    status ENUM('Pending', 'Processing', 'Paid') DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fee_id INT,
    payment_method VARCHAR(50),
    reference_no VARCHAR(100) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fee_id) REFERENCES fees(id)
);
CREATE TABLE reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT,
    is_read BOOLEAN DEFAULT 0,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fee_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (fee_id) REFERENCES fees(id)
);
