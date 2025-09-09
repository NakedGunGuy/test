# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Cardpoint is an e-commerce web application for selling Trading Card Game (TCG) cards, primarily focused on Grand Archive TCG. The platform serves as an online store where Cardpoint company sells physical TCG cards to customers.

## Commands

### Development
- **CSS Build**: `.\tailwindcss.exe -i public/css/style.css -o public/css/output.css --watch` - Build and watch Tailwind CSS

### Database & Console
- **Import Cards**: `php console/import_cards.php` - Import cards from external API
- **Create Admin User**: `php console/create_admin_user.php` - Create admin user account
- **Create User**: `php console/create_user.php` - Create regular user account
- **Database Schema**: `alters.sql` contains the SQLite schema

### Dependencies
- **Install**: `composer install` - Install PHP dependencies (PHPMailer)

## Architecture

### Core Framework
- **Custom PHP MVC**: Lightweight custom framework without external dependencies
- **Entry Point**: `public/index.php` handles all requests
- **Bootstrap**: `bootstrap.php` defines constants and initializes environment
- **Database**: SQLite database in `database/database.sqlite`

### Directory Structure
- `core/` - Core framework components (router, database, helpers, session management)
- `routes/` - Route definitions (web.php, admin.php, shop.php, profile.php)
- `views/` - PHP templates with layout system
- `content/` - YAML-based content management for pages
- `console/` - CLI scripts for maintenance tasks
- `public/` - Web root with assets and entry point
- `cards/` - Card-specific functionality and queries
- `mail/` - Email functionality using PHPMailer

### Routing System
- Custom router in `core/router.php` with parameter binding
- Route definitions use `get()` and `post()` functions
- Pattern-based routing with middleware support: `get('/user/{id}', $callback, $middleware)`
- Automatic route loading via `routes/autoload.php`

### View System  
- Template engine in `core/helpers.php`
- Layout inheritance with section management
- Views use `view($template, $data, $layout)` function
- Supports partials and content sections

### Content Management
- YAML-based page system in `content/pages/`
- Hierarchical page structure with slug-based routing
- Pages support custom views and layouts
- Page tree built dynamically from YAML files

### User System
- Session-based authentication in `core/session.php`
- User management in `core/user.php`
- Admin functionality in `core/admin.php`
- Role-based access control

### Card & Product System
- **Card Data**: Imported from game developers' API via `console/import_cards.php`
- **Card Schema**: Comprehensive SQLite schema with editions, elements, effects, costs, etc.
- **Products**: Site admins create sellable products based on card/edition combinations
- **Inventory Management**: Products have stock levels and pricing separate from card data
- **Search & Discovery**: Card queries and search in `cards/query.php`
- **Shop Functionality**: Core shopping logic in `core/products.php` and `core/shop.php`

### HTMX Integration
- **Frontend Framework**: Heavily relies on HTMX for dynamic interactions
- **HTMX Script**: `public/js/htmx.min.js` loaded in layouts
- **Body Attributes**: `hx-ext="preload"` enabled globally in default layout
- **Dialog System**: Custom dialog.js handles modal interactions with HTMX
- **Toast Notifications**: JavaScript toast system triggered by HTMX responses
- **Out-of-Band Swaps**: Used for cart badge updates (`hx-swap-oob="true"`)
- **Partial Updates**: Extensive use of partials for HTMX target swapping

### HTMX Patterns Used
- **Search with Debounce**: `hx-trigger="keyup changed delay:200ms"` for live search
- **Form Submissions**: `hx-post` for login, register, cart operations
- **Modal Loading**: `hx-get` to load content into `#dialog` target
- **Cart Management**: Dynamic cart updates without page refresh
- **Product Management**: Admin CRUD operations via HTMX
- **Image Loading**: Lazy loading for card images

### Configuration
- Environment variables in `.env` file
- Debug mode controls error reporting
- Path constants defined in `bootstrap.php`

## Development Notes

### Database Schema
SQLite database with foreign key support enabled. All database queries use prepared statements via PDO.

#### Card Data Structure
- **`cards`**: Core card information (name, element, effects, costs, stats)
- **`sets`**: Card sets with prefix and language
- **`editions`**: Specific printings of cards (collector number, rarity, illustrator)
- **`types`/`card_types`**: Card types (many-to-many relationship)
- **`subtypes`/`card_subtypes`**: Card subtypes (many-to-many relationship) 
- **`classes`/`card_classes`**: Card classes (many-to-many relationship)

#### E-commerce Structure
- **`products`**: Sellable items linked to editions or custom products
  - `edition_id` (NULL for custom products)
  - `price`, `quantity`, `description`, `is_foil`
- **`users`**: Customer accounts
- **`admin_users`**: Admin accounts (separate from regular users)
- **`carts`/`cart_items`**: Shopping cart system
- **`orders`/`order_items`**: Order management with status tracking

#### Key Relationships
- Cards → Editions (1:many) - One card can have multiple printings
- Editions → Products (1:many) - One edition can have multiple product variants (foil/non-foil, different conditions)
- Users → Carts → Cart Items → Products
- Users → Orders → Order Items → Products

### Assets
- Tailwind CSS compilation required for styling changes
- Custom CSS in `public/css/style.css`
- JavaScript in `public/js/`
- Static assets in `public/assets/`

### Email
- PHPMailer integration for email functionality
- Configuration in `mail/mailer.php`

### Security
- CSRF protection available
- Session management with flash messages
- Input validation and sanitization
- Prepared statements for database queries

## Development Roadmap

### Planned Features
- **Enhanced Admin Dashboard**: 
  - Order management and tracking
  - Sales metrics and analytics
  - Improved stock overview and product management
  - Streamlined product creation workflow
- **Product Detail Pages**: 
  - Individual card/product pages
  - Order history for specific cards
  - Edition-based filtering for card variants
- **E-commerce Features**:
  - Complete cart and checkout process
  - Payment gateway integration
  - Order confirmation and tracking

### Current State
- ✅ Card data import from developer API
- ✅ Basic product management (admin can create products from cards)
- ✅ User authentication and profiles
- ✅ Shopping cart with HTMX interactions
- ✅ Search and discovery features
- 🚧 Admin dashboard (basic, needs enhancement)
- ❌ Product detail pages
- ❌ Order management system
- ❌ Payment processing
- ❌ Sales analytics