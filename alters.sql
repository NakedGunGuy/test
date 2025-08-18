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
