# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Cardpoint is an e-commerce web application for selling Trading Card Game (TCG) cards, primarily focused on Grand Archive TCG. The platform serves as an online store where Cardpoint company sells physical TCG cards to customers.

### Database & Console
- **Import Cards**: `php console/import_cards.php` - Import cards from external API
- **Create Admin User**: `php console/create_admin_user.php` - Create admin user account
- **Create User**: `php console/create_user.php` - Create regular user account
- **Database Schema**: `alters.sql` contains the SQLite schema

### Dependencies
- **Install**: `composer install` - Install PHP dependencies (PHPMailer)

## Styling Guidelines

### CSS Framework
- **NO TAILWIND CSS** - The project has moved away from Tailwind CSS
- **Pure CSS Only** - Use custom CSS in `public/css/style.css` and `public/css/default.css`
- **Existing Class Reuse** - Always check and reuse existing CSS classes before creating new ones

### Button System
- **Base class**: `.btn` (flex layout, padding, border-radius, transitions)
- **Color variants**: `.btn.blue`, `.btn.black`, `.btn.red`
- **Size variants**: `.btn-small`, `.btn-full`
- **Style variants**: `.btn-outline`, `.btn-disabled`, `.btn-text`

### Layout Classes
- **Containers**: `.product-container`, `.product-section`, `.purchase-card`
- **Typography**: `.product-title`, `.section-title`, `.section-subtitle`, `.meta-label`
- **Cards**: `.product-section` (dark background, border, padding, border-radius)

### Design System
- **Colors**: Dark theme (`#07070A` body, `#1E1E27` cards, `#01AFFC` primary)
- **Border Radius**: Consistent `12px` throughout
- **Borders**: `#C0C0D133` for subtle borders
- **Transitions**: `all 0.2s ease` for hover effects
- **Shadows**: `0 4px 10px rgba(255, 255, 255, 0.1)` for hover states

### Component Patterns
- **Action Items**: Icon + content + arrow pattern
- **Stats Cards**: Icon + number + label structure  
- **Modal System**: Overlay + modal + header + content structure
- **Form Layout**: Label + input + help text + actions

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
- Separate admin user accounts (`admin_users` table) with dedicated login system
- Admin authentication middleware for protected routes

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

### Remaining Features to Implement
- **Payment Gateway Integration**:
  - Stripe or other payment processor integration
  - Real payment processing (currently in demo mode)
  - Payment webhooks and confirmation handling
- **Email System Enhancement**:
  - SMTP configuration for email notifications
  - Order confirmation emails
  - Admin notification emails for new orders
  - Password reset emails
- **Additional Admin Features**:
  - Bulk product operations
  - Advanced inventory management
  - Customer management dashboard
  - Detailed reporting and export functionality

### Current State
- ✅ Card data import from developer API
- ✅ Basic product management (admin can create products from cards)
- ✅ User authentication and profiles
- ✅ Shopping cart with HTMX interactions
- ✅ Search and discovery features
- ✅ **Enhanced Admin Dashboard** with comprehensive features:
  - Store overview with real-time statistics (products, orders, revenue)
  - Complete product management (CRUD operations, stock tracking)
  - Order management system with status tracking
  - Sales analytics and reporting dashboard
  - Store settings and configuration
  - Database backup and card import tools
- ✅ **Product Detail Pages** - Individual card/product pages with:
  - Product images and metadata
  - Order history for specific products
  - Card variants and edition filtering
  - Purchase functionality integrated with cart
- ✅ **Complete E-commerce System**:
  - Full cart and checkout process
  - Order creation and management
  - Shipping address collection
  - Order status tracking (pending, processing, shipped, delivered, cancelled)
  - Demo payment processing (ready for payment gateway integration)
- ❌ **Payment Gateway Integration** - Currently using demo/simulation mode
- ❌ **Email Notifications** - System ready but needs SMTP configuration