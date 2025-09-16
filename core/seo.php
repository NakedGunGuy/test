<?php

/**
 * SEO Helper Functions
 * Handles meta tags, schema markup, and sitemap generation
 */

/**
 * Generate XML Sitemap
 */
function generate_sitemap() {
    $pdo = db();
    $base_url = rtrim($_ENV['APP_URL'] ?? 'https://cardpoint.example.com', '/');

    $urls = [];

    // Static pages
    $static_pages = [
        '/' => ['priority' => '1.0', 'changefreq' => 'daily'],
        '/discover' => ['priority' => '0.9', 'changefreq' => 'daily'],
        '/about' => ['priority' => '0.5', 'changefreq' => 'monthly'],
        '/team' => ['priority' => '0.5', 'changefreq' => 'monthly'],
    ];

    foreach ($static_pages as $url => $config) {
        $urls[] = [
            'loc' => $base_url . $url,
            'lastmod' => date('Y-m-d'),
            'changefreq' => $config['changefreq'],
            'priority' => $config['priority']
        ];
    }

    // Product pages
    $stmt = $pdo->prepare("
        SELECT p.id, p.updated_at
        FROM products p
        WHERE p.quantity > 0
        ORDER BY p.updated_at DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $lastmod = $product['updated_at'] ? date('Y-m-d', strtotime($product['updated_at'])) : date('Y-m-d');
        $urls[] = [
            'loc' => $base_url . '/product/' . $product['id'],
            'lastmod' => $lastmod,
            'changefreq' => 'weekly',
            'priority' => '0.8'
        ];
    }

    // Generate XML
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;

    $urlset = $xml->createElement('urlset');
    $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $xml->appendChild($urlset);

    foreach ($urls as $url_data) {
        $url = $xml->createElement('url');

        $loc = $xml->createElement('loc', htmlspecialchars($url_data['loc']));
        $url->appendChild($loc);

        $lastmod = $xml->createElement('lastmod', $url_data['lastmod']);
        $url->appendChild($lastmod);

        $changefreq = $xml->createElement('changefreq', $url_data['changefreq']);
        $url->appendChild($changefreq);

        $priority = $xml->createElement('priority', $url_data['priority']);
        $url->appendChild($priority);

        $urlset->appendChild($url);
    }

    return $xml->saveXML();
}

/**
 * Get SEO meta data for a page
 */
function get_seo_meta($page_type, $data = []) {
    $app_name = $_ENV['APP_NAME'] ?? 'Cardpoint';
    $base_url = rtrim($_ENV['APP_URL'] ?? 'https://cardpoint.example.com', '/');

    switch ($page_type) {
        case 'home':
            return [
                'title' => $app_name . ' - Grand Archive TCG Cards',
                'description' => 'Shop authentic Grand Archive TCG cards at ' . $app_name . '. Find rare cards, booster packs, and singles with fast shipping worldwide.',
                'canonical' => $base_url . '/',
                'og_type' => 'website',
                'og_image' => $base_url . '/assets/logo.png'
            ];

        case 'discover':
            return [
                'title' => 'Discover Grand Archive Cards - ' . $app_name,
                'description' => 'Browse our complete collection of Grand Archive TCG cards. Filter by element, rarity, and set to find the perfect cards for your deck.',
                'canonical' => $base_url . '/discover',
                'og_type' => 'website',
                'og_image' => $base_url . '/assets/logo.png'
            ];

        case 'product':
            if (empty($data['product'])) {
                return get_seo_meta('home');
            }

            $product = $data['product'];
            $card_name = $product['card_name'] ?? $product['name'];
            $set_name = $product['set_name'] ?? '';
            $price = number_format($product['price'], 2);

            $title = $card_name . ($set_name ? ' - ' . $set_name : '') . ' | ' . $app_name;
            $description = "Buy {$card_name}" . ($set_name ? " from {$set_name}" : "") . " for €{$price}. " .
                          ($product['description'] ? strip_tags($product['description']) : "Premium Grand Archive TCG card with fast shipping.");

            // Truncate description to 160 characters
            if (strlen($description) > 160) {
                $description = substr($description, 0, 157) . '...';
            }

            return [
                'title' => $title,
                'description' => $description,
                'canonical' => $base_url . '/product/' . $product['id'],
                'og_type' => 'product',
                'og_image' => get_card_image_url($product['slug'] ?? ''),
                'product_price' => $product['price'],
                'product_currency' => 'EUR',
                'product_availability' => $product['quantity'] > 0 ? 'in_stock' : 'out_of_stock'
            ];

        default:
            return [
                'title' => $app_name,
                'description' => 'Premium Grand Archive TCG cards and collectibles.',
                'canonical' => $base_url,
                'og_type' => 'website',
                'og_image' => $base_url . '/assets/logo.png'
            ];
    }
}

/**
 * Generate JSON-LD schema markup
 */
function generate_schema_markup($type, $data = []) {
    $app_name = $_ENV['APP_NAME'] ?? 'Cardpoint';
    $base_url = rtrim($_ENV['APP_URL'] ?? 'https://cardpoint.example.com', '/');

    switch ($type) {
        case 'organization':
            return [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => $app_name,
                'url' => $base_url,
                'logo' => $base_url . '/assets/logo.png',
                'sameAs' => []
            ];

        case 'website':
            return [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $app_name,
                'url' => $base_url,
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => $base_url . '/discover?name={search_term_string}'
                    ],
                    'query-input' => 'required name=search_term_string'
                ]
            ];

        case 'product':
            if (empty($data['product'])) {
                return null;
            }

            $product = $data['product'];
            $card_name = $product['card_name'] ?? $product['name'];

            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $card_name,
                'description' => $product['description'] ?? "Premium Grand Archive TCG card",
                'image' => get_card_image_url($product['slug'] ?? ''),
                'sku' => 'CARD-' . $product['id'],
                'brand' => [
                    '@type' => 'Brand',
                    'name' => 'Grand Archive'
                ],
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $base_url . '/product/' . $product['id'],
                    'priceCurrency' => 'EUR',
                    'price' => $product['price'],
                    'priceValidUntil' => date('Y-m-d', strtotime('+1 year')),
                    'availability' => $product['quantity'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'seller' => [
                        '@type' => 'Organization',
                        'name' => $app_name
                    ]
                ]
            ];

            // Add additional product details if available
            if (!empty($product['rarity'])) {
                $schema['additionalProperty'][] = [
                    '@type' => 'PropertyValue',
                    'name' => 'Rarity',
                    'value' => $product['rarity']
                ];
            }

            if (!empty($product['set_name'])) {
                $schema['additionalProperty'][] = [
                    '@type' => 'PropertyValue',
                    'name' => 'Set',
                    'value' => $product['set_name']
                ];
            }

            return $schema;

        case 'breadcrumblist':
            if (empty($data['breadcrumbs'])) {
                return null;
            }

            $items = [];
            foreach ($data['breadcrumbs'] as $index => $crumb) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $crumb['name'],
                    'item' => $base_url . $crumb['url']
                ];
            }

            return [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $items
            ];

        default:
            return null;
    }
}

/**
 * Get card image URL with fallback
 */
function get_card_image_url($slug) {
    $base_url = rtrim($_ENV['APP_URL'] ?? 'https://cardpoint.example.com', '/');

    if (empty($slug)) {
        return $base_url . '/assets/logo.png';
    }

    // Check if cached image exists
    $cache_path = PUBLIC_PATH . '/assets/cards/' . $slug . '.jpg';
    if (file_exists($cache_path)) {
        return $base_url . '/assets/cards/' . $slug . '.jpg';
    }

    // Fallback to external image
    return 'https://index.gatcg.com/api/cards/' . $slug . '/250.jpg';
}

/**
 * Render meta tags in HTML head
 */
function render_meta_tags($seo_data) {
    $html = '';

    // Basic meta tags
    $html .= '<meta name="description" content="' . htmlspecialchars($seo_data['description']) . '">' . PHP_EOL;
    $html .= '<meta name="robots" content="index, follow">' . PHP_EOL;
    $html .= '<link rel="canonical" href="' . htmlspecialchars($seo_data['canonical']) . '">' . PHP_EOL;

    // Open Graph tags
    $html .= '<meta property="og:title" content="' . htmlspecialchars($seo_data['title']) . '">' . PHP_EOL;
    $html .= '<meta property="og:description" content="' . htmlspecialchars($seo_data['description']) . '">' . PHP_EOL;
    $html .= '<meta property="og:type" content="' . $seo_data['og_type'] . '">' . PHP_EOL;
    $html .= '<meta property="og:url" content="' . htmlspecialchars($seo_data['canonical']) . '">' . PHP_EOL;
    $html .= '<meta property="og:image" content="' . htmlspecialchars($seo_data['og_image']) . '">' . PHP_EOL;
    $html .= '<meta property="og:site_name" content="' . htmlspecialchars($_ENV['APP_NAME'] ?? 'Cardpoint') . '">' . PHP_EOL;

    // Twitter Card tags
    $html .= '<meta name="twitter:card" content="summary_large_image">' . PHP_EOL;
    $html .= '<meta name="twitter:title" content="' . htmlspecialchars($seo_data['title']) . '">' . PHP_EOL;
    $html .= '<meta name="twitter:description" content="' . htmlspecialchars($seo_data['description']) . '">' . PHP_EOL;
    $html .= '<meta name="twitter:image" content="' . htmlspecialchars($seo_data['og_image']) . '">' . PHP_EOL;

    // Product-specific meta tags
    if (isset($seo_data['product_price'])) {
        $html .= '<meta property="product:price:amount" content="' . $seo_data['product_price'] . '">' . PHP_EOL;
        $html .= '<meta property="product:price:currency" content="' . $seo_data['product_currency'] . '">' . PHP_EOL;
        $html .= '<meta property="product:availability" content="' . $seo_data['product_availability'] . '">' . PHP_EOL;
    }

    return $html;
}

/**
 * Render JSON-LD schema markup
 */
function render_schema_markup($schemas) {
    if (empty($schemas)) {
        return '';
    }

    $html = '<script type="application/ld+json">' . PHP_EOL;

    if (count($schemas) === 1) {
        $html .= json_encode($schemas[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    } else {
        $html .= json_encode($schemas, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    $html .= PHP_EOL . '</script>';

    return $html;
}