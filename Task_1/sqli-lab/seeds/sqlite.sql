-- SQLite Database Schema and Seed Data

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    email TEXT NOT NULL,
    role TEXT DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    category TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reports table
CREATE TABLE IF NOT EXISTS reports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    user_id INTEGER,
    status TEXT DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert test users
INSERT INTO users (username, password, email, role) VALUES
('admin', 'admin123', 'admin@sqli-lab.local', 'admin'),
('john_doe', 'password123', 'john@sqli-lab.local', 'user'),
('jane_smith', 'password123', 'jane@sqli-lab.local', 'user'),
('bob_wilson', 'password123', 'bob@sqli-lab.local', 'user'),
('alice_johnson', 'password123', 'alice@sqli-lab.local', 'moderator');

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

-- Insert test reports
INSERT INTO reports (title, content, user_id, status) VALUES
('Bug in login page', 'The login page shows an error when special characters are used', 2, 'pending'),
('Feature request: Dark mode', 'Please add a dark mode option to the application', 3, 'open'),
('Product search not working', 'Search returns no results for certain keywords', 4, 'in-progress'),
('Security concern', 'Found potential SQL injection vulnerability in search', 5, 'resolved'),
('Performance issue', 'Page loads slowly when many products are displayed', 2, 'open');
