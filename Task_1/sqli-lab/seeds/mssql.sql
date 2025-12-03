-- MSSQL Database Schema and Seed Data

-- Create database if not exists
IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = 'sqli_db')
BEGIN
    CREATE DATABASE sqli_db;
END
GO

USE sqli_db;
GO

-- Users table
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='users' AND xtype='U')
CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    created_at DATETIME DEFAULT GETDATE()
);
GO

-- Products table
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='products' AND xtype='U')
CREATE TABLE products (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100),
    created_at DATETIME DEFAULT GETDATE()
);
GO

-- Reports table
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='reports' AND xtype='U')
CREATE TABLE reports (
    id INT IDENTITY(1,1) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT,
    status VARCHAR(50) DEFAULT 'pending',
    created_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
GO

-- Insert test users
INSERT INTO users (username, password, email, role) VALUES
('admin', 'admin123', 'admin@sqli-lab.local', 'admin'),
('john_doe', 'password123', 'john@sqli-lab.local', 'user'),
('jane_smith', 'password123', 'jane@sqli-lab.local', 'user'),
('bob_wilson', 'password123', 'bob@sqli-lab.local', 'user'),
('alice_johnson', 'password123', 'alice@sqli-lab.local', 'moderator');
GO

-- Insert test products
INSERT INTO products (name, description, price, category) VALUES
('Laptop Pro 15', 'High-performance laptop with 16GB RAM', 1299.99, 'Electronics'),
('Wireless Mouse', 'Ergonomic wireless mouse with long battery life', 29.99, 'Accessories'),
('USB-C Hub', '7-in-1 USB-C hub with HDMI and Ethernet', 49.99, 'Accessories'),
('Mechanical Keyboard', 'RGB mechanical keyboard with blue switches', 89.99, 'Accessories'),
('4K Monitor', '27-inch 4K UHD monitor with HDR support', 399.99, 'Electronics'),
('Webcam HD', '1080p webcam with built-in microphone', 79.99, 'Electronics'),
('Desk Lamp', 'LED desk lamp with adjustable brightness', 34.99, 'Office'),
('Office Chair', 'Ergonomic office chair with lumbar support', 249.99, 'Office');
GO

-- Insert test reports
INSERT INTO reports (title, content, user_id, status) VALUES
('Bug in login page', 'The login page shows an error when special characters are used', 2, 'pending'),
('Feature request: Dark mode', 'Please add a dark mode option to the application', 3, 'open'),
('Product search not working', 'Search returns no results for certain keywords', 4, 'in-progress'),
('Security concern', 'Found potential SQL injection vulnerability in search', 5, 'resolved'),
('Performance issue', 'Page loads slowly when many products are displayed', 2, 'open');
GO

-- Enable xp_cmdshell for RCE demonstrations
EXEC sp_configure 'show advanced options', 1;
RECONFIGURE;
EXEC sp_configure 'xp_cmdshell', 1;
RECONFIGURE;
GO
