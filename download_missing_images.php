<?php
/**
 * Download missing images for Vinpearl Resort website
 */

// Define missing images with alternative URLs
$missing_images = [
    'images/attractions/long-son-pagoda.jpg' => 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
    'images/attractions/ba-ho-waterfalls.jpg' => 'https://images.unsplash.com/photo-1565679927145-73135c295718?q=80&w=800',
    'images/attractions/po-nagar-towers.jpg' => 'https://images.unsplash.com/photo-1629285384322-6b7082fd4e54?q=80&w=800',
    'images/attractions/hon-chong-promontory.jpg' => 'https://images.unsplash.com/photo-1577717908709-47603998060c?q=80&w=800',
    'images/attractions/hon-mun-island.jpg' => 'https://images.unsplash.com/photo-1517399155580-ca0341132991?q=80&w=800',
    'images/transportation/taxi.jpg' => 'https://images.unsplash.com/photo-1513639776629-7b61b0ac49cb?q=80&w=800'
];

// Download each image
foreach ($missing_images as $path => $url) {
    echo "Downloading {$url} to {$path}...\n";
    
    // Create file content from URL
    $content = @file_get_contents($url);
    
    if ($content === false) {
        echo "ERROR: Could not download {$url}\n";
        continue;
    }
    
    // Save to file
    if (file_put_contents($path, $content) === false) {
        echo "ERROR: Could not save to {$path}\n";
    } else {
        echo "SUCCESS: Image saved to {$path}\n";
    }
}

echo "Missing images download complete!\n";
?> 