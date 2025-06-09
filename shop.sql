DROP DATABASE IF EXISTS modern_ecommerce;

CREATE DATABASE modern_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE modern_ecommerce;


CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_admin TINYINT(1) DEFAULT 0
);


INSERT INTO users (username, password_hash, email, is_admin) VALUES
('user1', '$2y$10$Z3B1MDwNOK5Ug0RP8Wy9RukTV82xE1oTULRC4n59LTKv6JK4LxyZy', 'user1@example.com', 0),



CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255)
);


INSERT INTO products (name, description, price, image) VALUES
('Stylish T-Shirt', 'Comfortable cotton t-shirt in multiple colors.', 499.00, 'images/tshirt.jpg'),
('Wireless Earbuds', 'High-quality sound with noise cancellation.', 2999.00, 'images/earbuds.jpg'),
('Gaming Mouse', 'Ergonomic design with customizable buttons.', 1499.00, 'images/mouse.jpg'),
('Smartphone Case', 'Durable TPU case with shock absorption for popular smartphone models.', 699.00, 'images/phone_case.jpg'),
('Bluetooth Speaker', 'Portable speaker with deep bass and water-resistant design.', 2599.00, 'images/speaker.jpg'),
('Laptop Backpack', 'Waterproof backpack with padded laptop compartment and multiple pockets.', 3999.00, 'images/backpack.jpg'),
('Fitness Tracker', 'Track your daily activity, heart rate, and sleep with this lightweight tracker.', 1999.00, 'images/fitness_tracker.jpg'),
('LED Desk Lamp', 'Adjustable brightness and color temperature with USB charging port.', 1299.00, 'images/desk_lamp.jpg');


CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
);
