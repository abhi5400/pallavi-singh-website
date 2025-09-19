# Pallavi Singh Coaching - Database Setup Guide

This guide will help you set up a database to store form submissions from the Pallavi Singh coaching website.

## 🗄️ Database Overview

The database stores data from three main forms:
- **Contact Form**: General inquiries and messages
- **Booking Form**: Session booking requests with detailed information
- **Journey Form**: Initial signup for transformation journey

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB 10.2+)
- Web server (Apache/Nginx)
- PDO MySQL extension enabled

## 🚀 Quick Setup

### Step 1: Update Database Credentials
Edit the database credentials in `setup.php`:
```php
$db_host = 'localhost';
$db_name = 'pallavi_coaching_db';
$db_user = 'your_username';
$db_pass = 'your_password';
```

### Step 2: Run Setup Script
1. Upload all files to your web server
2. Visit `https://your-domain.com/setup.php` in your browser
3. Follow the on-screen instructions

### Step 3: Configure Email Settings
Edit `config/database.php` and update:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('FROM_EMAIL', 'noreply@your-domain.com');
define('SITE_URL', 'https://your-domain.com');
```

## 📊 Database Structure

### Tables Created:
- `contact_submissions` - Contact form data
- `booking_submissions` - Booking form data
- `journey_submissions` - Journey form data
- `newsletter_subscriptions` - Email subscriptions
- `form_analytics` - Form interaction tracking
- `admin_users` - Admin panel users
- `email_templates` - Email templates

### Default Admin Account:
- **Username**: `admin`
- **Password**: `admin123` ⚠️ **CHANGE THIS IMMEDIATELY!**

## 🔧 Manual Setup (Alternative)

If you prefer to set up manually:

1. Create a MySQL database named `pallavi_coaching_db`
2. Import the SQL schema:
   ```bash
   mysql -u username -p pallavi_coaching_db < database_schema.sql
   ```
3. Update `config/database.php` with your credentials
4. Test the connection

## 📝 Form Handlers

The following PHP files handle form submissions:

- `handlers/contact_handler.php` - Processes contact form
- `handlers/booking_handler.php` - Processes booking form
- `handlers/journey_handler.php` - Processes journey form

## 🎛️ Admin Panel

Access the admin panel at `/admin/` to:
- View all form submissions
- See submission statistics
- Manage submission statuses
- Export data

## 📧 Email Features

The system includes:
- **Auto-reply emails** to users after form submission
- **Admin notifications** for new submissions
- **Email templates** that can be customized
- **Newsletter subscription** management

## 🔒 Security Features

- **Input sanitization** and validation
- **SQL injection protection** using prepared statements
- **CSRF protection** (implement as needed)
- **IP address logging** for security
- **Error logging** for debugging

## 📈 Analytics

The system tracks:
- Form views and interactions
- Submission success/failure rates
- User behavior patterns
- Error tracking

## 🛠️ Customization

### Adding New Form Fields
1. Update the database schema
2. Modify the form handler
3. Update validation rules
4. Adjust email templates

### Custom Email Templates
Edit templates in the `email_templates` table or modify the handlers to use custom templates.

## 🔍 Troubleshooting

### Common Issues:

**Database Connection Failed**
- Check database credentials
- Ensure MySQL service is running
- Verify database exists

**Forms Not Submitting**
- Check file permissions
- Verify PHP error logs
- Test database connection

**Emails Not Sending**
- Update SMTP settings
- Check email server configuration
- Verify FROM_EMAIL domain

### Debug Mode
Enable error reporting in `config/database.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📋 Maintenance

### Regular Tasks:
- **Backup database** regularly
- **Monitor error logs**
- **Update admin passwords**
- **Clean old analytics data**
- **Review form submissions**

### Database Backup:
```bash
mysqldump -u username -p pallavi_coaching_db > backup_$(date +%Y%m%d).sql
```

## 🆘 Support

For technical support:
1. Check error logs in your web server
2. Verify database connectivity
3. Test form handlers individually
4. Review PHP error reporting

## 📄 File Structure

```
/
├── config/
│   └── database.php          # Database configuration
├── handlers/
│   ├── contact_handler.php   # Contact form handler
│   ├── booking_handler.php   # Booking form handler
│   └── journey_handler.php   # Journey form handler
├── admin/
│   └── index.php            # Admin dashboard
├── database_schema.sql      # Database schema
├── setup.php               # Setup script
└── DATABASE_README.md      # This file
```

## ⚠️ Important Notes

1. **Change default passwords** immediately after setup
2. **Use HTTPS** for production environments
3. **Regular backups** are essential
4. **Keep software updated** for security
5. **Monitor logs** for any issues

---

**Need Help?** Check the troubleshooting section or review the error logs for specific issues.

