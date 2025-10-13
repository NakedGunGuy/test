<?php

// Initialize language constants
define('DEFAULT_LANGUAGE', 'si');
define('AVAILABLE_LANGUAGES', ['en', 'si']);

/**
 * Get current language from URL or session
 */
function get_current_language(): string {
    // Check if language is already set in session
    if (isset($_SESSION['language']) && in_array($_SESSION['language'], AVAILABLE_LANGUAGES)) {
        return $_SESSION['language'];
    }

    // Parse language from URL
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = explode('/', trim($uri, '/'));

    if (!empty($segments[0]) && in_array($segments[0], AVAILABLE_LANGUAGES)) {
        $_SESSION['language'] = $segments[0];
        return $segments[0];
    }

    // Default to English
    $_SESSION['language'] = DEFAULT_LANGUAGE;
    return DEFAULT_LANGUAGE;
}

/**
 * Set current language
 */
function set_language(string $language): void {
    if (in_array($language, AVAILABLE_LANGUAGES)) {
        $_SESSION['language'] = $language;
    }
}

/**
 * Get URI without language prefix
 */
function get_uri_without_language(): string {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = explode('/', trim($uri, '/'));

    // Remove language prefix if present
    if (!empty($segments[0]) && in_array($segments[0], AVAILABLE_LANGUAGES)) {
        array_shift($segments);
    }

    return '/' . implode('/', $segments);
}

/**
 * Generate URL with language prefix
 */
function url_with_language(string $path, string $language = null): string {
    if ($language === null) {
        $language = get_current_language();
    }

    // Remove leading slash from path
    $path = ltrim($path, '/');

    // Always include language prefix for consistency
    return "/{$language}/" . $path;
}

/**
 * Helper function to generate language-aware URLs (shorter version)
 */
function url(string $path): string {
    return url_with_language($path);
}

/**
 * Redirect to current page with different language
 */
function redirect_to_language(string $language): void {
    if (!in_array($language, AVAILABLE_LANGUAGES)) {
        return;
    }

    $current_path = get_uri_without_language();
    $new_url = url_with_language($current_path, $language);

    // Handle query parameters
    if (!empty($_SERVER['QUERY_STRING'])) {
        $new_url .= '?' . $_SERVER['QUERY_STRING'];
    }

    set_language($language);
    header("Location: {$new_url}");
    exit;
}

/**
 * Enhanced route function with language support
 */
function route_with_language($pattern, $callback = null, $middleware = [])
{
    static $routes = [];
    if ($callback !== null) {
        // Add language prefix to pattern
        $language_pattern = '/{lang}' . $pattern;

        // Convert "/user/{id}" to regex with language support
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $language_pattern);
        $regex = "#^" . rtrim($regex, '/') . "$#"; // normalize trailing slash

        $routes[$regex] = [
            'callback' => $callback,
            'middleware' => $middleware,
        ];
        return;
    }

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = rtrim($uri, '/'); // normalize trailing slash

    foreach ($routes as $regex => $route) {
        if (preg_match($regex, $uri, $matches)) {
            // Set language from URL
            if (isset($matches['lang']) && in_array($matches['lang'], AVAILABLE_LANGUAGES)) {
                set_language($matches['lang']);
            }

            // Run middleware first
            foreach ($route['middleware'] as $mw) {
                if (is_callable($mw)) {
                    $mw();
                }
            }

            // Only keep named params (excluding 'lang')
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            unset($params['lang']); // Remove language param from callback

            call_user_func($route['callback'], $params);
            return true;
        }
    }

    return false;
}

/**
 * Language-aware GET route
 */
function get_lang($pattern, $callback, $middleware = [])
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        route_with_language($pattern, $callback, $middleware);
    }
}

/**
 * Language-aware POST route
 */
function post_lang($pattern, $callback, $middleware = [])
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        route_with_language($pattern, $callback, $middleware);
    }
}

/**
 * Handle language switching
 */
function handle_language_switch(): void {
    if (isset($_POST['language']) && in_array($_POST['language'], AVAILABLE_LANGUAGES)) {
        redirect_to_language($_POST['language']);
    }
}

/**
 * Translation system
 */
function t(string $key, array $replacements = []): string {
    static $translations = null;

    if ($translations === null) {
        $translations = [
            'en' => [
                // Navigation
                'nav.discover' => 'Discover',
                'nav.account' => 'Account',
                'nav.login' => 'Login',
                'nav.signup' => 'Sign Up',
                'nav.profile' => 'Profile',
                'nav.cart' => 'Cart',
                'nav.logout' => 'Logout',

                // Common
                'common.home' => 'Home',
                'common.search' => 'Search',
                'common.filter' => 'Filter',
                'common.reset' => 'Reset',
                'common.save' => 'Save',
                'common.cancel' => 'Cancel',
                'common.delete' => 'Delete',
                'common.edit' => 'Edit',
                'common.add' => 'Add',
                'common.loading' => 'Loading...',
                'common.search_cards' => 'Search cards...',
                'common.all' => 'All',

                // Product listing
                'products.search_filter' => 'Search & Filter',
                'products.search_cards' => 'Search Cards',
                'products.enter_card_name' => 'Enter card name...',
                'products.min_price' => 'Min Price',
                'products.max_price' => 'Max Price',
                'products.products_found' => 'Products ({count} found)',
                'products.show_per_page' => 'Show products per page',
                'products.view' => 'View:',
                'products.grid' => 'Grid',
                'products.list' => 'List',
                'products.box' => 'Box',
                'products.card_name' => 'Card Name',
                'products.edition' => 'Edition',
                'products.price' => 'Price',
                'products.stock' => 'Stock',
                'products.foil' => 'Foil',
                'products.set' => 'Set',
                'products.actions' => 'Actions',
                'products.in_stock' => '{count} in stock',
                'products.out_of_stock' => 'Out of stock',
                'products.unavailable' => 'Unavailable',
                'products.regular' => 'Regular',
                'products.foil_card' => '✨ Foil',

                // Authentication
                'auth.welcome_back' => 'Welcome Back',
                'auth.create_account' => 'Create Account',
                'auth.join_tcg_community' => 'Join our TCG community',
                'auth.sign_in_description' => 'Sign in to your account to continue',
                'auth.already_have_account' => 'Already have an account?',
                'auth.sign_in' => 'Sign in',
                'auth.admin_portal' => 'Admin Portal',
                'auth.admin_username' => 'Administrator Username',
                'auth.admin_password' => 'Administrator Password',
                'auth.access_admin_portal' => '🚀 Access Admin Portal',

                // Forms
                'form.username' => 'Username',
                'form.email' => 'Email',
                'form.email_address' => 'Email Address',
                'form.password' => 'Password',
                'form.confirm_password' => 'Confirm Password',
                'form.current_password' => 'Current Password',
                'form.new_password' => 'New Password',
                'form.confirm_new_password' => 'Confirm New Password',
                'form.required' => 'Required',
                'form.full_name' => 'Full Name',
                'form.address' => 'Address',
                'form.city' => 'City',
                'form.state' => 'State/Province',
                'form.zip' => 'ZIP/Postal Code',
                'form.country' => 'Country',

                // Placeholders
                'placeholder.enter_username' => 'Enter your username',
                'placeholder.enter_password' => 'Enter your password',
                'placeholder.enter_email' => 'Enter your email',
                'placeholder.choose_username' => 'Choose a username',
                'placeholder.create_password' => 'Create a password',
                'placeholder.confirm_password' => 'Confirm your password',
                'placeholder.enter_admin_username' => 'Enter admin username',
                'placeholder.enter_secure_password' => 'Enter secure password',
                'placeholder.address_example' => '123 Main Street',
                'placeholder.select_country' => 'Select Country',
                'placeholder.card_image' => 'Card image',

                // Buttons and Actions
                'button.add_to_cart' => 'Add to Cart',
                'button.view_cart' => 'View Cart',
                'button.browse_products' => 'Browse Products',
                'button.back_to_cart' => '← Back to Cart',
                'button.complete_order' => 'Complete Order',
                'button.update_profile' => 'Update Profile',
                'button.change_password' => 'Change Password',
                'button.save_settings' => 'Save Settings',
                'button.reset_defaults' => 'Reset to Defaults',
                'button.continue_shopping' => 'Continue Shopping',
                'button.proceed_to_checkout' => 'Proceed to Checkout',
                'button.view_orders' => 'View Orders',
                'button.return_to_cart' => 'Return to Cart',

                // Status and Messages
                'status.out_of_stock' => 'Out of Stock',
                'status.low_stock' => 'Low Stock',
                'status.in_stock' => 'In Stock',
                'status.pending' => 'Pending',
                'status.processing' => 'Processing',
                'status.shipped' => 'Shipped',
                'status.delivered' => 'Delivered',
                'status.cancelled' => 'Cancelled',
                'status.enabled' => 'Enabled',
                'status.disabled' => 'Disabled',

                // Cart and Shopping
                'cart.quantity' => 'Quantity',
                'cart.total' => 'Total',
                'cart.subtotal' => 'Subtotal',
                'cart.shipping' => 'Shipping',
                'cart.weight' => 'Weight',
                'cart.cards' => 'cards',
                'cart.select_country_first' => 'Select country first',
                'cart.remove_all' => 'Remove All',
                'cart.empty' => 'Your cart is empty',
                'cart.add_products' => 'Add some products to get started',
                'cart.shopping_cart' => 'Shopping Cart',

                // Profile and Orders
                'profile.total_orders' => 'Total Orders',
                'profile.total_spent' => 'Total Spent',
                'profile.account_settings' => 'Account Settings',
                'profile.order_history' => 'Order History',
                'profile.order_summary' => 'Order Summary',
                'profile.your_orders' => 'Your Orders',
                'profile.no_orders_yet' => 'No orders yet',
                'profile.start_shopping' => 'Start shopping to see your orders here.',
                'profile.order_details' => 'Order Details',
                'profile.back_to_profile' => '← Back to Profile',
                'profile.order_number' => 'Order #{id}',
                'profile.items' => '{count} item|{count} items',
                'profile.view_details' => 'View Details →',

                // Admin sections
                'admin.cache_statistics' => 'Cache Statistics',
                'admin.cached_images' => 'Cached Images',
                'admin.cache_size' => 'Cache Size',
                'admin.cache_management' => 'Cache Management',
                'admin.clear_old_images' => 'Clear Old Images',
                'admin.admin_user' => 'Admin User',
                'admin.administrator' => 'Administrator',
                'admin.no_cards_found' => 'No cards found for the selected filters.',
                'admin.add_products_for' => 'Add Products for {set}',
                'admin.cards_found' => '{count} cards found',
                'admin.no_image' => 'No Image',
                'admin.existing_products' => '{count} existing product(s)',
                'admin.product_name' => 'Product Name',
                'admin.price' => 'Price (€)',
                'admin.quantity' => 'Quantity',
                'admin.foil' => 'Foil',
                'admin.used' => 'Used',
                'admin.description' => 'Description',
                'admin.duplicate' => 'Duplicate',
                'admin.remove' => 'Remove',
                'admin.create_all_products' => 'Create All Products',

                // Success/Error messages
                'toast.login_successful' => 'Login successful!',
                'toast.success' => 'Success!',
                'toast.profile_updated' => 'Profile updated successfully!',
                'toast.password_changed' => 'Password changed successfully!',
                'toast.added_to_cart' => 'Added to cart!',

                // Product details
                'product.details' => 'Product Details',
                'product.set' => 'Set',
                'product.number' => 'Number',
                'product.rarity' => 'Rarity',
                'product.foil' => 'Foil',
                'product.description' => 'Description',
                'product.continue_shopping' => 'Continue Shopping',
                'product.recent_orders' => 'Recent Orders',
                'product.other_editions' => 'Other Editions & Variants',
                'product.view_details' => 'View Details',
                'product.available' => '{count} available',
                'product.customer' => 'Customer',
                'product.order_date' => 'Order Date',

                // Common values
                'common.yes' => 'Yes',
                'common.no' => 'No',
                'common.na' => 'N/A',
                'common.status' => 'Status',

                // Accessibility
                'aria.show_products_per_page' => 'Show products per page',
                'aria.close_dialog' => 'Close dialog',

                // Admin
                'admin.dashboard' => 'Dashboard',
                'admin.products' => 'Products',
                'admin.orders' => 'Orders',
                'admin.analytics' => 'Analytics',
                'admin.image_cache' => 'Image Cache',
                'admin.shipping' => 'Shipping',
                'admin.seo' => 'SEO',
                'admin.settings' => 'Settings',
                'admin.system_status' => 'System Status: Online',
            ],
            'si' => [
                // Navigation
                'nav.discover' => 'Odkrijte',
                'nav.account' => 'Račun',
                'nav.login' => 'Prijava',
                'nav.signup' => 'Registracija',
                'nav.profile' => 'Profil',
                'nav.cart' => 'Košarica',
                'nav.logout' => 'Odjava',

                // Common
                'common.home' => 'Domov',
                'common.search' => 'Iskanje',
                'common.filter' => 'Filter',
                'common.reset' => 'Ponastavi',
                'common.save' => 'Shrani',
                'common.cancel' => 'Prekliči',
                'common.delete' => 'Izbriši',
                'common.edit' => 'Uredi',
                'common.add' => 'Dodaj',
                'common.loading' => 'Nalaganje...',
                'common.search_cards' => 'Iščite karte...',
                'common.all' => 'Vse',

                // Product listing
                'products.search_filter' => 'Iskanje in filtriranje',
                'products.search_cards' => 'Iščite karte',
                'products.enter_card_name' => 'Vnesite ime karte...',
                'products.min_price' => 'Min. cena',
                'products.max_price' => 'Maks. cena',
                'products.products_found' => 'Izdelki (najdenih {count})',
                'products.show_per_page' => 'Prikaži izdelkov na stran',
                'products.view' => 'Pogled:',
                'products.grid' => 'Mreža',
                'products.list' => 'Seznam',
                'products.box' => 'Škatle',
                'products.card_name' => 'Ime karte',
                'products.edition' => 'Izdaja',
                'products.price' => 'Cena',
                'products.stock' => 'Zaloga',
                'products.foil' => 'Foil',
                'products.set' => 'Set',
                'products.actions' => 'Dejanja',
                'products.in_stock' => '{count} na zalogi',
                'products.out_of_stock' => 'Ni na zalogi',
                'products.unavailable' => 'Ni na voljo',
                'products.regular' => 'Običajno',
                'products.foil_card' => '✨ Foil',

                // Authentication
                'auth.welcome_back' => 'Dobrodošli nazaj',
                'auth.create_account' => 'Ustvari račun',
                'auth.join_tcg_community' => 'Pridružite se naši TCG skupnosti',
                'auth.sign_in_description' => 'Prijavite se v svoj račun za nadaljevanje',
                'auth.already_have_account' => 'Že imate račun?',
                'auth.sign_in' => 'Prijavite se',
                'auth.admin_portal' => 'Skrbniški portal',
                'auth.admin_username' => 'Skrbniško uporabniško ime',
                'auth.admin_password' => 'Skrbniško geslo',
                'auth.access_admin_portal' => '🚀 Dostop do skrbniškega portala',

                // Forms
                'form.username' => 'Uporabniško ime',
                'form.email' => 'E-pošta',
                'form.email_address' => 'E-poštni naslov',
                'form.password' => 'Geslo',
                'form.confirm_password' => 'Potrdite geslo',
                'form.current_password' => 'Trenutno geslo',
                'form.new_password' => 'Novo geslo',
                'form.confirm_new_password' => 'Potrdite novo geslo',
                'form.required' => 'Zahtevano',
                'form.full_name' => 'Polno ime',
                'form.address' => 'Naslov',
                'form.city' => 'Mesto',
                'form.state' => 'Država/Pokrajina',
                'form.zip' => 'Poštna številka',
                'form.country' => 'Država',

                // Placeholders
                'placeholder.enter_username' => 'Vnesite uporabniško ime',
                'placeholder.enter_password' => 'Vnesite geslo',
                'placeholder.enter_email' => 'Vnesite e-pošto',
                'placeholder.choose_username' => 'Izberite uporabniško ime',
                'placeholder.create_password' => 'Ustvarite geslo',
                'placeholder.confirm_password' => 'Potrdite geslo',
                'placeholder.enter_admin_username' => 'Vnesite skrbniško uporabniško ime',
                'placeholder.enter_secure_password' => 'Vnesite varno geslo',
                'placeholder.address_example' => 'Glavna ulica 123',
                'placeholder.select_country' => 'Izberite državo',
                'placeholder.card_image' => 'Slika karte',

                // Buttons and Actions
                'button.add_to_cart' => 'Dodaj v košarico',
                'button.view_cart' => 'Poglej košarico',
                'button.browse_products' => 'Brskaj po izdelkih',
                'button.back_to_cart' => '← Nazaj na košarico',
                'button.complete_order' => 'Dokončaj naročilo',
                'button.update_profile' => 'Posodobi profil',
                'button.change_password' => 'Spremeni geslo',
                'button.save_settings' => 'Shrani nastavitve',
                'button.reset_defaults' => 'Ponastavi na privzeto',
                'button.continue_shopping' => 'Nadaljuj z nakupovanjem',
                'button.proceed_to_checkout' => 'Nadaljuj na blagajno',
                'button.view_orders' => 'Poglej naročila',
                'button.return_to_cart' => 'Nazaj na košarico',

                // Status and Messages
                'status.out_of_stock' => 'Ni na zalogi',
                'status.low_stock' => 'Malo na zalogi',
                'status.in_stock' => 'Na zalogi',
                'status.pending' => 'Čakajoče',
                'status.processing' => 'V obdelavi',
                'status.shipped' => 'Poslano',
                'status.delivered' => 'Dostavljeno',
                'status.cancelled' => 'Preklicano',
                'status.enabled' => 'Omogočeno',
                'status.disabled' => 'Onemogočeno',

                // Cart and Shopping
                'cart.quantity' => 'Količina',
                'cart.total' => 'Skupaj',
                'cart.subtotal' => 'Vmesni seštevek',
                'cart.shipping' => 'Dostava',
                'cart.weight' => 'Teža',
                'cart.cards' => 'kart',
                'cart.select_country_first' => 'Najprej izberite državo',
                'cart.remove_all' => 'Odstrani vse',
                'cart.empty' => 'Vaša košarica je prazna',
                'cart.add_products' => 'Dodajte nekaj izdelkov za začetek',
                'cart.shopping_cart' => 'Nakupovalna košarica',

                // Profile and Orders
                'profile.total_orders' => 'Skupaj naročil',
                'profile.total_spent' => 'Skupaj porabljeno',
                'profile.account_settings' => 'Nastavitve računa',
                'profile.order_history' => 'Zgodovina naročil',
                'profile.order_summary' => 'Povzetek naročila',
                'profile.your_orders' => 'Vaša naročila',
                'profile.no_orders_yet' => 'Še ni naročil',
                'profile.start_shopping' => 'Začnite z nakupovanjem, da vidite svoja naročila tukaj.',
                'profile.order_details' => 'Podrobnosti naročila',
                'profile.back_to_profile' => '← Nazaj na profil',
                'profile.order_number' => 'Naročilo #{id}',
                'profile.items' => '{count} artikel|{count} artikel',
                'profile.view_details' => 'Poglej podrobnosti →',

                // Admin sections
                'admin.cache_statistics' => 'Statistike predpomnilnika',
                'admin.cached_images' => 'Predpomnjene slike',
                'admin.cache_size' => 'Velikost predpomnilnika',
                'admin.cache_management' => 'Upravljanje predpomnilnika',
                'admin.clear_old_images' => 'Počisti stare slike',
                'admin.admin_user' => 'Skrbniški uporabnik',
                'admin.administrator' => 'Skrbnik',
                'admin.no_cards_found' => 'Nobene karte niso bile najdene za izbrane filtre.',
                'admin.add_products_for' => 'Dodaj izdelke za {set}',
                'admin.cards_found' => '{count} kart najdenih',
                'admin.no_image' => 'Ni slike',
                'admin.existing_products' => '{count} obstoječih izdelkov',
                'admin.product_name' => 'Ime izdelka',
                'admin.price' => 'Cena (€)',
                'admin.quantity' => 'Količina',
                'admin.foil' => 'Foil',
                'admin.used' => 'Rabljeno',
                'admin.description' => 'Opis',
                'admin.duplicate' => 'Podvoji',
                'admin.remove' => 'Odstrani',
                'admin.create_all_products' => 'Ustvari vse izdelke',

                // Success/Error messages
                'toast.login_successful' => 'Prijava uspešna!',
                'toast.success' => 'Uspešno!',
                'toast.profile_updated' => 'Profil uspešno posodobljen!',
                'toast.password_changed' => 'Geslo uspešno spremenjeno!',
                'toast.added_to_cart' => 'Dodano v košarico!',

                // Product details
                'product.details' => 'Podrobnosti izdelka',
                'product.set' => 'Set',
                'product.number' => 'Številka',
                'product.rarity' => 'Redkost',
                'product.foil' => 'Foil',
                'product.description' => 'Opis',
                'product.continue_shopping' => 'Nadaljuj z nakupovanjem',
                'product.recent_orders' => 'Nedavna naročila',
                'product.other_editions' => 'Druge izdaje in variante',
                'product.view_details' => 'Poglej podrobnosti',
                'product.available' => '{count} na voljo',
                'product.customer' => 'Stranka',
                'product.order_date' => 'Datum naročila',

                // Common values
                'common.yes' => 'Da',
                'common.no' => 'Ne',
                'common.na' => 'N/A',
                'common.status' => 'Status',

                // Accessibility
                'aria.show_products_per_page' => 'Prikaži izdelkov na stran',
                'aria.close_dialog' => 'Zapri dialog',

                // Admin
                'admin.dashboard' => 'Nadzorna plošča',
                'admin.products' => 'Izdelki',
                'admin.orders' => 'Naročila',
                'admin.analytics' => 'Analitika',
                'admin.image_cache' => 'Predpomnilnik slik',
                'admin.shipping' => 'Dostava',
                'admin.seo' => 'SEO',
                'admin.settings' => 'Nastavitve',
                'admin.system_status' => 'Stanje sistema: Povezano',
            ]
        ];
    }

    $current_language = get_current_language();
    $translation = $translations[$current_language][$key] ?? $translations['en'][$key] ?? $key;

    // Handle replacements
    foreach ($replacements as $placeholder => $value) {
        $translation = str_replace('{' . $placeholder . '}', $value, $translation);
    }

    return $translation;
}

/**
 * Short alias for translation function
 */
function __(string $key, array $replacements = []): string {
    return t($key, $replacements);
}

/**
 * Get language display name
 */
function get_language_name(string $language): string {
    $names = [
        'en' => 'English',
        'si' => 'Slovenščina'
    ];

    return $names[$language] ?? $language;
}

/**
 * Get language flag emoji
 */
function get_language_flag(string $language): string {
    $flags = [
        'en' => '🇬🇧',
        'si' => '🇸🇮'
    ];

    return $flags[$language] ?? '🌐';
}