-- dtc_transport.sql
-- Create and select database
CREATE DATABASE IF NOT EXISTS dtc_transport
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE dtc_transport;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name  VARCHAR(50) NOT NULL,
  username   VARCHAR(50) NOT NULL UNIQUE,
  email      VARCHAR(120) NOT NULL UNIQUE,
  student_id VARCHAR(32),
  role       VARCHAR(20) NOT NULL DEFAULT 'student',
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buses
CREATE TABLE IF NOT EXISTS buses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  seat_count INT NOT NULL,
  type VARCHAR(30) NOT NULL,     -- e.g., AC, Deluxe AC, Non-AC
  amenities TEXT,                -- comma-separated list
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Trips (a scheduled service for a route and time)
CREATE TABLE IF NOT EXISTS trips (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bus_id INT UNSIGNED NOT NULL,
  route_from VARCHAR(80) NOT NULL,
  route_to   VARCHAR(80) NOT NULL,
  depart_time TIME NOT NULL,
  duration_minutes INT NOT NULL,
  price INT NOT NULL,            -- in BDT
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_trips_bus FOREIGN KEY (bus_id) REFERENCES buses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings
CREATE TABLE IF NOT EXISTS bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  trip_id INT UNSIGNED NOT NULL,
  journey_date DATE NOT NULL,
  booking_code CHAR(8) NOT NULL UNIQUE,
  payment_method VARCHAR(20) NOT NULL,
  service_charge INT NOT NULL DEFAULT 5,
  total_amount INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_bookings_trip FOREIGN KEY (trip_id) REFERENCES trips(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seats reserved per booking (unique by trip + date + seat)
CREATE TABLE IF NOT EXISTS booking_seats (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id INT UNSIGNED NOT NULL,
  trip_id INT UNSIGNED NOT NULL,
  journey_date DATE NOT NULL,
  seat_number INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_bseats_booking FOREIGN KEY (booking_id) REFERENCES bookings(id),
  CONSTRAINT fk_bseats_trip FOREIGN KEY (trip_id) REFERENCES trips(id),
  CONSTRAINT uq_seat UNIQUE (trip_id, journey_date, seat_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notices (optional)
CREATE TABLE IF NOT EXISTS notices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  body  TEXT NOT NULL,
  tag   VARCHAR(20) NOT NULL DEFAULT 'Update', -- e.g., Important/New/Update/Reminder/Event
  published_at DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed buses
INSERT INTO buses (name, seat_count, type, amenities) VALUES
('DIU Express 01', 40, 'AC', 'WiFi,AC,Comfortable Seats'),
('DIU Express 02', 40, 'AC', 'WiFi,AC,Comfortable Seats'),
('DIU Deluxe 01',  36, 'Deluxe AC', 'WiFi,AC,Reclining Seats,Charging Port'),
('DIU Standard 01',40, 'Non-AC', 'Comfortable Seats'),
('DIU Premium 01', 30, 'Premium AC', 'WiFi,AC,Luxury Seats,Charging Port,Refreshments');

-- Seed trips (matches your sample schedule)
-- DSC ↔ Dhanmondi
INSERT INTO trips (bus_id, route_from, route_to, depart_time, duration_minutes, price) VALUES
(1, 'DSC Campus', 'Dhanmondi', '08:00:00', 45, 50),
(2, 'DSC Campus', 'Dhanmondi', '14:00:00', 45, 50),
(1, 'Dhanmondi', 'DSC Campus', '10:00:00', 45, 50),
(2, 'Dhanmondi', 'DSC Campus', '17:00:00', 45, 50);

-- DSC ↔ Uttara
INSERT INTO trips (bus_id, route_from, route_to, depart_time, duration_minutes, price) VALUES
(1, 'DSC Campus', 'Uttara', '07:30:00', 50, 50),
(2, 'DSC Campus', 'Uttara', '13:30:00', 50, 50),
(1, 'Uttara', 'DSC Campus', '09:30:00', 50, 50),
(2, 'Uttara', 'DSC Campus', '16:30:00', 50, 50);

-- DSC ↔ Gulshan
INSERT INTO trips (bus_id, route_from, route_to, depart_time, duration_minutes, price) VALUES
(3, 'DSC Campus', 'Gulshan', '08:15:00', 40, 75),
(3, 'DSC Campus', 'Gulshan', '14:15:00', 40, 75),
(3, 'Gulshan', 'DSC Campus', '10:15:00', 40, 75),
(3, 'Gulshan', 'DSC Campus', '17:15:00', 40, 75);

-- DSC ↔ Mirpur
INSERT INTO trips (bus_id, route_from, route_to, depart_time, duration_minutes, price) VALUES
(4, 'DSC Campus', 'Mirpur', '07:45:00', 50, 35),
(4, 'DSC Campus', 'Mirpur', '13:45:00', 50, 35),
(4, 'Mirpur', 'DSC Campus', '09:45:00', 50, 35),
(4, 'Mirpur', 'DSC Campus', '16:45:00', 50, 35);

-- Premium example
INSERT INTO trips (bus_id, route_from, route_to, depart_time, duration_minutes, price) VALUES
(5, 'DSC Campus', 'Dhanmondi', '13:30:00', 35, 100);

-- Notices (optional)
INSERT INTO notices (title, body, tag, published_at) VALUES
('Holiday Schedule Update', 'Transport services will operate on modified schedule during winter break.', 'Important', '2024-12-15'),
('New Route Added: Mirpur to DSC', 'Service starts from Dec 18, 2024. Check timings on the schedule page.', 'New', '2024-12-12'),
('Mobile App Launch', 'The DIU Transport app is now available for Android and iOS.', 'Update', '2024-12-10');