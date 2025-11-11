<?php

require_once __DIR__ . '/../bootstrap.php';
require_once CORE_PATH . '/autoload.php';

echo "Generating sitemap...\n";

try {
    $sitemap_content = generate_sitemap();
    $sitemap_path = PUBLIC_PATH . '/sitemap.xml';

    file_put_contents($sitemap_path, $sitemap_content);

    echo "[✓] Sitemap generated successfully at: {$sitemap_path}\n";
    echo "[i] File size: " . number_format(strlen($sitemap_content)) . " bytes\n";

    // Count URLs
    $url_count = substr_count($sitemap_content, '<url>');
    echo "🔗 URLs included: {$url_count}\n";

} catch (Exception $e) {
    echo "[✗] Error generating sitemap: " . $e->getMessage() . "\n";
    exit(1);
}