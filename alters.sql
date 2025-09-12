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
    status TEXT NOT NULL DEFAULT 'pending', -- pending, processing, shipped, delivered, cancelled
    total_amount DECIMAL(10,2) NOT NULL,
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
