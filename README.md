# CARBS Lite

CARBS Lite is a simple web-based booking system for university clubs to reserve shared campus resources such as rooms, halls, and equipment.

## Project Overview

This project was developed for the Shortcut Asia Internship Challenge under the Simple Booking System topic. The system allows club leaders to check resource availability, create booking requests, edit pending bookings, cancel bookings, and track booking status. Admin users can approve, reject, or cancel booking requests.

## Main Features

- Role-based login for Club Leader and Admin
- Resource availability time-slot view
- Create booking request
- Edit pending booking
- Cancel booking
- Admin approve/reject booking
- Booking history with status
- Double-booking prevention

## Tech Stack

- PHP
- MySQL
- HTML
- CSS
- WAMP Server
- phpMyAdmin

## Demo Login

### Club Leader
Email: leader@apu.edu.my  
Password: 12345

### Admin
Email: admin@apu.edu.my  
Password: 12345

## How to Run the Project

1. Download or clone this repository.
2. Place the project folder inside:

   `C:/wamp64/www/`

3. Start WAMP Server.
4. Open phpMyAdmin:

   `http://localhost/phpmyadmin`

5. Create/import the database using `database.sql`.
6. Open the app in browser:

   `http://localhost/CARBS.php/index.php`

## Database

The database file is included as:

`database.sql`

Import this file into phpMyAdmin before running the system.

## Key Technical Challenge

The main technical challenge was preventing double bookings. The system checks whether the selected resource, date, and time overlaps with any existing pending or approved booking before allowing a new booking request or approval.

## Future Improvements

- Email notifications
- Report generation
- User profile management
- Better admin resource management
