🚍 DIU Transport Hub (DTH)
📌 Project Overview

The DIU Transport Hub (DTH) is a full-stack web-based application designed to streamline and modernize university transportation services for students and faculty members.

The system allows users to:

View bus schedules and routes
Book tickets online
Track buses (concept/implementation ready)
Manage their bookings digitally

By automating transport operations, the system reduces manual work and improves efficiency, transparency, and user convenience.

🎯 Project Objective

The objective of DTH is to build a structured and automated transport management system that:

Enables digital ticket booking
Reduces dependency on manual processes
Provides role-based access (Student, Faculty, Admin)
Improves travel planning with schedules and route visibility
✨ Key Features
🔐 Role-Based Authentication
পৃথ Separate login system for:
Students
Faculty Members
Admin
Different access levels and ticket pricing based on user roles
🎟️ Online Ticket Booking
Select route, date, and time
Instant booking confirmation
Unique booking ID / QR-based ticket (if implemented)
📍 Live Bus Tracking
Real-time bus tracking using map integration (or simulated if not fully implemented)
Displays current location and estimated arrival time
🚌 Bus Schedule & Route Management
View all routes, stops, and timings
Easy navigation for better travel planning
📂 Booking History & Ticket Management
View past and upcoming bookings
Download or display digital tickets
Cancel bookings (if implemented)
🛠️ Technologies Used
🌐 Frontend
HTML5
CSS3
JavaScript
⚙️ Backend
PHP
🗄️ Database
MySQL
🧰 Tools & Environment
VS Code
XAMPP Control Panel (Apache & MySQL)
🏗️ System Architecture
Frontend: User interface and interaction
Backend (PHP): Handles business logic, authentication, and booking system
Database (MySQL): Stores user data, bookings, routes, and schedules
🚀 How to Run the Project
🔧 Requirements
XAMPP / WAMP / LAMP
Web browser
▶️ Steps

Clone the repository:

git clone https://github.com/your-username/diu-transport-hub.git

Move the project folder to:

htdocs (for XAMPP)
Start services:
Apache
MySQL
Import the database:
Open phpMyAdmin
Create a database (e.g., dth_db)
Import the provided .sql file

Run the project:

http://localhost/diu-transport-hub


🔮 Future Improvements
Real-time GPS tracking with live APIs
Online payment integration (Stripe/SSLCommerz)
Mobile app version
Push notifications for bus arrival
Advanced admin analytics dashboard
👨‍💻 Author

MD Azijur Rahman Rafi
BSc Student | Daffodil International University

📄 License

This project is for educational purposes.
