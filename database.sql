-- Database creation statements removed for compatibility with shared hosting.
-- Clean start (fixes #1932 errors)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Addresses Table
CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_line TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    pincode VARCHAR(20) NOT NULL,
    address_type ENUM('HOME', 'OFFICE', 'OTHER') DEFAULT 'HOME',
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) -- e.g., 'bi-laptop', 'bi-phone'
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    category_id INT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    address TEXT NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'Demo Payment',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Cart Table (Optional: Session-based is easier for demo, but DB-based is more robust)
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert Sample Users (Password is 'password123' for both)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@vibrantshop.com', '$2y$10$S9VWhu8N1O4fS1bI2gN0ueo0z6R1Z9uY3kK5Q7jW3p3hE8t0z2v2a', 'admin'),
('john_doe', 'john@example.com', '$2y$10$8S.4Zqf4/q1F4j8e5p1QOe0p/0T1S2Y/8R1qL9t0T3Y0hE8v0v2a', 'user');

-- Insert Sample Categories
INSERT INTO categories (name, icon) VALUES 
('Electronics', 'bi-cpu'),
('Fashion', 'bi-bag-heart'),
('Home & Living', 'bi-house-heart'),
('Beauty', 'bi-stars');

-- Insert Sample Products
INSERT INTO products (name, description, price, stock, category_id, image) VALUES
('Premium Smartphone', 'Latest 5G smartphone with high-end features and stunning display.', 999.99, 50, 1, 'assets/images/phone.jpg'),
('Noise Cancelling Headphones', 'Immersive audio experience with active noise cancellation.', 299.00, 40, 1, 'assets/images/headphones.jpg'),
('Designer Watch', 'Elegant watch for all occasions, waterproof and stylish.', 199.50, 30, 2, 'assets/images/watch.jpg'),
('Ultra-soft Hoodie', 'Comfortable and stylish cotton hoodie for daily wear.', 45.00, 100, 2, 'assets/images/hoodie.jpg'),
('Smart Home Hub', 'Control your smart devices with a single voice command.', 120.00, 20, 1, 'assets/images/hub.jpg'),
('Ceramic Coffee Set', 'Minimalist design ceramic mugs for the perfect morning.', 35.00, 60, 3, 'assets/images/coffee.jpg'),
('Organic Skincare Kit', 'A complete set of organic skincare products for glowing skin.', 89.00, 25, 4, 'assets/images/skincare.jpg'),
('Ergonomic Office Chair', 'Breathable mesh chair designed for long working hours.', 250.00, 15, 3, 'assets/images/chair.jpg');
