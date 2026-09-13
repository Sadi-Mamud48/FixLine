
CREATE DATABASE IF NOT EXISTS fixline_db;
USE fixline_db;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    first_name VARCHAR(50) NULL,
    last_name VARCHAR(50) NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer','provider','moderator','finance','admin') NOT NULL DEFAULT 'customer',
    address VARCHAR(255) NULL,
    phone VARCHAR(30) NULL,
    profile_photo VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT UNSIGNED DEFAULT NULL,
    service_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS service_providers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    profession VARCHAR(100) NOT NULL,
    affiliate VARCHAR(100) DEFAULT NULL,
    experience VARCHAR(50) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    profile_picture VARCHAR(255) DEFAULT 'View/images/default-avatar.png',
    rating DECIMAL(2,1) DEFAULT 0.0,
    work_completed INT DEFAULT 0,
    work_pending INT DEFAULT 0,
    work_successful INT DEFAULT 0,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE services
    ADD CONSTRAINT fk_services_provider
    FOREIGN KEY (provider_id) REFERENCES service_providers(id) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS jobs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED DEFAULT NULL,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    location VARCHAR(150),
    budget DECIMAL(10,2) DEFAULT NULL,
    status ENUM('open','assigned','completed','cancelled') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS service_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    provider_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED DEFAULT NULL,
    job_application_id INT UNSIGNED DEFAULT NULL,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(150),
    budget DECIMAL(10,2) DEFAULT NULL,
    status ENUM('new','accepted','applied','rejected') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES service_providers(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS job_applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id INT UNSIGNED NOT NULL,
    provider_id INT UNSIGNED NOT NULL,
    cover_note TEXT,
    proposed_price DECIMAL(10,2) DEFAULT NULL,
    status ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES service_providers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (job_id, provider_id)
) ENGINE=InnoDB;

ALTER TABLE service_requests
    ADD CONSTRAINT fk_service_requests_application
    FOREIGN KEY (job_application_id) REFERENCES job_applications(id) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    service_request_id INT UNSIGNED DEFAULT NULL,
    service_id INT UNSIGNED DEFAULT NULL,
    booking_date DATE NOT NULL,
    status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED DEFAULT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS earnings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT UNSIGNED NOT NULL,
    job_id INT UNSIGNED DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','paid') NOT NULL DEFAULT 'pending',
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES service_providers(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(50) DEFAULT NULL,
    status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
    paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS refund_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id INT UNSIGNED NOT NULL UNIQUE,
    user_id INT UNSIGNED NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('requested','approved','rejected','refunded') DEFAULT 'requested',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO users (name, first_name, last_name, email, password, role)
VALUES
('MD. Faiz Uddin', 'Faiz', 'Uddin', 'faiz.provider@fixline.com', '$2y$10$examplehashexamplehashexamplehas', 'provider'),
('Nadia Rahman', 'Nadia', 'Rahman', 'nadia.customer@fixline.com', '$2y$10$examplehashexamplehashexamplehas', 'customer');

INSERT IGNORE INTO service_providers (user_id, profession, affiliate, experience, bio, rating, work_completed, work_pending, work_successful, status)
VALUES (
    (SELECT id FROM users WHERE email = 'faiz.provider@fixline.com'),
    'Plumber',
    'Store',
    '1y 6m',
    'Hi I am MD. Faiz Uddin, a dedicated plumber known for my expertise in solving diverse plumbing issues.',
    4.6,
    9,
    4,
    65,
    'approved'
);

INSERT IGNORE INTO service_requests (customer_id, provider_id, title, category, description, location, budget, status)
VALUES (
    (SELECT id FROM users WHERE email = 'nadia.customer@fixline.com'),
    (SELECT id FROM service_providers WHERE user_id = (SELECT id FROM users WHERE email = 'faiz.provider@fixline.com')),
    'Urgent bathroom pipe issue',
    'Plumber',
    'The bathroom pipe is leaking and needs a quick fix before the weekend.',
    'Dhanmondi, Dhaka',
    2000.00,
    'new'
);