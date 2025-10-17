# MySQL Database Setup Guide

## 🚀 Quick Setup Steps

### 1. **Access phpMyAdmin**
- Open your browser
- Go to: `http://localhost/phpmyadmin/`
- Login with:
  - **Username:** `root`
  - **Password:** (usually empty)

### 2. **Create Database**
- Click "New" in the left sidebar
- Database name: `pallavi_singh`
- Collation: `utf8mb4_unicode_ci`
- Click "Create"

### 3. **Import Database Structure**
- Select the `pallavi_singh` database
- Click "Import" tab
- Choose file: `database_setup.sql`
- Click "Go"

### 4. **Migrate Your Data**
Run this command in your project directory:
```bash
php migrate_data_to_mysql.php
```

### 5. **Verify Setup**
- Go to: `http://localhost/phpmyadmin/index.php?route=/database/structure&db=pallavi_singh`
- You should see all your tables with data

## 📊 Database Tables Created

- `admin_users` - Admin accounts
- `contact_submissions` - Contact form data
- `join_submissions` - Registration form data
- `newsletter_subscriptions` - Email signups
- `waitlist_subscriptions` - Waitlist data
- `blog_posts` - Blog content
- `clients` - Client information
- `services` - Service offerings
- `events_workshops` - Events and workshops
- `testimonials` - Client testimonials
- `sessions` - Coaching sessions
- `payments` - Payment records
- `media_library` - Media files
- `analytics` - Site analytics
- `email_log` - Email logs
- `booking_submissions` - Booking forms
- `journey_submissions` - Journey forms

## 🔧 Configuration Update

After migration, your application will automatically use MySQL instead of JSON files.

## 🎯 Admin Panel Access

- **URL:** `http://localhost:8000/admin/login.php`
- **Username:** `admin`
- **Password:** (check the hashed password in admin_users table)

## ✅ Verification

1. Check phpMyAdmin shows your data
2. Test admin panel login
3. Verify forms are saving to MySQL
4. Check that JSON files are no longer being updated

## 🆘 Troubleshooting

- **MySQL not running:** Start XAMPP/WAMP services
- **Connection failed:** Check username/password in config
- **Tables not created:** Run the SQL file manually
- **Data not migrated:** Check JSON file paths and permissions