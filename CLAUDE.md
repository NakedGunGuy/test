# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Cardpoint is a modern e-commerce platform for Trading Card Game (TCG) cards, specifically focused on Grand Archive TCG. Built with PHP and HTMX, it provides a seamless shopping experience for physical TCG cards with comprehensive admin management.

### Core Stack
- **Backend**: Custom PHP MVC framework (lightweight, no external dependencies)
- **Frontend**: HTMX for dynamic interactions, pure CSS for styling
- **Database**: SQLite with PDO (prepared statements)
- **Assets**: Vanilla JavaScript for enhanced UX
- **Email**: PHPMailer for notifications

### Console Commands
- **Import Cards**: `php console/import_cards.php` - Import card data from developer API
- **Create Admin User**: `php console/create_admin_user.php` - Create admin user account
- **Create User**: `php console/create_user.php` - Create regular user account
- **Cache Images**: `php console/cache_card_images.php` - Pre-cache card images
- **Send Emails**: `php console/send_emails.php` - Process email queue
- **Generate Sitemap**: `php console/generate_sitemap.php` - Generate XML sitemap for SEO
- **Database Schema**: `alters.sql` contains the complete SQLite schema

### Dependencies
- **Install**: `composer install` - Install PHP dependencies (PHPMailer only)

## Design System & Styling

### CSS Architecture
- **No Framework**: Pure CSS only - NO TAILWIND CSS
- **Main Stylesheet**: `public/css/default.css` - All styles consolidated here
- **Design Philosophy**: Dark theme with modern gradients and clean typography
- **Class Reuse**: Always check existing classes before creating new ones

### Color Palette
- **Background**: Gradient from `#000000` to `#0a0a0a`
- **Cards/Sections**: `rgba(0, 174, 239, 0.05)` with gradient overlays
- **Primary**: `#00AEEF` (Blue gradient from `#00AEEF` to `#0098d4`)
- **Text**: `#FFFFFF` primary, inherited colors
- **Borders**: `rgba(0, 174, 239, 0.2)` for main borders, `rgba(0, 174, 239, 0.3)` for headers

### Typography & Layout
- **Font Stack**: `-apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, 'Helvetica Neue', Arial, sans-serif`
- **Border Radius**: Consistent `12px` for buttons, `16px` for cards
- **Spacing**: CSS Grid and Flexbox for layouts
- **Transitions**: `all 0.2s ease` for hover effects
- **Shadows**: `0 8px 32px rgba(0, 0, 0, 0.6)` for main containers

### Component System
- **Buttons**: `.btn` base class with variants (`.btn.blue`, `.btn.black`, `.btn.red`)
- **Navigation**: Fixed sidebar navigation with active states
- **Cards**: Gradient backgrounds with subtle borders
- **Forms**: Consistent input styling with validation states
- **Mobile**: Responsive bottom navigation for mobile devices

### Layout Architecture
- **Sidebar Navigation**: Fixed 300px width with brand and menu items
- **Main Content**: Gradient background container with header and main sections
- **Mobile Navigation**: Bottom navigation bar for mobile screens
- **Dialog System**: Modal overlays using native `<dialog>` element

## Technical Architecture

### Framework Structure
- **Entry Point**: `public/index.php` - Handles all HTTP requests
- **Bootstrap**: `bootstrap.php` - Constants, environment, compression
- **Autoloading**: `core/autoload.php` and `routes/autoload.php`
- **Database**: SQLite in `database/database.sqlite`

### Directory Organization
```
├── core/           # Framework components
│   ├── router.php      # Custom routing system
│   ├── database.php    # PDO database layer
│   ├── helpers.php     # Template engine & utilities
│   ├── session.php     # Session management
│   ├── user.php        # User authentication
│   ├── admin.php       # Admin authentication
│   ├── products.php    # Product management
│   ├── shop.php        # Shopping cart logic
│   └── settings.php    # Application settings
├── routes/         # Route definitions
│   ├── web.php         # Public routes
│   ├── admin.php       # Admin panel routes
│   ├── shop.php        # Shopping/cart routes
│   └── profile.php     # User profile routes
├── views/          # PHP templates
│   ├── layouts/        # Layout templates
│   ├── partials/       # Reusable components
│   ├── admin/          # Admin panel views
│   ├── shop/           # Shopping views
│   └── profile/        # User profile views
├── content/        # YAML-based CMS
├── console/        # CLI scripts
├── public/         # Web root
│   ├── css/           # Stylesheets
│   ├── js/            # JavaScript files
│   └── assets/        # Static files
└── cards/          # Card-specific functionality
```

### Routing System
- **Pattern Matching**: `get('/user/{id}', $callback, $middleware)`
- **Parameter Binding**: Automatic parameter extraction
- **Middleware Support**: Authentication and authorization
- **YAML Pages**: Dynamic page routing from `content/pages/`

### Template Engine
- **Function**: `view($template, $data, $layout)`
- **Sections**: `start_section()` and `end_section()` for content blocks
- **Partials**: `partial($template, $data)` for reusable components
- **Layouts**: Layout inheritance system

### Authentication System
- **User Auth**: Session-based with `core/user.php`
- **Admin Auth**: Separate admin accounts with `core/admin.php`
- **Middleware**: `require_user_auth()` and `require_admin_auth()`
- **Security**: Login attempt limiting, secure sessions

## HTMX Integration

### Core Patterns
- **Dynamic Updates**: `hx-get`, `hx-post` for seamless interactions
- **Target Swapping**: `hx-target` for updating specific page sections
- **Out-of-Band**: `hx-swap-oob="true"` for multi-target updates
- **Preloading**: Global `hx-ext="preload"` for better UX
- **Debounced Search**: `hx-trigger="keyup changed delay:200ms"`

### JavaScript Integration
- **HTMX Core**: `public/js/htmx.min.js`
- **Dialog System**: `public/js/dialog.js` - Modal management
- **Image Handling**: `public/js/images.js` - Lazy loading
- **Quantity Controls**: `public/js/quantity.js` - Cart interactions
- **General Utils**: `public/js/general.js` - Utility functions

### HTMX Response Patterns
- **Multi-target Updates**: Cart badge + product list + purchase section
- **Error Handling**: HTTP status codes with user-friendly messages
- **Redirects**: `HX-Redirect` header for navigation
- **Partial Rendering**: Targeted component updates

## Database Schema

### Card Data Structure
```sql
cards           # Core card information (name, element, effects, stats)
├── sets        # Card sets with prefix and language
├── editions    # Specific printings (collector number, rarity, illustrator)
├── types       # Card types (many-to-many via card_types)
├── subtypes    # Card subtypes (many-to-many via card_subtypes)
└── classes     # Card classes (many-to-many via card_classes)
```

### E-commerce Structure
```sql
products        # Sellable items (linked to editions or custom)
├── users       # Customer accounts
├── admin_users # Admin accounts (separate system)
├── carts/cart_items    # Shopping cart system
├── orders/order_items  # Order management with shipping
└── settings    # Application configuration
```

### Key Relationships
- **Cards → Editions** (1:many) - Multiple printings per card
- **Editions → Products** (1:many) - Multiple variants (foil/condition)
- **Users → Carts → Cart Items → Products** - Shopping flow
- **Users → Orders → Order Items → Products** - Purchase history

## Feature Completeness

### ✅ Fully Implemented
- **Card Data Import**: Complete API integration with Grand Archive
- **User Management**: Registration, authentication, profiles
- **Admin Dashboard**: Comprehensive management interface with analytics
- **Product Management**: Full CRUD with stock tracking
- **Shopping System**: Cart, checkout, order management
- **Search & Discovery**: Advanced filtering and search
- **Order Processing**: Status tracking from pending to delivered
- **Responsive Design**: Mobile-optimized with bottom navigation
- **Image Management**: Lazy loading and caching system
- **Security**: CSRF protection, input validation, secure sessions

### ⚠️ Partially Implemented
- **Payment Processing**: Stripe integration ready, currently in demo mode
- **Email Notifications**: PHPMailer configured, needs SMTP setup
- **SEO Optimization**: Basic meta tags, needs comprehensive SEO implementation

### ❌ Missing Features
- **Sitemap Generation**: No XML sitemap for search engines
- **Meta Tag Management**: Missing product-specific and dynamic meta tags
- **Schema Markup**: No structured data for rich snippets
- **Social Media Integration**: Missing Open Graph and Twitter Card tags
- **Canonical URLs**: No canonical URL management
- **Robots.txt**: Missing search engine directives
- **Performance Monitoring**: No analytics or performance tracking
- **Bulk Operations**: Limited bulk product management tools
- **Advanced Reporting**: Basic analytics, needs detailed reporting
- **Customer Support**: No integrated support ticket system

## SEO Implementation Status

### Current SEO Issues
- **No XML Sitemap**: Search engines cannot efficiently crawl the site
- **Generic Meta Tags**: All pages use same title/description from APP_NAME
- **Missing Schema Markup**: No structured data for products
- **No Canonical URLs**: Risk of duplicate content issues
- **Missing robots.txt**: No search engine crawler guidance
- **No Social Meta Tags**: Poor social media sharing experience

### Recommended SEO Improvements
1. **Generate XML Sitemap**: Include all products, categories, and static pages
2. **Dynamic Meta Tags**: Product-specific titles, descriptions, and images
3. **Schema Markup**: Product, Organization, and BreadcrumbList schemas
4. **Canonical URLs**: Prevent duplicate content across product variants
5. **robots.txt**: Guide search engine crawling priorities
6. **Open Graph Tags**: Improve social media sharing
7. **Performance Optimization**: Image compression, caching headers
8. **Internal Linking**: Improve site architecture for SEO

## Development Commands

### Environment Setup
- **Install Dependencies**: `composer install`
- **Create Admin**: `php console/create_admin_user.php`
- **Import Cards**: `php console/import_cards.php`
- **Cache Images**: `php console/cache_card_images.php`

### Database Management
- **Schema File**: `alters.sql` - Complete SQLite schema
- **Backup**: Admin panel includes database backup functionality
- **Foreign Keys**: Enabled with cascade relationships

### Security Features
- **Session Management**: Secure session handling with flash messages
- **Input Validation**: Server-side validation with prepared statements
- **CSRF Protection**: Available but needs implementation
- **Rate Limiting**: Login attempt limiting by IP
- **SQL Injection Protection**: All queries use prepared statements

## Configuration

### Environment Variables (.env)
```
APP_NAME="Cardpoint"
DEBUG=true
APP_URL=                    # Required for Stripe integration
STRIPE_SECRET_KEY=          # Payment processing
STRIPE_PUBLISHABLE_KEY=     # Frontend payments
SMTP_HOST=                  # Email configuration
SMTP_PORT=                  # Email configuration
SMTP_USERNAME=              # Email configuration
SMTP_PASSWORD=              # Email configuration
```

### Performance Features
- **GZIP Compression**: Automatic response compression
- **Image Caching**: Card image caching system
- **Session Optimization**: Efficient session management
- **Database Optimization**: Indexed queries and foreign keys