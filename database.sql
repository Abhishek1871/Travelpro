-- Complete & Efficient Database for TravelPro Tourism System
-- Run this in phpMyAdmin: Copy → Paste → Go

CREATE DATABASE IF NOT EXISTS tourism;
USE tourism;

-- Safety: Disable foreign keys during setup
SET FOREIGN_KEY_CHECKS = 0;

-- Drop all tables to start fresh (fixes #1054 unknown column errors)
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS user_logs;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS places;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS admins;

-- 1. Admins Table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Places (Packages) Table (Now includes image2, image3, and advanced details!)
CREATE TABLE places (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image2_path VARCHAR(255) NULL,
    image3_path VARCHAR(255) NULL,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) DEFAULT 'General',
    discount_percent INT DEFAULT 0,
    description TEXT NOT NULL,
    location VARCHAR(255),
    -- Highlights
    duration VARCHAR(100) DEFAULT 'Flexible',
    group_size VARCHAR(100) DEFAULT '1-50 People',
    languages VARCHAR(100) DEFAULT 'English, Hindi',
    -- Inclusions (JSON or boolean flags)
    inc_hotel TINYINT(1) DEFAULT 1,
    inc_meals TINYINT(1) DEFAULT 1,
    inc_tours TINYINT(1) DEFAULT 1,
    inc_transfers TINYINT(1) DEFAULT 1,
    inc_insurance TINYINT(1) DEFAULT 1,
    inc_support TINYINT(1) DEFAULT 1,
    inc_custom TEXT,
    -- Exclusions
    exc_flights TINYINT(1) DEFAULT 1,
    exc_visa TINYINT(1) DEFAULT 1,
    exc_personal TINYINT(1) DEFAULT 1,
    exc_tips TINYINT(1) DEFAULT 1,
    exc_custom TEXT,
    -- Itinerary (JSON format)
    itinerary TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. User Logs Table
CREATE TABLE user_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    username VARCHAR(100),
    user_ip VARCHAR(45) NOT NULL,
    action ENUM('LOGIN','LOGOUT') NOT NULL DEFAULT 'LOGIN',
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Vehicles Table
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    capacity INT NOT NULL,
    price_per_day DECIMAL(10, 2) NOT NULL
);

-- 6. Bookings Table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    place_id INT NOT NULL,
    vehicle_id INT,
    passenger_name VARCHAR(100) NOT NULL,
    from_place VARCHAR(100) NOT NULL,
    total_distance INT DEFAULT 0,
    num_people INT DEFAULT 1,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'Cash',
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (place_id) REFERENCES places(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL
);

-- 7. Reviews Table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    place_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (place_id) REFERENCES places(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_place (user_id, place_id)
);

-- SEED DATA
INSERT INTO vehicles (name, type, capacity, price_per_day) VALUES 
('Toyota Innova', 'SUV', 6, 3500.00),
('Swift Dzire', 'Sedan', 4, 2500.00),
('Volvo Bus', 'Bus', 40, 15000.00),
('Tempo Traveller', 'Minibus', 12, 6000.00);

-- Safe Admin Insert
INSERT IGNORE INTO admins (username, password) VALUES ('admin', 'admin123');

-- Indian Packages Seed Data
INSERT INTO places (name, image_path, image2_path, image3_path, price, category, discount_percent, description) VALUES 
('Kerala Backwaters', 'assets/images/kerala1.jpg', 'assets/images/kerala2.jpg', 'assets/images/kerala3.jpg', 25000.00, 'Family', 10, 'Enjoy a peaceful house-boat cruise through the palm-fringed backwaters of Alleppey.'),
('Ladakh Adventure', 'assets/images/ladakh1.jpg', 'assets/images/ladakh2.jpg', 'assets/images/ladakh3.jpg', 45000.00, 'Adventure', 0, 'Experience high-altitude lakes and cold desert landscapes of the Himalayas.'),
('Varanasi Culture', 'assets/images/varanasi1.jpg', 'assets/images/varanasi2.jpg', 'assets/images/varanasi3.jpg', 15000.00, 'Solo', 5, 'Discover the spiritual capital of India with its historic ghats and Aarti ceremonies.'),
('Goa Beach Party', 'assets/images/goa1.jpg', 'assets/images/goa2.jpg', 'assets/images/goa3.jpg', 20000.00, 'Friends', 15, 'Sun, sand, and nightlife. Explore vibrant beaches and Portuguese heritage of Goa.'),
('Rajasthan Royal Heritage', 'assets/images/rajasthan1.jpg', 'assets/images/rajasthan2.jpg', 'assets/images/rajasthan3.jpg', 35000.00, 'Family', 0, 'Walk through the majestic palaces of Jaipur, Jodhpur, and Udaipur.'),
('Rishikesh Yoga Retreat', 'assets/images/rishikesh1.jpg', 'assets/images/rishikesh2.jpg', 'assets/images/rishikesh3.jpg', 18000.00, 'Solo', 20, 'Rejuvenate your soul in the Yoga capital of the world, beside the holy Ganges.');

-- Re-enable foreign keys
SET FOREIGN_KEY_CHECKS = 1;
