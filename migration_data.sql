-- Data Migration SQL for pallavi_singh database
-- Run this in phpMyAdmin SQL tab

-- Migrating data to admin_users table
DELETE FROM `admin_users`;
INSERT INTO `admin_users` (username, email, password_hash, full_name, role, is_active, last_login, id, created_at, updated_at) VALUES ('admin', 'admin@pallavi-coaching.com', '$2y$12$vom31xY.ODU4f147er38VeZDIoTHJWGGPzBgqupwFDO5cIq3DP4Jq', 'Pallavi Singh', 'admin', TRUE, '2025-09-27 10:49:28', 1, '2025-09-12 13:04:04', '2025-09-27 10:49:28');

-- Migrating data to contact_submissions table
DELETE FROM `contact_submissions`;
INSERT INTO `contact_submissions` (name, email, service_interest, message, submission_date, ip_address, user_agent, status, notes, id, created_at, updated_at) VALUES ('John Doe', 'john@example.com', 'coaching', 'I am interested in life coaching sessions.', '2025-09-12 04:47:50', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'new', NULL, 1, '2025-09-14 04:47:50', '2025-09-14 04:47:50');
INSERT INTO `contact_submissions` (name, email, service_interest, message, submission_date, ip_address, user_agent, status, notes, id, created_at, updated_at) VALUES ('Jane Smith', 'jane@example.com', 'anxiety', 'I need help with anxiety management.', '2025-09-13 04:47:50', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'contacted', 'Initial contact made', 2, '2025-09-14 04:47:50', '2025-09-14 04:47:50');
INSERT INTO `contact_submissions` (name, email, phone, subject, message, submission_date, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('Test Contact User', 'testcontact@example.com', '9876543210', 'general-inquiry', 'This is a test message for the contact form.', '2025-09-27 10:48:21', '127.0.0.1', 'Test Agent', 'new', 3, '2025-09-27 10:48:21', '2025-09-27 10:48:21');
INSERT INTO `contact_submissions` (name, email, subject, message, submission_date, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('Quick Test Contact', 'quickcontact@example.com', 'general-inquiry', 'Quick test message', '2025-09-27 10:48:55', '127.0.0.1', 'Quick Test', 'new', 4, '2025-09-27 10:48:55', '2025-09-27 10:48:55');
INSERT INTO `contact_submissions` (name, email, phone, subject, message, submission_date, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('Test User', 'test@example.com', '1234567890', 'Test Subject', 'Test message for debugging', '2025-09-29 06:24:45', '127.0.0.1', 'Test Agent', 'new', 5, '2025-09-29 06:24:45', '2025-09-29 06:24:45');

-- Migrating data to join_submissions table
DELETE FROM `join_submissions`;
INSERT INTO `join_submissions` (form_id, full_name, age, city, state, contact_number, email, issue_challenge, goals, terms_accepted, newsletter_subscription, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('TEST-JOIN-20250927-59DE72', 'Test Join User', 25, 'Test City', 'Test State', '1234567890', 'testjoin@example.com', 'This is a test challenge for the join form.', 'Test goals for the join form.', 1, 1, '127.0.0.1', 'Test Agent', 'new', 1, '2025-09-27 10:48:21', '2025-09-27 10:48:21');
INSERT INTO `join_submissions` (form_id, full_name, city, state, contact_number, email, issue_challenge, terms_accepted, newsletter_subscription, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('QUICK-TEST-1758970135', 'Quick Test User', 'Test City', 'Test State', '1234567890', 'quicktest@example.com', 'Quick test challenge', 1, 0, '127.0.0.1', 'Quick Test', 'new', 2, '2025-09-27 10:48:55', '2025-09-27 10:48:55');
INSERT INTO `join_submissions` (form_id, full_name, age, city, state, contact_number, email, issue_challenge, goals, terms_accepted, newsletter_subscription, submission_date, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('TEST-20250929-DAB7BA', 'Test User', 25, 'Test City', 'Test State', '1234567890', 'test@example.com', 'Test challenge', 'Test goals', TRUE, FALSE, '2025-09-29 06:24:45', '127.0.0.1', 'Test Agent', 'new', 3, '2025-09-29 06:24:45', '2025-09-29 06:24:45');
INSERT INTO `join_submissions` (form_id, full_name, age, city, state, contact_number, email, issue_challenge, goals, terms_accepted, newsletter_subscription, submission_date, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('TEST-20250929-ACA6BE', 'Test User', 25, 'Test City', 'Test State', '1234567890', 'test@example.com', 'Test challenge', 'Test goals', TRUE, FALSE, '2025-09-29 06:26:02', '127.0.0.1', 'Test Agent', 'new', 4, '2025-09-29 06:26:02', '2025-09-29 06:26:02');
INSERT INTO `join_submissions` (form_id, full_name, age, city, state, contact_number, email, issue_challenge, goals, terms_accepted, newsletter_subscription, submission_date, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('FIX-TEST-20250929062709', 'Fix Test User', 25, 'Test City', 'Test State', '1234567890', 'fixtest@example.com', 'Testing form fix', 'Test goals', TRUE, FALSE, '2025-09-29 06:27:09', '127.0.0.1', 'Fix Test Agent', 'new', 5, '2025-09-29 06:27:09', '2025-09-29 06:27:09');
INSERT INTO `join_submissions` (form_id, full_name, age, city, state, contact_number, email, issue_challenge, goals, terms_accepted, newsletter_subscription, submission_date, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('JOIN-20250929-211C1B', 'Abhijeet Jha', NULL, 'Jaipur', 'Rajasthan', '07891307864', 'abhijeetjha5400@gmail.com', 'asdfghjklkjhgfdsaASDFGHJKL', '', TRUE, TRUE, '2025-09-29 06:30:58', '0.0.0.0', '', 'new', 6, '2025-09-29 06:30:58', '2025-09-29 06:30:58');
INSERT INTO `join_submissions` (form_id, full_name, age, city, state, contact_number, email, issue_challenge, goals, terms_accepted, newsletter_subscription, submission_date, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('JOIN-20250929-521A11', 'Test User', 25, 'Test City', 'Test State', '1234567890', 'test@example.com', 'Test challenge', 'Test goals', TRUE, TRUE, '2025-09-29 06:31:33', '0.0.0.0', '', 'new', 7, '2025-09-29 06:31:33', '2025-09-29 06:31:33');

-- Migrating data to newsletter_subscriptions table
DELETE FROM `newsletter_subscriptions`;
INSERT INTO `newsletter_subscriptions` (email, first_name, last_name, source, subscription_date, ip_address, status, unsubscribe_token, id, created_at, updated_at) VALUES ('subscriber1@example.com', 'Sarah', 'Davis', 'contact_form', '2025-08-29 13:04:04', '127.0.0.1', 'active', 'b02620ce28397640a7a65e34380cbddd983fa8f120a31df2a8c877b1b10aa565', 1, '2025-09-12 13:04:04', '2025-09-12 13:04:04');
INSERT INTO `newsletter_subscriptions` (email, first_name, last_name, source, subscription_date, ip_address, status, unsubscribe_token, id, created_at, updated_at) VALUES ('subscriber2@example.com', 'Mike', 'Brown', 'booking_form', '2025-09-05 13:04:04', '127.0.0.1', 'active', 'c9684704f4da95c783de5beadd3a5e65e3952850d570256a3a86aaf5efd6147a', 2, '2025-09-12 13:04:04', '2025-09-12 13:04:04');
INSERT INTO `newsletter_subscriptions` (email, first_name, last_name, source, submission_date, ip_address, status, unsubscribe_token, id, created_at, updated_at) VALUES ('testnewsletter@example.com', 'Test', 'Newsletter', 'website', '2025-09-27 10:48:21', '127.0.0.1', 'active', '27af47f3f01a8ba19da3da888dbbda99c56ae6ad4578965178ab22ca34a78926', 3, '2025-09-27 10:48:21', '2025-09-27 10:48:21');
INSERT INTO `newsletter_subscriptions` (email, first_name, last_name, source, submission_date, ip_address, status, unsubscribe_token, id, created_at, updated_at) VALUES ('quicknewsletter@example.com', 'Quick', 'Test', 'website', '2025-09-27 10:48:55', '127.0.0.1', 'active', 'a0f9be17b703f60614da5c87443692ec87e51af2c252a35ee5f9cbd625ec269f', 4, '2025-09-27 10:48:55', '2025-09-27 10:48:55');
INSERT INTO `newsletter_subscriptions` (email, first_name, last_name, source, subscription_date, ip_address, status, unsubscribe_token, id, created_at, updated_at) VALUES ('abhijeetjha5400@gmail.com', 'Abhijeet', 'Jha', 'join_form', '2025-09-29 06:30:58', '0.0.0.0', 'active', '0277a2dac6e957ceb34cbe56e2af47b1', 5, '2025-09-29 06:30:58', '2025-09-29 06:30:58');
INSERT INTO `newsletter_subscriptions` (email, first_name, last_name, source, subscription_date, ip_address, status, unsubscribe_token, id, created_at, updated_at) VALUES ('test@example.com', 'Test', 'User', 'join_form', '2025-09-29 06:31:33', '0.0.0.0', 'active', '278c738e48d81b4f740c5c4e4ce30f52', 6, '2025-09-29 06:31:33', '2025-09-29 06:31:33');

-- Migrating data to waitlist_subscriptions table
DELETE FROM `waitlist_subscriptions`;
INSERT INTO `waitlist_subscriptions` (waitlist_id, name, email, submission_date, ip_address, user_agent, status, id, created_at, updated_at) VALUES ('WAIT-20250929-DB7E13', 'Test User', 'test@example.com', '2025-09-29 06:24:45', '127.0.0.1', 'Test Agent', 'active', 1, '2025-09-29 06:24:45', '2025-09-29 06:24:45');

-- Migrating data to blog_posts table
DELETE FROM `blog_posts`;
INSERT INTO `blog_posts` (id, title, slug, content, excerpt, author, status, featured_image, categories, tags, meta_description, views, created_at, updated_at, published_at) VALUES (1, 'Welcome to Your Journey of Transformation', 'welcome-to-your-journey-of-transformation', 'Welcome to The Story Tree, where your journey of personal transformation begins. Life coaching is not just about solving problems; it\'s about discovering your true potential and creating the life you\'ve always dreamed of.', 'Discover how life coaching can transform your life and help you achieve your goals.', 'Pallavi Singh', 'published', 'assets/images/image 1.jpg', Array, Array, 'Learn how life coaching can help you transform your life and achieve your goals.', 0, '2025-09-12 10:00:00', '2025-09-12 10:00:00', '2025-09-12 10:00:00');
INSERT INTO `blog_posts` (id, title, slug, content, excerpt, author, status, featured_image, categories, tags, meta_description, views, created_at, updated_at, published_at) VALUES (2, 'Overcoming Anxiety: A Step-by-Step Guide', 'overcoming-anxiety-step-by-step-guide', 'Anxiety can feel overwhelming, but with the right tools and mindset, you can learn to manage it effectively. In this comprehensive guide, we\'ll explore practical strategies for overcoming anxiety and building resilience.', 'Learn practical strategies to overcome anxiety and build mental resilience.', 'Pallavi Singh', 'published', 'assets/images/image 2.jpg', Array, Array, 'Discover effective strategies for managing anxiety and building mental resilience.', 0, '2025-09-11 14:30:00', '2025-09-11 14:30:00', '2025-09-11 14:30:00');
INSERT INTO `blog_posts` (title, slug, content, excerpt, categories, tags, featured_image, status, author, published_at, meta_title, meta_description, id, created_at, updated_at) VALUES ('The Science of Habit Formation', 'science-of-habit-formation', 'Understanding the psychology behind habit formation and how to create lasting positive changes in your life.', 'Learn the science behind building positive habits and breaking negative ones.', Array, Array, 'assets/images/image 3.jpg', 'published', 'Pallavi Singh', '2025-09-19 09:08:49', '', '', 3, '2025-09-19 09:08:49', '2025-09-19 09:08:49');

-- Migrating data to clients table
DELETE FROM `clients`;
INSERT INTO `clients` (id, name, email, phone, status, notes, created_at, updated_at) VALUES (1, 'Sarah Johnson', 'sarah.johnson@email.com', '+1-555-0123', 'active', 'Regular coaching client, very engaged in sessions', '2025-09-10 10:00:00', '2025-09-10 10:00:00');
INSERT INTO `clients` (id, name, email, phone, status, notes, created_at, updated_at) VALUES (2, 'Michael Chen', 'michael.chen@email.com', '+1-555-0124', 'active', 'Anxiety management program participant', '2025-09-09 14:30:00', '2025-09-09 14:30:00');
INSERT INTO `clients` (id, name, email, phone, status, notes, created_at, updated_at) VALUES (3, 'Emily Rodriguez', 'emily.rodriguez@email.com', '+1-555-0125', 'prospect', 'Interested in public speaking coaching', '2025-09-08 16:45:00', '2025-09-08 16:45:00');

-- Migrating data to services table
DELETE FROM `services`;
INSERT INTO `services` (id, name, description, category, duration, price, currency, features, status, featured, image, created_at, updated_at) VALUES (1, 'Life Coaching Sessions', 'One-on-one coaching sessions to help you identify goals, overcome obstacles, and create a roadmap for personal and professional success.', 'coaching', '60 minutes', 150, 'USD', Array, 'active', TRUE, '', '2025-09-12 08:00:00', '2025-09-12 08:00:00');
INSERT INTO `services` (id, name, description, category, duration, price, currency, features, status, featured, image, created_at, updated_at) VALUES (2, 'Anxiety Management Program', 'Comprehensive program designed to help you understand and manage anxiety through evidence-based techniques and personalized strategies.', 'mental-health', '8 weeks', 800, 'USD', Array, 'active', TRUE, '', '2025-09-12 08:30:00', '2025-09-12 08:30:00');
INSERT INTO `services` (id, name, description, category, duration, price, currency, features, status, featured, image, created_at, updated_at) VALUES (3, 'Public Speaking Confidence', 'Transform your public speaking anxiety into confidence with our specialized coaching program.', 'communication', '6 weeks', 600, 'USD', Array, 'active', FALSE, '', '2025-09-12 09:00:00', '2025-09-12 09:00:00');

-- Migrating data to events_workshops table
DELETE FROM `events_workshops`;
INSERT INTO `events_workshops` (id, title, description, event_type, date, time, duration, location, max_participants, current_participants, price, currency, status, featured_image, registration_link, requirements, instructor, created_at, updated_at) VALUES (1, 'Transform Your Life: 30-Day Challenge', 'Join our intensive 30-day transformation challenge designed to help you break through limiting beliefs and create lasting positive change in your life.', 'workshop', '2025-10-15', '10:00 AM', '4 hours', 'Online via Zoom', 50, 23, 199, 'USD', 'active', '', 'https://example.com/register/30-day-challenge', 'Open to all levels, no prior experience needed', 'Pallavi Singh', '2025-09-12 09:00:00', '2025-09-12 09:00:00');
INSERT INTO `events_workshops` (id, title, description, event_type, date, time, duration, location, max_participants, current_participants, price, currency, status, featured_image, registration_link, requirements, instructor, created_at, updated_at) VALUES (2, 'Anxiety Management Masterclass', 'Learn evidence-based techniques for managing anxiety and building emotional resilience in this comprehensive masterclass.', 'masterclass', '2025-10-22', '2:00 PM', '3 hours', 'Online via Zoom', 30, 18, 149, 'USD', 'active', '', 'https://example.com/register/anxiety-masterclass', 'Basic understanding of mental health concepts helpful', 'Pallavi Singh', '2025-09-11 16:00:00', '2025-09-11 16:00:00');

-- Migrating data to testimonials table
DELETE FROM `testimonials`;
INSERT INTO `testimonials` (id, client_name, client_title, client_company, content, rating, service, image, status, featured, created_at, updated_at) VALUES (1, 'Sarah Johnson', 'Marketing Manager', 'Tech Solutions Inc.', 'Pallavi\'s coaching completely transformed my approach to work-life balance. Her insights and guidance helped me overcome my anxiety and build confidence in both my personal and professional life. I can\'t recommend her enough!', 5, 'Life Coaching Sessions', '', 'approved', TRUE, '2025-09-10 15:30:00', '2025-09-10 15:30:00');
INSERT INTO `testimonials` (id, client_name, client_title, client_company, content, rating, service, image, status, featured, created_at, updated_at) VALUES (2, 'Michael Chen', 'Entrepreneur', 'Startup Ventures', 'The anxiety management program was a game-changer for me. Pallavi\'s techniques are practical and effective. I went from having daily panic attacks to feeling confident and in control. Thank you for giving me my life back!', 5, 'Anxiety Management Program', '', 'approved', TRUE, '2025-09-09 11:20:00', '2025-09-09 11:20:00');
INSERT INTO `testimonials` (id, client_name, client_title, client_company, content, rating, service, image, status, featured, created_at, updated_at) VALUES (3, 'Emily Rodriguez', 'Teacher', 'Elementary School', 'I was terrified of public speaking, but Pallavi\'s program helped me overcome my fears. Now I confidently present to large groups and even enjoy it! The transformation has been incredible.', 5, 'Public Speaking Confidence', '', 'approved', FALSE, '2025-09-08 14:45:00', '2025-09-08 14:45:00');

-- Migrating data to sessions table
DELETE FROM `sessions`;
INSERT INTO `sessions` (id, client_id, session_type, date, time, duration, status, notes, created_at, updated_at) VALUES (1, 1, 'life-coaching', '2025-09-15', '10:00', 60, 'scheduled', 'First session - goal setting and assessment', '2025-09-12 09:00:00', '2025-09-12 09:00:00');
INSERT INTO `sessions` (id, client_id, session_type, date, time, duration, status, notes, created_at, updated_at) VALUES (2, 2, 'anxiety-management', '2025-09-14', '14:00', 90, 'completed', 'Great progress on breathing techniques', '2025-09-11 10:30:00', '2025-09-11 10:30:00');
INSERT INTO `sessions` (id, client_id, session_type, date, time, duration, status, notes, created_at, updated_at) VALUES (3, 3, 'public-speaking', '2025-09-16', '16:00', 60, 'confirmed', 'Practice presentation skills', '2025-09-10 15:45:00', '2025-09-10 15:45:00');

-- Migrating data to payments table
DELETE FROM `payments`;
INSERT INTO `payments` (id, client_id, amount, currency, payment_method, status, description, payment_date, created_at, updated_at) VALUES (1, 1, 150, 'USD', 'credit-card', 'paid', 'Life coaching session', '2025-09-12', '2025-09-12 10:00:00', '2025-09-12 10:00:00');
INSERT INTO `payments` (id, client_id, amount, currency, payment_method, status, description, payment_date, created_at, updated_at) VALUES (2, 2, 800, 'USD', 'bank-transfer', 'paid', 'Anxiety management program - full payment', '2025-09-10', '2025-09-10 14:30:00', '2025-09-10 14:30:00');
INSERT INTO `payments` (id, client_id, amount, currency, payment_method, status, description, payment_date, created_at, updated_at) VALUES (3, 3, 600, 'USD', 'paypal', 'pending', 'Public speaking confidence program', '2025-09-15', '2025-09-08 16:45:00', '2025-09-08 16:45:00');

-- Migrating data to media_library table
DELETE FROM `media_library`;
INSERT INTO `media_library` (id, filename, original_name, file_path, file_type, file_size, alt_text, title, description, category, tags, uploaded_by, created_at, updated_at) VALUES (1, 'hero-image.jpg', 'hero-image.jpg', 'assets/images/hero-image.jpg', 'image/jpeg', 245760, 'Hero image for website', 'Website Hero Image', 'Main hero image used on the homepage', 'images', Array, 'admin', '2025-09-12 10:00:00', '2025-09-12 10:00:00');
INSERT INTO `media_library` (id, filename, original_name, file_path, file_type, file_size, alt_text, title, description, category, tags, uploaded_by, created_at, updated_at) VALUES (2, 'coaching-session.jpg', 'coaching-session.jpg', 'assets/images/coaching-session.jpg', 'image/jpeg', 189440, 'Life coaching session in progress', 'Coaching Session', 'Image showing a life coaching session', 'images', Array, 'admin', '2025-09-11 15:30:00', '2025-09-11 15:30:00');

-- Migrating data to analytics table
DELETE FROM `analytics`;
INSERT INTO `analytics` (id, metric, value, date, source, created_at) VALUES (1, 'page_views', 1250, '2025-09-12', 'homepage', '2025-09-12 23:59:59');
INSERT INTO `analytics` (id, metric, value, date, source, created_at) VALUES (2, 'contact_form_submissions', 8, '2025-09-12', 'contact_form', '2025-09-12 23:59:59');
INSERT INTO `analytics` (id, metric, value, date, source, created_at) VALUES (3, 'booking_requests', 3, '2025-09-12', 'booking_form', '2025-09-12 23:59:59');
INSERT INTO `analytics` (id, metric, value, date, source, created_at) VALUES (4, 'newsletter_signups', 12, '2025-09-12', 'newsletter_form', '2025-09-12 23:59:59');

-- Migrating data to email_log table
DELETE FROM `email_log`;
INSERT INTO `email_log` (timestamp, to, subject, message_preview, headers, status) VALUES ('2025-09-29 06:30:58', 'abhijeetjha5400@gmail.com', 'Welcome to Pallavi Singh Coaching - Form Confirmation', '
    
    
    
        
        
            body { font-family: Arial, sans-serif; line-height: 1....', Array, 'logged');
INSERT INTO `email_log` (timestamp, to, subject, message_preview, headers, status) VALUES ('2025-09-29 06:31:33', 'test@example.com', 'Welcome to Pallavi Singh Coaching - Form Confirmation', '
    
    
    
        
        
            body { font-family: Arial, sans-serif; line-height: 1....', Array, 'logged');

-- Migrating data to booking_submissions table
DELETE FROM `booking_submissions`;
INSERT INTO `booking_submissions` (first_name, last_name, email, service_type, session_type, preferred_date, preferred_time, timezone, goals, experience_level, additional_notes, terms_accepted, newsletter_subscription, submission_date, ip_address, user_agent, status, scheduled_date, session_notes, id, created_at, updated_at) VALUES ('Alice', 'Johnson', 'alice@example.com', 'habit-mastery', 'discovery', '2025-09-21', 'morning', 'UTC+5:30', 'I want to develop better daily habits and routines.', 'some', 'Available on weekends', TRUE, TRUE, '2025-09-11 04:47:50', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'pending', NULL, NULL, 1, '2025-09-14 04:47:50', '2025-09-14 04:47:50');

-- Migrating data to journey_submissions table
DELETE FROM `journey_submissions`;
INSERT INTO `journey_submissions` (name, age, city, issue_challenge, terms_accepted, submission_date, ip_address, user_agent, status, follow_up_notes, id, created_at, updated_at) VALUES ('Bob Wilson', 28, 'Mumbai', 'I struggle with public speaking and confidence.', TRUE, '2025-09-07 04:47:50', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'new', NULL, 1, '2025-09-14 04:47:50', '2025-09-14 04:47:50');

