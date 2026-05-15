CREATE DATABASE IF NOT EXISTS carbs_lite;
USE carbs_lite;

DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('club_leader', 'admin') NOT NULL
);

CREATE TABLE resources (
    resource_id INT AUTO_INCREMENT PRIMARY KEY,
    resource_name VARCHAR(100) NOT NULL,
    resource_type VARCHAR(50) NOT NULL,
    capacity INT,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resource_id INT NOT NULL,
    event_name VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    purpose TEXT,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    admin_remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (resource_id) REFERENCES resources(resource_id)
);

INSERT INTO users (name, email, password, role) VALUES
('Club Leader', 'leader@apu.edu.my', '12345', 'club_leader'),
('Admin', 'admin@apu.edu.my', '12345', 'admin');

INSERT INTO resources (resource_name, resource_type, capacity, status) VALUES
('Auditorium 1', 'Hall', 300, 'active'),
('Seminar Room 1', 'Room', 60, 'active'),
('Seminar Room 2', 'Room', 60, 'active'),
('Meeting Room A', 'Room', 20, 'active'),
('Meeting Room B', 'Room', 20, 'active'),
('Sports Hall', 'Sports Facility', 100, 'active'),
('Projector Set 1', 'Equipment', NULL, 'active'),
('PA System 1', 'Equipment', NULL, 'active');

INSERT INTO bookings 
(user_id, resource_id, event_name, event_date, start_time, end_time, purpose, status) 
VALUES
(1, 1, 'Tamil Cultural Night Dry Run', '2026-05-10', '10:00:00', '12:00:00', 'Dry run for cultural event', 'pending'),
(1, 2, 'Tech Workshop', '2026-05-11', '14:00:00', '16:00:00', 'Workshop for club members', 'approved'),
(1, 3, 'Sports Club Briefing', '2026-05-12', '09:00:00', '10:00:00', 'Briefing session for members', 'rejected'),
(1, 4, 'ICYS Committee Meeting', '2026-05-13', '15:00:00', '16:00:00', 'Planning meeting', 'cancelled');
