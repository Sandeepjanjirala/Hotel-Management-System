<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo html_escape($page_title); ?> | HMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f1ff 0%, #f8fbff 55%, #eef4f8 100%);
        }

        .login-wrapper {
            min-height: 100vh;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            border: 0;
            border-radius: 14px;
            box-shadow: 0 1rem 3rem rgba(15, 23, 42, 0.12);
        }

        .brand-title {
            font-size: 1.7rem;
            font-weight: 700;
            color: #0d6efd;
        }

        .brand-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
        }

        .form-label {
            font-weight: 600;
        }

        .btn-login {
            min-height: 46px;
            font-weight: 600;
        }

        .validation-block p {
            margin-bottom: 0.35rem;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .is-invalid {
            border-color: #dc3545;
        }
        
        .is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center login-wrapper">
        <div class="col-12 d-flex justify-content-center">
            <div class="card login-card">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="brand-title">HMS Login</div>
                        <div class="brand-subtitle">Sign in to access your hospital panel</div>
                    </div>

                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo html_escape($this->session->flashdata('success')); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo html_escape($this->session->flashdata('error')); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (validation_errors()): ?>
                        <div class="alert alert-danger validation-block" role="alert">
                            <?php echo validation_errors(); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Login Error Display Area -->
                    <div id="loginError" class="alert alert-danger" style="display: none;" role="alert">
                        <span id="errorMessage"></span>
                    </div>

                    <?php echo form_open('auth/login', array('id' => 'loginForm', 'autocomplete' => 'off', 'novalidate' => 'novalidate')); ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input
                                type="email"
                                class="form-control form-control-lg"
                                id="email"
                                name="email"
                                value="<?php echo set_value('email'); ?>"
                                placeholder="Enter your email"
                                required
                            >
                            <div id="emailError" class="error-message"></div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input
                                type="password"
                                class="form-control form-control-lg"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                required
                            >
                            <div id="passwordError" class="error-message"></div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" id="loginButton" class="btn btn-primary btn-lg btn-login">
                                Login
                            </button>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"
></script>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>
<script>
$(function () {
    // Pre-fill email from session flash data if available
    var flashEmail = '<?php echo $this->session->flashdata('email'); ?>';
    if (flashEmail) {
        $('#email').val(flashEmail);
    }
    
    // Real-time validation on email input
    $('#email').on('input', function() {
        var email = $(this).val().trim();
        var emailError = $('#emailError');
        var emailField = $(this);
        
        if (email === '') {
            emailError.text('Email is required.');
            emailField.addClass('is-invalid');
        } else if (!isValidEmail(email)) {
            emailError.text('Please enter a valid email address.');
            emailField.addClass('is-invalid');
        } else {
            emailError.text('');
            emailField.removeClass('is-invalid');
        }
    });
    
    // Real-time validation on password input
    $('#password').on('input', function() {
        var password = $(this).val();
        var passwordError = $('#passwordError');
        var passwordField = $(this);
        
        if (password === '') {
            passwordError.text('Password is required.');
            passwordField.addClass('is-invalid');
        } else if (password.length < 6) {
            passwordError.text('Password must be at least 6 characters long.');
            passwordField.addClass('is-invalid');
        } else {
            passwordError.text('');
            passwordField.removeClass('is-invalid');
        }
    });
    
    $('#loginForm').on('submit', function(e) {
        // Prevent multiple submissions
        var loginButton = $('#loginButton');
        if (loginButton.prop('disabled')) {
            e.preventDefault();
            return false;
        }
        
        // Clear previous errors
        $('#loginError').hide();
        $('#emailError').text('');
        $('#email').removeClass('is-invalid');
        $('#passwordError').text('');
        $('#password').removeClass('is-invalid');
        
        var email = $('#email').val().trim();
        var password = $('#password').val();
        var isValid = true;
        var errorMessages = [];
        
        // Validate email
        if (email === '') {
            $('#emailError').text('Email is required.');
            $('#email').addClass('is-invalid');
            isValid = false;
            errorMessages.push('Email is required.');
        } else if (!isValidEmail(email)) {
            $('#emailError').text('Please enter a valid email address.');
            $('#email').addClass('is-invalid');
            isValid = false;
            errorMessages.push('Please enter a valid email address.');
        }
        
        // Validate password
        if (password === '') {
            $('#passwordError').text('Password is required.');
            $('#password').addClass('is-invalid');
            isValid = false;
            errorMessages.push('Password is required.');
        } else if (password.length < 6) {
            $('#passwordError').text('Password must be at least 6 characters long.');
            $('#password').addClass('is-invalid');
            isValid = false;
            errorMessages.push('Password must be at least 6 characters long.');
        }
        
        if (!isValid) {
            e.preventDefault();
            showError(errorMessages.join(' '));
            return false;
        }
        
        // Disable button and show loading state
        loginButton
            .prop('disabled', true)
            .text('Signing in...');
    });
    
    // Helper function to validate email format
    function isValidEmail(email) {
        var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailRegex.test(email);
    }
    
    // Helper function to show error messages
    function showError(message) {
        $('#errorMessage').text(message);
        $('#loginError').show();
        
        // Auto-hide error after 5 seconds if it's a server error
        setTimeout(function() {
            $('#loginError').fadeOut('slow');
        }, 5000);
    }
    
    // Handle server-side validation errors from PHP
    <?php if ($this->session->flashdata('error')): ?>
        var errorMessage = '<?php echo addslashes($this->session->flashdata('error')); ?>';
        if (errorMessage.toLowerCase().includes('password')) {
            $('#passwordError').text(errorMessage);
            $('#password').addClass('is-invalid');
        } else if (errorMessage.toLowerCase().includes('user')) {
            $('#emailError').text(errorMessage);
            $('#email').addClass('is-invalid');
        } else {
            showError(errorMessage);
        }
        // Re-enable button if there was an error
        $('#loginButton').prop('disabled', false).text('Login');
    <?php endif; ?>
    
    // Reset button state when form is reset
    $(document).ajaxStart(function() {
        // Any AJAX start events
    }).ajaxComplete(function() {
        // Any AJAX complete events
    });
    
    // Handle form reset (if needed)
    $('input').on('keydown', function(e) {
        if (e.key === 'Escape') {
            $(this).val('');
            $(this).removeClass('is-invalid');
            $(this).siblings('.error-message').text('');
            $('#loginError').hide();
        }
    });
});

// Alternative: Use AJAX for login without page refresh
/*
$(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        var loginButton = $('#loginButton');
        var formData = $(this).serialize();
        
        loginButton.prop('disabled', true).text('Signing in...');
        $('#loginError').hide();
        
        $.ajax({
            url: '<?php echo base_url('auth/login'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = response.redirect;
                } else {
                    if (response.error.toLowerCase().includes('password')) {
                        $('#passwordError').text(response.error);
                        $('#password').addClass('is-invalid');
                    } else if (response.error.toLowerCase().includes('user')) {
                        $('#emailError').text(response.error);
                        $('#email').addClass('is-invalid');
                    } else {
                        showError(response.error);
                    }
                    loginButton.prop('disabled', false).text('Login');
                }
            },
            error: function() {
                showError('An error occurred. Please try again.');
                loginButton.prop('disabled', false).text('Login');
            }
        });
    });
});
*/
</script>
</body>
</html>