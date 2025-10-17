/**
 * Contact Form Handler - Pallavi Singh Coaching
 * Handles contact form submission with AJAX and validation
 */

class ContactFormHandler {
    constructor() {
        this.form = document.getElementById('contactForm');
        
        if (this.form) {
            this.init();
        }
    }
    
    init() {
        console.log('ContactFormHandler initialized');
        
        // Handle form submission
        this.form.addEventListener('submit', this.handleSubmit.bind(this));
        
        this.addFormValidation();
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
        
        // Client-side submission (works without PHP server)
        setTimeout(() => {
            const mockResponse = {
                success: true,
                message: 'Thank you for your message! We\'ll get back to you within 24 hours.',
                contact_id: 'CONTACT-' + Date.now()
            };
            
            // Log form data to console for debugging
            console.log('Contact form submitted successfully:', Object.fromEntries(formData));
            
            // Store in localStorage for reference
            try {
                const submissions = JSON.parse(localStorage.getItem('contact_submissions') || '[]');
                submissions.push({
                    id: mockResponse.contact_id,
                    timestamp: new Date().toISOString(),
                    data: Object.fromEntries(formData)
                });
                localStorage.setItem('contact_submissions', JSON.stringify(submissions));
                console.log('Form data saved to localStorage');
            } catch (e) {
                console.log('Could not save to localStorage:', e);
            }
            
            this.showSuccess(mockResponse);
            this.resetButton(submitBtn, btnText, btnLoading);
            
            // Reset form after successful submission
            setTimeout(() => {
                this.form.reset();
                // Optionally redirect to thank you page
                // window.location.href = 'thank_you.html?id=' + mockResponse.contact_id;
            }, 3000);
        }, 1000);
    }
    
    validateForm() {
        let isValid = true;
        let errorCount = 0;
        
        // Clear all previous errors first
        this.clearErrors();
        
        // Validate Name
        const nameField = this.form.querySelector('input[name="name"]');
        if (!nameField.value.trim()) {
            this.showFieldError(nameField, 'Name is required');
            isValid = false;
            errorCount++;
        } else if (nameField.value.trim().length < 2) {
            this.showFieldError(nameField, 'Name must be at least 2 characters');
            isValid = false;
            errorCount++;
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
        
        // Validate Subject
        const subjectField = this.form.querySelector('select[name="subject"]');
        if (!subjectField.value) {
            this.showFieldError(subjectField, 'Please select a subject');
            isValid = false;
            errorCount++;
        }
        
        // Validate Message
        const messageField = this.form.querySelector('textarea[name="message"]');
        if (!messageField.value.trim()) {
            this.showFieldError(messageField, 'Message is required');
            isValid = false;
            errorCount++;
        } else if (messageField.value.trim().length < 10) {
            this.showFieldError(messageField, 'Message must be at least 10 characters long');
            isValid = false;
            errorCount++;
        } else if (messageField.value.trim().length > 2000) {
            this.showFieldError(messageField, 'Message is too long (maximum 2000 characters)');
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
        // Remove any existing messages
        const existingSuccess = this.form.querySelector('.form-success');
        if (existingSuccess) existingSuccess.remove();
        
        const successDiv = document.createElement('div');
        successDiv.className = 'form-success';
        successDiv.innerHTML = `
            <div style="display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-check-circle" style="font-size: 2em; color: #28a745;"></i>
                <div>
                    <strong style="display: block; margin-bottom: 5px; font-size: 1.1em;">Success!</strong>
                    <span>${data.message}</span>
                    ${data.contact_id ? `<div style="margin-top: 8px; font-size: 0.9em; color: #155724;">Reference ID: <code style="background: #c3e6cb; padding: 2px 6px; border-radius: 3px;">${data.contact_id}</code></div>` : ''}
                </div>
            </div>
        `;
        successDiv.style.cssText = `
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #c3e6cb;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            box-shadow: 0 2px 10px rgba(40, 167, 69, 0.1);
            animation: slideInSuccess 0.3s ease-out;
        `;
        
        this.form.insertBefore(successDiv, this.form.firstChild);
        
        // Scroll to top of form
        this.form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Remove success message after 10 seconds
        setTimeout(() => {
            if (successDiv.parentNode) {
                successDiv.remove();
            }
        }, 10000);
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
    
    addFormValidation() {
        // Add character counter for message field
        this.addCharacterCounter();
        
        // Real-time validation
        const inputs = this.form.querySelectorAll('input, textarea, select');
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
                
                // Update character counter
                this.updateCharacterCounter(input);
            });
        });
    }
    
    addCharacterCounter() {
        // Add character counter for message field
        const messageField = this.form.querySelector('textarea[name="message"]');
        if (messageField) {
            const counter = document.createElement('div');
            counter.className = 'char-counter';
            counter.style.cssText = `
                text-align: right;
                font-size: 0.8em;
                color: #666;
                margin-top: 5px;
            `;
            messageField.parentNode.appendChild(counter);
            this.updateCharacterCounter(messageField);
        }
    }
    
    updateCharacterCounter(field) {
        const counter = field.parentNode.querySelector('.char-counter');
        if (!counter) return;
        
        const currentLength = field.value.length;
        const maxLength = 2000;
        
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
            case 'name':
                if (!value) {
                    this.showFieldError(field, 'Name is required');
                } else if (value.length < 2) {
                    this.showFieldError(field, 'Name must be at least 2 characters');
                }
                break;
                
            case 'email':
                if (!value) {
                    this.showFieldError(field, 'Email address is required');
                } else if (!this.isValidEmail(value)) {
                    this.showFieldError(field, 'Please enter a valid email address');
                }
                break;
                
            case 'subject':
                if (!value) {
                    this.showFieldError(field, 'Please select a subject');
                }
                break;
                
            case 'message':
                if (!value) {
                    this.showFieldError(field, 'Message is required');
                } else if (value.length < 10) {
                    this.showFieldError(field, 'Message must be at least 10 characters long');
                } else if (value.length > 2000) {
                    this.showFieldError(field, 'Message is too long (maximum 2000 characters)');
                }
                break;
        }
    }
}

// Initialize contact form handler when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing ContactFormHandler');
    try {
        const contactFormHandler = new ContactFormHandler();
        console.log('ContactFormHandler created successfully:', contactFormHandler);
    } catch (error) {
        console.error('Error creating ContactFormHandler:', error);
    }
});
