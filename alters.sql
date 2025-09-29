-- Enable foreign key support
PRAGMA foreign_keys = ON;
-- Table: cards
CREATE TABLE IF NOT EXISTS cards (
                                     id INTEGER PRIMARY KEY AUTOINCREMENT,
                                     uuid TEXT UNIQUE NOT NULL,
                                     element TEXT NOT NULL,
                                     name TEXT NOT NULL,
                                     slug TEXT NOT NULL,
                                     effect TEXT,
                                     effect_raw TEXT,
                                     flavor TEXT,
                                     cost_memory TEXT,
                                     cost_reserve TEXT,
                                     level TEXT,
                                     power TEXT,
                                     life TEXT,
                                     durability TEXT,
                                     speed TEXT,
                                     last_update DATETIME,
                                     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                     updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
-- Table: sets
CREATE TABLE IF NOT EXISTS sets (
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    name TEXT UNIQUE NOT NULL,
                                    prefix TEXT,
                                    language TEXT,
                                    last_update DATETIME,
                                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
-- Table: editions
CREATE TABLE IF NOT EXISTS editions (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                                        uuid TEXT UNIQUE NOT NULL,
                                        card_id INTEGER NOT NULL,
                                        card_uuid TEXT NOT NULL,
                                        collector_number TEXT NOT NULL,
                                        slug TEXT NOT NULL,
                                        flavor TEXT,
                                        illustrator TEXT,
                                        rarity TEXT,
                                        set_id INTEGER NOT NULL,
                                        last_update DATETIME,
                                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                        FOREIGN KEY(card_id) REFERENCES cards(id),
    FOREIGN KEY(set_id) REFERENCES sets(id)
    );
-- Table: subtypes
CREATE TABLE IF NOT EXISTS subtypes (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                                        value TEXT UNIQUE NOT NULL,
                                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
-- Table: types
CREATE TABLE IF NOT EXISTS types (
                                     id INTEGER PRIMARY KEY AUTOINCREMENT,
                                     value TEXT UNIQUE NOT NULL,
                                     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                     updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
-- Pivot Table: card_subtypes
CREATE TABLE IF NOT EXISTS card_subtypes (
                                             id INTEGER PRIMARY KEY AUTOINCREMENT,
                                             card_id INTEGER NOT NULL,
                                             subtype_id INTEGER NOT NULL,
                                             created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                             updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                             FOREIGN KEY(card_id) REFERENCES cards(id),
    FOREIGN KEY(subtype_id) REFERENCES subtypes(id),
    UNIQUE(card_id, subtype_id)
    );
-- Pivot Table: card_types
CREATE TABLE IF NOT EXISTS card_types (
                                          id INTEGER PRIMARY KEY AUTOINCREMENT,
                                          card_id INTEGER NOT NULL,
                                          type_id INTEGER NOT NULL,
                                          created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                          updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                          FOREIGN KEY(card_id) REFERENCES cards(id),
    FOREIGN KEY(type_id) REFERENCES types(id),
    UNIQUE(card_id, type_id)
    );
-- Table: classes
CREATE TABLE IF NOT EXISTS classes (
                                       id INTEGER PRIMARY KEY AUTOINCREMENT,
                                       value TEXT UNIQUE NOT NULL,
                                       created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                       updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
-- Pivot Table: card_classes
CREATE TABLE IF NOT EXISTS card_classes (
                                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                                            card_id INTEGER NOT NULL,
                                            class_id INTEGER NOT NULL,
                                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                            FOREIGN KEY(card_id) REFERENCES cards(id),
    FOREIGN KEY(class_id) REFERENCES classes(id),
    UNIQUE(card_id, class_id)
    );

-- products (the admin listings)
CREATE TABLE products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    edition_id INTEGER,    -- NULL if custom product
    name TEXT,             -- required if custom product
    price DECIMAL(10,2) NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (edition_id) REFERENCES editions(id)
);

-- users table (if not already exists)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    email TEXT UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- carts table
CREATE TABLE IF NOT EXISTS carts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- cart_items table
CREATE TABLE IF NOT EXISTS cart_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cart_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- orders table
CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending', -- pending, shipped, delivered, cancelled
    total_amount DECIMAL(10,2) NOT NULL,
    tracking_number TEXT,
    shipping_address TEXT,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    price DECIMAL(10,2) NOT NULL, -- price at time of order
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- settings table
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key TEXT UNIQUE NOT NULL,
    value TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT OR IGNORE INTO settings (key, value) VALUES ('low_stock_threshold', '5');

-- Add shipping_address column to orders table if it doesn't exist
-- (This is needed for Stripe payment integration with shipping info)
ALTER TABLE orders ADD COLUMN shipping_address TEXT;

-- Add name column to order_items table if it doesn't exist
-- (This stores the product name at time of order)
ALTER TABLE order_items ADD COLUMN name TEXT NOT NULL DEFAULT '';

-- Create email queue table for asynchronous email sending
CREATE TABLE IF NOT EXISTS email_queue (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    to_email TEXT NOT NULL,
    subject TEXT NOT NULL,
    template TEXT NOT NULL,
    data TEXT NOT NULL, -- JSON data for template
    from_email TEXT DEFAULT 'noreply@cardpoint.com',
    from_name TEXT DEFAULT 'Cardpoint',
    status TEXT DEFAULT 'pending', -- pending, sent, failed
    attempts INTEGER DEFAULT 0,
    max_attempts INTEGER DEFAULT 3,
    error_message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    sent_at DATETIME
);

-- Shipping Countries Table
CREATE TABLE IF NOT EXISTS shipping_countries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    country_code TEXT NOT NULL UNIQUE, -- ISO 3166-1 alpha-2 (e.g., 'US', 'GB', 'DE')
    country_name TEXT NOT NULL,
    estimated_days_min INTEGER NOT NULL DEFAULT 7,
    estimated_days_max INTEGER NOT NULL DEFAULT 14,
    is_enabled INTEGER DEFAULT 1, -- 0 = disabled, 1 = enabled
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Shipping Weight Tiers Table (admin defines weight ranges and prices per country)
CREATE TABLE IF NOT EXISTS shipping_weight_tiers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    country_id INTEGER NOT NULL, -- Links to shipping_countries table
    tier_name TEXT NOT NULL, -- e.g., "Up to 0.5kg", "0.5-1kg", "1-2kg"
    max_weight_kg DECIMAL(4,2) NOT NULL, -- Maximum weight for this tier in kg
    price DECIMAL(10,2) NOT NULL,
    is_enabled INTEGER DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES shipping_countries(id) ON DELETE CASCADE
);

-- Add shipping columns to orders table if they don't exist
ALTER TABLE orders ADD COLUMN shipping_country TEXT;
ALTER TABLE orders ADD COLUMN shipping_weight_grams INTEGER DEFAULT 0;
ALTER TABLE orders ADD COLUMN shipping_cost DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE orders ADD COLUMN shipping_tier_id INTEGER REFERENCES shipping_weight_tiers(id);

-- Insert default shipping countries (major markets)
INSERT OR IGNORE INTO shipping_countries (country_code, country_name, estimated_days_min, estimated_days_max) VALUES
('US', 'United States', 5, 10),
('CA', 'Canada', 7, 14),
('GB', 'United Kingdom', 7, 14),
('DE', 'Germany', 7, 14),
('FR', 'France', 7, 14),
('IT', 'Italy', 7, 14),
('ES', 'Spain', 7, 14),
('NL', 'Netherlands', 7, 14),
('BE', 'Belgium', 7, 14),
('AU', 'Australia', 10, 21),
('NZ', 'New Zealand', 10, 21),
('JP', 'Japan', 7, 14),
('KR', 'South Korea', 7, 14),
('SG', 'Singapore', 7, 14);

-- Migration: Add country_id column to existing shipping_weight_tiers table
-- This will be needed if the table already exists without the country_id column
-- ALTER TABLE shipping_weight_tiers ADD COLUMN country_id INTEGER;
-- UPDATE shipping_weight_tiers SET country_id = 1 WHERE country_id IS NULL; -- Default to first country
-- CREATE UNIQUE INDEX IF NOT EXISTS idx_shipping_tiers_country_weight ON shipping_weight_tiers(country_id, max_weight_kg);

-- Insert default weight tiers for each country (admin can modify these)
-- Note: These inserts will create tiers for all countries. In practice, admin should customize per country.
INSERT OR IGNORE INTO shipping_weight_tiers (country_id, tier_name, max_weight_kg, price, sort_order) 
SELECT sc.id, 'Up to 0.5kg', 0.5, 4.99, 1 FROM shipping_countries sc WHERE sc.is_enabled = 1;

INSERT OR IGNORE INTO shipping_weight_tiers (country_id, tier_name, max_weight_kg, price, sort_order) 
SELECT sc.id, 'Up to 1kg', 1.0, 7.99, 2 FROM shipping_countries sc WHERE sc.is_enabled = 1;

INSERT OR IGNORE INTO shipping_weight_tiers (country_id, tier_name, max_weight_kg, price, sort_order) 
SELECT sc.id, 'Up to 2kg', 2.0, 12.99, 3 FROM shipping_countries sc WHERE sc.is_enabled = 1;

INSERT OR IGNORE INTO shipping_weight_tiers (country_id, tier_name, max_weight_kg, price, sort_order) 
SELECT sc.id, 'Up to 5kg', 5.0, 18.99, 4 FROM shipping_countries sc WHERE sc.is_enabled = 1;

INSERT OR IGNORE INTO shipping_weight_tiers (country_id, tier_name, max_weight_kg, price, sort_order) 
SELECT sc.id, 'Up to 10kg', 10.0, 24.99, 5 FROM shipping_countries sc WHERE sc.is_enabled = 1;
