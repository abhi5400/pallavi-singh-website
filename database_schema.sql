-- Pallavi Singh Coaching Website Database Schema
-- Created for storing form submissions and user data

-- Create database
CREATE DATABASE IF NOT EXISTS pallavi_coaching_db;
USE pallavi_coaching_db;

-- Contact Form Submissions Table
CREATE TABLE contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    service_interest ENUM('coaching', 'habit-mastery', 'anxiety', 'relationships', 'storytelling', 'public-speaking') NULL,
    message TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('new', 'contacted', 'in_progress', 'completed', 'closed') DEFAULT 'new',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Book Now Form Submissions Table
CREATE TABLE booking_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL,
    service_type ENUM('coaching', 'habit-mastery', 'anxiety', 'relationships', 'storytelling', 'public-speaking') NOT NULL,
    session_type ENUM('discovery', 'individual', 'package-3', 'package-6', 'intensive') NOT NULL,
    preferred_date DATE NULL,
    preferred_time ENUM('morning', 'afternoon', 'evening', 'weekend') NULL,
    timezone VARCHAR(50) NULL,
    goals TEXT NOT NULL,
    experience_level ENUM('none', 'some', 'experienced', 'professional') NULL,
    additional_notes TEXT NULL,
    terms_accepted BOOLEAN NOT NULL DEFAULT FALSE,
    newsletter_subscription BOOLEAN DEFAULT FALSE,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('pending', 'scheduled', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    scheduled_date DATETIME NULL,
    session_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Journey Form Submissions Table
CREATE TABLE journey_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    city VARCHAR(100) NOT NULL,
    issue_challenge TEXT NULL,
    terms_accepted BOOLEAN NOT NULL DEFAULT FALSE,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('new', 'contacted', 'converted', 'closed') DEFAULT 'new',
    follow_up_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Newsletter Subscriptions Table
CREATE TABLE newsletter_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    first_name VARCHAR(50) NULL,
    last_name VARCHAR(50) NULL,
    source VARCHAR(100) NULL, -- Which form they subscribed from
    subscription_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    status ENUM('active', 'unsubscribed', 'bounced') DEFAULT 'active',
    unsubscribe_token VARCHAR(100) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Website Analytics Table (for tracking form interactions)
CREATE TABLE form_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_type ENUM('contact', 'booking', 'journey', 'newsletter') NOT NULL,
    action_type ENUM('view', 'start', 'submit', 'error') NOT NULL,
    user_ip VARCHAR(45),
    user_agent TEXT,
    referrer VARCHAR(500),
    form_data JSON NULL, -- Store partial form data for analytics
    error_message TEXT NULL,
    session_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Users Table (for managing submissions)
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'moderator', 'viewer') DEFAULT 'viewer',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Email Templates Table (for automated responses)
CREATE TABLE email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(100) NOT NULL UNIQUE,
    subject VARCHAR(200) NOT NULL,
    body_html TEXT NOT NULL,
    body_text TEXT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123 - change this!)
INSERT INTO admin_users (username, email, password_hash, full_name, role) 
VALUES ('admin', 'admin@pallavi-coaching.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pallavi Singh', 'admin');

-- Insert default email templates
INSERT INTO email_templates (template_name, subject, body_html, body_text) VALUES
('contact_auto_reply', 'Thank you for contacting Pallavi Singh', 
'<h2>Thank you for reaching out!</h2><p>Dear {{name}},</p><p>Thank you for contacting me. I have received your message and will get back to you within 24 hours.</p><p>Best regards,<br>Pallavi Singh</p>',
'Thank you for reaching out!\n\nDear {{name}},\n\nThank you for contacting me. I have received your message and will get back to you within 24 hours.\n\nBest regards,\nPallavi Singh'),

('booking_confirmation', 'Your coaching session has been booked', 
'<h2>Session Booked Successfully!</h2><p>Dear {{first_name}},</p><p>Your {{session_type}} session has been booked. We will contact you soon to confirm the details.</p><p>Best regards,<br>Pallavi Singh</p>',
'Session Booked Successfully!\n\nDear {{first_name}},\n\nYour {{session_type}} session has been booked. We will contact you soon to confirm the details.\n\nBest regards,\nPallavi Singh'),

('journey_welcome', 'Welcome to your transformation journey', 
'<h2>Welcome to Your Journey!</h2><p>Dear {{name}},</p><p>Thank you for taking the first step towards transformation. I will be in touch soon to guide you on your journey.</p><p>Best regards,<br>Pallavi Singh</p>',
'Welcome to Your Journey!\n\nDear {{name}},\n\nThank you for taking the first step towards transformation. I will be in touch soon to guide you on your journey.\n\nBest regards,\nPallavi Singh');

-- Create indexes for better performance
CREATE INDEX idx_contact_email ON contact_submissions(email);
CREATE INDEX idx_contact_date ON contact_submissions(submission_date);
CREATE INDEX idx_contact_status ON contact_submissions(status);

CREATE INDEX idx_booking_email ON booking_submissions(email);
CREATE INDEX idx_booking_date ON booking_submissions(submission_date);
CREATE INDEX idx_booking_status ON booking_submissions(status);
CREATE INDEX idx_booking_service ON booking_submissions(service_type);

CREATE INDEX idx_journey_email ON journey_submissions(name);
CREATE INDEX idx_journey_date ON journey_submissions(submission_date);
CREATE INDEX idx_journey_status ON journey_submissions(status);

CREATE INDEX idx_newsletter_email ON newsletter_subscriptions(email);
CREATE INDEX idx_newsletter_status ON newsletter_subscriptions(status);

CREATE INDEX idx_analytics_form ON form_analytics(form_type);
CREATE INDEX idx_analytics_date ON form_analytics(created_at);

