<?php
/**
 * Download remaining missing images from Pexels
 */

// Define missing images with direct Pexels URLs
$missing_images = [
    'images/attractions/ba-ho-waterfalls.jpg' => 'https://images.pexels.com/photos/2042404/pexels-photo-2042404.jpeg?auto=compress&cs=tinysrgb&w=800',
    'images/attractions/po-nagar-towers.jpg' => 'https://images.pexels.com/photos/5458388/pexels-photo-5458388.jpeg?auto=compress&cs=tinysrgb&w=800',
    'images/attractions/hon-chong-promontory.jpg' => 'https://images.pexels.com/photos/1635086/pexels-photo-1635086.jpeg?auto=compress&cs=tinysrgb&w=800',
    'images/attractions/hon-mun-island.jpg' => 'https://images.pexels.com/photos/386148/pexels-photo-386148.jpeg?auto=compress&cs=tinysrgb&w=800',
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

echo "Pexels images download complete!\n";
?> 