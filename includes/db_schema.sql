-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('USER', 'ADMIN') DEFAULT 'USER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    attraction_name VARCHAR(100) NOT NULL,
    booking_type VARCHAR(50) NOT NULL,
    booking_date DATE NOT NULL,
    number_of_people INT(11) NOT NULL,
    special_requests TEXT,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('PENDING', 'CONFIRMED', 'COMPLETED', 'CANCELLED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Attractions table
CREATE TABLE IF NOT EXISTS attractions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    location VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    base_price DECIMAL(10, 2) NOT NULL,
    distance VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Insert sample attractions data
INSERT INTO attractions (name, description, category, location, image_path, base_price, distance)
VALUES 
('Long Son Pagoda', 'Famous Buddhist temple featuring a large white Buddha statue overlooking the city.', 'cultural', 'https://goo.gl/maps/JfqpvmTLNdAcyork7', 'images/attractions/long-son-pagoda.jpg', 25.00, '3.5 km'),
('Ba Ho Waterfalls', 'A series of three picturesque waterfalls nestled in lush jungle surroundings.', 'nature', 'https://goo.gl/maps/NZrnZja7NZoUyaL46', 'images/attractions/ba-ho-waterfalls.jpg', 35.00, '25 km'),
('Doc Let Beach', 'Pristine white sand beach with crystal clear waters, less crowded than the main city beaches.', 'beaches', 'https://goo.gl/maps/ynG5g9C9XoqUdFqr5', 'images/attractions/doc-let-beach.jpg', 30.00, '45 km'),
('Po Nagar Cham Towers', 'Ancient Hindu temple complex built between the 7th and 12th centuries.', 'cultural', 'https://goo.gl/maps/mNwN1Xk5GJeLRx7aA', 'images/attractions/po-nagar-towers.jpg', 20.00, '5 km'),
('Hon Chong Promontory', 'Dramatic rock formations jutting out into the sea, offering stunning views of the coastline.', 'nature', 'https://goo.gl/maps/UNT7RPPUvbWwB2VH9', 'images/attractions/hon-chong-promontory.jpg', 15.00, '7 km'),
('Nha Trang Night Market', 'Vibrant market offering a variety of local cuisine, fresh seafood, and souvenirs.', 'dining', 'https://goo.gl/maps/TLB5iHpfoXV6s8J38', 'images/attractions/nha-trang-night-market.jpg', 40.00, '4 km'),
('Cho Dam Market', 'Nha Trang\'s largest market, offering everything from fresh produce and seafood to clothing and souvenirs.', 'shopping', 'https://goo.gl/maps/YeBcEmTrBmFP2pgo7', 'images/attractions/cho-dam-market.jpg', 25.00, '6 km'),
('Hon Mun Island', 'Vietnam\'s first marine protected area, known for its crystal clear waters, diverse coral reefs, and colorful marine life.', 'beaches', 'https://goo.gl/maps/ckTuQHnJLrjhCHox7', 'images/attractions/hon-mun-island.jpg', 45.00, '10 km'),
('Sailing Club Nha Trang', 'Iconic beachfront restaurant and bar offering international cuisine, cocktails, and nightlife entertainment.', 'dining', 'https://goo.gl/maps/tZU8YHqZPvB2DqU16', 'images/attractions/sailing-club.jpg', 50.00, '3 km'); 