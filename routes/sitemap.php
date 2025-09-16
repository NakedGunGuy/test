<?php

// XML Sitemap route
get('/sitemap.xml', function () {
    header('Content-Type: application/xml; charset=UTF-8');
    echo generate_sitemap();
    exit;
});

// Robots.txt route
get('/robots.txt', function () {
    header('Content-Type: text/plain; charset=UTF-8');

    $base_url = rtrim($_ENV['APP_URL'] ?? 'https://cardpoint.example.com', '/');

    $robots = "User-agent: *\n";
    $robots .= "Allow: /\n";
    $robots .= "Disallow: /admin/\n";
    $robots .= "Disallow: /profile/\n";
    $robots .= "Disallow: /cart/\n";
    $robots .= "Disallow: /checkout/\n";
    $robots .= "Disallow: /login\n";
    $robots .= "Disallow: /register\n";
    $robots .= "\n";
    $robots .= "Sitemap: {$base_url}/sitemap.xml\n";

    echo $robots;
    exit;
});