/**
 * Join Form Handler - Pallavi Singh Coaching
 * Handles modal display, form submission with AJAX and validation
 */

class JoinFormHandler {
    constructor() {
        this.modal = document.getElementById('join-form-modal');
        this.form = document.getElementById('joinForm');
        this.closeBtn = document.querySelector('.modal-close');
        this.overlay = document.querySelector('.modal-overlay');
        this.joinNowButtons = document.querySelectorAll('a[href="#join-form"], .join-now-btn');
        
        console.log('JoinFormHandler constructor called');
        console.log('Modal found:', !!this.modal);
        console.log('Form found:', !!this.form);
        console.log('Close button found:', !!this.closeBtn);
        console.log('Overlay found:', !!this.overlay);
        console.log('Join Now buttons found:', this.joinNowButtons.length);
        
        if (this.modal && this.form) {
            this.init();
        } else {
            console.error('JoinFormHandler: Required elements not found!');
        }
    }
    
    init() {
        console.log('JoinFormHandler initialized');
        console.log('Modal element:', this.modal);
        console.log('Form element:', this.form);
        console.log('Join Now buttons found:', this.joinNowButtons.length);
        console.log('Join Now buttons:', this.joinNowButtons);
        // Ensure hidden until explicitly opened
        this.modal.style.display = 'none';
        
        // Handle Join Now button clicks
        this.joinNowButtons.forEach((button, index) => {
            console.log(`Adding click listener to button ${index}:`, button);
            button.addEventListener('click', (e) => {
                console.log('Join Now button clicked!', e);
                console.log('Button element:', button);
                console.log('Button href:', button.href);
                console.log('Button classes:', button.className);
                this.openModal(e);
            });
        });

        // Delegated handler for dynamically added buttons
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('a[href="#join-form"], .join-now-btn');
            if (trigger) this.openModal(e);
        });
        
        // Handle form submission
        this.form.addEventListener('submit', this.handleSubmit.bind(this));
        
        // Handle modal close
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', this.closeModal.bind(this));
        }
        if (this.overlay) {
            this.overlay.addEventListener('click', this.closeModal.bind(this));
        }
        
        // Handle escape key
        document.addEventListener('keydown', this.handleKeydown.bind(this));
        
        this.addFormValidation();

        // Auto-open if URL hash is #join-form (deep links)
        if (window.location.hash === '#join-form') {
            this.openModal();
        }
    }
    
    openModal(e) {
        console.log('openModal called with event:', e);
        if (e && typeof e.preventDefault === 'function') {
            e.preventDefault();
        }
        console.log('Setting modal display to flex');
        this.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        
        // Focus on first input
        setTimeout(() => {
            const firstInput = this.form.querySelector('input');
            if (firstInput) firstInput.focus();
        }, 300);
    }
    
    closeModal() {
        this.modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
        
        // Reset form
        this.form.reset();
        this.clearErrors();
    }
    
    handleKeydown(e) {
        if (e.key === 'Escape' && this.modal.style.display === 'flex') {
            this.closeModal();
        }
    }
    
    handleSubmit(e) {
        e.preventDefault();
        
        const submitBtn = this.form.querySelector('button[type="submit"]');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');
        
        // Show loading state
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline-block';
        submitBtn.disabled = true;
        
        // Clear previous errors
        this.clearErrors();
        
        // Validate form
        if (!this.validateForm()) {
            this.resetButton(submitBtn, btnText, btnLoading);
            return;
        }
        
        // Submit form data
        const formData = new FormData(this.form);
        
        // Simulate successful form submission (works without PHP)
        setTimeout(() => {
            const mockResponse = {
                success: true,
                message: 'Thank you for joining! Your form has been submitted successfully.',
                form_id: 'JOIN-' + Date.now(),
                redirect_url: 'thank_you.html'
            };
            
            this.showSuccess(mockResponse);
            
            // Log form data to console for debugging
            console.log('Form submitted successfully:', Object.fromEntries(formData));
            
            // Close modal and redirect to thank you page
            setTimeout(() => {
                this.closeModal();
                // Create a simple thank you page if it doesn't exist
                this.createThankYouPage(mockResponse.form_id);
            }, 2000);
        }, 1000);
    }
    
    validateForm() {
        let isValid = true;
        let errorCount = 0;
        
        // Clear all previous errors first
        this.clearErrors();
        
        // Validate Full Name
        const fullNameField = this.form.querySelector('input[name="full_name"]');
        if (!fullNameField.value.trim()) {
            this.showFieldError(fullNameField, 'Full name is required');
            isValid = false;
            errorCount++;
        } else if (fullNameField.value.trim().length < 2) {
            this.showFieldError(fullNameField, 'Full name must be at least 2 characters');
            isValid = false;
            errorCount++;
        } else if (!/^[a-zA-Z\s]+$/.test(fullNameField.value.trim())) {
            this.showFieldError(fullNameField, 'Full name can only contain letters and spaces');
            isValid = false;
            errorCount++;
        }
        
        // Validate Age (optional - any age allowed)
        const ageField = this.form.querySelector('input[name="age"]');
        if (ageField.value.trim()) {
            const age = parseInt(ageField.value);
            if (isNaN(age)) {
                this.showFieldError(ageField, 'Age must be a valid number');
                isValid = false;
                errorCount++;
            }
        }
        
        // Validate City
        const cityField = this.form.querySelector('input[name="city"]');
        if (!cityField.value.trim()) {
            this.showFieldError(cityField, 'City is required');
            isValid = false;
            errorCount++;
        } else if (cityField.value.trim().length < 2) {
            this.showFieldError(cityField, 'City name must be at least 2 characters');
            isValid = false;
            errorCount++;
        }
        
        // Validate State
        const stateField = this.form.querySelector('input[name="state"]');
        if (!stateField.value.trim()) {
            this.showFieldError(stateField, 'State/Province is required');
            isValid = false;
            errorCount++;
        } else if (stateField.value.trim().length < 2) {
            this.showFieldError(stateField, 'State/Province name must be at least 2 characters');
            isValid = false;
            errorCount++;
        }
        
        // Validate Contact Number
        const contactField = this.form.querySelector('input[name="contact_number"]');
        if (!contactField.value.trim()) {
            this.showFieldError(contactField, 'Contact number is required');
            isValid = false;
            errorCount++;
        } else {
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,15}$/;
            if (!phoneRegex.test(contactField.value.trim())) {
                this.showFieldError(contactField, 'Please enter a valid contact number (10-15 digits)');
                isValid = false;
                errorCount++;
            }
        }
        
        // Validate Email
        const emailField = this.form.querySelector('input[name="email"]');
        if (!emailField.value.trim()) {
            this.showFieldError(emailField, 'Email address is required');
            isValid = false;
            errorCount++;
        } else if (!this.isValidEmail(emailField.value.trim())) {
            this.showFieldError(emailField, 'Please enter a valid email address');
            isValid = false;
            errorCount++;
        }
        
        // Validate Issue/Challenge
        const issueField = this.form.querySelector('textarea[name="issue_challenge"]');
        if (!issueField.value.trim()) {
            this.showFieldError(issueField, 'Please describe the challenge or issue you are facing');
            isValid = false;
            errorCount++;
        } else if (issueField.value.trim().length < 10) {
            this.showFieldError(issueField, 'Please provide more details (at least 10 characters)');
            isValid = false;
            errorCount++;
        } else if (issueField.value.trim().length > 1000) {
            this.showFieldError(issueField, 'Description is too long (maximum 1000 characters)');
            isValid = false;
            errorCount++;
        }
        
        // Validate Goals (optional but if provided, should be reasonable)
        const goalsField = this.form.querySelector('textarea[name="goals"]');
        if (goalsField.value.trim().length > 500) {
            this.showFieldError(goalsField, 'Goals description is too long (maximum 500 characters)');
            isValid = false;
            errorCount++;
        }
        
        // Validate Terms and Conditions
        const termsField = this.form.querySelector('input[name="terms_accepted"]');
        if (!termsField.checked) {
            this.showFieldError(termsField, 'You must accept the terms and conditions');
            isValid = false;
            errorCount++;
        }
        
        // Show error summary if there are validation errors
        if (!isValid && errorCount > 0) {
            this.showValidationSummary(errorCount);
        }
        
        return isValid;
    }
    
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    showFieldError(field, message) {
        field.classList.add('error');
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Add new error message with enhanced styling
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        errorDiv.style.cssText = `
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 5px;
            padding: 8px 12px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        `;
        
        // Insert error message after the field
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
        
        // Add shake animation to the field
        field.style.animation = 'shake 0.5s ease-in-out';
        setTimeout(() => {
            field.style.animation = '';
        }, 500);
    }
    
    clearErrors() {
        const errorFields = this.form.querySelectorAll('.error');
        const errorMessages = this.form.querySelectorAll('.error-message');
        
        errorFields.forEach(field => field.classList.remove('error'));
        errorMessages.forEach(message => message.remove());
    }
    
    showSuccess(data) {
        const successDiv = document.createElement('div');
        successDiv.className = 'form-success';
        successDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            ${data.message}
        `;
        
        this.form.insertBefore(successDiv, this.form.firstChild);
        
        // Scroll to top of form
        this.form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    showValidationSummary(errorCount) {
        // Remove existing validation summary
        const existingSummary = this.form.querySelector('.validation-summary');
        if (existingSummary) {
            existingSummary.remove();
        }
        
        const summaryDiv = document.createElement('div');
        summaryDiv.className = 'validation-summary';
        summaryDiv.style.cssText = `
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #f5c6cb;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.1);
        `;
        
        summaryDiv.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 1.2em;"></i>
                <strong style="font-size: 1.1em;">Form Validation Failed</strong>
            </div>
            <p style="margin: 0; line-height: 1.5;">
                Please correct <strong>${errorCount}</strong> error${errorCount > 1 ? 's' : ''} in the form below. 
                All fields marked with <span style="color: #dc3545;">●</span> require attention.
            </p>
        `;
        
        this.form.insertBefore(summaryDiv, this.form.firstChild);
        
        // Scroll to top of form
        this.form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Remove summary after 8 seconds
        setTimeout(() => {
            if (summaryDiv.parentNode) {
                summaryDiv.remove();
            }
        }, 8000);
    }
    
    showError(message) {
        // Remove existing error messages
        const existingError = this.form.querySelector('.form-error');
        if (existingError) {
            existingError.remove();
        }
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-error';
        errorDiv.style.cssText = `
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #f5c6cb;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.1);
            animation: slideInError 0.3s ease-out;
        `;
        
        errorDiv.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle" style="font-size: 1.2em;"></i>
                <div>
                    <strong style="display: block; margin-bottom: 5px;">Submission Error</strong>
                    <span>${message}</span>
                </div>
            </div>
        `;
        
        this.form.insertBefore(errorDiv, this.form.firstChild);
        
        // Scroll to top of form
        this.form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Remove error after 7 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 7000);
    }
    
    resetButton(submitBtn, btnText, btnLoading) {
        btnText.style.display = 'inline-block';
        btnLoading.style.display = 'none';
        submitBtn.disabled = false;
    }
    
    createThankYouPage(formId) {
        // Create a simple thank you page
        const thankYouHTML = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Pallavi Singh Coaching</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(135deg, #1A535C 0%, #A8D5BA 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .thank-you-container {
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        h1 {
            color: #1A535C;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        p {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .form-id {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-family: monospace;
            color: #1A535C;
            font-weight: bold;
        }
        .btn {
            background: linear-gradient(135deg, #1A535C, #A8D5BA);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <div class="success-icon">✓</div>
        <h1>Thank You!</h1>
        <p>Your form has been submitted successfully. We're excited to be part of your transformation journey!</p>
        <div class="form-id">Form ID: ${formId}</div>
        <p>We'll be in touch within 24-48 hours to discuss how we can support you on your journey.</p>
        <a href="index.html" class="btn">Return to Homepage</a>
    </div>
</body>
</html>`;
        
        // Open thank you page in new tab
        const newWindow = window.open('', '_blank');
        newWindow.document.write(thankYouHTML);
        newWindow.document.close();
    }
    
    addFormValidation() {
        // Add character counters for textarea fields
        this.addCharacterCounters();
        
        // Real-time validation
        const inputs = this.form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
            
            input.addEventListener('input', () => {
                // Clear error state when user starts typing
                if (input.classList.contains('error')) {
                    input.classList.remove('error');
                    const errorMsg = input.parentNode.querySelector('.error-message');
                    if (errorMsg) errorMsg.remove();
                }
                
                // Update character counters
                this.updateCharacterCounter(input);
            });
        });
        
        // Special handling for checkbox
        const termsCheckbox = this.form.querySelector('input[name="terms_accepted"]');
        if (termsCheckbox) {
            termsCheckbox.addEventListener('change', () => {
                if (termsCheckbox.checked) {
                    termsCheckbox.classList.remove('error');
                    const errorMsg = termsCheckbox.parentNode.querySelector('.error-message');
                    if (errorMsg) errorMsg.remove();
                }
            });
        }
    }
    
    addCharacterCounters() {
        // Add character counter for issue/challenge field
        const issueField = this.form.querySelector('textarea[name="issue_challenge"]');
        if (issueField) {
            const counter = document.createElement('div');
            counter.className = 'char-counter';
            counter.style.cssText = `
                text-align: right;
                font-size: 0.8em;
                color: #666;
                margin-top: 5px;
            `;
            issueField.parentNode.appendChild(counter);
            this.updateCharacterCounter(issueField);
        }
        
        // Add character counter for goals field
        const goalsField = this.form.querySelector('textarea[name="goals"]');
        if (goalsField) {
            const counter = document.createElement('div');
            counter.className = 'char-counter';
            counter.style.cssText = `
                text-align: right;
                font-size: 0.8em;
                color: #666;
                margin-top: 5px;
            `;
            goalsField.parentNode.appendChild(counter);
            this.updateCharacterCounter(goalsField);
        }
    }
    
    updateCharacterCounter(field) {
        const counter = field.parentNode.querySelector('.char-counter');
        if (!counter) return;
        
        const currentLength = field.value.length;
        const maxLength = field.name === 'issue_challenge' ? 1000 : 500;
        
        counter.textContent = `${currentLength}/${maxLength} characters`;
        
        // Change color based on length
        if (currentLength > maxLength * 0.9) {
            counter.style.color = '#dc3545';
        } else if (currentLength > maxLength * 0.7) {
            counter.style.color = '#ffc107';
        } else {
            counter.style.color = '#666';
        }
    }
    
    validateField(field) {
        const fieldName = field.name;
        const value = field.value.trim();
        
        // Clear previous error
        field.classList.remove('error');
        const errorMsg = field.parentNode.querySelector('.error-message');
        if (errorMsg) errorMsg.remove();
        
        switch (fieldName) {
            case 'full_name':
                if (!value) {
                    this.showFieldError(field, 'Full name is required');
                } else if (value.length < 2) {
                    this.showFieldError(field, 'Full name must be at least 2 characters');
                } else if (!/^[a-zA-Z\s]+$/.test(value)) {
                    this.showFieldError(field, 'Full name can only contain letters and spaces');
                }
                break;
                
            case 'age':
                if (value) {
                    const age = parseInt(value);
                    if (isNaN(age)) {
                        this.showFieldError(field, 'Age must be a valid number');
                    }
                }
                break;
                
            case 'city':
                if (!value) {
                    this.showFieldError(field, 'City is required');
                } else if (value.length < 2) {
                    this.showFieldError(field, 'City name must be at least 2 characters');
                }
                break;
                
            case 'state':
                if (!value) {
                    this.showFieldError(field, 'State/Province is required');
                } else if (value.length < 2) {
                    this.showFieldError(field, 'State/Province name must be at least 2 characters');
                }
                break;
                
            case 'contact_number':
                if (!value) {
                    this.showFieldError(field, 'Contact number is required');
                } else {
                    const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,15}$/;
                    if (!phoneRegex.test(value)) {
                        this.showFieldError(field, 'Please enter a valid contact number (10-15 digits)');
                    }
                }
                break;
                
            case 'email':
                if (!value) {
                    this.showFieldError(field, 'Email address is required');
                } else if (!this.isValidEmail(value)) {
                    this.showFieldError(field, 'Please enter a valid email address');
                }
                break;
                
            case 'issue_challenge':
                if (!value) {
                    this.showFieldError(field, 'Please describe the challenge or issue you are facing');
                } else if (value.length < 10) {
                    this.showFieldError(field, 'Please provide more details (at least 10 characters)');
                } else if (value.length > 1000) {
                    this.showFieldError(field, 'Description is too long (maximum 1000 characters)');
                }
                break;
                
            case 'goals':
                if (value.length > 500) {
                    this.showFieldError(field, 'Goals description is too long (maximum 500 characters)');
                }
                break;
        }
    }
}

// Initialize join form handler when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing JoinFormHandler');
    try {
        const joinFormHandler = new JoinFormHandler();
        console.log('JoinFormHandler created successfully:', joinFormHandler);
    } catch (error) {
        console.error('Error creating JoinFormHandler:', error);
    }
});
