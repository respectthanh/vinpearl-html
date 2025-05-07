<?php
/**
 * Image downloader for Vinpearl Resort website
 * Downloads attraction images from Unsplash
 */

// Define image URLs and local paths
$images = [
    // Attractions
    'images/attractions/nha-trang-panorama.jpg' => 'https://images.unsplash.com/photo-1544550581-1bcabf842b77?q=80&w=2000&auto=format&fit=crop',
    'images/attractions/long-son-pagoda.jpg' => 'https://images.unsplash.com/photo-1580688650045-918307617984?q=80&w=800&auto=format&fit=crop',
    'images/attractions/ba-ho-waterfalls.jpg' => 'https://images.unsplash.com/photo-1541622835480-fac299c5d0e4?q=80&w=800&auto=format&fit=crop',
    'images/attractions/doc-let-beach.jpg' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800&auto=format&fit=crop',
    'images/attractions/po-nagar-towers.jpg' => 'https://images.unsplash.com/photo-1580746738099-537616eeb7c4?q=80&w=800&auto=format&fit=crop',
    'images/attractions/hon-chong-promontory.jpg' => 'https://images.unsplash.com/photo-1609608938845-a431a9d1b0ca?q=80&w=800&auto=format&fit=crop',
    'images/attractions/nha-trang-night-market.jpg' => 'https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?q=80&w=800&auto=format&fit=crop',
    'images/attractions/cho-dam-market.jpg' => 'https://images.unsplash.com/photo-1555529771-7888783a18d3?q=80&w=800&auto=format&fit=crop',
    'images/attractions/hon-mun-island.jpg' => 'https://images.unsplash.com/photo-1587950430093-e1a2afb66d8d?q=80&w=800&auto=format&fit=crop',
    'images/attractions/sailing-club.jpg' => 'https://images.unsplash.com/photo-1601042879364-f3947d3f9c16?q=80&w=800&auto=format&fit=crop',
    
    // Transportation
    'images/transportation/resort-shuttle.jpg' => 'https://images.unsplash.com/photo-1570125909517-53cb21c89ff2?q=80&w=800&auto=format&fit=crop',
    'images/transportation/private-car.jpg' => 'https://images.unsplash.com/photo-1494905998402-395d579af36f?q=80&w=800&auto=format&fit=crop',
    'images/transportation/taxi.jpg' => 'https://images.unsplash.com/photo-1589670301572-734d642c885c?q=80&w=800&auto=format&fit=crop'
];

// Create directories if they don't exist
if (!is_dir('images/attractions')) {
    mkdir('images/attractions', 0755, true);
    echo "Created attractions directory.\n";
}

if (!is_dir('images/transportation')) {
    mkdir('images/transportation', 0755, true);
    echo "Created transportation directory.\n";
}

// Download each image
foreach ($images as $path => $url) {
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

echo "Image download complete!\n";
?> 