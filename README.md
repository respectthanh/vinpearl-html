# Vinpearl Nha Trang Resort Website

This is the HTML/CSS/JS implementation of the Vinpearl Nha Trang Resort website. It includes pages for the resort's rooms, tours, nearby attractions, and more.

## Setup Instructions

1. Clone the repository to your local machine
2. Set up a local PHP server (e.g., XAMPP, MAMP, WAMP)
3. Import the database schema from `includes/db_schema.sql`
4. Update database connection details in `includes/db_connect.php` if needed
5. Place the project files in your web server's document root
6. Access the website via your local server URL (e.g., http://localhost/vinpearl-html)

## Features

- Responsive design that works on desktop, tablet, and mobile devices
- Multi-language support (English and Vietnamese)
- User authentication and account management
- Tour and attraction booking system
- Resort information pages

## Booking Functionality

The website includes a complete booking system for tours and attractions:

1. Users can browse nearby attractions and select ones to book
2. The booking modal allows selection of date and number of people
3. Bookings are stored in the database and linked to user accounts
4. Users can view and manage their bookings in their profile

## Database Tables

The database includes the following tables:

- `users` - Stores user information and credentials
- `bookings` - Stores all booking information
- `attractions` - Stores information about nearby attractions

## File Structure

- `css/` - Stylesheet files
- `js/` - JavaScript files
- `images/` - Image assets
- `includes/` - PHP includes and database connections
- `pages/` - Website pages organized by section

## Admin Features

Administrator accounts have access to additional features:
- Viewing all bookings across all users
- Managing attraction information
- User management

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 