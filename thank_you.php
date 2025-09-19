<?php
/**
 * Thank You Page - Pallavi Singh Coaching
 * Displays confirmation after successful form submission
 */

require_once 'config/database_json.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get form ID from URL
$form_id = $_GET['id'] ?? '';
$form_data = null;

// If form ID is provided, fetch form data
if (!empty($form_id)) {
    try {
        $db = Database::getInstance();
        $submissions = $db->where('join_submissions', ['form_id' => $form_id]);
        $form_data = !empty($submissions) ? $submissions[0] : null;
    } catch (Exception $e) {
        error_log("Error fetching form data: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Pallavi Singh Coaching</title>
    <meta name="description" content="Thank you for joining Pallavi Singh Coaching. Your transformation journey begins now.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/pallavi-logo.png">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .thank-you-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1A535C 0%, #4ECDC4 100%);
            position: relative;
            overflow: hidden;
        }
        
        .thank-you-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('./assets/images/img.png') repeat;
            background-size: cover;
            opacity: 0.1;
            z-index: 1;
        }
        
        .thank-you-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        
        .thank-you-card {
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .success-icon {
            font-size: 4em;
            color: #28a745;
            margin-bottom: 30px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .thank-you-title {
            font-size: 2.5em;
            color: #1A535C;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .thank-you-subtitle {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .form-id-display {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            display: inline-block;
        }
        
        .form-id-label {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        
        .form-id-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #1A535C;
            font-family: monospace;
        }
        
        .next-steps {
            background: #e8f5e8;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }
        
        .next-steps h3 {
            color: #155724;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .next-steps ul {
            list-style: none;
            padding: 0;
        }
        
        .next-steps li {
            padding: 8px 0;
            color: #155724;
            position: relative;
            padding-left: 25px;
        }
        
        .next-steps li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
        
        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 40px;
        }
        
        .btn-home {
            background: #1A535C;
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-home:hover {
            background: #144046;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-contact {
            background: #4ECDC4;
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-contact:hover {
            background: #3bb5ac;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .form-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .form-details h4 {
            color: #1A535C;
            margin-bottom: 15px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
        }
        
        .detail-value {
            color: #333;
        }
        
        @media (max-width: 768px) {
            .thank-you-card {
                padding: 40px 20px;
            }
            
            .thank-you-title {
                font-size: 2em;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <section class="thank-you-section">
        <div class="thank-you-container">
            <div class="thank-you-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                
                <h1 class="thank-you-title">Welcome to Your Journey!</h1>
                <p class="thank-you-subtitle">
                    Thank you for joining Pallavi Singh Coaching. We're excited to be part of your transformation story.
                </p>
                
                <?php if ($form_data): ?>
                    <div class="form-id-display">
                        <div class="form-id-label">Your Form ID:</div>
                        <div class="form-id-value"><?php echo htmlspecialchars($form_data['form_id']); ?></div>
                    </div>
                    
                    <div class="form-details">
                        <h4><i class="fas fa-user"></i> Your Details</h4>
                        <div class="detail-row">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($form_data['full_name']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Location:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($form_data['city'] . ', ' . $form_data['state']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Submitted:</span>
                            <span class="detail-value"><?php echo date('F j, Y \a\t g:i A', strtotime($form_data['submission_date'])); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="next-steps">
                    <h3><i class="fas fa-list-check"></i> What Happens Next?</h3>
                    <ul>
                        <li>We'll review your submission within 24 hours</li>
                        <li>Our team will contact you to schedule a consultation</li>
                        <li>We'll create a personalized coaching plan for you</li>
                        <li>Your transformation journey will begin</li>
                    </ul>
                </div>
                
                <div class="action-buttons">
                    <a href="index.html" class="btn-home">
                        <i class="fas fa-home"></i>
                        Return Home
                    </a>
                    <a href="mailto:pallavi@thestorytree.com" class="btn-contact">
                        <i class="fas fa-envelope"></i>
                        Contact Us
                    </a>
                </div>
                
                <p style="margin-top: 30px; color: #666; font-size: 0.9em;">
                    <i class="fas fa-info-circle"></i>
                    Keep your Form ID safe - you can use it to reference your submission.
                </p>
            </div>
        </div>
    </section>
    
    <script>
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add entrance animation
            const card = document.querySelector('.thank-you-card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
            
            // Copy form ID to clipboard functionality
            const formIdElement = document.querySelector('.form-id-value');
            if (formIdElement) {
                formIdElement.style.cursor = 'pointer';
                formIdElement.title = 'Click to copy Form ID';
                
                formIdElement.addEventListener('click', function() {
                    navigator.clipboard.writeText(this.textContent).then(() => {
                        const originalText = this.textContent;
                        this.textContent = 'Copied!';
                        this.style.color = '#28a745';
                        
                        setTimeout(() => {
                            this.textContent = originalText;
                            this.style.color = '#1A535C';
                        }, 2000);
                    });
                });
            }
        });
    </script>
</body>
</html>
